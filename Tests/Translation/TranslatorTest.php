<?php

namespace Knplabs\Bundle\TranslatorBundle\Tests\Translation;

use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Knplabs\Bundle\TranslatorBundle\Translation\Translator;
use Knplabs\Bundle\TranslatorBundle\Dumper\YamlDumper;
use Knplabs\Bundle\TranslatorBundle\Tests\Dumper\DumperTest;

class TranslatorTest extends DumperTest
{

    public function getTestFiles()
    {
        return array(
            __DIR__.'/../Fixtures/tests.fr.xliff.dist' => __DIR__.'/../Fixtures/tests.fr.xliff',
            __DIR__.'/../Fixtures/tests.en.xliff.dist' => __DIR__.'/../Fixtures/tests.en.xliff',

            __DIR__.'/../Fixtures/tests.fr.yml.dist' => __DIR__.'/../Fixtures/tests.fr.yml',
            __DIR__.'/../Fixtures/tests.en.yml.dist' => __DIR__.'/../Fixtures/tests.en.yml',
        );
    }

    public function testYamlUpdate()
    {
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $translator = new Translator($containerMock, new MessageSelector());
        $translator->setLocale('en');

        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addResource('yaml', __DIR__.'/../Fixtures/tests.en.yml', 'en', 'tests');
        $translator->addResource('yaml', __DIR__.'/../Fixtures/tests.fr.yml', 'fr', 'tests');

        $translator->addDumper(new YamlDumper());

        $this->assertEquals('foobarbaz', $translator->trans('foo.bar.baz', array(), 'tests', 'en'), 'translation uses initial value');

        $translator->update('foo.bar.baz', 'foofoofoo', 'tests', 'en');
        $updatedEnContent = <<<YAML
foo:
  bar:
    baz: foofoofoo

YAML;
        $this->assertEquals($updatedEnContent, file_get_contents(__DIR__.'/../Fixtures/tests.en.yml'), 'file content is updated with new data');

        $this->assertEquals('foofoofoo', $translator->trans('foo.bar.baz', array(), 'tests', 'en'), 'translation uses updated value');
    }
}
