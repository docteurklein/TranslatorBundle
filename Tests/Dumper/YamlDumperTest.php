<?php

namespace Knplabs\Bundle\TranslatorBundle\Tests\Dumper;

use Knplabs\Bundle\TranslatorBundle\Dumper\YamlDumper;

class YamlDumperTest extends DumperTest
{
    public function getTestFiles()
    {
        return array(
            __DIR__.'/../Fixtures/tests.fr.yml.dist' => __DIR__.'/../Fixtures/tests.fr.yml',
            __DIR__.'/../Fixtures/tests.en.yml.dist'=> __DIR__.'/../Fixtures/tests.en.yml'
        );
    }

    public function testSupportsYaml()
    {
        $dumper = new YamlDumper;
        $this->assertSupportsFormat($dumper, __DIR__.'/../Fixtures/tests.en.yml');
    }

    public function testNotSupportsXml()
    {
        $dumper = new YamlDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.xml');
        $this->assertFalse($dumper->supports($stub));
    }

    public function testUpdateReturnValue()
    {
        $dumper = new YamlDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.yml');
        $this->assertTrue($dumper->update($stub, 'foo.bar.baz', 'test'));
    }

    /**
     * @expectedException Knplabs\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException
     * @dataProvider provideInvalidKeys
     */
    public function testUpdateWithInvalidKey($key, $value)
    {
        $dumper = new YamlDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.yml');
        $dumper->update($stub, $key, $value);
    }

    public function provideInvalidKeys()
    {
        return array(
            array('i.dont.exist',       'I do'),
            array('i have no dots',     'me neither'),
            array('singleelementkey',   'not me'),
            array('foo.bar',            'i exist, but i\'m not a scalar node'),
            array('',                   'huh'),
        );
    }

    public function provideValidKeys()
    {
        return array(
            array('foo.bar.baz',        'bar!'),
        );
    }
}
