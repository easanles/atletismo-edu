<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class TprfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    	    ->add('nombre', 'text', array('label' => 'Nombre de la prueba'))
    	    ->add('unidades', 'text', array('label' => 'Unidades'))
    	    ->add('numint', 'integer', array('label' => 'Intentos por prueba'))
          ->add('modalidades', 'collection', array('type' => new TprmType(), 'cascade_validation' => true));
    }
 
    public function getName()
    {
        return 'tprf';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\TipoPruebaFormato',
    			'cascade_validation' => true
    	));
    }
}