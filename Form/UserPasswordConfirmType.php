<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 23/10/17
 * Time: 21:50
 */

namespace Umbrella\AdminBundle\Form;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserPasswordConfirmType
 */
class UserPasswordConfirmType extends AbstractType
{
    /**
     * @var ParameterBagInterface
     */
    private $parameters;

    /**
     * UserGroupTableType constructor.
     *
     * @param ParameterBagInterface $parameters
     */
    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => [
                'attr' => [
                    'class' => 'md-input',
                ],
            ],
            'second_options' => [
                'attr' => [
                    'class' => 'md-input',
                ],
            ],
            'invalid_message' => 'error.password.mismatch',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->parameters->get('umbrella_admin.user.user_crud.class'),
        ]);
    }
}
