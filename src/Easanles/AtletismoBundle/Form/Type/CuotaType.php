<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class CuotaType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
    	    ->add('nombre', 'text', array('label' => 'Nombre de la cuota*'))
    	    ->add('temp', 'integer', array('label' => 'Temporada (año de inicio)*'))
    	    ->add('fecha', 'date', array(
    	    		'label' => 'Fecha de inicio (dd/mm/aaaa)',
    	    		'widget' => 'single_text',
    	    		'format' => 'dd/MM/yyyy',
    	    		'placeholder' => 'dd/mm/aaaa',
    	    		'invalid_message' => 'Fecha no válida. Formato: dd/mm/aaaa',
    	    		'required' => false))
    	    ->add('fechaFin', 'date', array(
    	    		'label' => 'Fecha de fin (dd/mm/aaaa)',
    	    		'widget' => 'single_text',
    	    		'format' => 'dd/MM/yyyy',
    	    		'placeholder' => 'dd/mm/aaaa',
    	    		'invalid_message' => 'Fecha no válida. Formato: dd/mm/aaaa',
    	    		'required' => false))
    	    ->add('coste', 'money', array('label' => 'Precio estándar en euros', 'currency' => 'EUR', 'mapped' => false));
    }
 
    public function getName() {
        return 'cuota';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Competicion',
    	));
    }
}