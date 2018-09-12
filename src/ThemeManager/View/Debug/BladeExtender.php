<?php

namespace Zento\ThemeManager\View\Debug;

use Illuminate\Container\Container;
use Illuminate\View\Compilers\Concerns\CompilesIncludes;
use Illuminate\View\Compilers\Concerns\CompilesComponents;
use Illuminate\View\Compilers\Concerns\CompilesLayouts;
use Illuminate\Support\Str;

use Zento\ThemeManager\Services\ThemeManager;

class BladeExtender
{
    use CompilesIncludes;
    use CompilesComponents;
    use CompilesLayouts;

    protected $blade;

    /**
     * add a red background container to the view, so can see the view details
     */
    protected function debugView($key, $compiledView) {
        if (!ThemeManager::debugMode()) {
            return $compiledView;
        }

        return sprintf('<div style="border:1px red solid">
            <span style="background-color:lime;color:blue;">%s</span>
            %s
            </div>', 
            $key, 
            $compiledView);
    }

    /**
     * add a yellow background container to the view, so can see the view details
     */
    protected function debugSection($key, $compiledView) {
        if (!ThemeManager::debugMode()) {
            return $compiledView;
        }

        return sprintf('%s<div style="border:2px red solid">
            <span style="background-color:yellow;color:blue;">section%s</span>',
            $compiledView, 
            $key);
    }

    protected function debugCloseTag() {
        return ThemeManager::debugMode() ? '</div>' : '';
    }

    public function inject(Container $app) {
        $this->blade = $app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $this->blade->directive('addjs', function ($var) {
            $var = trim($var, "()'\" ");
            return sprintf('<script src="%s"></script>', $var);
        });

        $this->blade->directive('addcss', function ($var) {
            $var = trim($var, "()'\" ");
            return sprintf(' <link href="%s" rel="stylesheet" type="text/css">', $var);
        });

        // override the default include, just can add debug view
        $this->blade->directive('include', function ($expression) {
            return $this->debugView($expression, $this->compileInclude($expression));
        });

        // override the default includeIf, just can add debug view
        $this->blade->directive('includeIf', function ($expression) {
            return $this->debugView($expression, $this->compileIncludeIf($expression));
        });

        // $this->blade->directive('includeWhen', function ($expression) {
        //     return $this->compileMyIncludeWhen($expression);
        // });

        // override the default component, just can add debug view
        $this->blade->directive('component', function ($expression) {
            return $this->debugView($expression, 
                $this->compileComponent($expression)
            );
        });

        // override the default section, just can add debug view
        $this->blade->directive('section', function ($expression) {
            $expression = '(' . $expression . ')';
            return $this->debugSection($expression, 
                $this->compileSection($expression)
            );
        });

        // override the default endsection, just can add debug view close tag
        $this->blade->directive('endsection', function () {
            return $this->debugCloseTag() 
                . $this->compileEndsection();
        });

        $this->blade->directive('parent', function () {
            return $this->compileParent();
        });

        // add cms block to allow we defined template in DB
        $this->blade->directive('cms', function($expression) {
            $expression = trim($expression, "()'\" ");
            return $this->compileCms('cms::' . $expression);
        });

        // add cms block to allow we defined template in DB
        $this->blade->directive('layout', function($expression) {
            $pos = strpos($expression, CmsViewFinder::CMS_BLOCK_PREFIX);
            if ($pos === 0) {
                $expression = sprintf('%slayout.%s', CmsViewFinder::CMS_BLOCK_PREFIX, substr($expression, strlen(CmsViewFinder::CMS_BLOCK_PREFIX)));
            } else {
                $expression = 'layout.' . $expression;
            }
            return $this->compileExtends($expression);
        });

        return $this;
    }

    protected function compileCms($expression) {
        return $this->debugView($expression, sprintf('<?php echo $__env->make("%s") ?>', $expression));
    }

    protected function stripParentheses($expression) {
        return $this->blade->stripParentheses($expression);
    }

}
