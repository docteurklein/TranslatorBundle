<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Knplabs\Bundle\TranslatorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TranslatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('translator.debug') || !$container->hasDefinition('translator.real')) {
            return;
        }

        $translatorRealDefinition = $container->findDefinition('translator.real');
        $translatorDefinition = $container->findDefinition('translator.debug');

        $translatorDefinition->replaceArgument(2, $translatorRealDefinition->getArgument(2));

        foreach($translatorRealDefinition->getMethodCalls() as $methodCall) {
            $translatorDefinition->addMethodCall($methodCall[0], $methodCall[1]);
        }

        foreach($container->findTaggedServiceIds('knplabs_translator.dumper') as $id => $attributes) {
            $translatorDefinition->addMethodCall('addDumper', array($container->getDefinition($id)));
        }
    }
}
