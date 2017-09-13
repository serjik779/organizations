<?php

namespace AppBundle\Form;

use AppBundle\Validator\Constraints\UserNumeric;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lastname', TextType::class, array(
            'label' => 'lastname'
            ))
            ->add('firstname', TextType::class, array(
                'label' => 'firstname'
            ))
            ->add('middlename', TextType::class, array(
                'label' => 'middlename'
            ))
            ->add('birth', DateType::class, array(
                'label' => 'birth',
                'widget' => 'single_text',
            ))
            ->add('tin', TextType::class, array(
                'label' => 'TIN',
                'constraints' => new UserNumeric(array(
                    'count' => 16
                ))
            ))
            ->add('snils',TextType::class, array(
                'label' => 'SNILS',
                'constraints' => new UserNumeric(array(
                    'count' => 13
                ))
            ))
            ->add('organization', EntityType::class, array(
                'class' => 'AppBundle:Organizations',
                'label' => 'organizations'
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Users',
            'translation_domain' => 'messages'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_users';
    }


}
