<?php

namespace Knp\Bundle\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SearchType
 */
class SearchType extends AbstractType
{
    /**
     * buildForm
     *
     * @param FormBuilderInterface $builder builder
     * @param array                $options options
     *
     * @return null
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', 'choice', array(
                    'label'              => 'translator.form.locale.label',
                    'translation_domain' => 'translator',
                    'choices'            => array_combine($options['locale'], $options['locale'])
                ))
            ->add('domain', 'choice', array(
                    'label'              => 'translator.form.domain.label',
                    'translation_domain' => 'translator',
                    'choices'            => array_combine($options['domain'], $options['domain'])
                ))
            ->add('key', 'text', array(
                    'label'              => 'translator.form.key.label',
                    'translation_domain' => 'translator',
                    'required'           => false
                ));
    }

    /**
     * setDefaultOptions
     *
     * @param OptionsResolverInterface $resolver resolver
     *
     * @return null
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'domain'    => array(),
                'locale'    => array()
            )
        );
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return 'knplabs_translator_search_type';
    }
}