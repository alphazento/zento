<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute;

use DB;
use Zento\Kernel\Facades\DanamicAttributeFactory;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\ModelDynamicAttribute;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\AttributeSet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Builder extends \Illuminate\Database\Eloquent\Builder {
    protected $append_columns;
    protected $dyn_eagerLoad;
    protected $_withDynamicOptionAttributeet;

    public function __construct(\Illuminate\Database\Eloquent\Builder $builder) {
        $this->query = $builder->getQuery();
        $this->model = $builder->getModel();
        $this->eagerLoad = $builder->getEagerLoads();
        $this->append_columns = [];
        $this->dyn_eagerLoad = [];
        $this->_withDynamicOptionAttributeet = false;
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
     * load dynamicattribute set
     * @return $this
     */
    public function withDynamicOptionAttributeet() {
        $this->_withDynamicOptionAttributeet = true;
        $this->with(['attributeset.attributes']);
        return $this;
    }

    
    /**
     * Eager load the relationships for the models.
     *
     * @param  array  $models
     * @return array
     */
    public function eagerLoadRelations(array $models)
    {
        if ($this->_withDynamicOptionAttributeet) {
            $collection = ModelDynamicAttribute::select('attribute', 'single')
                ->where('model', $this->getModel()->getTable())
                ->get()
                ->toArray();
            foreach($collection as $item) {
                if ($item['single']) {
                    $this->withDynamicSingleAttribute($item['attribute']);
                } else {
                    $this->withDynamicOptionAttribute($item['attribute']);
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
        if (count($this->append_columns) > 0) {
            $this->select($this->model->getTable() . '.*', ...$this->append_columns);
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