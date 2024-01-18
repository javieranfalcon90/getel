<?php

namespace App\Form;

use App\Entity\Llamada;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LlamadaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha')
            ->add('tronco')
            ->add('telefono')
            ->add('duracion')
            ->add('costo')
            ->add('localidad')
            ->add('identificador')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Llamada::class,
        ]);
    }
}
