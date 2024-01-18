<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'required' => false
            ))
            ->add('password', RepeatedType::class, array(
                'required' => false,
                'type' => PasswordType::class,
                'first_options' => array('label' => 'password'),
                'second_options' => array('label' => 'repetir_password'),
            ))
            ->add('nombre')
            ->add('email', TextType::class, array(
                'required' => false
            ))
            ->add('role',ChoiceType::class, array(
                'placeholder' => '',
                'required' => true,
                'choices' => array(
                    'ROLE_USUARIO' => 'ROLE_USUARIO',
                    'ROLE_ADMINISTRADOR' => 'ROLE_ADMINISTRADOR',
                    'ROLE_ECONOMIA' => 'ROLE_ECONOMIA',
                    'ROLE_J.DEPARTAMENTO' => 'ROLE_J.DEPARTAMENTO',
                    'ROLE_J.DEPENDENCIA' => 'ROLE_J.DEPENDENCIA',
                    'ROLE_J.CENTRO' => 'ROLE_J.CENTRO',
                    'ROLE_CONSULTOR' => 'ROLE_CONSULTOR',
                ))
            )
            ->add('estado', CheckboxType::class, array('required' => false))
            ->add('departamento')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
