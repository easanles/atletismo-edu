<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class ComType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    	    ->add('nombre', 'text')
    	    ->add('temp', 'integer')
    	    ->add('ubicacion', 'text')
    	    ->add('sede', 'text')
    	    ->add('fecha', 'date')
    	    ->add('desc', 'textarea')
    	    ->add('nivel', 'text')
    	    ->add('feder', 'text')
    	    ->add('web', 'url')
    	    ->add('email', 'email')
    	    ->add('cartel', 'file')
    	    ->add('esfeder', 'checkbox')
    	    ->add('esoficial', 'checkbox');
    }
 
    public function getName()
    {
        return 'com';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Competicion',
    	));
    }
}