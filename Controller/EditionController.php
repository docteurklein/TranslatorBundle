<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Knplabs\Bundle\TranslatorBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class EditionController
{
    private $translator;
    private $request;
    private $templating;

    public function __construct(Request $request, Translator $translator, EngineInterface $templating)
    {
        $this->request = $request;
        $this->translator = $translator;
        $this->templating = $templating;
    }

    public function listAction()
    {
        // to avoid repetition of the default catalog
        $fallbackLocale = $this->translator->getFallbackLocale();
        $this->translator->setFallbackLocale(null);
        $translations = $this->translator->all();

        $this->translator->setFallbackLocale($fallbackLocale);

        return $this->templating->renderResponse('KnplabsTranslatorBundle:Edition:list.html.twig', array(
            'translations' => $translations,
            'translator' => $this->translator
        ));
    }
}
