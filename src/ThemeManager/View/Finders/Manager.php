<?php
namespace Zento\ThemeManager\View\Finders;

use Illuminate\View\FileViewFinder;
use Illuminate\View\ViewFinderInterface;
use Illuminate\Support\Str;
use Session;

class Manager implements ViewFinderInterface {

    // const CACHE_KEY = '_EXTRA_VIEW_CACHE_'; 

    /**
     * @var CmsViewFinder
     */
    protected $cmsFinder;

    /**
     * extra view finders. such as DB, S3
     * 
     * @var array finder which should accept two params.
     */
    protected $extraFinders = [];
    
    /**
     * default local file system view finder
     * 
     * @var \Illuminate\View\FileViewFinder
     */
    protected $localfileFinder;

    /**
     * cahced views
     */
    protected $views;
    

    public function __construct(FileViewFinder $fileviewfinder, $app) {
        $this->localfileFinder = $fileviewfinder;
        $this->cmsFinder = new CmsViewFinder($this);

        Session::put('s3theme-debug', false);
        if (!empty(Session::get('s3theme-debug'))) {
            $path = storage_path('framework/debugviews');
            if (file_exists($path)) {
                system('rm -rf ' .$path);
            }
            mkdir($path, 0666, true);
            $app['config']['view.compiled'] = realpath($path);
        }
    }
 
    /**
     * Get the fully qualified location of the view.
     *
     * @param  string  $name
     * @return string
     */
    public function find($name) {
        //check if cached, return
        if (isset($this->views[$name])) {
            return $this->views[$name];
        }

        // check if is CMS
        $view = $this->cmsFinder->find($name);
        if ($view !== null) {
            // return $this->views[$name] = $view;
        }

        //check if any extra finder exist
        foreach($this->extraFinders as $finder) {
            $view = $finder->find($name, $this);
            if ($view !== null) {
                return $this->views[$name] = $view;
            }
        }

        //fall back to default finder
        return $this->localfileFinder->find($name);
    }

    /**
     * extend an extra finder
     * 
     * @param \Clouser
     */
    public function extend( $finder) {
        $this->extraFinders[] = $finder;
    }

    public function getCacheFileName($prefix, $filename) {
        $folder = implode('/', [storage_path('framework/extraviews'),  $prefix]);
        if (!file_exists($folder)) {
            \mkdir($folder, 0666, true);
        }
        $path = sprintf('%s/%s.blade.php', $folder, $filename);
        return $path;
    }

    /**
     * cache the content to cache folder
     */
    public function cacheToFile($prefix, $filename, $content) {
        $path = $this->getCacheFileName($prefix, $filename);
        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }
        return $path;
    }

    /**
     * below functions are for ViewFinderInterface
     */

    public function addLocation($location) {
        return $this->localfileFinder->addLocation($location);
    }

    public function addNamespace($namespace, $hints) {
        return $this->localfileFinder->addNamespace($namespace, $hints);
    }

    public function prependNamespace($namespace, $hints) {
        return $this->localfileFinder->prependNamespace($namespace, $hints);
    }

    public function replaceNamespace($namespace, $hints) {
        return $this->localfileFinder->replaceNamespace($namespace, $hints);
    }

    public function addExtension($extension) {
        return $this->localfileFinder->addExtension($extension);
    }

    public function flush() {
        return $this->localfileFinder->flush();
    }

    public function prependLocation($location) {
        return $this->localfileFinder->prependLocation($location);
    }
}