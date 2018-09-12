<?php

namespace Zento\Kernel\Booster\Routing;

use Closure;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class RouteCollection extends \Illuminate\Routing\RouteCollection {
    protected $origin;
    protected $requestHandlers;

    public function __construct($originRouteCollection) {
        $this->origin = $originRouteCollection;
        $this->requestHandlers = [];
    }

    /**
     * Append request pre handler
     */
    public function appendRequestHandlers(Closure $func) {
        $this->requestHandlers[] = $func;
    }

    /**
    * @override
    */
    public function match(Request $request) {
        foreach($this->requestHandlers as $func) {
            $req = $func($request);
            if ($req) {
                $request = $req;
                break;
            }
        }
        return $this->origin->match($request);
    }

    /**
    * @override
    */
    public function add(Route $route)
    {
        return $this->origin->add($route);
    }

    /**
    * @override
    */
    protected function addToCollections($route)
    {
        return $this->origin->addToCollections($route);
    }

    /**
    * @override
    */
    protected function addLookups($route)
    {
        return $this->origin->addLookups($route);
    }

    /**
    * @override
    */
    protected function addToActionList($action, $route)
    {
        return $this->origin->addToActionList($action, $route);
    }


    /**
    * @override
    */
    public function refreshNameLookups()
    {
        return $this->origin->refreshNameLookups();
    }

    /**
    * @override
    */
    public function refreshActionLookups()
    {
        return $this->origin->refreshActionLookups();
    }

    /**
    * @override
    */
    public function get($method = null)
    {
        return $this->origin->get($method);
    }

    /**
    * @override
    */
    public function hasNamedRoute($name)
    {
        return $this->origin->hasNamedRoute($name);
    }

    /**
    * @override
    */
    public function getByName($name)
    {
        return $this->origin->getByName($name);
    }

    /**
    * @override
    */
    public function getByAction($action)
    {
        return $this->origin->getByAction($action);
    }

    /**
    * @override
    */
    public function getRoutes()
    {
        return $this->origin->getRoutes();
    }

    /**
    * @override
    */
    public function getRoutesByMethod()
    {
        return $this->origin->getRoutesByMethod();
    }

    /**
    * @override
    */
    public function getRoutesByName()
    {
        return $this->origin->getRoutesByName();
    }

    /**
    * @override
    */
    public function getIterator()
    {
        return $this->origin->getIterator();
    }

    /**
    * @override
    */
    public function count()
    {
        return $this->origin->count();
    }
}

