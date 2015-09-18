<?php

namespace Knp\Bundle\TranslatorBundle\Dumper;

interface Dumper
{
    function supports($resource);

    function update($resource, $id, $value);
}
