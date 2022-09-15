<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PanierConfirmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom complet',
                'attr' => [
                    'placeholder' => 'Nom complet pour la livraison',
                ],
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse complète',
                'attr' => [
                    'placeholder' => 'Adresse complète pour la livraison',
                ],

            ])
            ->add('postcode', TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Code postal pour la livraison',
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ville pour la livraison',
                ],
            ])
            ->add('total')
            ->add('date')
            ->add('status')
            ->add('user');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
