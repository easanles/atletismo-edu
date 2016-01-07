<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;
 
class IntTypeGroup extends AbstractType{
	
	 private $intentos;
	
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('intentos', 'collection', array(
        		'type' => new IntType(),
        		'allow_add' => true,
        		'allow_delete' => true,
        		'mapped' => false,
        		'cascade_validation' => true,
        		'data' => $this->intentos
        ));
    }
    
    public function __construct($listaInt) {
    	 $this->intentos = $listaInt;
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