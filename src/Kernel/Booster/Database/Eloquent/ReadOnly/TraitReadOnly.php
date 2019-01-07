<?php
namespace Zento\Kernel\Booster\Database\Eloquent\ReadOnly;

use Illuminate\Database\Eloquent\Builder;

trait TraitReadOnly
{
    public static function create(array $attributes = [])
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public static function forceCreate(array $attributes)
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public function save(array $options = [])
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public function update(array $attributes = [], array $options = [])
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public static function firstOrCreate(array $attributes, array $values = [])
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public static function firstOrNew(array $attributes, array $values = [])
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public static function updateOrCreate(array $attributes, array $values = [])
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public function delete()
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public static function destroy($ids)
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public function restore()
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public function forceDelete()
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }


    public function performDeleteOnModel()
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }


    public function push()
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }


    public function finishSave(array $options)
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public function performUpdate(Builder $query, array $options = [])
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    
    public function touch()
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public function insert()
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }

    public function truncate()
    {
        throw new Exception(__FUNCTION__, get_called_class());
    }
}