<?php
namespace Zento\Kernel\Booster\Config;

use Illuminate\Support\Arr;
class Repository extends \Illuminate\Config\Repository
{
    /**
     * @var \Illuminate\Config\Repository
     */
    protected $origin;

    protected $extraPreloaded;

    /**
     * @var \Zento\Kernel\Booster\Config\ProviderInterface
     */
    protected $extraProvider;

    public function __construct(\Illuminate\Config\Repository $origin,
        \Zento\Kernel\Booster\Config\AbstractExtraConfigRepository $extraProvider)
    {
        $this->extraProvider = $extraProvider;
        $this->extraPreloaded = $extraProvider->isPreLoaded();
        $this->items = &$extraProvider->loadConfigs();
        $this->origin = $origin;
    }

    /**
     * Save a key value paire to extraProvider
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function save($key, $value) {
        $this->extraProvider->persist($key, $value);
        if ($this->extraPreloaded) {
            $this->items[$key] = $value;
        }
        return $this;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return $this->origin->has($key) ?: ($this->extraPreloaded ? parent::has($key) : $this->extraProvider->has($key));
    }
    /**
     * Get the specified configuration value.
     *
     * @param  array|string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $ret = $this->origin->get($key);
        if ($ret === null) {
            if (!$this->extraPreloaded) {
                $this->extraProvider->preGet($key, $default);
            }
            return parent::get($key, $default);
        }
        return $ret;
    }
    /**
     * Get many configuration values.
     *
     * @param  array  $keys
     * @return array
     */
    public function getMany($keys)
    {
        $ret = $this->origin->getMany($keys);
        if (empty($ret)) {
            $rets = [];
            foreach($keys as $key) {
                $ret = $this->get($key);
                if ($ret) {
                    $rets[$key] = $ret;
                }
            }
            return $rets;
        }
        return [];
    }
    /**
     * Set a given configuration value.
     *
     * @param  array|string  $key
     * @param  mixed   $value
     * @return void
     */
    public function set($key, $value = null)
    {
        return $this->origin->set($key, $value);
    }
    /**
     * Prepend a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function prepend($key, $value)
    {
        return $this->origin->prepend($key, $value);
    }
    /**
     * Push a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push($key, $value)
    {
        return $this->origin->push($key, $value);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        return array_merge($this->origin->all(), $this->items);
    }
    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->origin->offsetExists($key) ?: parent::offsetExists($key);
    }
}