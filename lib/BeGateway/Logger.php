<?php

namespace BeGateway;

class Logger
{
    const INFO = 0;
    const WARNING = 1;
    const ERROR = 2;
    const DEBUG = 4;

    private $level;
    private static $instance;
    private $output = 'php://stderr';
    /**
     * @var callable|false
     */
    private $callback = false;
    private $mask = true;

    private function __construct()
    {
        $this->level = self::INFO;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static;
        }

        return self::$instance;
    }

    public function write($msg, $level = self::INFO, $place = '')
    {
        $p = '';
        if (!empty($place)) {
            $p = "( $place )";
        }

        if ($this->level >= $level) {
            $message = "[" . self::getLevelName($level) . " $p] => ";
            $message .= print_r($this->filter(var_export($msg, true)), true);
            $message .= PHP_EOL;
            if ($this->output) {
                $this->sendToFile($message);
            }
            if ($this->callback != false) {
                call_user_func($this->callback, $message);
            }
        }
    }

    public function setLogLevel($level)
    {
        $this->level = $level;
    }

    public function setPANfitering($option)
    {
        $this->mask = $option;
    }

    public function setOutputCallback($callback)
    {
        $this->callback = $callback;
    }

    public function setOutputFile($path)
    {
        $this->output = $path;
    }

    /**
     * @param string $level
     * @return string the exception level name
     * @throws \Exception if provided level is not supported
     */
    public static function getLevelName($level)
    {
        switch ($level) {
            case self::INFO :
                return 'INFO';
                break;
            case self::WARNING :
                return 'WARNING';
                break;
            case self::DEBUG :
                return 'DEBUG';
                break;
            default:
                throw new \Exception('Unknown log level ' . $level);
        }
    }

    private function sendToFile($message)
    {
        $fh = fopen($this->output, 'w+');
        fwrite($fh, $message);
        fclose($fh);
    }

    private function filter($message)
    {
        $cardFilter = '/("number":")(\d{1})\d{8,13}(\d{4})(")/';
        $cvcFilter = '/("verification_value":")(\d{3,4})(")/';

        if ($this->mask) {
            $message = preg_replace($cardFilter, '$1$2 xxxx $3$4', $message);
            $message = preg_replace($cvcFilter, '$1xxx$3', $message);
        }

        return $message;
    }
}
