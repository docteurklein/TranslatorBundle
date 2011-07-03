<?php

namespace Knplabs\Bundle\TranslatorBundle\Tests\Dumper;

use Knplabs\Bundle\TranslatorBundle\Dumper\XliffDumper;

class XliffDumperTest extends DumperTest
{
    public function getTestFiles()
    {
        return array(
            __DIR__.'/../Fixtures/tests.fr.xliff.dist' => __DIR__.'/../Fixtures/tests.fr.xliff',
            __DIR__.'/../Fixtures/tests.en.xliff.dist' => __DIR__.'/../Fixtures/tests.en.xliff'
        );
    }

    public function testSupportsXliff()
    {
        $dumper = new XliffDumper;
        $this->assertSupportsFormat($dumper, __DIR__.'/../Fixtures/tests.en.xliff');
    }

    public function testNotSupportsYaml()
    {
        $dumper = new XliffDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.yml');
        $this->assertFalse($dumper->supports($stub));
    }

    public function testUpdateReturnValue()
    {
        $dumper = new XliffDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.xliff');
        $this->assertTrue($dumper->update($stub, 'foo.bar.baz', 'test'));
    }

    /**
     * @expectedException Knplabs\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException
     * @dataProvider provideInvalidKeys
     */
    public function testUpdateWithInvalidKey($key, $value)
    {
        $dumper = new XliffDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.xliff');
        $dumper->update($stub, $key, $value);
    }

    /**
     * @dataProvider provideValidKeys
     */
    public function testUpdateValidKey($key, $value)
    {
        $dumper = new XliffDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.xliff');
        $this->assertTrue($dumper->update($stub, $key, $value));
    }

    public function testFinalContent()
    {
        $this->setup();
        $dumper = new XliffDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.xliff');
        foreach ($this->provideValidKeys() as $data) {
            $dumper->update($stub, $data[0], $data[1]);
        }

        $this->assertFileEquals( __DIR__.'/../Fixtures/tests.en.expected.xliff',  __DIR__.'/../Fixtures/tests.en.xliff');
    }

    public function provideInvalidKeys()
    {
        return array(
            array('',                   'huh'),
        );
    }

    public function provideValidKeys()
    {
        return array(
            array('i.dont.exist',                       'I do'),
            array('i have no dots',                     'me neither'),
            array('singleelementkey',                   'not me'),
            array('foo.bar',                            'i exist'),
            array('foo.bar.baz',                        'bar!'),
            array('a key with %placeholder%',           'bar with %placeholder% too!'),
            array('a key with single \' quotes \'\'',   'bar with double " quotes "!'),
            array('a key with double " quotes """!',    'bar with double " quotes " too!'),
            array('some xpath| < \\ || \' "chars"!',    'bar with double " quotes " too!'),
        );
    }
}
