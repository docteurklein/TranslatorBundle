<?php

namespace Knp\Bundle\TranslatorBundle\Templating\Twig;

use Symfony\Bridge\Twig\Extension\TranslationExtension as BaseTranslationExtension;
use Knp\Bundle\TranslatorBundle\Templating\Helper\TranslatorHelper;

/**
 * Provides integration of the Translation component with Twig.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TranslationExtension extends BaseTranslationExtension
{
    private $translatorHelper;

    public function __construct(TranslatorHelper $translatorHelper)
    {
        $this->translatorHelper = $translatorHelper;
    }

    public function trans($message, array $arguments = array(), $domain = "messages")
    {
        return $this->translatorHelper->trans($message, $arguments, $domain);
    }

    public function transchoice($message, $count, array $arguments = array(), $domain = "messages")
    {
        return $this->translatorHelper->transChoice($message, $count, array_merge(array('%count%' => $count), $arguments), $domain);
    }
}
