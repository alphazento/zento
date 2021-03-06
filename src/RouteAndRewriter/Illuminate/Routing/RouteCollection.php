<?php

namespace Zento\RouteAndRewriter\Illuminate\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * inherit and replace default RouteCollection
 * It will call run customise request handlers first, then run default match function
 */
class RouteCollection extends \Illuminate\Routing\RouteCollection
{
    protected $origin;
    protected $requestHandlers;

    public function __construct($originRouteCollection)
    {
        $this->origin = $originRouteCollection;
        $this->requestHandlers = [];
    }

    /**
     * Append request pre handler
     */
    public function appendRequestHandlers(\Zento\RouteAndRewriter\Engine\UrlRewriteEngineAbstract $engine)
    {
        $this->requestHandlers[] = $engine;
    }

    public function match(Request $request)
    {
        $routes = $this->origin->get($request->getMethod());
        $route = $this->matchAgainstRoutes($routes, $request);
        if (is_null($route)) {
            list($rewrite, $request) = $this->matchUrlRewrite($request);
            if ($rewrite) {
                $routes = $this->origin->get($request->getMethod());
                $route = $this->matchAgainstRoutes($routes, $request);
            }
        }
        return $this->handleMatchedRoute($request, $route);
    }

    /**
     * it's design for laravel 5.x, not necessary for laravel 7
     */
    protected function handleMatchedRoute(Request $request, $route)
    {
        if (!is_null($route)) {
            return $route->bind($request);
        }

        // If no route was found we will now check if a matching route is specified by
        // another HTTP verb. If it is we will need to throw a MethodNotAllowed and
        // inform the user agent of which HTTP verb it should use for this route.
        $others = $this->checkForAlternateVerbs($request);

        if (count($others) > 0) {
            return $this->getRouteForMethods($request, $others);
        }

        throw new NotFoundHttpException;
    }

    /**
     * run extra request handlers
     *
     * @param Request $request
     * @return void
     */
    protected function matchUrlRewrite(Request $request)
    {
        foreach ($this->requestHandlers as $engine) {
            if ($req = $engine->execute($request)) {
                return [true, $req];
            }
        }
        return [false, $request];
    }

    /**
     * designed for API
     *
     * @param string $uri
     * @return void
     */
    public function findRewriteRule(string $uri)
    {
        $rule = false;
        foreach ($this->requestHandlers as $engine) {
            if ($rule = $engine->findRewriteRule($uri)) {
                break;
            }
        }
        return $rule;
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
