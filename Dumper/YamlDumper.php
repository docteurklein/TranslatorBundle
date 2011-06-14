<?php

namespace Knplabs\Bundle\TranslatorBundle\Dumper;

use Knplabs\Bundle\TranslatorBundle\Dumper\DumperInterface;
use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\Yaml\Yaml;

class YamlDumper implements DumperInterface
{
    public function supports(ResourceInterface $resource)
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
    public function update(ResourceInterface $resource, $id, $value)
    {
        $translations = Yaml::load($resource->getResource());
        $finalNode =& $translations;

        $exploded = explode('.', $id);
        foreach ($exploded as $node) {
            if (isset($finalNode[$node])) {
                $finalNode =& $finalNode[$node];
            }
        }

        if (null !== $finalNode) {
            $finalNode = $value;
        }
        // dump yaml and switch to inline at 1000th level
        $yaml = Yaml::dump($translations, 1000);

        return false !== file_put_contents($resource->getResource(), $yaml);
    }
}
