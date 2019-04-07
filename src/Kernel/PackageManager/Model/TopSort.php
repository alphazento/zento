<?php

namespace Zento\Kernel\PackageManager\Model;

use Exception;

class TopSort
{
    protected $circularInterceptor;

    public function __construct(array $elements = array())
    {
        $this->set($elements);
    }

    public function setCircularInterceptor($circularInterceptor)
    {
        $this->circularInterceptor = $circularInterceptor;
    }

    protected function throwCircularExceptionIfNeeded($element, $parents)
    {
        if (isset($parents[$element->id])) {

            $nodes = array_keys($parents);
            $nodes[] = $element->id;

            if ($this->circularInterceptor) {
                call_user_func($this->circularInterceptor, $nodes);
            } else {
                throw Exception(sprintf('%s are CircularDependency', json_encode($nodes)));
            }
        }
    }

    protected $elements = array();
    protected $sorted;
    protected $delimiter = "\0";

    public function set(array $elements)
    {
        foreach ($elements as $element => $dependencies) {
            $this->add($element, $dependencies);
        }
    }
    public function add($element, $dependencies = array())
    {
        $this->elements[$element] = (object)array(
            'id' => $element,
            'dependencies' => (array)$dependencies,
            'visited' => false
        );
    }

    protected function visit($element, &$parents = null)
    {
        $this->throwCircularExceptionIfNeeded($element, $parents);
        if (!$element->visited) {
            $parents[$element->id] = true;
            $element->visited = true;
            foreach ($element->dependencies as $dependency) {
                if (isset($this->elements[$dependency])) {
                    $newParents = $parents;
                    $this->visit($this->elements[$dependency], $newParents);
                } else {
                    throw ElementNotFoundException::create($element->id, $dependency);
                }
            }
            $this->addToList($element);
        }
    }


    protected function addToList($element)
    {
        $this->sorted .= $element->id . $this->delimiter;
    }

    public function sort()
    {
        return explode($this->delimiter, rtrim($this->doSort(), $this->delimiter));
    }

    public function doSort()
    {
        $this->sorted = '';
        foreach ($this->elements as $element) {
            $parents = array();
            $this->visit($element, $parents);
        }
        return $this->sorted;
    }
}