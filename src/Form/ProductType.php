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
use Symfony\Component\Validator\Constraints\NotNull;

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
            ->add('imageFile', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false,
                'required' => $options['image_required'],
                'constraints' => $this->imageConstraints($options['image_required']),
                'attr' => ['accept' => 'image/*'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'image_required' => false,
        ]);

        $resolver->setAllowedTypes('image_required', 'bool');
    }

    /**
     * Contraintes du champ image : format et taille toujours vérifiés,
     * présence obligatoire seulement si demandé (à la création).
     */
    private function imageConstraints(bool $required): array
    {
        $constraints = [
            new File([
                'maxSize' => '2M',
                'mimeTypes' => ['image/jpeg', 'image/png'],
                'mimeTypesMessage' => 'Seuls les formats JPEG et PNG sont autorisés.',
            ]),
        ];

        if ($required) {
            $constraints[] = new NotNull(message: 'Veuillez ajouter une image du produit.');
        }

        return $constraints;
    }
}
