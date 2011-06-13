<?php

namespace Knplabs\Bundle\TranslatorBundle\Dumper;

use Symfony\Component\Config\Resource\ResourceInterface;

interface DumperInterface
{
    function supports(ResourceInterface $resource);

    function update(ResourceInterface $resource, $id, $value);
}
