<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute;

use DB;
use Zento\Kernel\Facades\DanamicAttributeFactory;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\Attribute;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\ModelDynamicAttribute;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\AttributeSet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Builder extends \Illuminate\Database\Eloquent\Builder {
    protected $append_columns;
    protected $dyn_eagerLoad;
    protected $isGetAllColumn;

    public function __construct(\Illuminate\Database\Eloquent\Builder $builder) {
        $this->query = $builder->getQuery();
        $this->model = $builder->getModel();
        $this->eagerLoad = $builder->getEagerLoads();
        $this->append_columns = [];
        $this->dyn_eagerLoad = [];
    }

    /**
     * direct join single type DynamicAttribute
     *
     * @param string $attributeName
     * @return void
     */
    public function joinDyn($attributeName) {
        $table = DanamicAttributeFactory::getTable($this->model, $attributeName);
        $dynColumn = sprintf('%s.value as %s', $table, $attributeName);
        $this->leftJoin($table, 
                sprintf('%s.%s', $this->model->getTable(), $this->model->getKeyName()),
                '=',
                sprintf('%s.foreignkey', $table));
        $this->append_columns[] = $dynColumn;
        return $this;
    }

    /**
     * with single type dynamic attribute
     *
     * @param string $attributeName
     * @return $this
     */
    public function withDynamicSingleAttribute($attributeName) {
        $eagerLoad = $this->parseWithRelations(func_get_args());
        $this->eagerLoad = array_merge($this->eagerLoad, $eagerLoad);
        $this->dyn_eagerLoad[$attributeName] = 1;      //1 means single
        return $this;
    }

    /**
     * with option type dynamic attribute
     *
     * @param string $attributeName
     * @return $this
     */
    public function withDynamicOptionAttribute($attributeName) {
        $eagerLoad = $this->parseWithRelations(func_get_args());
        $this->eagerLoad = array_merge($this->eagerLoad, $eagerLoad);
        $this->dyn_eagerLoad[$attributeName] = 2;     //2 means options
        return $this;
    }
    
    /**
     * Gather the keys from an array of related models.
     *
     * @param  array  $models
     * @return array
     */
    protected function getEagerModelAttributeSetIds(array $models)
    {
        $keys = [];

        // First we need to gather all of the keys from the parent models so we know what
        // to query for via the eager loading query. We will add them to an array then
        // execute a "where in" statement to gather up all of those related records.
        foreach ($models as $model) {
            if (! is_null($value = $model->attribute_set_id)) {
                $keys[$value] = 1;
            }
        }

        return array_keys($keys);
    }

    /**
     * Eager load the relationships for the models.
     *
     * @param  array  $models
     * @return array
     */
    public function eagerLoadRelations(array $models)
    {
        $attrSetIds = $this->getEagerModelAttributeSetIds($models);

        if ($this->isGetAllColumn) {
            $dynaAttrs = DanamicAttributeFactory::getModelDynamicAttributes($this->model, $attrSetIds);
            foreach($dynaAttrs as $row) {
                if ($row['single']) {
                    $this->withDynamicSingleAttribute($row['attribute']);
                } else {
                    $this->withDynamicOptionAttribute($row['attribute']);
                }
            }
        }
        return parent::eagerLoadRelations($models);
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get($columns = ['*'])
    {
        $this->isGetAllColumn = ($columns == ['*']);

        if (count($this->append_columns) > 0) {
            $this->select($this->model->getTable() . '.*', ...$this->append_columns);
        }


        if ($this->isGetAllColumn && property_exists($this->model, 'preload_relations')) {
            foreach($this->model->preload_relations ?? [] as $relation) {
                $this->with($relation);
            }
        }

        return parent::get($columns);
    }

    /**
     * Get the relation instance for the given relation name.
     *
     * @param  string  $name
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function getRelation($name)
    {
        if (isset($this->dyn_eagerLoad[$name])) {
            return $this->getDynRelation($name);
        } else {
            return parent::getRelation($name);
        }
    }

    /**
     * get dynamic Attribute relation
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    protected function getDynRelation($name) {
        return Relation::noConstraints(function () use ($name) {
            try {
                if ($this->dyn_eagerLoad[$name] === 1) {
                    return $this->getModel()->hasOneDyn($name, 'foreignkey');
                } else {
                    return $this->getModel()->hasManyDyns($name, 'foreignkey');
                }
            } catch (BadMethodCallException $e) {
                throw RelationNotFoundException::make($this->getModel(), $name);
            }
        });
    }
}