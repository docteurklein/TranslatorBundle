<?php

namespace Knplabs\Bundle\TranslatorBundle\Tests\Dumper;

use Knplabs\Bundle\TranslatorBundle\Dumper\DumperInterface;

abstract class DumperTest extends \PHPUnit_Framework_TestCase
{
    abstract function getTestFiles();

    public function setUp()
    {
        foreach ($this->getTestFiles() as $source => $destination) {
            copy($source, $destination);
        }
    }

    public function tearDown()
    {
        foreach ($this->getTestFiles() as $source => $destination) {
            unlink($destination);
        }
    }

    /**
     * We use Mocks of FileResource to avoid resolving the filename by realpath
     * @see Symfony\Component\Config\Resource\FileResource::__construct
     *
     * @return FileResource a mock of FileResource
     *
     */
    protected function getFileResourceStub($filename)
    {
        $stub = $this->getMockBuilder('Symfony\Component\Config\Resource\FileResource')
            ->disableOriginalConstructor()->getMock();

        $stub
            ->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($filename));

        return $stub;
    }

    public function assertSupportsFormat(DumperInterface $dumper, $filename)
    {
        $stub = $this->getFileResourceStub($filename);
        $this->assertTrue($dumper->supports($stub));
    }
}
