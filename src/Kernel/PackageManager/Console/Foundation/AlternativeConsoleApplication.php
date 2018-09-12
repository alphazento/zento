<?php

namespace Zento\Kernel\PackageManager\Console\Foundation;

use ArrayAccess;
use Zento\Kernel\PackageManager\Console\Foundation\Commands\ConsoleMakeCommand;
use Zento\Kernel\PackageManager\Console\Foundation\Commands\MailMakeCommand;
use Zento\Kernel\PackageManager\Console\Foundation\Commands\ModelMakeCommand;
use Zento\Kernel\PackageManager\Console\Foundation\Commands\ControllerMakeCommand;
use Zento\Kernel\PackageManager\Console\Foundation\Commands\MiddlewareMakeCommand;
use Zento\Kernel\PackageManager\Console\Foundation\Commands\ProviderMakeCommand;

class AlternativeConsoleApplication implements ArrayAccess {
    private $core;
    protected $namespace;

    public function __construct($core) {
        $this->core = $core;
    }

    public function __call($method, $args) {
        return call_user_func_array([$this->core, $method], $args);
    }

    public function make($name) {
        switch($name) {
            case 'command.package.discover':
                return $this->core->make(Commands\PackageDiscoverCommand::class);
                break;
            default:
            break;
        }
        return $this->core->make($name);
    }

    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    public function getNamespace() {
        if (!empty($this->namespace)) {
            return $this->namespace;
        }

        return $this->core->getNamespace();
    }

    public function getApp() {
        return $this->core;
    }

    public function getPath() {
        return $this->core['path'];
    }

    public function offsetSet($offset, $value) {
        return $this->core->offsetSet();
    }

    public function offsetExists($offset) {
        return $this->core->offsetExists($offset);
    }

    public function offsetUnset($offset) {
        return $this->core->offsetUnset($offset);
    }

    public function offsetGet($offset) {
        return $this->core->offsetGet($offset);
    }
}

