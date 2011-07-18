<?php

namespace Knp\Bundle\TranslatorBundle\Dumper;

use Knp\Bundle\TranslatorBundle\Dumper\DumperInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;
use Knp\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException;

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
        if('' === $id) {
            throw new InvalidTranslationKeyException(
                sprintf('An empty key can not be used in "%s"', $resource->getResource())
            );
        }
        $translations = Yaml::parse($resource->getResource());
        if (!is_array($translations)) {
            return false;
        }
        // working on references of the array elements
        $finalNode =& $translations;

        $explodedId = explode('.', $id);
        $count = count($explodedId);
        $i = 1;
        foreach ($explodedId as $key) {
            if (false === array_key_exists($key, $finalNode)) {
                // node doesn't exist, create it
                // if last node, create scalar node, else array
                $node = ($i === $count) ? '' : array();
                $finalNode[$key] = $node;
            }
            // working on references of the array elements
            $finalNode =& $finalNode[$key];
            $i++;
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
