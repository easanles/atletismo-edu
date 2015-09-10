<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
 
class PruType extends AbstractType {
	
	private $listaFormatos;
	
	public function __construct($listaFormatos) {
		$this->listaFormatos = $listaFormatos;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
    	    ->add('sidtprm', 'choice', array(
    	    		'choices' => array(-1 => "Seleccione tipo de prueba"),    	    		 
    	    		'expanded' => false,
    	     		'label' => 'Seleccione modalidad',
    	    		'disabled' => true,
    	    		'required' => true))
    	    ->add('idcat', 'choice', array(
    	    		'choices' => array('1'),
    	    		'expanded' => false,
    	    		'label' => 'Categoría',
    	    		'required' => true))
    	    ->add('nombre', 'text', array(
    	    		'label' => 'Nombre de la primera ronda',
    	    		'required' => false));
    	    
    	  $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
    	    	$form = $event->getForm();
    	    	$choices = array('-1' => " ");
    	    	foreach($this->listaFormatos as $tprf){
    	    		$choices[$tprf['sid']] = $tprf['nombre'];
    	    	}
    	    
            $form->add('tprf', 'choice', array(
               'choices' => $choices,
          		'expanded' => false,
          		'mapped' => false,
    	     		'label' => 'Seleccione tipo de prueba',
               'required' => true));
    	  });

    }
 
    public function getName() {
        return 'pru';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Prueba',
    	));
    }
    
}