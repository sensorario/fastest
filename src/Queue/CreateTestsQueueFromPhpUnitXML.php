<?php

namespace Liuggio\Fastest\Queue;

// BC
if (class_exists('\PHPUnit_Util_Configuration')) {
    class_alias('\PHPUnit_Util_Configuration', '\PHPUnit\Util\Configuration');
}

if (class_exists('\PHPUnit_Framework_TestSuite')) {
    class_alias('\PHPUnit_Framework_TestSuite', '\PHPUnit\Framework\TestSuite');
}

if (class_exists('\PHPUnit_Util_TestSuiteIterator')) {
    class_alias('\PHPUnit_Util_TestSuiteIterator', '\PHPUnit\Framework\TestSuiteIterator');
}

class CreateTestsQueueFromPhpUnitXML
{
    public static function execute($xmlFile)
    {
        $configuration = \PHPUnit\Util\Configuration::getInstance($xmlFile);
        $testSuites = new TestsQueue();

        self::processTestSuite($testSuites, $configuration->getTestSuiteConfiguration()->getIterator());

        return $testSuites;
    }

    private static function processTestSuite(TestsQueue $testSuites, \PHPUnit\Framework\TestSuiteIterator $testSuiteIterator)
    {
        foreach ($testSuiteIterator as $testSuite) {
            self::addTestFile($testSuites, $testSuite);

            if ($testSuite instanceof \PHPUnit\Framework\TestSuite) {
                self::processTestSuite($testSuites, $testSuite->getIterator());
            }
        }
    }

    private static function addTestFile(TestsQueue $testSuites, $testSuite)
    {
        $name = $testSuite->getName();
        if (class_exists($name)) {
            $class = new \ReflectionClass($name);
            $testSuites->add($class->getFileName());
        }
    }
}
