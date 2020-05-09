<?php

namespace Zento\Kernel\Booster\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;

trait TraitMorphModel {
    protected static $morphTypes = [];

    /**
     * register a morph type to a class
     */
    public static function registerMorph($alias) {
        self::$morphTypes[$alias] = static::class;
    }

    /**
     * find the class morph type(from class name to alias)
     */
    public static function getMorphType($className = null) {
        $className = $className ? $className : static::class;
        return array_search($className, self::$morphTypes);
    }

    /**
     * get all morph types(alias)
     */
    public static function getMorphTypes() {
        return array_keys(self::$morphTypes);
    }

    /**
     * @override from \Illuminate\Database\Eloquent\Model
     *
     * @param  array  $attributes
     * @param  string|null  $connection
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->newInstance($attributes, true);
        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);
        $model->lazyLoadRelation();
        return $model;
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = $this->selfMorph($attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        $model->setTable($this->getTable());

        $model->mergeCasts($this->casts);

        return $model;
    }

    protected function selfMorph($attributes = []) {
        $attrs = (array) $attributes;
        $morphType = $attrs['morph_type'] ?? false;
        if ($morphType) {
            if (isset(self::$morphTypes[$morphType])) {
                $class = self::$morphTypes[$morphType];
                return new $class($attrs);
            }
        }
        return new static($attrs);
    }
}