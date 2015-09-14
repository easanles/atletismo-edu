<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Psr\Log\NullLogger;
use Doctrine\ORM\EntityRepository;
 
class PruType extends AbstractType {
	
	private $listaFormatos;
	private $repo;
	
	public function __construct($doctrine) {
		$repository = $doctrine->getRepository('EasanlesAtletismoBundle:TipoPruebaFormato');
		$this->listaFormatos = $repository->findAllOrdered();
		$this->repo = $doctrine->getRepository('EasanlesAtletismoBundle:TipoPruebaModalidad');
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options) {
    	  //opciones directamente mapeadas
        $builder
    	    ->add('idcat', 'choice', array(
    	    		'choices' => array('1'),
    	    		'expanded' => false,
    	    		'label' => 'Categoría',
    	    		'required' => true))
    	    ->add('nombre', 'text', array(
    	    		'label' => 'Nombre de la primera ronda',
    	    		'required' => false));

    	  	$formModifier = function (FormInterface $form, $tprf) {
    	  		$modalidades = $tprf === null ? array() : $this->repo->findFor($tprf);
    	  		$choices = array();
    	  		foreach($modalidades as $mod){
    	  			$choices[] = $this->repo->find($mod['sid']);
    	  		}
    	  		$form->add('sidtprm', 'entity', array(
    	  				'class' => 'Easanles\AtletismoBundle\Entity\TipoPruebaModalidad',
    	  			   'choices' => $choices,
    	  				'choice_label' => function ($allChoices, $currentChoiceKey) {
    	  					$nombre = "";
    	  					if ($allChoices->getSexo() == 0) $nombre = "Masculino";
    	  					else if ($allChoices->getSexo() == 1) $nombre = "Femenino";
    	  					return $nombre.", ".$allChoices->getEntorno();
                   },
    	  				'empty_value' => ' ',
    	  				'expanded' => false,
    	  				'disabled' => ($tprf === null),
    	  				'label' => 'Seleccione modalidad',
    	  				'required' => true));
    	  	};
    	  	 
    	  	$builder->addEventListener(
    	  			FormEvents::PRE_SET_DATA,
    	  			function (FormEvent $event) use ($formModifier) {
    	  				$form = $event->getForm();
    	  				
    	  				$choices = array();
    	  				foreach($this->listaFormatos as $tprf){
    	  					$choices[$tprf['sid']] = $tprf['nombre'];
    	  				}
    	  					
    	  				$form->add('tprf', 'choice', array(
    	  						'choices' => $choices,
    	  						'empty_value' => '...',
    	  						'expanded' => false,
    	  						'mapped' => false,
    	  						'label' => 'Seleccione tipo de prueba',
    	  						'required' => true));
    	  				    	  				 
    	  				$formModifier($event->getForm(), $form['tprf']->getData());
    	  			}
    	  	);
    	  	
    	  	$builder->addEventListener(
    	  			FormEvents::PRE_SUBMIT,
    	  			function (FormEvent $event) use ($formModifier) {
    	  				$tprf = $event->getData()['tprf'];
    	  				$formModifier($event->getForm(), $tprf);
    	  			}
    	  	);
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