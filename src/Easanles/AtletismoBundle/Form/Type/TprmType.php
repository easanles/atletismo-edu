<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class TprmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    	    ->add('sexo', 'choice', array('choices' => array(
    	    		    '2' => 'Ambos',
    	    		    '0' => 'Masculino',
    	    		    '1' => 'Femenino',
    	          ),
    	    		 'expanded' => false,
    	    		 'label' => 'Sexo', 'required' => true))
    	    ->add('entorno', 'text', array('label' => 'Entorno'));
    }
 
    public function getName()
    {
        return 'tprm';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\TipoPruebaModalidad',
    	));
    }
}