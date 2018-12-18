<?php

namespace Zento\RouteAndRewriter\Services;

use Illuminate\Support\Str;
use Zento\RouteAndRewriter\Model\UrlRewriteRule;
use Zento\RouteAndRewriter\Illuminate\Routing\RouteCollection;

class RouteAndRewriterService
{
    protected $routeCollection;
    public function __construct($app) {
        //replace default RouteCollection
        if ($app->bound('router')) {
            $this->routeCollection = new RouteCollection($app['router']->getRoutes());
            $app['router']->setRoutes($this->routeCollection);
        }
    }

    /**
     * append engine
     *
     * @param \Closure $engine
     * @return $this
     */
    public function appendRewriteEngine(\Zento\RouteAndRewriter\Engine\UrlRewriteEngineAbstract $engine) {
        $this->routeCollection->appendRequestHandlers($engine);
        return $this;
    }

    /**
     * add new rewrite rule
     *
     * @param string $reqUri
     * @param string $toUri
     * @param string $description
     * @param integer $status_code
     * @return $this
     */
    public function addRewriteRule(string $reqUri, 
        string $toUri, 
        // array $params = [], 
        // $is_system = 1, 
        // string $route = '', 
        string $description = '', 
        $status_code = 302) {
        $md5 = md5(strtolower($reqUri));
        $flight = UrlRewriteRule::updateOrCreate(
            ['req_hash' => $md5],
            [
                'req_hash' => $md5, 
                'req_uri' => $reqUri, 
                'to_uri' => $to_uri, 
                // 'params' => json_encode($params),
                'status_code' => $status_code,
                'description' => $description
            ]
        );
        return $this;
    }

    /**
     * delete rewrite rule
     *
     * @param string $value
     * @param string $field
     * @return void
     */
    public function delRewriteRule(string $value, string $field = 'req_uri') {
        if ($field === 'req_uri') {
            $value = md5(strtolower($value));
            $field = 'req_hash';
        }
        return UrlRewriteRule::where($field, $value)->delete();
    }

    /**
     * check if requesting a API 
     *
     * @param \Illuminate\Http\Request $request
     * @return boolean
     */
    public function isRequestsApi(\Illuminate\Http\Request $request) {
        return $request->segment(1) == config('api_url_prefix');
    }

    public function findRewriteRule(string $url) {
        return $this->routeCollection->findRewriteRule($url);
    }
}