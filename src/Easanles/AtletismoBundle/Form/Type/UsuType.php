<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
 
class UsuType extends AbstractType{
	
	 private $mode = "";
	
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
    	    ->add('nombre', 'text', array('label' => 'Nombre de usuario'))
    	    ->add('rol', 'choice', array(
        		'label' => 'Rol',
            'choices' => array(
               'socio' => 'Socio',
               'coordinador' => 'Coordinador (control total)'
             ),
        		'expanded' => true))
    	    ->add('idAtl', 'text', array(
    	  				 'label' => 'ID atleta asociado',
    	    		    'mapped' => false,
    	  				 'required' => false));
    	  if ($this->mode === "new"){
    	  	  $builder->add('contra', 'repeated', array(
             'type' => 'password',
             'invalid_message' => 'Los campos no coinciden',
             'options' => array('attr' => array('class' => 'password-field')),
             'required' => true,
             'first_options'  => array('label' => 'Contraseña'),
             'second_options' => array('label' => 'Repetir contraseña')));
    	  } else if ($this->mode === "edit"){
    	  	  $builder->add('contra', 'repeated', array(
    	  			'type' => 'password',
    	  			'invalid_message' => 'Los campos no coinciden',
    	  			'options' => array('attr' => array('class' => 'password-field')),
    	  			'required' => false,
    	  	  		'mapped' => false,
    	  			'first_options'  => array('label' => 'Cambiar contraseña'),
    	  			'second_options' => array('label' => 'Repetir contraseña')));
    	  }
    }
    
    public function __construct($mode) {
    	 $this->mode = $mode;
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