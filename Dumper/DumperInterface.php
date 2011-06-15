<?php

namespace Knplabs\Bundle\TranslatorBundle\Dumper;

use Symfony\Component\Config\Resource\FileResource;

interface DumperInterface
{
    function supports(FileResource $resource);

    function update(FileResource $resource, $id, $value);
}
