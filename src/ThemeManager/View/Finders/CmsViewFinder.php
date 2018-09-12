<?php
namespace Zento\ThemeManager\View\Finders;

use Registry;

use Illuminate\View\FileViewFinder;
use Illuminate\View\ViewFinderInterface;
use Illuminate\Support\Str;

use Zento\Base\Model\DB\CmsBlock;

class CmsViewFinder {
    const HINT_TYPE_DELIMITER = ':';
    const CMS_BLOCK_PREFIX = 'cms::';

    protected $finderManager;

    public function __construct(Manager $manager) {
        $this->finderManager = $manager;
    }

    /**
     * Get the fully qualified location of the view.
     *
     * @param  string  $name
     * @return string
     */
    public function find($name) {
        if (Str::startsWith($name, self::CMS_BLOCK_PREFIX)) {
            return $this->findCms($name);
        }
        return null;
    }

    protected function findCms($name) {
        $name = \substr($name, strlen(self::CMS_BLOCK_PREFIX));

        if ($this->hasHintInformation($name = trim($name))) {
            return $this->findTypedView($name);
        }

        return $this->cacheCmsInPath($name, '');
    }

    /**
     * Get the path to a template with a named path.
     *
     * @param  string  $name
     * @return string
     */
    protected function findTypedView($name) {
        list($type, $view) = $this->parseTypeSegments($name);
        return $this->cacheCmsInPath($view, $type);
    }

    /**
     * Returns whether or not the view name has any hint information.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasHintInformation($name) {
        return strpos($name, static::HINT_TYPE_DELIMITER) > 0;
    }

    /**
     * parse view name to get cms type
     */
    protected function parseTypeSegments($name) {
        $segments = explode(static::HINT_TYPE_DELIMITER, $name);

        if (count($segments) != 2) {
            throw new InvalidArgumentException("View [$name] has an invalid name.");
        }

        return $segments;
    }

    /**
     * cache CMS block template to file
     */
    protected function cacheCmsInPath($name, $type) {
        $block = CmsBlock::where('name', '=', $name)
            ->whereIn('type', ['', $type])
            ->where('hidden', '=', '0')
            ->orderBy('version', 'desc')
            ->first();
        if ($block) {
            return $this->finderManager->cacheToFile('cms', $block->theme . '.' . $name, $block->content);
        }
        return null;
    }        
}