<?php

/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 08/05/17
 * Time: 19:33.
 */

namespace Umbrella\AdminBundle\Form;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Umbrella\CoreBundle\Form\CustomCheckboxType;
use Umbrella\CoreBundle\Form\Entity2Type;
use Umbrella\CoreBundle\Form\UmbrellaFileType;

/**
 * Class UserType.
 */
class UserType extends AbstractType
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('active', CustomCheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('firstname', TextType::class);
        $builder->add('lastname', TextType::class);
        $builder->add('avatar', UmbrellaFileType::class, [
            'file_attr' => [
                'accept' => 'image/*',
            ],
            'file_constraints' => [
                new Image(),
            ],
            'required' => false,
        ]);

        $builder->add('email', EmailType::class);

        $params = [
            'label' => 'password',
            'required' => $options['password_required'],
        ];

        if (!$options['password_required']) {
            $params['attr']['placeholder'] = 'form.placeholder.password_not_set_if_empty';
        }

        $builder->add('plainPassword', PasswordType::class, $params);

        $builder->add('groups', Entity2Type::class, [
            'class' => $this->parameters->get('umbrella_admin.user.group_crud.class'),
            'required' => false,
            'multiple' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->parameters->get('umbrella_admin.user.user_crud.class'),
            'password_required' => false,
        ]);
    }
}
