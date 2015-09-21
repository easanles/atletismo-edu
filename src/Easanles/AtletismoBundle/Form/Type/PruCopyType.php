<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class PruCopyType extends AbstractType {
	
	private $nombreTpr;
	private $nombreCat;
	
	public function __construct($nombreTpr, $nombreCat) {
		$this->nombreTpr = $nombreTpr;
		$this->nombreCat = $nombreCat;
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
          ->add('idCat', 'text', array(
                'label' => 'Categoría',
          		 'mapped' => false,
          		 'disabled' => true,
                'data' => $this->nombreCat))
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