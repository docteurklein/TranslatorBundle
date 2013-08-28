<?php

namespace Knp\Bundle\TranslatorBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class KnpTranslatorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (!$config['enabled']) {
            return;
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach (array('translation', 'controller') as $basename) {
            $loader->load(sprintf('%s.yml', $basename));
        }

        foreach (array('include_vendor_assets') as $attribute) {
            if (isset($config[$attribute])) {
                $container->setParameter('knplabs.translator.'.$attribute, $config[$attribute]);
            }
        }

        // Use the "writer" translator instead of the default one
        $container->setAlias('translator', 'translator.writer');
        $container->setAlias('templating.helper.translator', 'templating.helper.translator.writer');
    }
}
