<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Movies;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('movieTitle', TextType::class, [
                'mapped' => false,
                'label' => 'Titre du film',
                'required' => true,
            ])
            ->add('movieId', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('descriptionProduct', null, [
                'label' => 'Description',
            ])
            ->add('priceProduct', NumberType::class, [
                'label' => 'Prix (en €)',
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'step' => '0.01',
                    'min' => 0,
                ],
            ])
            ->add('slug', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Seuls les formats JPEG et PNG sont autorisés.',
                    ])
                ],
                'attr' => ['accept' => 'image/*'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class, // ou Produit::class
            'autocomplete_url' => null,
        ]);
    }
}
