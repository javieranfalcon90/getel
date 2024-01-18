<?php

namespace App\Form;

use App\Entity\Tarifa;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TarifaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('desdediurno')
            ->add('hastadiurno')
            ->add('tarifadiurno')
            ->add('desdenocturno')
            ->add('hastanocturno')
            ->add('tarifanocturno')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tarifa::class,
        ]);
    }
}
