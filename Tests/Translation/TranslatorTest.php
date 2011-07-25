<?php

namespace Knp\Bundle\TranslatorBundle\Tests\Translation;

use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Knp\Bundle\TranslatorBundle\Translation\Translator;
use Knp\Bundle\TranslatorBundle\Dumper\YamlDumper;
use Knp\Bundle\TranslatorBundle\Tests\Dumper\DumperTest;
use Symfony\Component\Translation\Loader\XliffFileLoader;

class TranslatorTest extends DumperTest
{

    public function getTestFiles()
    {
        return array(
            __DIR__.'/../Fixtures/tests.fr.xliff.dist' => __DIR__.'/../Fixtures/tests.fr.xliff',
            __DIR__.'/../Fixtures/tests.en.xliff.dist' => __DIR__.'/../Fixtures/tests.en.xliff',

            __DIR__.'/../Fixtures/tests.fr.yml.dist' => __DIR__.'/../Fixtures/tests.fr.yml',
            __DIR__.'/../Fixtures/tests.en.yml.dist' => __DIR__.'/../Fixtures/tests.en.yml',

            __DIR__.'/../Fixtures/thedomain.fr.yml.dist' => __DIR__.'/../Fixtures/thedomain.fr.yml',
            __DIR__.'/../Fixtures/thedomain.en.yml.dist' => __DIR__.'/../Fixtures/thedomain.en.yml',
        );
    }

    public function testYamlUpdate()
    {
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $translator = new Translator($containerMock, new MessageSelector());
        $translator->setLocale('en');
        $translator->setFallbackLocale('en');

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
    }

    public function testResourcesRetrieval()
    {
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $translator = new Translator($containerMock, new MessageSelector());
        $translator->setLocale('en');
        $translator->setFallbackLocale('en');

        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addLoader('xliff', new XliffFileLoader());

        $translator->addResource('yaml', __DIR__.'/../Fixtures/tests.en.yml',       'en', 'tests');
        $translator->addResource('yaml', __DIR__.'/../Fixtures/tests.fr.yml',       'fr', 'tests');

        $translator->addResource('yaml', __DIR__.'/../Fixtures/thedomain.en.yml',   'en', 'thedomain');
        $translator->addResource('yaml', __DIR__.'/../Fixtures/thedomain.fr.yml',   'fr', 'thedomain');

        $translator->addResource('xliff', __DIR__.'/../Fixtures/tests.en.xliff',    'en', 'tests');
        $translator->addResource('xliff', __DIR__.'/../Fixtures/tests.fr.xliff',    'fr', 'tests');

        $this->assertEquals(2, count($translator->getResources('en', 'tests')));
        $this->assertEquals(1, count($translator->getResources('en', 'thedomain')));
    }
}
