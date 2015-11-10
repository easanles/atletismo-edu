<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class ParType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
    	    ->add('dorsal', 'text', array(
    	    		 'label' => 'Dorsal recibido', 'required' => false))
    	    ->add('asisten', 'checkbox', array('label' => 'Asistió al evento'));
    }
 
    public function getName(){
        return 'par';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver){
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Participacion',
    	));
    }
}