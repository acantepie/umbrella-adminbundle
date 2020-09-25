<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 23/10/17
 * Time: 19:02
 */

namespace Umbrella\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Umbrella\CoreBundle\Form\UmbrellaFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class AccountType
 */
class ProfileType extends AbstractType
{

    /**
     * @var ParameterBagInterface
     */
    private $parameters;

    /**
     * UserGroupTableType constructor.
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
        $builder->add('firstname', TextType::class);
        $builder->add('lastname', TextType::class);
        $builder->add('avatar', UmbrellaFileType::class, [
            'file_attr' => [
                'accept' => 'image/*'
            ],
            'file_constraints' => [
                new Image()
            ],
            'required' => false
        ]);
        $builder->add('email', EmailType::class);

        $builder->add('plainPassword', PasswordType::class, [
            'label' => 'password',
            'required' => false,
            'attr' => [
                'placeholder' => 'form.placeholder.password_not_set_if_empty'
            ]
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->parameters->get('umbrella_admin.user.user_crud.class')
        ]);
    }
}
