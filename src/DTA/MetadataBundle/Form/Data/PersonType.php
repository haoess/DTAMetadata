<?php

namespace DTA\MetadataBundle\Form\Data;

use Propel\PropelBundle\Form\BaseAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use \DTA\MetadataBundle\Form;

class PersonType extends BaseAbstractType {

    protected $options = array(
        'data_class' => 'DTA\MetadataBundle\Model\Data\Person',
        'name' => 'person',
    );

    /**
     *  {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('personalNames', new Form\DerivedType\DynamicCollectionType(), array(
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'type' => new PersonalnameType(),
            'label' => 'Namen',
        ));
        $builder->add('gnd', new GndType());
    }

}
