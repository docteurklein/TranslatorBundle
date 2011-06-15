<?php

namespace Knplabs\Bundle\TranslatorBundle\Dumper;

use Knplabs\Bundle\TranslatorBundle\Dumper\DumperInterface;
use Knplabs\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\CssSelector\XPathExpr;
use \DOMDocument;
use \DOMXPath;

class XliffDumper implements DumperInterface
{
    public function supports(FileResource $resource)
    {
        return 'xliff' === pathinfo($resource->getResource(), PATHINFO_EXTENSION);
    }

    /**
     *
     * Updates the content of a xliff file with value for the matched trans id
     *
     * @return Boolean true on success
     *
     */
    public function update(FileResource $resource, $id, $value)
    {
        if('' === $id) {
            throw new InvalidTranslationKeyException(
                sprintf('An empty key can not be used in "%s"', $id, $resource->getResource())
            );
        }

        $document = new DOMDocument;
        // avoid creating textNodes for each carriage return
        $document->preserveWhiteSpace = false;
        // but preserve output indentation when dumping
        $document->formatOutput = true;

        $document->load($resource->getResource());

        $xpath = new DOMXPath($document);
        $escapedId = XPathExpr::xpathLiteral($id);
        //$sources = $xpath->query(sprintf('//trans-unit/source[. =%s]', $escapedId));
        //$sources = $xpath->query(sprintf('//trans-unit/source[contains(., %s)]', $escapedId));
        $sources = $document->getElementsByTagName('source');

        if (false === $sources or 0 === $sources->length) {
            throw new InvalidTranslationKeyException(
                sprintf('The key "%s" can not be found in "%s"', $id, $resource->getResource())
            );
        }

        $updated = false;
        foreach ($sources as $source) {
            // @TODO replace this with a xpath query!
            if($source->nodeValue === $id) {
                if ($target = $source->nextSibling) {
                    $target->nodeValue = $value;
                }
                // @TODO create new target node if not existing yet ?
                $updated = true;
            }
        }

        if (false === $updated) {
            throw new InvalidTranslationKeyException(
                sprintf('The key "%s" can not be found in "%s"', $id, $resource->getResource())
            );
        }

        $result = $document->save($resource->getResource());

        return false !== $result;
    }
}
