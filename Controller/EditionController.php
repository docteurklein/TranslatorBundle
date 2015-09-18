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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class EditionController
{
    private $translator;
    private $templating;

    public function __construct(TranslatorBagInterface $translator, EngineInterface $templating)
    {
        $this->translator = $translator;
        $this->templating = $templating;
    }

    public function listAction()
    {
        $translations = $this->translator->getMessages();

        return $this->templating->renderResponse('KnpTranslatorBundle:Edition:list.html.twig', array(
            'domains' => $translations,
        ));
    }
}
