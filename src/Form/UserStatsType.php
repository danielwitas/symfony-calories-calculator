<?php


namespace App\Form;


use App\Entity\UserStats;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserStatsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('calories', NumberType::class, ['label' => 'calories'])
            ->add('protein', NumberType::class, ['label' => 'protein'])
            ->add('carbs', NumberType::class, ['label' => 'carbs'])
            ->add('fat', NumberType::class, ['label' => 'fat'])
            ->add('submit', SubmitType::class, ['label' => 'Set']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => UserStats::class]);
    }
}