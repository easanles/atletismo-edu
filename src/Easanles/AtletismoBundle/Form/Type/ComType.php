<?php 

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class ComType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
    	    ->add('nombre', 'text', array('label' => 'Nombre de la competición'))
    	    ->add('temp', 'integer', array('label' => 'Temporada (año de inicio)'))
    	    ->add('ubicacion', 'text', array('label' => 'Ubicación','required' => false))
    	    ->add('sede', 'text', array('label' => 'Sede','required' => false))
    	    ->add('fecha', 'date', array(
    	    		'label' => 'Fecha de comienzo',
    	    		'widget' => 'single_text',
    	    		'format' => 'dd-MM-yyyy',
    	    		'placeholder' => 'dd/mm/aaaa',
    	    		'invalid_message' => 'Fecha no válida. Formato: dd/mm/aaaa',
    	    		'required' => false))
    	    ->add('desc', 'textarea', array('label' => 'Descripción','required' => false))
    	    ->add('nivel', 'text', array('label' => 'Nivel','required' => false))
    	    ->add('feder', 'text', array('label' => 'Federación','required' => false))
    	    ->add('web', 'url', array('label' => 'Página web','required' => false))
    	    ->add('email', 'email', array('label' => 'Correo electrónico de contacto','required' => false))
    	    ->add('cartelFile', 'vich_image', array(
    	    		'label' => 'Archivo de imagen del cartel',
    	    		'required' => false,
    	    		'allow_delete'  => true,
    	    		'download_link' => false,
    	    ))
    	    ->add('esfeder', 'checkbox', array('label' => 'Competición federada','required' => false))
    	    ->add('esoficial', 'checkbox', array('label' => 'Competición oficial del club','required' => false));
    }
 
    public function getName() {
        return 'com';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Competicion',
    	));
    }
}