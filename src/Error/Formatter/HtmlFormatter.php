<?php
/**
 * HtmlFormatter class file
 *
 * @author LowG33kDev <loic.marchand73.dev@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://www.github.com/lowg33kdev/gear-errors Repository
 */
namespace Gear\Error\Formatter;

/**
 * HtmlFormatter used to render error on web browser.
 */
class HtmlFormatter implements FormatterInterface
{

    /**
     * The template file.
     *
     * @var string
     */
    private $template = '';
    
    /**
     * The no display template file.
     *
     * @var string
     */
    private $noDisplayTemplate = '';
    
    
    /**
     * Used to format error datas for output.
     *
     * @param mixed $type
     * @param string $message
     * @param string $file
     * @param int $line
     * @param boolean $displayErrors
     *
     * @return string Html format datas
     */
    public function format($type, $message, $file, $line, $displayErrors)
    {
        // Verify if display errors
        if (!$this->displayErrors($type, $displayErrors)) {
            echo $this->noDisplayErrorTemplate();
            return;
        }
        
        if (!empty($this->template) && file_exists($this->template)) {
            return file_get_contents($this->template);
        } else {
            if (is_object($type)) {
                $title = get_class($type);
                $type = 'exception';
            } else {
                $title = $this->errorTitle($type);
                $type = $this->errorClass($type);
            }
            
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS);
            array_shift($backtrace); // shift template call
            array_shift($backtrace); // shift format call
            
            // Page header
            $content = '';
            $content .= '<header class="error-header error-header--'.$type.'">';
            $content .= '   <h1 class="error-title error-title--'.$type.'">'.$title.'</h1>';
            $content .= '   <h2>'.$message.'</h2>';
            $content .= '</header>';
                    
            // Error details
            $content .= '<div class="error-details">';
            $content .= $this->writeCode($file, $line, 0);
            
            // $bt contains backtrace
            $bt = '';
            $bt .= '<div class="trace" data-trace="0">';
            $bt .= '    <p>'.$file.' on line '.$line.'</p>';
            $bt .= '    <strong>'.$message.'</strong>';
            $bt .= '</div>';
            
            foreach ($backtrace as $k => $v) {
                $f = isset($v['file']) ? $v['file'] : '<#unknown file>';
                $l = isset($v['line']) ? ($v['line']+1) : ':0';
                $content .= $this->writeCode($f, $l-1, $k+1);
                
                $bt .= '<div class="trace" data-trace="'.($k+1).'">';
                $bt .= '    <p>'.$f.' on line '.($l-1).'</p>';
                $bt .= '    <code>'.$this->formatBacktrace($v).'</code>';
                $bt .= '</div>';
            }
            $content .= '</div>';
            
            $content .= '<div class="column-layout">';
            $content .= '   <aside class="stacktrace-column">';
            $content .= '       <div class="stacktrace">';
            $content .= $bt;
            $content .= '       </div>';
            $content .= '   </aside>';
            
            $content .= '   <aside class="variables">';
            $content .= $this->getVariables();
            $content .= '   </aside>';
            $content .= '   <hr style="clear:both;display:none;">';
            $content .= '</div>';
            
            return $this->template($content);
        }
    }
    
    /**
     * Check if displaying errors.
     *
     * @param mixed $type
     * @param boolean $displayErrors
     */
    private function displayErrors($type, $displayErrors)
    {
        if (is_object($type)) { // for excpetion, always display error if needed
            return $displayErrors;
        } else { // For normal error, display error_reporting
            return ((error_reporting() & $type) && $displayErrors);
        }
    }
    
    /**
     * Construct default template.
     *
     * @param string $content
     */
    private function template($content)
    {
        $template = '<!DOCTYPE html>';
        $template .= '<html>';
        $template .= '<head>';
        $template .= '<meta charset="utf-8">';
        $template .= '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">';
        $template .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">';
        $template .= '<title>Error | </title>';
        $template .= $this->css();
        $template .= '</head>';
        $template .= '<body>';
        $template .= $content;
        $template .= $this->script();
        $template .= '</body>';
        $template .= '</html>';

        return $template;
    }
    
    /**
     * Generate stylesheet.
     *
     * @return string Css datas
     */
    private function css()
    {
        $css = '<style>';
        $css .= '*{margin: 0;font-family: sans-serif;box-sizing: border-box;}';
        $css .= 'body{background: #F2F2F2;}';
        $css .= 'pre,code{font-family:monospace;}';
        $css .= '.error-header{background: #252525; color: #DDD; padding: 20px;border-left: solid 5px #F66;}';
        $css .= '.error-header--fatal{border-color: #9E0000;}';
        $css .= '.error-header--parse{border-color: #A721B7;}';
        $css .= '.error-header--warning{border-color: #C17929;}';
        $css .= '.error-header--notice{border-color: #1BA3B5;}';
        $css .= '.error-header--strict{border-color: #A25459;}';
        $css .= '.error-header--deprecated{border-color: #6BB796;}';
        $css .= '.error-header--exception{border-color: #772673;}';
        $css .= '.error-title{font-size: 2.5em;}';
        $css .= '.error-title--fatal{color: #9E0000;}';
        $css .= '.error-title--parse{color: #A721B7;}';
        $css .= '.error-title--warning{color: #C17929;}';
        $css .= '.error-title--notice{color: #1BA3B5;}';
        $css .= '.error-title--strict{color: #A25459;}';
        $css .= '.error-title--deprecated{color: #6BB796;}';
        $css .= '.error-title--exception{color: #772673;}';
        $css .= '.file{padding: 5px;font-family:consolas;font-size: 0.8em;}';
        $css .= '.error-details{background:#CECECE;padding: 15px;border-left: solid 5px #252525;}';
        $css .= '.code-part{display: none; background:#DCDCDC;border-radius:10px;}';
        $css .= '.code-part--active{display: block;}';
        $css .= '.code-details{padding: 15px 5px; margin: 0; background: #252525; color: #CECECE;border-bottom-right-radius:10px;border-bottom-left-radius:10px;}';
        $css .= '.highligth{width: 100%;font-family:inherit; background:rgba(220,100,100,0.7); display: inline-block;}';

        $css .= '.column-layout {display: table;width: 100%;}';
        $css .= '.stacktrace-column{width: 40%;display: table-cell;}';
        $css .= '.variables{width: 60%;display: table-cell;padding: 0 20px;border-left: 1px dashed #6F6F6F;}';

        $css .= '.stacktrace{padding: 0;}';
        $css .= '.trace{display: block;padding: 20px; list-style: none; border-bottom: 1px dashed #6F6F6F;cursor: pointer;}';
        $css .= '.trace:hover{background: rgba(0,0,250,0.5);}';
        
        $css .= '.variables-list{list-style:none;padding:0;margin-bottom:15px;}';
        $css .= '.variable-title{border-bottom: 1px dashed #6F6F6F;font-size: 2.2em;font-family:monospace; margin-bottom: 10px;}';
        $css .= '.variable-empty{font-size: 0.5em; color: #6F6F6F;}';
        $css .= '</style>';
        
        return $css;
    }
    
    /**
     * Generate javascript
     *
     * @return string The script
     */
    private function script()
    {
        $script = "<script>\n";
        $script .= "var current = 0;\n";
        $script .= "var traces = document.querySelectorAll('.trace');\n";
        $script .= "for(var i=0,len=traces.length;i<len;i++){\n";
        $script .= "    traces[i].addEventListener('click', function(e){\n";
        $script .= "        if (this.dataset.trace!=current){\n";
        $script .= "            var oldTrace = document.querySelector('.code-part-'+current);\n";
        $script .= "            oldTrace.classList.toggle('code-part--active');\n";
        $script .= "            var trace = document.querySelector('.code-part-'+this.dataset.trace);\n";
        $script .= "            trace.classList.toggle('code-part--active');\n";
        $script .= "            current=this.dataset.trace;\n";
        $script .= "        }";
        $script .= "    }, false);\n";
        $script .= "}";
        $script .= '</script>';
        
        return $script;
    }
        
    /**
     * Format the code datas
     *
     * @param string $file File must be display
     * @param integer $errorLine The error line number
     * @param integer $errorNum Error number (used for javascript)
     *
     * @return string The section contains code lines
     */
    private function writeCode($file, $errorLine, $errorNum)
    {
        $codeLines = '  <section class="code-part '.(($errorNum===0) ? 'code-part--active ' : '') . 'code-part-'.$errorNum.'">';
        $codeLines .= '     <h1 class="file">'.$file.'</h1>';
        $codeLines .= '     <pre class="code-details"><ol>';
        if (file_exists($file)) {
            $lines = file($file);
            $isFirstLine = true;
            foreach ($lines as $line_num => $line) {
                if ($line_num>$errorLine-9 && $line_num<$errorLine+2) {
                    if ($line_num+1===$errorLine) {
                        $codeLines .= '<li'.($isFirstLine ? ' value="'.($line_num+1).'"' : '').'><mark class="highligth">' . htmlspecialchars($line) . '</mark></li>';
                    } else {
                        $codeLines .= '<li'.($isFirstLine ? ' value="'.($line_num+1).'"' : '').'>' . htmlspecialchars($line) . '</li>';
                    }
                    $isFirstLine=false;
                }
            }
        }
        $codeLines .= '     </ol></pre>';
        $codeLines .= ' </section>';
        
        return $codeLines;
    }
    
    /**
     * Compiled variables to display.
     *
     * @return string Compiled variables
     */
    private function getVariables()
    {
        $vars = [
            '$_SERVER' => isset($_SERVER) ? $_SERVER : [],
            '$_ENV' => isset($_ENV) ? $_ENV : [],
            '$_SESSION' => isset( $_SESSION) ? $_SESSION : [],
            '$_COOKIE' => isset( $_COOKIE) ? $_COOKIE : [],
            '$_GET' => isset( $_GET) ? $_GET : [],
            '$_POST' => isset( $_POST) ? $_POST : [],
            '$_FILES' => isset( $_FILES) ? $_FILES : []
        ];

        $variables = '';
        foreach ($vars as $varName => $varValue) {
            $variables .= '<h2 class="variable-title">'.$varName;
            if (isset($varValue) && !empty($varValue)) {
                $variables .= '</h2><ul class="variables-list">';
                foreach ($varValue as $key => $value) {
                $variables .= '<li>'.$key.' => '.print_r($value,true).'</li>';
            }
                $variables .= '</ul>';
            } else {
                $variables .= ' <small class="variable-empty">empty</small></h2>';
            }
        }
        
        return $variables;
    }
    
    /**
     * Format trace from array datas.
     *
     * @param array $trace Contains one trace of backtrace
     *
     * @return string Formatted trace.
     */
    private function formatBacktrace($trace)
    {
        $backtrace = '';
        if (!isset($trace['class'])) {
            $backtrace .= $trace['function'].'()';
        } else {
            $backtrace .= $trace['class'].$trace['type'].$trace['function'].'()';
        }
        return $backtrace;
    }
    
    /**
     * Used to generate default no display errors template.
     *
     * @return string String containing default no display template
     */
    private function noDisplayErrorTemplate()
    {
        $template = '';
            
        if (!empty($this->noDisplayTemplate) && file_exists($this->noDisplayTemplate)) {
            $template = file_get_contents($this->noDisplayTemplate);
        } else {
            $template .= '<!DOCTYPE html>';
            $template .= '<html>';
            $template .= '  <head>';
            $template .= '      <meta charset="utf-8">';
            $template .= '      <title>Error</title>';
            $template .= '';
            $template .= '      <style>';
            $template .= '          *{margin: 0;font-family: sans-serif;box-sizing: border-box;}';
            $template .= '          body, html { height:100%; }';
            $template .= '          .wrapper { height:100%; width:100%; display:table; background:#F2F2F2;; overflow:hidden }';
            $template .= '          .container { display:table-cell; vertical-align:middle; }';
            $template .= '          .content { position: relative; width: 480px; height: 360px; margin: auto; background: #252525; color: #CECECE; border-radius: 15px; padding: 20px; }';
            $template .= '          .error-title{color: #9E0000;}';
            $template .= '      </style>';
            $template .= '  </head>';
            $template .= '  <body>';
            $template .= '      <div class="wrapper">';
            $template .= '          <div class="container">';
            $template .= '              <div class="content">';
            $template .= '                  <h1 class="error-title">Server internal error</h1>';
            $template .= '                  <h2>Something went wrong</h2>';
            $template .= '                  <p>Please try again later.</p>';
            $template .= '              </div>';
            $template .= '          </div>';
            $template .= '      </div>';
            $template .= '  </body>';
            $template .= '</html>';
        }

        return $template;
    }
    
    /**
     * Generate title frome error level.
     *
     * @param int $errorLevel
     *
     * @return string The error title
     */
    private function errorTitle($errorLevel)
    {
        switch ($errorLevel) {
            case E_ERROR: // Fatal runtime error
            case E_USER_ERROR: // User-triggered fatal error
            case E_CORE_ERROR: // Fatal startup error
            case E_COMPILE_ERROR: // Fatal compile-time error
            case E_RECOVERABLE_ERROR:
                return 'Fatal Error';
                break;
            case E_PARSE: // Runtime parse error
                return 'Parse Error';
                break;
            case E_WARNING: // Non-fatal runtime error
            case E_USER_WARNING: // User-triggered non-fatal error
            case E_CORE_WARNING: // Non-fatal startup error
            case E_COMPILE_WARNING: // Non-fatal compile-time error
                return 'Warning';
                break;
            case E_NOTICE: // Non-fatal runtime notice
            case E_USER_NOTICE: // User-triggered notice
                return 'Notice';
                break;
            case E_STRICT:
                return 'Strict Standards';
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return 'Deprecated';
                break;
            default:
                return 'Unknown Error';
        }
    }

    /**
     * Generate css class from error level.
     *
     * @param int $errorLevel
     *
     * @return string Error css class
     */
    private function errorClass($errorLevel)
    {
        switch ($errorLevel) {
            case E_ERROR: // Fatal runtime error
            case E_USER_ERROR: // User-triggered fatal error
            case E_CORE_ERROR: // Fatal startup error
            case E_COMPILE_ERROR: // Fatal compile-time error
            case E_RECOVERABLE_ERROR:
                return 'fatal';
                break;
            case E_PARSE: // Runtime parse error
                return 'parse';
                break;
            case E_WARNING: // Non-fatal runtime error
            case E_USER_WARNING: // User-triggered non-fatal error
            case E_CORE_WARNING: // Non-fatal startup error
            case E_COMPILE_WARNING: // Non-fatal compile-time error
                return 'warning';
                break;
            case E_NOTICE: // Non-fatal runtime notice
            case E_USER_NOTICE: // User-triggered notice
                return 'notice';
                break;
            case E_STRICT:
                return 'strict';
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return 'deprecated';
                break;
            default:
                return '';
        }
    }
}
