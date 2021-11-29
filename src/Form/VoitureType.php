<?php

namespace App\Form;

use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque', TextType::class)
            ->add('color', ChoiceType::class, [
                'choices' => [
                    "Bleu" => "bleu",
                    "Rouge" => "rouge"
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    "Sport" => "sport",
                    "Famille" => "famille"
                ]
            ])
            ->add('prix')
            ->add('photo', FileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
