<?php

namespace Zento\Kernel\ThemeManager;

use BadMethodCallException;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class BrowserDetector extends \Mobile_Detect
{
    /**
     * @var CrawlerDetect
     */
    protected static $crawlerDetect;

    /**
     * @inheritdoc
     */
    public function getRules()
    {
        return static::getMobileDetectionRules();
    }

    /**
     * @return CrawlerDetect
     */
    public function getCrawlerDetect()
    {
        if (self::$crawlerDetect === null) {
            self::$crawlerDetect = new CrawlerDetect();
        }

        return self::$crawlerDetect;
    }

    public function isDesktop($userAgent = null, $httpHeaders = null)
    {
        return ! $this->isMobile($userAgent, $httpHeaders) && ! $this->isTablet($userAgent, $httpHeaders) && ! $this->isRobot($userAgent);
    }

    public function isPhone($userAgent = null, $httpHeaders = null)
    {
        return $this->isMobile($userAgent, $httpHeaders) && ! $this->isTablet($userAgent, $httpHeaders);
    }

    public function isRobot($userAgent = null)
    {
        return $this->getCrawlerDetect()->isCrawler($userAgent ?: $this->userAgent);
    }

    /**
     * @inheritdoc
     */
    public function __call($name, $arguments)
    {
        // Make sure the name starts with 'is', otherwise
        if (substr($name, 0, 2) != 'is') {
            throw new BadMethodCallException("No such method exists: $name");
        }

        $key = substr($name, 2);

        return $this->matchUAAgainstKey($key);
    }
}