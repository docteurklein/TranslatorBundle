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

    /**
     * We use Mocks of FileResource to avoid resolving the filename by realpath
     * @see Symfony\Component\Config\Resource\FileResource::__construct
     *
     * @return FileResource a mock of FileResource
     *
     */
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
