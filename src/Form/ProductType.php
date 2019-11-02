<?php


namespace App\Form;


use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('value', NumberType::class, ['label' => 'Value'])
            ->add('calories', NumberType::class, ['label' => 'Calories'])
            ->add('protein', NumberType::class, ['label' => 'Protein'])
            ->add('carbs', NumberType::class, ['label' => 'Carbs'])
            ->add('fat', NumberType::class, ['label' => 'Fat'])
            ->add('submit', SubmitType::class, ['label' => 'Add']);



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Product::class]);
    }
}