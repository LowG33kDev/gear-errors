<?php

namespace Gear\Test\Error\Formatter;

class JsonFormatterTest extends \PHPUnit_Framework_TestCase
{
    
    private static $_formatter = null;
    
    public static function setUpBeforeClass()
    {
        self::$_formatter = new \Gear\Error\Formatter\JsonFormatter();
    }
    
    public static function tearDownAfterClass()
    {
        self::$_formatter = null;
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testFormatContainsErrorKey()
    {
        $result = json_decode(self::$_formatter->format(E_ERROR,'Test error','test.php',11,true),true);
        $this->assertArrayHasKey('error',$result);
    }
    
    /**
     * @depends testFormatContainsErrorKey
     * @runInSeparateProcess
     */
    public function testFormatContainsDatasKeys()
    {
        $result = json_decode(self::$_formatter->format(E_ERROR,'Test error','test.php',11,true),true);
        $this->assertArrayHasKey('type',$result['error']);
        $this->assertArrayHasKey('message',$result['error']);
        $this->assertArrayHasKey('file',$result['error']);
        $this->assertArrayHasKey('line',$result['error']);
    }
    
    /**
     * @depends testFormatContainsDatasKeys
     * @runInSeparateProcess
     * @dataProvider providerTestErrorReturns
     */
    public function testErrorReturns($errorType, $errorMessage, $errorFile, $errorLine, $errorDisplay)
    {
        $result = json_decode(self::$_formatter->format($errorType,$errorMessage,$errorFile,$errorLine,$errorDisplay),true);
        $this->assertEquals($errorType,$result['error']['type']);
        $this->assertEquals($errorMessage,$result['error']['message']);
        $this->assertEquals($errorFile,$result['error']['file']);
        $this->assertEquals($errorLine,$result['error']['line']);
    }
    
    /**
     * @codeCoverageIgnore
     */
    public function providerTestErrorReturns()
    {
        return array(
            'Fatal error' => array(E_ERROR, 'Error message', 'test.php', 10, true),
            'User error' => array(E_USER_ERROR, 'Error message', 'test.php', 10, true),
            'Warning error' => array(E_WARNING, 'Error message', 'test.php', 10, true)
        );
    }
    
    
}
