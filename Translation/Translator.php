<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knplabs\Bundle\TranslatorBundle\Translation;

use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;
use Symfony\Component\Config\Resource\ResourceInterface;
use Knplabs\Bundle\TranslatorBundle\Dumper\DumperInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Translator.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class Translator extends BaseTranslator
{
    private $dumpers = array();

    public function addDumper(DumperInterface $dumper)
    {
        $this->dumpers[] = $dumper;
    }

    private function getDumper(ResourceInterface $resource)
    {
        foreach ($this->dumpers as $dumper) {
            if ($dumper->supports($resource)) {
                return $dumper;
            }
        }

        return null;
    }

    public function getCatalog($locale)
    {
        $this->loadCatalogue($locale);

        if (isset($this->catalogues[$locale])) {

            return $this->catalogues[$locale];
        }

        throw new \InvalidArgumentException(
            sprintf('The locale "%s" does not exist in Translations catalogues', $locale)
        );
    }

    public function update($id, $value, $domain, $locale)
    {
        $catalog = $this->getCatalog($locale);

        $resources = $this->getMatchedResources($catalog, $domain);

        foreach ($resources as $resource) {
            if ($dumper = $this->getDumper($resource)) {
                $dumper->update($resource, $id, $value);
            }
        }

        return false;
    }

    private function getMatchedResources(MessageCatalogue $catalog, $domain)
    {
        $matched = array();
        foreach ($catalog->getResources() as $resource) {

            // @see Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
            // filename is domain.locale.format
            $basename = \basename($resource->getResource());
            list($resourceDomain, $locale, $format) = explode('.', $basename);

            if ($domain === $resourceDomain) {
                $matched[] = $resource;
            }
        }

        return $matched;
    }
}
