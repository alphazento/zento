<?php
namespace Zento\Kernel\Booster\Database\Eloquent;

use Illuminate\Http\Request;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    use Macroable;

    protected $request;
    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)                                                                              
    {                                                                                                                    
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name) || static::hasMacro($name)) {
                $this->{$name}($value);
            }
        }
        return $this->builder;
    }

    public function filters()
    {
        return $this->request->all();
    }
}