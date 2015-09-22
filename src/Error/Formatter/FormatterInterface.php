<?php
/**
 * FormatterInterface file
 *
 * @author LowG33kDev <loic.marchand73.dev@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://www.github.com/lowg33kdev/gear-errors Repository
 */
namespace Gear\Error\Formatter;

/**
 * Interface to create Formatter for error handling
 */
interface FormatterInterface
{

    /**
     * Used to format error datas for output.
     *
     * @param mixed $type
     * @param string $message
     * @param string $file
     * @param int $line
     * @param boolean $displayErrors
     */
    public function format($type, $message, $file, $line, $displayErrors);
}
