<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\Relationship;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as OriginBuilder;

class Single extends Base {
    /**
     * find a Model instance by parent's key
     *
     * @param Model $parent
     * @return Model Dyna Column instance
     */
    protected function findModel($parent = null) {
        $row = DB::connection(($parent ?? $this->parent)->getConnectionName())
            ->table($this->table)
            ->where('foreignkey', ($parent ?? $this->parent)->getKey())
            ->first();
        
        if ($row) {
            $model = $this->makeModel($parent);
            $model->setRawAttributes((array)$row, true);
            $model->exists = true;
            return $model;
        } else {
            return null;
        }
    }

    /**
     * add a new dynacolumn value
     *
     * @param string $columnName
     * @param Model $parent
     * @return Model
     */
    public function new($value, $parent = null) {
        // $model = $this->makeModel();
        // $model->foreignkey = ($parent ?? $this->parent)->getKey();
        // $model->value = $value;
        // $model->save();
        // return $model;
        return $this->update($value, $parent);
    }

    /**
     * update a dynacolumn value
     *
     * @param string $columnName
     * @param Model $parent
     * @return void
     */
    public function update($value, $parent = null) {
        $model = $this->findModel($parent) ?? $this->makeModel();
        $model->foreignkey = ($parent ?? $this->parent)->getKey();
        $model->value = $value;
        $model->save();
        return $model;
    }

    /**
     * delete a dynacolumn value
     *
     * @param Model $parent
     * @return void
     */
    public function delete($parent = null) {
        if ($model = $this->findModel($parent)) {
            $model->delete();
        }
    }

    public function getValue($parent = null) {
        if ($model = $this->findModel($parent)) {
            return $model->value;
        }
        return null;
    }
}
