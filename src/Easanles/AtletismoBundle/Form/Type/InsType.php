<?php 

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
    	    		 'choices'=> array(
    	    		    'Pendiente' => 'Pendiente',
    	    			 'Pagado' => 'Pagado'
    	    		  ), 
    	    		 'required' => false))
    	    ->add('eliminar', 'checkbox', array('label' => "Eliminar inscripción", 'mapped' => false));
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