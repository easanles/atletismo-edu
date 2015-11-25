<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;
 
class InsTypeGroup extends AbstractType{
	
	 private $inscripciones;
	
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('inscripciones', 'collection', array('cascade_validation' => true));
        $count = 0;
        foreach($this->inscripciones as $ins){
           $builder->get('inscripciones')->add('ins_'.$count++, new InsType($ins));           
        }
    }
    
    public function __construct($listaIns) {
    	 $this->inscripciones = $listaIns;
    }
 
    public function getName(){
        return 'insGroup';
    }
    
}