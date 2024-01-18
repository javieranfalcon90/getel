<?php

namespace App\Form;

use App\Entity\Identificador;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class IdentificadorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero')
            ->add('tipo',ChoiceType::class, array(
                'placeholder' => '',
                'required' => true,
                'choices' => array(
                    'Cod' => 'Cod',
                    'Ext' => 'Ext',
                ))
            )
            ->add('responsable')
            ->add('departamento')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Identificador::class,
        ]);
    }
}
