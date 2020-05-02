<?php

namespace Zento\Kernel\Providers;

use Cookie;
use Illuminate\Contracts\Debug\ExceptionHandler;

class DebuggerServiceProvider extends \Illuminate\Support\ServiceProvider {
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->isActive()) {
            $settings = $this->settings();
            $callback = $settings['enable_callback'];
            if ($callback && is_callable($callback)) {
                $callback($this->app);
            }
        }
    }

    /**
     * @return string
     */
    protected function env() {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('app.env', 'product');
        }
        return 'product';
    }

    protected function defaultSettings() {
        return [
            'cookie_name' => 'debugger',
            'cookie_value' => 'enable',
            'enable_callback' => null
        ];
    }

    protected function settings() {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('debugger', $this->defaultSettings());
        }
        return $this->defaultSettings();
    }

    protected function isActive() {
        $env = $this->env();

        if ($env === 'local') {
            return true;
        }

        if (!$this->app->runningInConsole()) {
            $settings = $this->settings();
            if (!empty($settings['cookie_name']) && isset($settings['cookie_value']) && $settings['cookie_value'] == $this->getCookieValue($settings['cookie_name'])) {
                return true;
            }
        }
        return false;
    }

    protected function getCookieValue($name) {
        if ($this->app->bound('cookie')) {
            return Cookie::get($name);
        }
        return null;
    }
}
