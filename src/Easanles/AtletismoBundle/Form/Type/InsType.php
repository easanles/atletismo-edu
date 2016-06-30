<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class InsType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
          ->add('coste', 'money', array('label' => 'Precio', 'currency' => 'EUR', 'required' => true))
    	    ->add('estado', 'choice', array(
    	    		 'label' => 'Estado de pago',
    	    		 'empty_value' => false,
    	    		 'choices'=> array(
    	    		    'Pendiente' => 'Pendiente',
    	    			 'Pagado' => 'Pagado'
    	    		  ), 
    	    		 'required' => false));
    }
 
    public function getName(){
        return 'ins';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver){
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Inscripcion',
    	));
    }
}