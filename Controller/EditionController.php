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

use Knp\Bundle\TranslatorBundle\Form\SearchType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper;
use Symfony\Component\Translation\MessageCatalogue;

class EditionController
{
    private $translator;
    private $translatorHelper;
    private $request;
    private $templating;
    private $formFactory;

    public function __construct(Request $request, Translator $translator, TranslatorHelper $translatorHelper, EngineInterface $templating, FormFactoryInterface $formFactory)
    {
        $this->request          = $request;
        $this->translator       = $translator;
        $this->translatorHelper = $translatorHelper;
        $this->templating       = $templating;
        $this->formFactory      = $formFactory;
    }

    public function listAction()
    {
        // to avoid repetition of the default catalog
        $fallbackLocales = $this->translator->getFallbackLocales();
        $this->translator->setFallbackLocales(array());
        $translations = $this->translator->all();

        $this->translator->setFallbackLocales($fallbackLocales);

        return $this->templating->renderResponse('KnpTranslatorBundle:Edition:list.html.twig', array(
            'translations' => $translations,
            'translatorHelper' => $this->translatorHelper,
            'translator' => $this->translator,
        ));
    }

    public function indexAction()
    {
        $currentLocale  = $this->request->getLocale();
        $domains[]      = $currentDomain = 'All';
        $locales        = $this->translator->getLocales();
        $catalogs       = $translations = array();
        $key            = null;

        foreach ($locales as $locale) {
            $catalogs[$locale] = $this->translator->getCatalog($locale);
            $domains = $domains + $catalogs[$locale]->getDomains();
        }

        $form = $this->formFactory->create(
            new SearchType(),
            array(
                'locale' => $currentLocale,
                'domain' => $currentDomain
            ),
            array(
                'locale' => $locales,
                'domain' => $domains
            )
        );

        if ($this->request->getMethod() == 'POST') {
            $form->submit($this->request);
            if ($form->isValid()) {
                $currentLocale = $form->get('locale')->getData();
                $currentDomain = $form->get('domain')->getData();
                $key           = $form->get('key')->getData();
            }
        }

        //Build a message catalogue for the current locale and domain.
        $catalog = new MessageCatalogue($currentLocale);
        foreach ($catalogs as $locale => $element) {
            foreach ($element->all() as $domain => $translations) {
                foreach ($translations as $id => $value) {
                    if (!$catalog->has($id, $domain) && (empty($key) || $key == $id)) {
                        $catalog->set($id, ($locale == $currentLocale ? $value : ""), $domain);
                    }elseif($locale == $currentLocale && (empty($key) || $key == $id)){
                        $catalog->set($id, $value, $domain);
                    }
                }
            }
        }

        //Return translation for the specified domain.
        if(in_array($currentDomain, $domains) && $currentDomain != "All"){
            $translations = array(
                $currentDomain => $catalog->all($currentDomain)
            );
        } else {
            $translations = $catalog->all();
        }

        return $this->templating->renderResponse('KnpTranslatorBundle:Edition:index.html.twig', array(
                'translations' => $translations,
                'translatorHelper' => $this->translatorHelper,
                'translator' => $this->translator,
                'locale' => $currentLocale,
                'form' => $form->createView()
            ));
    }
}
