<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;
 
class InsTypeGroup extends AbstractType{
	
	 private $inscripciones;
	
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('inscripciones', 'collection', array(
        		'type' => new InsType(),
        		'mapped' => false,
        		'cascade_validation' => true,
        		'data' => $this->inscripciones
        ));
    }
    
    public function __construct($listaIns) {
    	 $this->inscripciones = $listaIns;
    }
 
    public function getName(){
        return 'insGroup';
    }
    
}