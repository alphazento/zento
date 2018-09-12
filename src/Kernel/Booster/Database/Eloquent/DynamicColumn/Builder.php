<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicColumn;

use DB;
use Illuminate\Database\Eloquent\Model;
use Zento\Kernel\Facades\DynaColumnFactory;
use Illuminate\Database\Eloquent\Relations\Relation;

class Builder extends \Illuminate\Database\Eloquent\Builder {
    protected $append_columns;
    protected $dyn_eagerLoad;

    public function __construct(\Illuminate\Database\Eloquent\Builder $builder) {
        $this->query = $builder->getQuery();
        $this->model = $builder->getModel();
        $this->eagerLoad = $builder->getEagerLoads();
        $this->append_columns = [];
        $this->dyn_eagerLoad = [];
    }

    /**
     * direct join single type dynacolumn
     *
     * @param [type] $columnName
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
     * @param [type] $columnName
     * @return void
     */
    public function withDyn($columnName) {
        $eagerLoad = $this->parseWithRelations(func_get_args());
        $this->eagerLoad = array_merge($this->eagerLoad, $eagerLoad);
        $this->dyn_eagerLoad[$columnName] = 1;
        return $this;
    }

    /**
     * with option type dynacolumn
     *
     * @param [type] $columnName
     * @return void
     */
    public function withDyns($columnName) {
        $eagerLoad = $this->parseWithRelations(func_get_args());
        $this->eagerLoad = array_merge($this->eagerLoad, $eagerLoad);
        $this->dyn_eagerLoad[$columnName] = 2;
        return $this;
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