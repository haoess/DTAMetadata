<?php

namespace DTA\MetadataBundle\Form\Workflow;

use Propel\PropelBundle\Form\BaseAbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends BaseAbstractType
{
    protected $options = array(
        'data_class' => 'DTA\MetadataBundle\Model\Workflow\Task',
        'name'       => 'task',
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tasktype', new \DTA\MetadataBundle\Form\DerivedType\SelectOrAddType(), array(
            'class' => '\DTA\MetadataBundle\Model\Workflow\Tasktype',
            'property' => 'name',
        ));
        $builder->add('done', null, array(
            'label' => 'erledigt',
            'attr' => array('expanded'=>true),
        ));
        $builder->add('start', null, array('years'=>array('2013'),'widget' => 'choice'));
        $builder->add('end');
        $builder->add('comments');
        $builder->add('User', 'model', array(
            'property' => 'username',
            'class' => 'DTA\MetadataBundle\Model\Workflow\User',
            'label' => 'verantwortlich'
        ));
    }
}
