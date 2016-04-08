<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class IntType extends AbstractType{
	 private $unidades;
	
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
    	    ->add('validez', 'checkbox', array('label' => 'Vál.', 'required' => false))
    	    ->add('premios', 'text', array('label' => 'Premios','required' => false));
        if ($this->unidades == "segundos"){
        	  $builder->add('marca', 'hidden', array('required' => false));
        } else {
           $builder->add('marca', 'number', array(
           		'invalid_message' => "Este valor debe ser un número entero o con decimales",
           		'label' => 'Marca',
           		'required' => false));
        }
    }
    
    public function __construct($unidades) {
    	$this->unidades = $unidades;
    }
    
 
    public function getName() {
        return 'int_';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Intento',
    	));
    }
}