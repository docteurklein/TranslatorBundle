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
use Knp\Bundle\TranslatorBundle\Exception\InvalidTranslationKeyException;

class TranslatorController
{
    private $translator;
    private $request;
    private $logger;

    public function __construct(Request $request, Translator $translator, $logger)
    {
        $this->request = $request;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    public function getAction($id, $domain, $locale)
    {
        $trans = $this->translator->getTranslatedValue($id, array(), $domain, $locale);

        return new Response($trans);
    }

    public function putAction()
    {
        $id = $this->request->request->get('id');
        $domain = $this->request->request->get('domain');
        $locale = $this->request->request->get('locale');
        $value = $this->request->request->get('value');

        $error = null;
        try {
            $success = $this->translator->update($id, $value, $domain, $locale);
            $trans = $value;
        }
        catch (InvalidTranslationKeyException $e) {
            $success = false;
            $trans = $this->translator->getTranslatedValue($id, array(), $domain, $locale);
            $error = $e->getMessage();
        }

        return new Response(json_encode(array(
            'success' => $success,
            'trans' => $trans,
            'error' => $error,
        )));
    }
}
