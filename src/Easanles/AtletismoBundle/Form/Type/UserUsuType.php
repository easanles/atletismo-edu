<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
 
class UserUsuType extends AbstractType{
	
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
    	    ->add('contra', 'repeated', array(
    	    		'type' => 'password',
    	    		'invalid_message' => 'Los campos no coinciden',
    	    		'options' => array('attr' => array('class' => 'password-field')),
    	    		'required' => true,
    	    		'mapped' => true,
    	    		'first_options'  => array('label' => 'Cambiar contraseña'),
    	    		'second_options' => array('label' => 'Repetir contraseña')))
    	    ->add('submit', 'submit', array('label' => 'Enviar'));
    }
 
    public function getName() {
        return 'usu';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Usuario',
    	));
    }
    
    
}