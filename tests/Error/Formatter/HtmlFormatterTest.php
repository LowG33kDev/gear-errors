<?php

namespace Gear\Test\Error\Formatter;

class HtmlFormatterTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @runInSeparateProcess
     */
    public function testFormat()
    {
        $formatter = new \Gear\Error\Formatter\HtmlFormatter();
        $result = $formatter->format(E_ERROR,'Test error','test.php',11,true);
    }
    
}
