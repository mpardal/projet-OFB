<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\ExhibitorGroup;
use App\Validator\UniqueEmail;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;


class ExhibitorGroupByExhibitorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('groupName', TextType::class, [
                'label' => 'Nom du stand',
                'disabled' => true,
            ])
            ->add('website', TextType::class, [
                'label' => 'Site internet',
                'required' => false,
            ])
            ->add('emailContact', EmailType::class, [
                'label' => 'Email de contact',
                'constraints' => [
                    new NotBlank(message: 'L\'email est obligatoire.'),
                    new Email(message: 'Veuillez fournir un email valide.'),
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Présentation de votre stand',
            ])
            ->add('video', FileType::class, [
                'label' => 'Ajouter une vidéo (si vous le souhaitez)',
                'required' => false,
                'mapped' => false,
            ])
            ->add('images', FileType::class, [
                'label' => 'Insérer une image',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])
            ->add('event', EntityType::class, [
                'label' => 'Événement',
                'class' => Event::class,
                'choice_label' => 'title', // Assuming 'title' is a field in the Event entity
                'placeholder' => 'Sélectionnez un événement',
                'disabled' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExhibitorGroup::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'exhibitor_group_by_exhibitor';
    }
}