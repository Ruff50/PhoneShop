<?php

namespace App\Form;

use App\Entity\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Saisissez le nom du produit']

            ])
            ->add('designation', TextareaType::class, [
                'label' => 'Description du produit',
                'attr' => ['placeholder' => 'Saisissez la description du produit']

            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix du produit',
                'attr' => ['placeholder' => 'Saisissez le prix du produit en €']

            ])
            ->add('tva', ChoiceType::class, [
                'label' => 'TVA du produit',
                'placeholder' => '-- Choisir une TVA --',
                'choices' => [
                    '0%' => 0,
                    '2.10%' => 2.10,
                    '8.5%' => 8.5,
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du produit',
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité du produit',
                'attr' => ['placeholder' => 'Saisissez la quantité du produit']
            ])

            ->add('category_id', EntityType::class, [
                'label' => 'Catégorie du produit',
                'placeholder' => '--Choisissez une catégorie--',
                'class' => Category::class,
                'choice_label' => 'nom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
