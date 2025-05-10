<?php

namespace App\Form;

use App\Entity\Playlist;
use App\Entity\Song;
use App\Entity\PlaylistSong;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaylistSongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'nom',
                'label' => 'Playlist',
                'attr' => ['class' => 'form-select']
            ])
            ->add('song', EntityType::class, [
                'class' => Song::class,
                'choice_label' => function(Song $song) {
                    return $song->getTitre() . ' - ' . $song->getArtiste();
                },
                'label' => 'Song',
                'attr' => ['class' => 'form-select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlaylistSong::class,
        ]);
    }
}