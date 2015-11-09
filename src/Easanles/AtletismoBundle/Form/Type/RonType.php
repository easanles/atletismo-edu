<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class RonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    	    ->add('num', 'integer', array(
    	    		 'label' => 'Número',
    	    		 'required' => true))
    	    ->add('nombre', 'text', array('label' => 'Nombre'));
    }
 
    public function getName()
    {
        return 'ron';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Ronda',
    	));
    }
}