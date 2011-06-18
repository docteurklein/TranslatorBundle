<?php

namespace Knplabs\Bundle\TranslatorBundle\Tests\Dumper;

use Knplabs\Bundle\TranslatorBundle\Dumper\CsvDumper;

class CsvDumperTest extends DumperTest
{
    public function getTestFiles()
    {
        return array(
            __DIR__.'/../Fixtures/tests.en.csv.dist' => __DIR__.'/../Fixtures/tests.en.csv'
        );
    }

    public function testSupportsCsv()
    {
        $dumper = new CsvDumper;
        $this->assertSupportsFormat($dumper, __DIR__.'/../Fixtures/tests.en.csv');
    }

    public function testNotSupportsXml()
    {
        $dumper = new CsvDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.xml');
        $this->assertFalse($dumper->supports($stub));
    }

    public function testUpdateReturnValue()
    {
        $dumper = new CsvDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.csv');
        $this->assertTrue($dumper->update($stub, 'foo.bar.baz', 'test'));
    }

    /**
     * @expectedException Knplabs\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException
     * @dataProvider provideInvalidKeys
     */
    public function testUpdateWithInvalidKey($key, $value)
    {
        $dumper = new CsvDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.csv');
        $dumper->update($stub, $key, $value);
    }

    /**
     * @dataProvider provideValidKeys
     */
    public function testUpdateValidKey($key, $value)
    {
        $dumper = new CsvDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.csv');
        $this->assertTrue($dumper->update($stub, $key, $value));
    }

    public function testFinalContent()
    {
        $dumper = new CsvDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.csv');
        foreach ($this->provideValidKeys() as $data) {
            $dumper->update($stub, $data[0], $data[1]);
        }

        $this->assertFileEquals( __DIR__.'/../Fixtures/tests.en.expected.csv',  __DIR__.'/../Fixtures/tests.en.csv');
    }

    public function provideInvalidKeys()
    {
        return array(
            array('',                                     'huh'),
            array("i \r have \r\n carriage \n returns!",  'I do not'),
            array("i \r have \r\n carriage \n returns!",  "I \n do too! \r\n \r"),
        );
    }

    public function provideValidKeys()
    {
        return array(
            array('foo.bar.baz',                          'bar!'),
            array('test with double " quotes"',           "bar ''! '"),
            array("test with simple ' quotes'",           'bar ""! "'),
            array('i have ; csv separators;',             'I do not'),
            array('i have ; csv separators;',             'I ; do too!'),
        );
    }
}
