<?php
/**
 * HandlerInterface file
 *
 * @author LowG33kDev <loic.marchand73.dev@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://www.github.com/lowg33kdev/gear-errors Repository
 */
namespace Gear\Error\Handler;

/**
 * Intraface to handle error.
 */
interface HandlerInterface
{
    
    /**
     * Used to handle error.
     *
     * @param mixed $type
     * @param string $message
     * @param string $file
     * @param int $line
     */
    public function handle($type, $message, $file, $line);
}
