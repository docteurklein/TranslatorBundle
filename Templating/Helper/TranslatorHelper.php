<?php

namespace Knp\Bundle\TranslatorBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper as BaseTranslatorHelper;

/**
 * TranslatorHelper.
 *
 * @author Florian Klein <florian.klein@free.fr>
 */
class TranslatorHelper extends BaseTranslatorHelper
{
    /**
     * @see TranslatorInterface::trans()
     */
    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        if (!isset($locale)) {
            $locale = $this->translator->getLocale();
        }

        $trans = parent::trans($id, $parameters, $domain, $locale);

        return $this->wrap($id, $trans, $domain, $locale);
    }

    /**
     * @see TranslatorInterface::transChoice()
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        if (!isset($locale)) {
            $locale = $this->translator->getLocale();
        }

        $trans = parent::transChoice($id, $number, $parameters, $domain, $locale);

        return $this->wrap($id, $trans, $domain, $locale);
    }

    /**
     * Wraps a translated value with [T id="%s" domain="%s" locale="%s"]%s[/T]
     * Used to detect in-line edition of translations
     *
     * @return string
     */
    public function wrap($id, $trans, $domain = 'messages', $locale = null)
    {

        $class = array('knp-translator', 'translatable');
        if($id === $trans) {
            $class[] = 'untranslated';
        }

        $startTag =  vsprintf(
            '<ins class="%s" data-id="%s" data-domain="%s" data-locale="%s" data-value="%s">',
            array(
                implode(' ', $class),
                $id,
                $domain,
                $locale,
                $trans
            )
        );


        return sprintf('%s%s%s', $startTag, $trans, '</ins>');
    }
}
