<?php

namespace Zento\Kernel\Providers;

use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider as LaravelConsoleSupportServiceProvider;

class ConsoleSupportServiceProvider extends LaravelConsoleSupportServiceProvider
{
    protected static $replaceProviders = [];
    public static function replaceProvider(string $fromProvider, string $toProvider)
    {
        self::$replaceProviders[$fromProvider] = $toProvider;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->preProcessProviders();
        parent::register();
    }

    protected function preProcessProviders()
    {
        foreach (self::$replaceProviders as $from => $to) {
            $i = array_search($from, $this->providers);
            if ($i !== false) {
                $this->providers[$i] = $to;
            }
        }
    }
}
