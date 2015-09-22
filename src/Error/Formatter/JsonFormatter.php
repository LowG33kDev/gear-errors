<?php
/**
 * JsonFormatter class file
 *
 * @author LowG33kDev <loic.marchand73.dev@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://www.github.com/lowg33kdev/gear-errors Repository
 */
namespace Gear\Error\Formatter;

/**
 * JsonFormatter used to render error to Json format.
 */
class JsonFormatter implements FormatterInterface
{

    /**
     * Used to format error datas for output.
     *
     * @param mixed $type
     * @param string $message
     * @param string $file
     * @param int $line
     * @param boolean $displayErrors
     *
     * @return string Encode json datas.
     */
    public function format($type, $message, $file, $line, $displayErrors)
    {
        header('content-type:text/json'); // force json mime type
        return json_encode(['error'=>['type'=>$type,'message'=>$message,'file'=>$file,'line'=>$line]]);
    }
}
