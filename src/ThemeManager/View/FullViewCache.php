<?php
namespace Zento\ThemeManager\View;

use Cache;

class FullViewCache implements \Illuminate\Contracts\View\View {
    private $name;
    private $view;
    public function __construct($viewname, &$view) {
        $this->name = $viewname;
        $this->view = $view;
    }
    public function name() {
        return $this->name;
    }

    public function render() {
        $key = '{cache}' . $this->name;
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        
        $contents = $this->view->render();
        Cache::forever($key, $contents);
        return $contents;
    }

    public function with($key, $value = null){
        return $this;
    }
}