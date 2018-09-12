<?php
namespace Zento\ThemeManager\View;

use Illuminate\Support\Str;

class ViewFactory extends \Illuminate\View\Factory {
    protected $makers = [];
    public function appendMaker($maker) {
        if (!in_array($maker, $this->makers)) {
            $this->makers[] = $maker;
        }
        return $this;
    }

    public function rawmake($view, $data = [], $mergeData = []) {
        return parent::make($view, $data, $mergeData);
    }

    protected $firstViewData = false;
    /**
     * Override make to provide a possibility that other modules can define an new view maker without touch this class
     */
    public function make($view, $data = [], $mergeData = []) {
        if (!$this->firstViewData) {
            $this->firstViewData = array_merge($mergeData, $this->parseData($data));
        }
        $cache = \substr($view, 0, 7) == '{cache}';
        if ($cache) {
            $view = \substr($view, 7);
        }

        $t = count($this->makers);
        if ($t > 0) {
            for($i=0; $i < $t; $i++) {
                $maker = $this->makers[$i];
                if (is_string($maker)) {
                    $this->makers[$i] = new $maker();
                }
                $result = $this->makers[$i]->make($this, $view, $data, $mergeData);
                if ($result) {
                    return $result;
                }
            }
        }
        
        $result = parent::make($view, $data, $mergeData);
        if ($cache) {
            return new \Zento\ThemeManager\View\FullViewCache($view, $result);
        }
        return $result;
    }
}
