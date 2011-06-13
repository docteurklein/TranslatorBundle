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
        $yaml = Yaml::dump($translations);

        return false !== file_put_contents($resource->getResource(), $yaml);
    }
}
