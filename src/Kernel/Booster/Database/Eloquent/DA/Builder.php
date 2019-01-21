<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA;

use DB;
use Zento\Kernel\Facades\DanamicAttributeFactory;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\Attribute;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttribute;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttributeSet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Builder extends \Illuminate\Database\Eloquent\Builder {
    // protected $append_columns;
    protected $dyn_eagerLoad;
    protected $isGetAllColumn;

    public function __construct(\Illuminate\Database\Eloquent\Builder $builder) {
        $this->query = $builder->getQuery();
        $this->model = $builder->getModel();
        $this->eagerLoad = $builder->getEagerLoads();
        // $this->append_columns = [];
        $this->dyn_eagerLoad = [];
    }

    /**
     * direct join single type DynamicAttribute
     *
     * @param string $attributeName
     * @return void
     */
    // public function joinDyn($attributeName) {
    //     $table = DanamicAttributeFactory::getTable($this->model, $attributeName);
    //     $dynColumn = sprintf('%s.value as %s', $table, $attributeName);
    //     $this->leftJoin($table, 
    //             sprintf('%s.%s', $this->model->getTable(), $this->model->getKeyName()),
    //             '=',
    //             sprintf('%s.foreignkey', $table));
    //     $this->append_columns[] = $dynColumn;
    //     return $this;
    // }

    /**
     * with single type dynamic attribute
     *
     * @param string $attributeName
     * @return $this
     */
    public function withSingleDynamicAttribute($attributeName) {
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
    public function withOptionDynamicAttribute($attributeName) {
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
    protected function getEagerModelDynamicAttributeSetIds(array $models)
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
        $attrSetIds = $this->getEagerModelDynamicAttributeSetIds($models);

        if ($this->isGetAllColumn) {
            $dynaAttrs = DanamicAttributeFactory::getDynamicAttributes($this->model, $attrSetIds);
            foreach($dynaAttrs as $row) {
                if ($row['single']) {
                    $this->withSingleDynamicAttribute($row['attribute']);
                } else {
                    $this->withOptionDynamicAttribute($row['attribute']);
                }
            }
        }
        $models = parent::eagerLoadRelations($models);
        if ($this->isGetAllColumn) {
            foreach($models as $model) {
                $model->setDynRelations($this->dyn_eagerLoad);
            }
        }
        return $models;
    }

    /**
     * Get the hydrated models without eager loading.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model[]
     */
    public function getModels($columns = ['*'])
    {
        $this->isGetAllColumn = \in_array('*', $columns);
        $models = parent::getModels($columns);
        if (count($models)) {
            $this->preloadRelation();
        }
        return $models;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get($columns = ['*'])
    {
        $keys = $this->performDynConditions();
        if ($keys !== null) {
            $this->whereIn($this->model->getQualifiedKeyName(), $keys);
        }
        $this->isGetAllColumn = \in_array('*', $columns);
        // if (count($this->append_columns) > 0) {
        //     $this->select($this->model->getTable() . '.*', ...$this->append_columns);
        // }

        // $this->preloadRelation();

        return parent::get($columns);
    }

    /**
     *
     * @return void
     */
    protected function preloadRelation() {
        if ($this->isGetAllColumn) {
            foreach($this->model->getPreloadRelations() ?? [] as $relation => $extra) {
                if ($relation === 'withcount') {
                    foreach($extra as $relation) {
                        $this->withCount($relation);
                    }
                } else {
                    $this->with(is_numeric($relation) && is_string($extra) ? $extra : $relation );
                }
            }
        }
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

    protected $dynConditionBuilders = [];
    protected function performDynConditions() {
        $modelIds = null;
        foreach($this->dynConditionBuilders as $condition) {
            $ret = $condition[0]->get()->pluck('foreignkey')->toArray();
            if ($modelIds === null) {
                $modelIds = $ret;
            } else {
                if ($condition[1] == 'and') {
                    $modelIds = array_intersect($modelIds, $ret);
                } 
                if ($condition[1] == 'or') {
                    $modelIds = array_merge($modelIds, $ret);
                }
            }
        }
        return $modelIds;
    }
    /**
     * Add a basic where clause to the query.
     *
     * @param  string|array|\Closure  $column
     * @param  mixed   $operator
     * @param  mixed   $value
     * @param  string  $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (!$this->buildConditionForDyn('where', $column, [$operator, $value], $boolean)) {
           return parent::where($column, $operator, $value, $boolean);
        }
        return $this;
    }

    protected function buildConditionForDyn($method, $column, $argvs, $boolean = 'and') {
        if (!is_string($column)) {
            return false;
        }
        $dynaAttrs = DanamicAttributeFactory::getDynamicAttributes($this->model, []);
        foreach($dynaAttrs ?? [] as $dyn) {
            if ($dyn['attribute'] == $column) {
                $instance = new ORM\OptionDynamicAttribute();
                $instance->setConnection($this->model->getConnectionName());
                $instance->setTable(DanamicAttributeFactory::getTable($this->model, $column, $dyn['single']));
                $builder = $instance->newQuery()->{$method}('value', ...$argvs)->select(['foreignkey']);
                $this->dynConditionBuilders[] = [$builder, $boolean];
                return true;
            }
        }
        return false;
    }

    public function whereIn($column, $values, $boolean = 'and', $not = false) {
        if (!$this->buildConditionForDyn('whereIn', $column, [$values, $boolean, $not], $boolean)) {
            return parent::whereIn($column, $values, $boolean, $not); 
        }
        return $this;
    }
}