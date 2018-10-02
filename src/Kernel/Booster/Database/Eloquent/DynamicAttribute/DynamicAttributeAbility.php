<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute;

use Zento\Kernel\Facades\DanamicAttributeFactory;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\Builder;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\ModelDynamicAttribute;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\AttributeSet;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\Attribute;

trait DynamicAttributeAbility
{
    /**
     * Get a new query builder for the model's table.
     *
     * @return \Zento\Kernel\Foundation\Eloquent\DynamicAttribute\Builder
     */
    public function newQuery()
    {
        return new Builder(parent::newQuery());
    }

    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $attributeName
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneDyn($attributeName, $foreignKey = 'foreignKey', $localKey = null)
    {
        $instance =new ORM\DynamicSingleAttribute();
        $instance->setConnection($this->getConnectionName());
        $instance->setTable(DanamicAttributeFactory::getTable($this, $attributeName));
        
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $attributeName
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyDyns($attributeName, $foreignKey = 'foreignKey', $localKey = null)
    {
        $instance = new ORM\DynamicOptionAttribute();
        $instance->setConnection($this->getConnectionName());
        $instance->setTable(DanamicAttributeFactory::getTable($this, $attributeName, false));

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasMany(
            $instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey
        );
    }

    public function attributeset() {
        return $this->hasOne(AttributeSet::class, 'id', 'attribute_set_id');
    }
}