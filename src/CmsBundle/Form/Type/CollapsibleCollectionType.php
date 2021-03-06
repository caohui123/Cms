<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class CollapsibleCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'bootstrap_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'collapsible_collection';
    }
}
