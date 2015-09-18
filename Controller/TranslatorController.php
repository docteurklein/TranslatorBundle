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
use Knp\Bundle\TranslatorBundle\Translation\Writer;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorController
{
    private $translator;
    private $writer;
    private $logger;

    public function __construct(TranslatorInterface $translator, Writer $writer, $logger)
    {
        $this->translator = $translator;
        $this->writer = $writer;
        $this->logger = $logger;
    }

    public function getAction($id, $domain, $locale)
    {
        $trans = $this->translator->trans($id, array(), $domain, $locale);

        return new Response($trans);
    }

    public function putAction(Request $request)
    {
        $translations = $request->request->get('trans');

        foreach($translations as $trans) {
            $id     = @$trans['id'];
            $domain = @$trans['domain'];
            $locale = @$trans['locale'];
            $value  = @$trans['value'];

            $this->writer->write($id, $value, $domain, $locale);
        }

        return new Response;
    }
}
