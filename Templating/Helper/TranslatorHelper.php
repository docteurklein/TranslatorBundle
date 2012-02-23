<?php

namespace Knp\Bundle\TranslatorBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper as BaseTranslatorHelper;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * TranslatorHelper.
 *
 * @author Florian Klein <florian.klein@free.fr>
 */
class TranslatorHelper extends BaseTranslatorHelper
{
    private $securityContext;
    private $roles;

    public function setSecurityContext(SecurityContext $context)
    {
        $this->securityContext = $context;
    }

    public function setRoles(array $roles = null)
    {
        $this->roles = $roles;
    }

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
        if ($this->isGranted()) {
            $startTag = sprintf('[T id="%s" domain="%s" locale="%s"]', $id, $domain, $locale);

            return sprintf('%s%s%s', $startTag, $trans, '[/T]');
        }

        return $trans;
    }

    private function isGranted()
    {
        if (null === $this->roles or null === $this->securityContext->getToken()) {
            return true;
        }

        return $this->securityContext->isGranted($this->roles);
    }
}
