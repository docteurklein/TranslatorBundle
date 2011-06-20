<?php

namespace Knplabs\Bundle\TranslatorBundle\Dumper;

use Knplabs\Bundle\TranslatorBundle\Dumper\DumperInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;
use Knplabs\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException;

class YamlDumper implements DumperInterface
{
    public function supports(FileResource $resource)
    {
        return 'yml' === pathinfo($resource->getResource(), PATHINFO_EXTENSION);
    }

    /**
     *
     * Updates the content of a yaml file with value for the matched trans id
     * The separation of trans id with dots describes the nested level of the node in yaml
     *
     * @see Symfony\Component\Translator\Loader\ArrayLoader which has the inverse behavior.
     *
     * knplabs_translator.title will match:
     * ``` yaml
     *
     * knplabs_translator:
     *     title: <new value>
     *
     * ```
     */
    public function update(FileResource $resource, $id, $value)
    {
        $translations = Yaml::parse($resource->getResource());
        if (!is_array($translations)) {
            return false;
        }
        // working on references of the array elements
        $finalNode =& $translations;

        $explodedId = explode('.', $id);
        foreach ($explodedId as $node) {
            if (false === array_key_exists($node, $finalNode)) {
                throw new InvalidTranslationKeyException(
                    sprintf('The key "%s" can not be found in "%s"', $id, $resource->getResource())
                );
            }
            // working on references of the array elements
            $finalNode =& $finalNode[$node];
        }

        if(is_array($finalNode)) {
            throw new InvalidTranslationKeyException(
                sprintf('The key "%s" is not a scalar yaml node in "%s"', $id, $resource->getResource())
            );
        }

        //this puts the value in the translations array, by reference
        $finalNode = $value;
        // dump yaml and switch to inline at 1000th level
        $yaml = Yaml::dump($translations, 1000);

        $result = file_put_contents($resource->getResource(), $yaml);

        return false !== $result;
    }
}
