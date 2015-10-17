<?php
/**
 * Exception class file
 *
 * @author LowG33kDev <loic.marchand73.dev@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://www.github.com/lowg33kdev/gear-errors Repository
 */
namespace Gear\Exception;

/**
 *
 */
class Exception extends \RuntimeException
{
	
	/**
	 *
	 */
	protected $httpMessage = 'Internal Server Error';
	
	/**
	 *
	 */
	public function __construct($message, $code = 500, $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
	
	/**
	 *
	 */
	public function getHttpMessage()
	{
		return $this->httpMessage;
	}
}
