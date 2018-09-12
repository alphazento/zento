<?php

namespace Zento\Kernel\Booster\Log;

use Monolog\Logger as MonologLogger;
use Illuminate\Contracts\Events\Dispatcher;

class LogManager extends \Illuminate\Log\LogManager
{
    protected $_mark;
    protected $_stdout;
    protected $depressStdout;

    /**
     * Create a new Log manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);
        if ($app->runningInConsole()) {
            $this->_stdout = new \Illuminate\Console\OutputStyle(
                new \Symfony\Component\Console\Input\ArgvInput, 
                new \Symfony\Component\Console\Output\ConsoleOutput
            );
        }
    }

    /**
     * to allow ouput class tag to log
     *
     * @param string|object $classNameOrInstance
     * @param boolean $depressStdout to avoid console log be output twitce
     * @return $this
     */
    public function mark($classNameOrInstance, $depressStdout = false) {
        if (empty($classNameOrInstance)) {
            $this->_mark = null;
        } else {
            if (is_string($classNameOrInstance)) {
                $this->_mark = $classNameOrInstance;
            } elseif (is_object($classNameOrInstance)) {
                $this->_mark = get_class($classNameOrInstance);
            }
        }
        $this->depressStdout = $depressStdout;
        
        return $this;
    }

    public function getMark() {
        return $this->_mark;
    }

    protected function withMark(array &$context) {
        if (!empty($this->_mark)) {
            $context['mark'] = $this->_mark;
        }
        return $this;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->withMark($context);
        $this->_stdout && !$this->depressStdout && $this->_stdout->error($message);
        return parent::emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->withMark($context);
        $this->_stdout && !$this->depressStdout && $this->_stdout->caution($message);
        return parent::alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->withMark($context);
        $this->_stdout && !$this->depressStdout && $this->_stdout->error($message);

        return parent::critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->withMark($context);
        $this->_stdout && !$this->depressStdout && $this->_stdout->error($message);
        return parent::error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->withMark($context);
        $this->_stdout && !$this->depressStdout && $this->_stdout->warning($message);
        return parent::warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function notice($message, array $context = [])
    {
        $this->withMark($context);
        $this->_stdout && !$this->depressStdout && $this->_stdout->note($message);
        return parent::notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->withMark($context);
        $this->_stdout && !$this->depressStdout && $this->_stdout->success($message);
        return parent::info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->withMark($context);
        $this->_stdout && !$this->depressStdout && $this->_stdout->debug($message);
        return parent::debug($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->withMark($context);
        return parent::log($level, $message, $context);
    }

}
