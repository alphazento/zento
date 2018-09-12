<?php

namespace Zento\UrlRewriter\Services;

use Illuminate\Support\Str;
use Zento\UrlRewriter\Model\UrlRewriteRule;
use Zento\UrlRewriter\Illuminate\Routing\RouteCollection;

class UrlRewriterManagerService
{
    protected $routeCollection;
    public function __construct($app) {
        if($app->bound('router')) {
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
    public function appendRewriteEngine(\Closure $engine) {
        $this->routeCollection->appendRequestHandlers($engine);
        return $this;
    }

    /**
     * add new rewrite rule
     *
     * @param string $reqUri
     * @param string $toUri
     * @param string $description
     * @param integer $statusCode
     * @return $this
     */
    public function addRewriteRule(string $reqUri, string $toUri, string $description = '', $statusCode = 302) {
        $md5 = md5(strtolower($reqUri));
        $flight = UrlRewriteRule::updateOrCreate(
            ['req_hash' => $md5],
            [
                'req_hash' => $md5, 
                'req_uri' => $reqUri, 
                'to_uri' => $to_uri, 
                'status_code' => $statusCode,
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
}