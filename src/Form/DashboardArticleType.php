<?php

namespace App\Form;

use App\Entity\Competition;
use App\Entity\DashboardArticle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DashboardArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'article',
            ])
            ->add('description', TextType::class, [
                'label' => 'Texte de l\'article',
            ])
            ->add('image', FileType::class, [
                'label' => 'Image lié à l\'article',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DashboardArticle::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'dashboard_article';
    }
}