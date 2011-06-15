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

class EditionController
{
    private $translator;
    private $request;

    public function __construct(Request $request, Translator $translator)
    {
        $this->request = $request;
        $this->translator = $translator;
    }

    public function listAction($locale)
    {
        $translations = array();
        foreach ($this->translator->getLocales() as $locale) {

            $translations[$locale] = $this->translator->getAll($locale);
        }

        return new Response(json_encode($translations));
    }
}
