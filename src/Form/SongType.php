<?php

// src/Form/SongType.php
namespace App\Form;

use App\Entity\Song;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de la chanson',
                'attr' => [
                    'placeholder' => 'Entrez le titre de la chanson'
                ]
            ])
            ->add('artiste', TextType::class, [
                'label' => 'Artiste',
                'attr' => [
                    'placeholder' => 'Nom de l\'artiste ou du groupe'
                ]
            ])
            ->add('lienYoutube', UrlType::class, [
                'label' => 'Lien YouTube',
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://www.youtube.com/watch?v=...'
                ]
            ])
            ->add('humeurPrincipale', TextType::class, [
                'label' => 'Humeur principale',
                'attr' => [
                    'placeholder' => 'Ex: Joyeux, Triste, Energique...'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
        ]);
    }
}