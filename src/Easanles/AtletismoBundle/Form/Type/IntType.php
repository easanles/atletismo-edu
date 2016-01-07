<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class IntType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
    	    ->add('marca', 'number', array('label' => 'Marca', 'required' => false))
    	    ->add('validez', 'checkbox', array('label' => 'Vál.', 'required' => false))
    	    ->add('premios', 'text', array('label' => 'Premios','required' => false));
    }
 
    public function getName() {
        return 'int';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Intento',
    	));
    }
}