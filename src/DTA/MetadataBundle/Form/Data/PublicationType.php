<?php

namespace DTA\MetadataBundle\Form\Data;

use Propel\PropelBundle\Form\BaseAbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use DTA\MetadataBundle\Form\DerivedType\DynamicCollectionType;
use DTA\MetadataBundle\Form\DerivedType\SelectOrAddType;

use DTA\MetadataBundle\Form\Master;
use DTA\MetadataBundle\Form\Data;
use DTA\MetadataBundle\Form\Workflow;
use DTA\MetadataBundle\Form\Classification;

class PublicationType extends BaseAbstractType
{
    protected $options = array(
        'data_class' => 'DTA\MetadataBundle\Model\Data\Publication',
        'name'       => 'publication',
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('work', new WorkType());

//        $builder->add('personPublications', new DynamicCollectionType(), array(
//            'type' => new PersonPublicationType(),
//            'allow_add' => true,
//            'allow_delete' => true,
//            'by_reference' => false,
//            'inlineLabel' => false,
//            'sortable' => false,
//            'label' => 'Publikationsbezogene Personalia',
//        ));
//        
//        $builder->add('place', new SelectOrAddType(), array(
//            'class' => 'DTA\MetadataBundle\Model\Data\Place',
//            'property' => 'Name',
//            'label' => 'Druckort',
//            'searchable' => true,
//            
//        ));
//
        $builder->add('PersonPublications', new DynamicCollectionType(), array(
            'type' => new Master\PersonPublicationType(),
            'inlineLabel' => false,
            'sortable' => false,
            'label' => 'Publikationsbezogene Personalia',
            'options' => array('isPublicationSelectable'=>false),  // the work is implied by the context (the work that is currently edited)
        ));
        
        $builder->add('DatespecificationRelatedByPublicationdateId', new Data\DatespecificationType(), array(
            'label' => 'Erscheinungsjahr (f.a.)'
        ));
        
        $builder->add('DatespecificationRelatedByFirstpublicationdateId', new Data\DatespecificationType(), array(
            'label' => 'Erscheinungsjahr der Erstausgabe (f.a.)'
        ));
//        
        $builder->add('editiondescription', 'text', array(
            'required' => false
        ));
//        $builder->add('editionNumerical', null, array(
//            'label' => 'Edition (numerisch)',
//        ));
        
        $builder->add('publishingcompany', new SelectOrAddType(), array(
            'class' => 'DTA\MetadataBundle\Model\Data\Publishingcompany',
            'property' => 'Name',
            'label' => 'Verlag'
        ));
            
        $builder->add('ImageSources', new DynamicCollectionType(), array(
            'type' => new Workflow\ImagesourceType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'inlineLabel' => true,
            'sortable' => false,
            'label' => 'Bildquellen'
        ));
        
    }
}