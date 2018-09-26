<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\Schema;

use DB;
use Illuminate\Support\Str;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class Mysql {
    public function addParentKeyColumn(Model $parent, Blueprint $table, $singleMode = true) {
        $collection = DB::connection($parent->getConnectionName())
            ->select(sprintf("SHOW COLUMNS FROM %s WHERE field = '%s'", $parent->getTable(), $parent->getKeyName()));
        if ($field = $collection[0] ?? null) {
            $str = $field->Type;
            if (preg_match('/(\w+)\((\d+)\)([\s\w]+)?/', $str, $match)) {
                $type = $match[1];
                $len = $match[2];
                
                $column = $this->createParentKeyColumn($table, $type, $len, true);
                if ($extra = trim($match[3] ?? '')) {
                    $column->{$extra}();
                }
                if ($singleMode) {
                    $column->unique();
                }
            } else {
                $column = $table->{$this->createParentKeyColumn($type)}($table, $type, $len);
                if ($singleMode) {
                    $column->unique();
                }
            }
        }
    }

    /**
     * create parent key column in dynamic column table
     *
     * @param Blueprint $table
     * @param string $type
     * @param number $len
     * @param boolean $keyCheck
     * @return void
     */
    protected function createParentKeyColumn(Blueprint $table, $type, $len, $keyCheck = false) {
        $columnName = 'foreignkey';
        switch($type) {
            case 'char':
                return $table->char($columnName, $len);
            case 'varchar':
                return $table->string($columnName, $len);
            case 'tinyint':
                return $table->tinyInteger($columnName);
                break;
            case 'smallint':
                return $table->smallInteger($columnName);
            case 'mediumint':
                return $table->mediumInteger($columnName);
            case 'int':
                return $table->integer($columnName);
            case 'bigint':
                return $table->bigInteger($columnName);
            
            default:
                if ($keyCheck) {
                    throw new \Exception(sprintf('%s can not be a primary key.', $type));
                }
        }
    }

    public function addValueColumne(Blueprint $table, $type, ...$params) {
        $columnName = 'value';
        switch($type) {
            case 'tinyInteger':
            case 'smallInteger':
            case 'integer':
            case 'mediumInteger':
            case 'bigInteger':
            case 'binary':
            case 'boolean':
            case 'text':
            case 'mediumText':
            case 'longText':
            case 'time':
            case 'timestamp':
            case 'date':
            case 'float':
                return $table->{$type}($columnName);
                break;
            case 'dateTime':
            case 'datetime':
                return $table->dateTime($columnName);
            case 'varchar':
                return $table->string($columnName);
                break;
            case 'char':
            case 'string':
            case 'decimal':
            case 'double':
            case 'enum':
                return $table->{$type}($columnName, ...$params);
            default:
                throw new \Exception(sprintf('%s is not supported', $type));
        }
    }

    public function listDynaColumns(Model $parent) {
        $parentTable = $parent->getTable(); 
        $collection = DB::connection($parent->getConnectionName())
            ->select(sprintf("SHOW Tables like '%s_dyn", $parentTable) . "%'");
        $columns = [];
        foreach($collection ?? [] as $row) {
            $row = json_decode(json_encode($row), true);
            foreach($row as $k => $column) {
                $column = Str::singular(substr($column, strlen($parentTable)));
                $isSingle = Str::startsWith($column, '_dyn_');
                $columns[] = [$isSingle, substr($column, strlen('_dyn_') + ($isSingle ? 0 : 1))];
            }
        }
        return $columns;
    }
}