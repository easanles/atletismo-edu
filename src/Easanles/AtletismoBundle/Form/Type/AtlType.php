<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class AtlType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
        ->add('nombre', 'text', array('label' => 'Nombre*'))
        ->add('apellidos', 'text', array('label' => 'Apellidos*'))
        ->add('nick', 'text', array('label' => 'Nick','required' => false))
        ->add('dni', 'text', array('label' => 'DNI','required' => false))
        ->add('fnac', 'date', array(
        		'label' => 'Fecha de nacimiento*',
        		'widget' => 'single_text',
        		'format' => 'dd/MM/yyyy',
        		'placeholder' => 'dd/mm/aaaa',
        		'invalid_message' => 'Fecha no válida. Formato: dd/mm/aaaa'))
        ->add('sexo', 'choice', array(
        		'label' => 'Sexo',
            'choices' => array(
               false   => 'Hombre',
               true => 'Mujer'
             ),
        		'expanded' => true
        ))
        ->add('tipo', 'text', array('label' => 'Tipo','required' => false))
        ->add('esalta', 'checkbox', array('label' => 'Dado de alta', 'required' => false))
        ->add('direccion', 'text', array('label' => 'Dirección','required' => false))
        ->add('cp', 'text', array('label' => 'Código Postal','required' => false))
        ->add('localidad', 'text', array('label' => 'Localidad','required' => false))
        ->add('provincia', 'text', array('label' => 'Provincia','required' => false))
        ->add('pais', 'text', array('label' => 'País','required' => false))
        ->add('nacion', 'country', array('label' => 'Nacionalidad','preferred_choices' => array('ES'), 'required' => false))
        ->add('lfga', 'text', array('label' => 'Licencia FGA','required' => false))
        ->add('lxogade', 'text', array('label' => 'Licencia XOGADE','required' => false))
        ->add('fotoFile', 'vich_image', array(
        		'label' => 'Foto de atleta',
        		'required' => false,
        		'allow_delete'  => true,
        		'download_link' => false,
        ))
        ->add('notas', 'textarea', array('label' => 'Notas','required' => false))
        ->add('email', 'email', array('label' => 'Email de contacto','required' => false))
        ->add('url1', 'url', array('label' => 'Dirección url 1','required' => false))
        ->add('url2', 'url', array('label' => 'Dirección url 2','required' => false))
        ->add('usu_nombre', 'hidden', array(
        		'mapped' => false,
        		'required' => false
        ))
        ->add('usu_contra', 'hidden', array(
        		'mapped' => false,
        		'required' => false
        ))
        ->add('usu_rol', 'hidden', array(
        		'mapped' => false,
        		'required' => false
        ))
        ->add('warn_dni', 'hidden', array(
        		'required' => false
        ))
        ->add('warn_nick', 'hidden', array(
        		'required' => false
        ));
    }
 
    public function getName() {
        return 'atl';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver->setDefaults(array(
    			'data_class' => 'Easanles\AtletismoBundle\Entity\Atleta',
    	));
    }
}