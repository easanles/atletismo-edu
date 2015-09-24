<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
 
class CatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
    	    ->add('nombre', 'text', array('label' => 'Nombre'))
    	    ->add('edadMax', 'integer', array('label' => 'Edad máxima', 'required' => false))
    	    ->add('tIniVal', 'integer', array('label' => 'Temporada inicial de validez'));
    }
 
    public function getName() {
        return 'cat';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Categoria',
    	));
    }
    
    
}