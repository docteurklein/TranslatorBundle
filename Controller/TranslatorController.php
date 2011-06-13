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

class TranslatorController
{
    private $translator;
    private $request;

    public function __construct(Request $request, Translator $translator)
    {
        $this->request = $request;
        $this->translator = $translator;
    }

    public function getAction($id, $domain, $locale)
    {
        $trans = $this->translator->trans($id, array(), $domain, $locale);

        return new Response($trans);
    }

    public function putAction($id, $domain, $locale)
    {
        $value = $this->request->get('value');

        $success = $this->translator->update($id, $value, $domain, $locale);

        return new Response(json_encode($success));
    }
}
