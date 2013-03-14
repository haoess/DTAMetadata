<?php

namespace DTA\MetadataBundle\Form\DerivedType;

use Symfony\Bridge\Propel1\Form\ChoiceList\ModelChoiceList;
use Symfony\Bridge\Propel1\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Using this type in a formbuilder allows selection of an existing entity or creation of a new one. 
 * The gui element (button) for creating a new entity is added via form theming (see dtaFormExtensions.html.twig / selectOrAdd_widget).
 * The javascript is located in selectOrAdd.js.
 * 
 * Usage: specify the class and property e.g.
 *      'class' => 'DTA\MetadataBundle\Model\Place',
 *      'property' => 'Name',
 * The class determines which basic formtype will be used in the selectbox
 * The property determines which attribute will be used to summarize each entity in a select option
 * @author carlwitt
 */

class SelectOrAddType extends \Symfony\Bridge\Propel1\Form\Type\ModelType {
    
    public function getName() {
        return 'selectOrAdd';
    }
    
    /*
     * Passes the class name of the model to the view and the property that has been used to 
     * display the option of the select element. These two properties are used in the 
     * forms generated by the view.
     * modelClass: which route to chose to request a nested form and to update the database
     * captionProperty: which property to use to generate the newly created option.
     */
    public function finishView(FormView $view, FormInterface $form, array $options){
        
        // fully qualified class name (e.g. DTA\MetadataBundle\Model\Status)
        $className = $options['class'];
        
        // extract the class name (e.g. Status)
        $parts = explode('\\', $className);
        $modelClass = array_pop($parts);
        
        $view->vars['modelClass'] = $modelClass;
        $view->vars['captionProperty'] = $options['property'];
        $view->vars['searchable'] = $options['searchable'];
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'searchable' => false,   // whether to apply the select2 plugin to add typeahead functionality
//            'captionProperty' => 'Id', // getId is available on all propel objects.
        ));
    }
}

?>
