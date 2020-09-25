<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 13/05/17
 * Time: 18:23.
 */

namespace Umbrella\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Umbrella\CoreBundle\Form\Choice2Type;
use Umbrella\CoreBundle\Utils\ArrayUtils;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class UserGroupType.
 */
class UserGroupType extends AbstractType
{

    /**
     * @var ParameterBagInterface
     */
    private $parameters;

    /**
     * UserGroupType constructor.
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
        $roles = $this->parameters->get('umbrella_admin.user.roles');

        $builder->add('title', TextType::class);
        $builder->add('roles', Choice2Type::class, [
            'choices' => ArrayUtils::values_as_keys($roles),
            'multiple' => true,
            'choice_prefix' => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->parameters->get('umbrella_admin.user.group_crud.class')
        ]);
    }
}
