<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class TprfType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
    	    ->add('nombre', 'text', array('label' => 'Nombre de la prueba'))
          ->add('unidades', 'choice', array(
        		'label' => 'Unidades',
            'choices' => array(
               'segundos' => 'Segundos',
               'metros' => 'Metros',
            	'puntosdesc' => 'Puntos (de más a menos)',
            	'puntosasc' => 'Puntos (de menos a más)'
            ),
        		'expanded' => false
          ))
     	    ->add('numint', 'integer', array('label' => 'Intentos por prueba'))
          ->add('modalidades', 'collection', array('type' => new TprmType(),
          		                                    'allow_add' => true,
          		                                    'allow_delete' => true,
          		                                    'by_reference' => false,
          		                                    'cascade_validation' => true));
    }
 
    public function getName(){
        return 'tprf';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver){
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\TipoPruebaFormato',
    			'cascade_validation' => true
    	));
    }
}