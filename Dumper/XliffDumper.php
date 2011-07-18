<?php

namespace Knp\Bundle\TranslatorBundle\Dumper;

use Knp\Bundle\TranslatorBundle\Dumper\DumperInterface;
use Knp\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\CssSelector\XPathExpr;
use \DOMDocument;
use \DOMNode;
use \DOMXPath;

class XliffDumper implements DumperInterface
{
    private $currentLibXmlErrorHandler;

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
                sprintf('An empty key can not be used in "%s"', $resource->getResource())
            );
        }
        $document = $this->getDomDocument($resource);
        $xpath = $this->getDomXPath($document);

        $escapedId = XPathExpr::xpathLiteral($id);
        $sources = $xpath->query(sprintf('//xliff:trans-unit/xliff:source[. =%s]', $escapedId));

        $updated = false;
        foreach ($sources as $source) {
            if (null === $target = $source->nextSibling) {
                $target = $document->createElement('target');
                $source->parentNode->appendChild($target);
            }
            $target->nodeValue = $value;
            $updated = true;
        }

        if (false === $updated) {
            $nodeList = $xpath->evaluate('//xliff:trans-unit/@id[php:function("Knp\Bundle\TranslatorBundle\Dumper\dom_xpath_max", ., //xliff:trans-unit/@id)]');

            $number = $nodeList->item(0)->value + 1;
            $node = $this->create($document, $id, $value, $number);
            $body = $xpath->query('//xliff:body')->item(0);
            $body->appendChild($node);
        }
        $this->checkErrors();
        $result = $document->save($resource->getResource());
        $this->checkErrors();
        libxml_use_internal_errors($this->currentLibXmlErrorHandler);

        return false !== $result;
    }

    private function create(DomDocument $document, $id, $value, $number)
    {
        $transUnit = $document->createElement('trans-unit');
        $transUnit->setAttribute('id', $number);
        $source = $document->createElement('source');
        $source->nodeValue = $id;
        $target = $document->createElement('target');
        $target->nodeValue = $value;
        $transUnit->appendChild($source);
        $transUnit->appendChild($target);

        return $transUnit;
    }

    private function getDomDocument(FileResource $resource)
    {
        $this->currentLibXmlErrorHandler = libxml_use_internal_errors(true);

        $document = new DOMDocument;
        // avoid creating textNodes for each carriage return
        $document->preserveWhiteSpace = false;
        // but preserve output indentation when dumping
        $document->formatOutput = true;

        $document->load($resource->getResource());

        return $document;
    }

    private function getDomXPath(DOMDocument $document)
    {
        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('xliff', 'urn:oasis:names:tc:xliff:document:1.2');
        $xpath->registerNamespace('php', 'http://php.net/xpath');
        $xpath->registerPHPFunctions('Knp\Bundle\TranslatorBundle\Dumper\dom_xpath_max');

        return $xpath;
    }

    private function checkErrors()
    {
        if($errors = $this->getXmlErrors()) {
            libxml_use_internal_errors($this->currentLibXmlErrorHandler);
            throw new \InvalidArgumentException(implode("\n", $errors));
        }
    }

    /**
     * Returns an array of XML errors.
     *
     * @return array
     */
    private function getXmlErrors()
    {
        $errors = array();
        foreach (libxml_get_errors() as $error) {
            $errors[] = sprintf('[%s %s] %s (in %s - line %d, column %d)',
                LIBXML_ERR_WARNING == $error->level ? 'WARNING' : 'ERROR',
                $error->code,
                trim($error->message),
                $error->file ? $error->file : 'n/a',
                $error->line,
                $error->column
            );
        }

        libxml_clear_errors();

        return $errors;
    }
}

function dom_xpath_max($that, $nodes)
{
    usort($nodes, function($a, $b) {
        return $b->value > $a->value;
    });

    return $that[0]->value == $nodes[0]->value;
}
