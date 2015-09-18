<?php

namespace Knp\Bundle\TranslatorBundle\Translation;

use Knp\Bundle\TranslatorBundle\Dumper\Dumper;
use Knp\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException;

/**
 * Writes a translation to the catalogue resource files
 *
 * @author Florian Klein <florian.klein@free.fr>
 */
class Writer
{
    private $dumpers = array();
    private $resources;

    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    public function addDumper(Dumper$dumper)
    {
        $this->dumpers[] = $dumper;
    }

    /**
     * Updates the value of a given trans id for a specified domain and locale
     *
     * @param string $id the trans id
     * @param string $value the translated value
     * @param string domain the domain name
     * @param string $locale
     *
     * @throws \Exception on error
     * @throws InvalidTranslationKeyException on invalid key
     */
    public function write($id, $value, $domain, $locale)
    {
        if (empty($id)) {
            throw new InvalidTranslationKeyException('Empty key not allowed');
        }
        $resources = $this->getMatchedResources($domain, $locale);

        foreach ($resources as $resource) {
            if ($dumper = $this->getDumper($resource)) {
                if (!$dumper->update($resource, $id, $value)) {
                    throw new \Exception;
                }
            }
        }
    }

    /**
     * Gets the resources that matches a domain and a locale on a particular catalog
     *
     * @param string $domain the domain name (default is 'messages')
     * @param string $locae the locale, to filter fallbackLocale
     *
     * @return array of paths
     */
    private function getMatchedResources($domain, $locale)
    {
        $matched = array();
        foreach ($this->resources as $resources) {
            foreach ($resources as $resource) {

                // @see Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
                // filename is domain.locale.format
                $basename = basename($resource);
                list($resourceDomain, $resourceLocale, $format) = explode('.', $basename);

                if ($domain === $resourceDomain && $locale === $resourceLocale) {
                    $matched[] = $resource;
                }
            }
        }

        return $matched;
    }

    /**
     * @return Dumper
     */
    private function getDumper($resource)
    {
        foreach ($this->dumpers as $dumper) {
            if ($dumper->supports($resource)) {
                return $dumper;
            }
        }

        return null;
    }
}
