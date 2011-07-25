<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Knp\Bundle\TranslatorBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper;

class EditionController
{
    private $translator;
    private $translatorHelper;
    private $request;
    private $templating;

    public function __construct(Request $request, Translator $translator, TranslatorHelper $translatorHelper, EngineInterface $templating)
    {
        $this->request = $request;
        $this->translator = $translator;
        $this->translatorHelper = $translatorHelper;
        $this->templating = $templating;
    }

    public function listAction()
    {
        // to avoid repetition of the default catalog
        $fallbackLocale = $this->translator->getFallbackLocale();
        $this->translator->setFallbackLocale(null);
        $translations = $this->translator->all();

        $this->translator->setFallbackLocale($fallbackLocale);

        return $this->templating->renderResponse('KnpTranslatorBundle:Edition:list.html.twig', array(
            'translations' => $translations,
            'translatorHelper' => $this->translatorHelper,
            'translator' => $this->translator,
        ));
    }
}
