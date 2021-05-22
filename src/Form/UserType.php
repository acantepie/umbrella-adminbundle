<?php

namespace Umbrella\AdminBundle\Form;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType.
 */
class UserType extends AbstractType
{
    private ParameterBagInterface $parameters;

    /**
     * UserGroupTableType constructor.
     */
    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('active', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('firstname', TextType::class);
        $builder->add('lastname', TextType::class);
        $builder->add('email', EmailType::class);

        $params = [
            'label' => 'password',
            'required' => $options['password_required'],
        ];

        if (!$options['password_required']) {
            $params['attr']['placeholder'] = 'placeholder.password_not_set_if_empty';
        }

        $builder->add('plainPassword', PasswordType::class, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->parameters->get('umbrella_admin.user.class'),
            'password_required' => false,
        ]);
    }
}