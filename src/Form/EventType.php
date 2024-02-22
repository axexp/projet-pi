<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Form\Extension\Core\Type\FileType;



class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('type')
            ->add('description')
            ->add('address')
            ->add('datedebut', DateType::class, [
                'widget' => 'single_text', // Afficher en tant qu'entrée texte unique
                'format' => 'yyyy-MM-dd', // Définir le format de date souhaité
            ])
            ->add('datefin', DateType::class, [
                'widget' => 'single_text', // Afficher en tant qu'entrée texte unique
                'format' => 'yyyy-MM-dd', // Définir le format de date souhaité
            ])
            /*->add('image', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'data_class' => null,
            ]);*/
            ->add('nbPlaces')
            ->add('image', FileType::class, [
                'label' => 'Event Image',
                'required' => false,
                'mapped' => false, // Do not map this field to the entity property
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
