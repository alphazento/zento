<?php

namespace Zento\Kernel;

interface RouteRule
{
    public function registerFrontAPIRoutes();
    public function registerFrontWebRoutes();
    public function registerAdminAPIRoutes();
    public function registerAdminWebRoutes();
}