<?php

namespace DTA\MetadataBundle\Form\Data;

use Propel\PropelBundle\Form\BaseAbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NamefragmentType extends BaseAbstractType {

    protected $options = array(
        'data_class' => 'DTA\MetadataBundle\Model\Data\Namefragment',
        'name' => 'namefragment',
    );

    /**
     *  {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('namefragmenttype', 'model', array(
            'property' => 'Name',
            'class' => 'DTA\MetadataBundle\Model\Classification\Namefragmenttype',
        ));
        $builder->add('name', 'text');
        $builder->add('sortableRank', 'hidden');     
    }

}