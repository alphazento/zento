<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicColumn;

use DB;
use Zento\Kernel\Facades\DynaColumnFactory;
use Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\ORM\ModelDynacolumn;
use Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\ORM\DynacolumnSet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Builder extends \Illuminate\Database\Eloquent\Builder {
    protected $append_columns;
    protected $dyn_eagerLoad;
    protected $_withDynSet;

    public function __construct(\Illuminate\Database\Eloquent\Builder $builder) {
        $this->query = $builder->getQuery();
        $this->model = $builder->getModel();
        $this->eagerLoad = $builder->getEagerLoads();
        $this->append_columns = [];
        $this->dyn_eagerLoad = [];
        $this->_withDynSet = false;
    }

    /**
     * direct join single type dynacolumn
     *
     * @param string $columnName
     * @return void
     */
    public function joinDyn($columnName) {
        $table = DynaColumnFactory::getTable($this->model, $columnName);
        $dynColumn = sprintf('%s.value as %s', $table, $columnName);
        $this->leftJoin($table, 
                sprintf('%s.%s', $this->model->getTable(), $this->model->getKeyName()),
                '=',
                sprintf('%s.foreignkey', $table));
        $this->append_columns[] = $dynColumn;
        return $this;
    }

    /**
     * with single type dynacolumn
     *
     * @param string $columnName
     * @return $this
     */
    public function withDyn($columnName) {
        $eagerLoad = $this->parseWithRelations(func_get_args());
        $this->eagerLoad = array_merge($this->eagerLoad, $eagerLoad);
        $this->dyn_eagerLoad[$columnName] = 1;      //1 means single
        return $this;
    }

    /**
     * with option type dynacolumn
     *
     * @param string $columnName
     * @return $this
     */
    public function withDyns($columnName) {
        $eagerLoad = $this->parseWithRelations(func_get_args());
        $this->eagerLoad = array_merge($this->eagerLoad, $eagerLoad);
        $this->dyn_eagerLoad[$columnName] = 2;     //2 means options
        return $this;
    }

    /**
     * load dynacolumn set
     * @return $this
     */
    public function withDynSet() {
        $this->_withDynSet = true;
        $this->with(['dynacolumnset.dynacolumns']);
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
        if ($this->_withDynSet) {
            $collection = ModelDynacolumn::select('dynacolumn', 'single')
                ->where('model', $this->getModel()->getTable())
                ->get()
                ->toArray();
            foreach($collection as $item) {
                if ($item['single']) {
                    $this->withDyn($item['dynacolumn']);
                } else {
                    $this->withDyns($item['dynacolumn']);
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
     * get dynamic column relation
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