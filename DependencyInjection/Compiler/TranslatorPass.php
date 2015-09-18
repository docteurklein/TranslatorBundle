<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Knp\Bundle\TranslatorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TranslatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_translator.writer') || !$container->hasDefinition('translator.default')) {
            return;
        }

        $writer = $container->findDefinition('knp_translator.writer');
        $translator = $container->findDefinition('translator');
        $writer->replaceArgument(0, $translator->getArgument(3)['resource_files']);

        foreach($container->findTaggedServiceIds('knp_translator.dumper') as $id => $attributes) {
            $writer
                ->addMethodCall('addDumper', array($container->getDefinition($id)))
            ;
        }

        $container->setDefinition('twig.extension.trans', $container->getDefinition('knp_translator.twig.extension.trans'));
    }
}
