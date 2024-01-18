<?php

namespace App\Form;

use App\Entity\Dependencia;
use App\Entity\Departamento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DependenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('centro')
            ->add('departamentos', EntityType::class, [
                'class' => Departamento::class,
                'multiple' => true,
                'by_reference' => false,
                'required'=> false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dependencia::class,
        ]);
    }
}
