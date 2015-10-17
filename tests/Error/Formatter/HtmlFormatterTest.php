<?php

namespace Gear\Test\Error\Formatter;

class HtmlFormatterTest extends \PHPUnit_Framework_TestCase
{
    
	private static $_formatter = null;
    
    public static function setUpBeforeClass()
    {
        self::$_formatter = new \Gear\Error\Formatter\HtmlFormatter();
    }
    
    public static function tearDownAfterClass()
    {
        self::$_formatter = null;
    }
	   
	
	/**
     * @runInSeparateProcess
	 * @dataProvider providerTitleError
     */
	public static function testDefaultDisplayErrorsTitle($errorType, $errorMessage, $errorFile, $errorLine, $titleNeeded)
	{
		error_reporting(0);
		$result = self::$formatter->format($errorType, $errorMessage, $errorFile, $errorLine, true);
		$this->assertContains("<title>Error | {$titleNeeded}</title>",$result);
	}
	
	/**
     * @runInSeparateProcess
	 * @dataProvider providerTitleError
     */
	public function testDefaultHiddenErrorsTitle($errorType, $errorMessage, $errorFile, $errorLine)
	{
		error_reporting(0);
		$result = self::$_formatter->format($errorType, $errorMessage, $errorFile, $errorLine, false);
		$this->assertContains('<title>Error</title>',$result);
	}
	
	/**
     * @codeCoverageIgnore
     */
    public function providerTitleError()
    {
        return array(
            'Fatal error' => array(E_ERROR, 'Error message', 'test.php', 10, 'Fatal Error'),
            'Warning error' => array(E_WARNING, 'Error message', 'test.php', 10, 'Warning'),
            'Notice error' => array(E_NOTICE, 'Error message', 'test.php', 10, 'Notice'),
            'Depreacted error' => array(E_USER_DEPRECATED, 'Error message', 'test.php', 10, 'Deprecated')
        );
    }
}
