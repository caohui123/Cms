<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ObjectClass form field for templates
 *
 * Gives the option to choose one of the available objectclasses for templates.
 */
class TemplateObjectClassType extends AbstractType
{
    protected $entities;

    /**
     * Constructor
     *
     * @param array $entities
     */
    public function __construct(array $entities = array())
    {
        $this->entities = $entities;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = array();
        foreach ($this->entities as $label => $class) {
            $choices[$class] = ucfirst($label);
        }

        $resolver->setDefaults([
            'choices'  => $choices,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'template_object_class';
    }
}