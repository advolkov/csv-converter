<?php
/**
 * @author Anton Volkov <advolkov1@gmail.com>
 */

class CsvConverterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * This method is for gaining access to private methods
     *
     * @param string $class_name
     * @param string $name
     * @return ReflectionMethod
     */
    protected static function getPrivateMethod($class_name, $name) {
        $class = new ReflectionClass($class_name);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
