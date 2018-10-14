<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\Relationship;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as OriginBuilder;

class Single extends Base {
    /**
     * find a Model instance by parent's key
     *
     * @return Model Dyna Attribute instance
     */
    protected function findModel() {
        if ($this->model) {
            return $this->model;
        }
        $row = DB::connection($this->parent->getConnectionName())
            ->table($this->table)
            ->where('foreignkey', $this->parent->getKey())
            ->first();
        
        if ($row) {
            $this->model = $this->makeModel();
            $this->model->setRawAttributes((array)$row, true);
            $this->model->exists = true;
            return $this->model;
        } else {
            return null;
        }
    }

    /**
     * add a new dynamic attribute value
     *
     * @param string $value
     * @param boolean $pesistInstant
     * @return Model
     */
    public function new($value, $pesistInstant = true) {
        // $model = $this->makeModel();
        // $model->foreignkey = ($parent ?? $this->parent)->getKey();
        // $model->value = $value;
        // $model->save();
        // return $model;
        return $this->update($value, $pesistInstant);
    }

    /**
     * update a dynamic attribute value
     *
     * @param string $value
     * @param boolean $pesistInstant
     * @return void
     */
    public function update($value, $pesistInstant = true) {
        if ($model = $this->findModel() ?? $this->makeModel()) {
            $model->foreignkey = $this->parent->getKey();
            $model->value = $value;
            if($pesistInstant) {
                $model->save();
            }
            return $model;
        }
    }

    /**
     * delete a dynamic attribute value
     *
     * @return void
     */
    public function delete() {
        if ($model = $this->findModel()) {
            $model->delete();
        }
    }

    public function getValue() {
        if ($model = $this->findModel()) {
            return $model->value;
        }
        return null;
    }

    public function save() {
        if ($model = $this->findModel()) {
            $model->save();
        }
    }
}
