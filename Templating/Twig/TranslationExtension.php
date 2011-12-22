<?php

namespace Knp\Bundle\TranslatorBundle\Templating\Twig;

use Symfony\Bridge\Twig\Extension\TranslationExtension as BaseTranslationExtension;
use Knp\Bundle\TranslatorBundle\Templating\Helper\TranslatorHelper;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Provides integration of the Translation component with Twig.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TranslationExtension extends BaseTranslationExtension
{
    private $translatorHelper;

    public function __construct(TranslatorHelper $translatorHelper, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->translatorHelper = $translatorHelper;
    }

    public function trans($message, array $arguments = array(), $domain = 'messages', $locale = null)
    {
        return $this->translatorHelper->trans($message, $arguments, $domain);
    }

    public function transchoice($message, $count, array $arguments = array(), $domain = 'messages', $locale = null)
    {
        return $this->translatorHelper->transChoice($message, $count, array_merge(array('%count%' => $count), $arguments), $domain);
    }

    public function getTranslator()
    {
        return $this->translatorHelper;
    }
}
