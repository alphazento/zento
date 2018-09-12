<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicColumn;

use Zento\Kernel\Facades\DynaColumnFactory;
use Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\Builder;

trait DynamicColumnAbility
{
    /**
     * Get a new query builder for the model's table.
     *
     * @return \Zento\Kernel\Foundation\Eloquent\DynamicColumn\Builder
     */
    public function newQuery()
    {
        return new Builder(parent::newQuery());
    }

    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $columnName
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneDyn($columnName, $foreignKey = 'foreignKey', $localKey = null)
    {
        $instance =new ORM\SingleDynaColumn();
        $instance->setConnection($this->getConnectionName());
        $instance->setTable(DynaColumnFactory::getTable($this, $columnName));
        
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }
    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $columnName
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyDyns($columnName, $foreignKey = 'foreignKey', $localKey = null)
    {
        $instance = new ORM\OptionDynaColumn();
        $instance->setConnection($this->getConnectionName());
        $instance->setTable(DynaColumnFactory::getTable($this, $columnName));

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasMany(
            $instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey
        );
    }

    /**
     * list all dyna columns which belongs to this model
     *
     * @return array
     */
    public static function listDynaColumns() {
        (new \Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\Schema\Mysql)->listDynaColumns(new static);
    }
}