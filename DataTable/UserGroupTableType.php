<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 21/05/17
 * Time: 17:07.
 */

namespace Umbrella\AdminBundle\DataTable;

use Umbrella\CoreBundle\Form\SearchType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Umbrella\CoreBundle\Component\Toolbar\ToolbarBuilder;
use Umbrella\CoreBundle\Component\Action\Type\AddActionType;
use Umbrella\CoreBundle\Component\Column\Type\ManyColumnType;
use Umbrella\CoreBundle\Component\DataTable\DataTableBuilder;
use Umbrella\CoreBundle\Component\Column\Type\ActionColumnType;
use Umbrella\CoreBundle\Component\DataTable\Type\DataTableType;
use Umbrella\CoreBundle\Component\Column\Type\PropertyColumnType;
use Umbrella\CoreBundle\Component\DataTable\RowAction\RowActionBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Umbrella\CoreBundle\Component\DataTable\Source\Modifier\EntitySearchModifier;

/**
 * Class UserGroupTableType.
 */
class UserGroupTableType extends DataTableType
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
     * @inheritdoc
     */
    public function buildToolbar(ToolbarBuilder $builder, array $options = [])
    {
        $builder->addFilter('search', SearchType::class);
        $builder->addAction('add', AddActionType::class, [
            'route' => 'umbrella_admin_usergroup_edit',
            'label' => 'add_group',
            'xhr' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildTable(DataTableBuilder $builder, array $options = [])
    {
        $builder->add('title', PropertyColumnType::class);
        $builder->add('roles', ManyColumnType::class);

        $builder->add('actions', ActionColumnType::class, [
            'action_builder' => function (RowActionBuilder $builder, $entity) {
                $builder->createXhrEdit('umbrella_admin_usergroup_edit', ['id' => $entity->id]);
                $builder->createXhrDelete('umbrella_admin_usergroup_delete', ['id' => $entity->id]);
            }
        ]);

        $builder->addSourceModifier(new EntitySearchModifier());
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
