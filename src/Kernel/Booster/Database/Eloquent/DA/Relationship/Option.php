<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\Relationship;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder as OriginBuilder;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttribute\Option as OptionDynamicAttribute;

/**
 * Normally only use for assign new dynamic attribute
 */
class Option extends Base {
    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $items;

    /**
     * @param Model $parent
     * @param string $model
     * @param array|Collection|null $items
     */
    public function __construct($parent, $table, Collection $items = null) {
        $this->parent = $parent;
        $this->table = $table;
        if ($items === null) {
            $this->loadItems();
        } else {
            $this->items = $items;
        }
    }

    public function isSingle() {
        return false;
    }

    /**
     * load all items first
     *
     * @return void
     */
    protected function loadItems() {
        $all = DB::connection($this->parent->getConnectionName())
            ->table($this->table)
            ->where('foreignkey', $this->parent->getKey())
            ->get();
        $items = [];
        foreach($all as $row) {
            $key = md5(strtolower($row->value));
            $model = new OptionDynamicAttribute();
            $model->setRawAttributes((array)$row, true);
            $model->exists = true;
            $items[$key] = $model;
        }
        $this->items = new Collection($items);
        return $this;
    }

    /**
     * find a Model instance by parent's key
     *
     * @return Model Dyna Attribute instance
     */
    protected function findModel($value = null) {
        $key = md5(strtolower($value));
        return $this->items->get($key, null);
    }

    /**
     * add a new dynamic attribute value
     *
     * @param string $value
     * @return Model
     */
    public function newValue($value, $disabled = -1, $sort = -1) {
        $model = $this->findModel($value) ?? $this->makeModel();
        $model->foreignkey = $this->parent->getKey();
        $model->value = $value;
        if ($disabled > -1) {
            $model->disabled = $disabled;
        }
        if ($sort > -1) {
            $model->sort = $sort;
        }
        $model->save();

        $key = md5(strtolower($value));
        $this->items->offsetSet($key, $model);
        return $model;
    }

    /**
     * update a dynamic attribute value
     *
     * @param string $value
     * @return void
     */
    public function updateValue($oldValue, $newValue, $disabled = -1, $sort = -1) {
        $model = $this->findModel($oldValue) ?? $this->makeModel();
        $model->foreignkey = $this->parent->getKey();
        $model->value = $newValue;
        if ($disabled > -1) {
            $model->disabled = $disabled;
        }
        if ($sort > -1) {
            $model->sort = $sort;
        }
        $model->save();

        //unset old model
        $key = md5(strtolower($oldValue));
        $this->items->offsetUnset($key);

        //set new model
        $key = md5(strtolower($newValue));
        $this->items->offsetSet($key, $model);
        return $model;
    }

    /**
     * delete a dynamic attribute value
     *
     * @param Model $parent
     * @return void
     */
    public function deleteValue($oldValue) {
        if ($model = $this->findModel($oldValue)) {
            $model->delete();
            $key = md5(strtolower($oldValue));
            $this->items->offsetUnset($key);
        }
    }

    /**
     * delete all items
     *
     * @param [type] $parent
     * @return void
     */
    public function purge($parent) {
        DB::connection($this->parent->getConnectionName())
            ->table($this->table)
            ->where('foreignkey', $this->parent->getKey())
            ->delete();
        $this->items = new Collection([]);
        return $this;
    }

    public function getValues($includesDisabled = true) {
        return DB::connection($this->parent->getConnectionName())
            ->table($this->table)
            ->select('value')
            ->where('foreignkey', $this->parent->getKey())
            ->orderBy('sort')
            ->orderBy('id')
            ->get()
            ->toArray();
    }


    /**
     * reset a whole values, if old values not in the new values set, will be delete.
     *
     * @param array $values  ['value1', 'value2']
     * @return void
     */
    public function setValues($values = []) {
        if (count($values) == 0) {
            return $this->purge();
        }

        $valueMap = [];
        foreach($values as $v) {
            $valueMap[md5(strtolower($v))] = $v;
        }
        $keys = array_keys($valueMap);
        $existKeys = $this->items->modelKeys();
        $needtodeletes = array_diff($existKeys, $keys);
        $needtodadds = array_diff($keys, $existKeys);
        foreach($needtodeletes as $key) {
            if ($model = $this->items[$key]) {
                $model->deleteValue();
                $this->items->forget([$key]);
            }
        }
        foreach($needtodadds as $key) {
            $this->newValue($valueMap[$key]);
        }
        return $this;
    }

    /**
     * update
     *
     * @param array ['oldvalue'=>'newvalue']
     * @return void
     */
    public function updateValues($values) {
        foreach($values as $old => $new) {
            $this->updateValue($old, $new);
        }
    }
}
