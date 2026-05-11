<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'La date est obligatoire.']),
                    new LessThanOrEqual([
                        'value' => new \DateTime('today'),
                        'message' => 'La date ne peut pas être postérieure à aujourd\'hui.'
                    ])
                ]
            ])
            ->add('title', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire.'])
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('videoId', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'L\'identifiant vidéo est obligatoire.'])
                ]
            ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'La playlist est obligatoire.'])
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'expanded' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
