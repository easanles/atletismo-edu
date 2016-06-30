<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;
 
class IntTypeGroup extends AbstractType{
	
	 private $intentos;
	 private $unidades;
	
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('intentos', 'collection', array(
        		'type' => new IntType($this->unidades),
        		'allow_add' => true,
        		'allow_delete' => true,
        		'mapped' => false,
        		'cascade_validation' => true,
        		'data' => $this->intentos
        ));
    }
    
    public function __construct($listaInt, $unidades) {
    	 $this->intentos = $listaInt;
    	 $this->unidades = $unidades;
    }
 
    public function getName(){
        return 'intGroup';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver){
    	$resolver->setDefaults(array(
    			'cascade_validation' => true
    	));
    }
    
}