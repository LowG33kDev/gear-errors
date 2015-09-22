<?php
/**
 * Errors class file
 *
 * @author LowG33kDev <loic.marchand73.dev@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://www.github.com/lowg33kdev/gear-errors Repository
 */
namespace Gear\Error;

/**
 * Class used to overload all PHP error handler.
 */
class Errors
{

    /**
     * Contain the formatter used to rendering errors.
     *
     * @var \Gear\Error\Formatter\FormatterInterface
     */
    private static $formatter = null;

    /**
     * Used to catch error before rendering (ie. to add error on a log file).
     *
     * @var array
     */
    private static $handlers = [];

    /**
     * True if you want display errors (during development), false otherwise (on production).
     *
     * @var boolean
     */
    private static $displayErrors = false;


    /**
     * Call this to register error handler.
     *
     * @param \Gear\Error\Formatter\FormatterInterface $formatter Choose how your errors are rendering with formatter
     * @param boolean $displayErrors True if you want display errors(during development), false otherwise(on production)
     */
    public static function register(\Gear\Error\Formatter\FormatterInterface $formatter, $displayErrors = false)
    {
        self::$displayErrors = $displayErrors;
        self::$formatter = $formatter;

        set_error_handler(['\Gear\Error\Errors','errorHandler']);
        set_exception_handler(['\Gear\Error\Errors','exceptionHandler']);
        register_shutdown_function(['\Gear\Error\Errors','shutdownHandler']);
    }

    /**
     * Activate or deactive errors display.
     *
     * @param boolean $displayErrors True if you want display errors(during development), false otherwise(on production)
     */
    public static function setDisplayErrors($displayErrors)
    {
        self::$displayErrors = $displayErrors;
    }

    /**
     * Add an handler.
     *
     * @param \Gear\Error\Handler\HandlerInterace $handler The handler
     */
    public static function addHandler(\Gear\Error\Handler\HandlerInterace $handler)
    {
        self::$handlers[] = $handler;
    }

    /**
     * Overload PHP error handler.
     *
     * @param mixed $type    The type
     * @param string $message    The message
     * @param string $file    The file
     * @param integer $line    The line
     */
    public static function errorHandler($type, $message, $file, $line)
    {
        $header = "HTTP/1.1 500 Internal Server Error";
        if (is_object($type)) { // for exception can find http code
            if (property_exists(get_class($type), 'httpCode') && property_exists(get_class($type), 'httpMessage')) {
                $header = 'HTTP/1.1 ' . $type->httpCode . ' ' . $type->httpMessage;
            }
        }
        header($header);
        
        foreach (self::$handlers as $handler) {
            $handler->handle($type, $message, $file, $line);
        }
        echo self::$formatter->format($type, $message, $file, $line, self::$displayErrors);
        exit(1);
    }

    /**
     * Overload PHP Exception handler.
     *
     * @param \Exception $e The exception
     */
    public static function exceptionHandler(\Exception $e)
    {
        static::errorHandler($e, $e->getMessage(), $e->getFile(), $e->getLine());
    }

    /**
     * Overload PHP fatal error handler.
     */
    public static function shutdownHandler()
    {
        if (is_array($e = error_get_last())) { // if have an error
            $type = isset($e['type']) ? $e['type'] : 0;
            $message = isset($e['message']) ? $e['message'] : '';
            $file = isset($e['file']) ? $e['file'] : '';
            $line = isset($e['line']) ? $e['line'] : '';

            if ($type > 0) {
                static::errorHandler($type, $message, $file, $line);
            }
        }
    }
}
