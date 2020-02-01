<?php

namespace App\Form;

use App\Entity\Currency;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // @note: an important example how to implement an entity reference
            ->add('currency', EntityType::class, [
                'placeholder' => 'Choose an option',
                'class' => Currency::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->getAllOrderingByCode();
                }
            ])
            ->add('name')
            ->add('email')
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
