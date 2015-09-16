<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class PruCopyType extends AbstractType {
	
	private $nombreTpr;
	
	public function __construct($nombreTpr) {
		$this->nombreTpr = $nombreTpr;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
          ->add('tpr', 'text', array(
                'label' => 'Prueba',
          		 'mapped' => false,
          		 'disabled' => true,
                'data' => $this->nombreTpr))
          ->add('ronda', 'integer', array(
                'label' => 'Ronda',
          		 'disabled' => true))
    	    ->add('idcat', 'choice', array(
    	    		'choices' => array('1'),
    	    		'expanded' => false,
    	    		'disabled' => true,
    	    		'label' => 'Categoría',
    	    		'required' => true))
    	    ->add('nombre', 'text', array(
    	    		'label' => 'Nombre',
    	    		'required' => false));
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