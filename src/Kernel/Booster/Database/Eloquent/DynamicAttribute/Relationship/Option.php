<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\Relationship;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as OriginBuilder;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\DynamicOptionAttribute;

class Option extends Base {
    protected $items;

    /**
     * @param Model $parent
     * @param string $model
     */
    public function __construct($parent, $table) {
        $this->parent = $parent;
        $this->table = $table;
        $this->loadItems();
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
        $this->items = [];
        foreach($all as $row) {
            $key = md5(strtolower($row->value));
            $model = new DynamicOptionAttribute();
            $model->setRawAttributes((array)$row, true);
            $model->exists = true;
            $this->items[$key] = $model;
        }
        return $this;
    }

    /**
     * find a Model instance by parent's key
     *
     * @return Model Dyna Attribute instance
     */
    protected function findModel($value = null) {
        $key = md5(strtolower($value));
        return isset($this->items[$key]) ? $this->items[$key] : null;
    }

    /**
     * add a new dynamic attribute value
     *
     * @param string $value
     * @return Model
     */
    public function new($value) {
        $model = $this->findModel($value) ?? $this->makeModel();
        $model->foreignkey = $this->parent->getKey();
        $model->value = $value;
        $model->save();
        $key = md5(strtolower($value));
        $this->items[$key] = $model;
        return $model;
    }

    /**
     * update a dynamic attribute value
     *
     * @param string $value
     * @return void
     */
    public function update($oldValue, $newValue) {
        $model = $this->findModel($oldValue) ?? $this->makeModel();
        $model->foreignkey = $this->parent->getKey();
        $model->value = $newValue;
        $model->save();

        //unset old model
        $key = md5(strtolower($oldValue));
        unset($this->items[$key]);

        //set new model
        $key = md5(strtolower($newValue));
        $this->items[$key] = $model;
        return $model;
    }

    /**
     * delete a dynamic attribute value
     *
     * @param Model $parent
     * @return void
     */
    public function delete($oldValue) {
        if ($model = $this->findModel($oldValue)) {
            $model->delete();
            $key = md5(strtolower($oldValue));
            unset($this->items[$key]);
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
        $this->items = [];
        return $this;
    }

    public function getValues() {
        return DB::connection($this->parent->getConnectionName())
            ->table($this->table)
            ->select('value')
            ->where('foreignkey', $this->parent->getKey())
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
        $existKeys = array_keys($this->items);
        $needtodeletes = array_diff($existKeys, $keys);
        $needtodadds = array_diff($keys, $existKeys);
        foreach($needtodeletes as $key) {
            if ($model = $this->items[$key]) {
                $model->delete();
                unset($this->items[$key]);
            }
        }
        foreach($needtodadds as $key) {
            $this->new($valueMap[$key]);
        }
    }

    /**
     * update
     *
     * @param array ['oldvalue'=>'newvalue']
     * @return void
     */
    public function updateValues($values) {
        foreach($values as $old => $new) {
            $this->update($old, $new);
        }
    }
}
