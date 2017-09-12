<?php

namespace AppBundle\Form;

use AppBundle\Validator\Constraints\UserNumeric;
use Doctrine\DBAL\Types\BigIntType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, array(
            'label' => 'Наименование'
        ))
            ->add('ogrn', TextType::class, array(
                'label' => 'ОГРН',
                'constraints' => new UserNumeric(array(
                    'count' => 13
                ))
            ))
            ->add('oktmo',NumberType::class, array(
                'label' => 'ОКТМО',
                'constraints' => new UserNumeric(array(
                    'count' => 11
                ))
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Organizations'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_organizations';
    }


}
