<?php

namespace Knplabs\Bundle\TranslatorBundle\Tests\Dumper;

use Knplabs\Bundle\TranslatorBundle\Dumper\YamlDumper;
use Symfony\Component\Config\Resource\FileResource;

class YamlDumperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        copy(__DIR__.'/../Fixtures/tests.fr.yml.dist', __DIR__.'/../Fixtures/tests.fr.yml');
        copy(__DIR__.'/../Fixtures/tests.en.yml.dist', __DIR__.'/../Fixtures/tests.en.yml');
    }

    public function tearDown()
    {
        unlink(__DIR__.'/../Fixtures/tests.fr.yml');
        unlink(__DIR__.'/../Fixtures/tests.en.yml');
    }

    private function getFileResourceStub($filename)
    {
        $stub = $this->getMockBuilder('Symfony\Component\Config\Resource\FileResource')
            ->disableOriginalConstructor()->getMock();

        $stub
            ->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($filename));

        return $stub;
    }

    public function testSupportsYaml()
    {
        $dumper = new YamlDumper;
        $stub = $this->getFileResourceStub(__DIR__.'/../Fixtures/tests.en.yml');
        $this->assertTrue($dumper->supports($stub));
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
        $resource = new FileResource(__DIR__.'/../Fixtures/tests.en.yml');
        $this->assertTrue($dumper->update($resource, 'foo.bar.baz', 'test'));
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
            array('i.dont.exist',       'me neither'),
            array('i have no dots',     'me neither'),
            array('singleelementkey',   'me neither'),
            array('foo.bar',            'bar!'),
            array('',                   'me neither'),
        );
    }

    public function provideValidKeys()
    {
        return array(
            array('foo.bar.baz',        'bar!'),
        );
    }
}
