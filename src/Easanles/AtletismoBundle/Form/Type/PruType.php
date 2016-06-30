<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

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
	private $selectedTpr;
	private $repoCat;
	
	public function __construct($doctrine, $selectedTpr) {
		$repository = $doctrine->getRepository('EasanlesAtletismoBundle:TipoPruebaFormato');
		$this->listaFormatos = $repository->findAllOrdered();
		$this->repo = $doctrine->getRepository('EasanlesAtletismoBundle:TipoPruebaModalidad');
		$this->selectedTpr = $selectedTpr;
		$this->repoCat = $doctrine->getRepository('EasanlesAtletismoBundle:Categoria');
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options) {  	
    	   $builder->add('coste', 'money', array('label' => 'Precio estándar en euros', 'currency' => 'EUR', 'required' => true))
    	   ->add('rondas', 'collection', array('type' => new RonType(),
          		                                    'allow_add' => true,
          		                                    'allow_delete' => true,
          		                                    'by_reference' => false,
          		                                    'cascade_validation' => true));
    	   
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
    	  					if ($allChoices->getSexo() == 0) $nombre = "Masculino, ";
    	  					else if ($allChoices->getSexo() == 1) $nombre = "Femenino, ";
    	  					return $nombre.$allChoices->getEntorno();
                   },
    	  				'empty_value' => ' ',
    	  				'expanded' => false,
    	  				'disabled' => ($tprf == null),
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
    	  				$options = array(
    	  						'choices' => $choices,
    	  						'empty_value' => '',
    	  						'expanded' => false,
    	  						'mapped' => false,
    	  						'label' => 'Seleccione tipo de prueba',
    	  						'error_mapping' => false,
    	  						'required' => true);
    	  				if ($this->selectedTpr != null){
    	  				   $options['data'] = $this->selectedTpr->getSidTprf()->getSid();
    	  				}
    	  				$form->add('tprf', 'choice', $options);
    	  				
    	  				$choices = array();
    	  				$choices[] = $this->repoCat->findOneBy(array("esTodos" => true));
    	  				$listaCategorias = $this->repoCat->findAllCurrent();
    	  				foreach($listaCategorias as $cat){
    	  					$choices[] = $this->repoCat->find($cat['id']);
    	  				}
    	  		      $form->add('idCat', 'entity', array(
    	  				   'class' => 'Easanles\AtletismoBundle\Entity\Categoria',
    	  			      'choices' => $choices,
    	  				   'choice_label' => function ($allChoices, $currentChoiceKey) {
    	  				   	$choice_label = $allChoices->getNombre();
    	  				   	if ($allChoices->getEsTodos() == false)
    	  				   		   $choice_label = $choice_label." (".$allChoices->getEdadMax().")";
    	  					   return $choice_label;
                      },
    	  				   'empty_value' => '',
    	  				   'expanded' => false,
    	  				   'label' => 'Categoría',
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
    			'cascade_validation' => true
    	));
    }
    
}