<?php

/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 21/05/17
 * Time: 16:44.
 */

namespace Umbrella\AdminBundle\DataTable;

use Umbrella\CoreBundle\Form\SearchType;
use Umbrella\AdminBundle\Entity\BaseUser;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Umbrella\CoreBundle\Component\Toolbar\ToolbarBuilder;
use Umbrella\CoreBundle\Component\Action\Type\AddActionType;
use Umbrella\CoreBundle\Component\Column\Type\DateColumnType;
use Umbrella\CoreBundle\Component\Column\Type\ManyColumnType;
use Umbrella\CoreBundle\Component\DataTable\DataTableBuilder;
use Umbrella\CoreBundle\Component\Column\Type\ActionColumnType;
use Umbrella\CoreBundle\Component\Column\Type\ToggleColumnType;
use Umbrella\CoreBundle\Component\DataTable\Type\DataTableType;
use Umbrella\CoreBundle\Component\Column\Type\PropertyColumnType;
use Umbrella\CoreBundle\Component\DataTable\RowAction\RowActionBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Umbrella\CoreBundle\Component\DataTable\Source\Modifier\EntitySearchModifier;

/**
 * Class UserTableType.
 */
class UserTableType extends DataTableType
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var ParameterBagInterface
     */
    private $parameters;

    /**
     * UserTableType constructor.
     * @param CacheManager          $cacheManager
     * @param ParameterBagInterface $parameters
     */
    public function __construct(CacheManager $cacheManager, ParameterBagInterface $parameters)
    {
        $this->cacheManager = $cacheManager;
        $this->parameters = $parameters;
    }

    /**
     * @inheritdoc
     */
    public function buildToolbar(ToolbarBuilder $builder, array $options = [])
    {
        $builder->addFilter('search', SearchType::class);
        $builder->addAction('add', AddActionType::class, [
            'route' => 'umbrella_admin_user_edit',
            'label' => 'add_user',
            'xhr' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildTable(DataTableBuilder $builder, array $options = [])
    {
        $builder->add('name', PropertyColumnType::class, [
            'order' => 'ASC',
            'order_by' => ['firstname', 'lastname'],
            'renderer' => function (BaseUser $user) {
                $html = '<div class="d-flex">';
                if ($user->avatar) {
                    $url = $this->cacheManager->getBrowserPath($user->avatar->getWebPath(), 'ub_avatar_sm');
                    $html .= sprintf('<img src="%s" class="avatar-sm rounded-circle mr-2">', $url);
                } else {
                    $html .= '<div class="avatar-sm avatar-icon rounded-circle mr-2"><i class="uil-user font-20"></i></div>';
                }
                $html .= '<div>';
                $html .= sprintf('<div>%s</div>', $user->getFullName());
                $html .= sprintf('<div class="text-muted">%s</div>', $user->email);
                $html .= '</div>';
                $html .= '</div>';
                return $html;
            }
        ]);

        $builder->add('createdAt', DateColumnType::class);

        $builder->add('groups', ManyColumnType::class, [
            'one_path' => 'title',
        ]);

        $builder->add('active', ToggleColumnType::class, [
            'route' => 'umbrella_admin_user_toggleactive',
            'route_params' => function ($entity) {
                return ['id' => $entity->id];
            }
        ]);

        $builder->add('actions', ActionColumnType::class, [
            'action_builder' => function (RowActionBuilder $builder, $entity) {
                $builder->createXhrEdit('umbrella_admin_user_edit', ['id' => $entity->id]);
                $builder->createXhrDelete('umbrella_admin_user_delete', ['id' => $entity->id]);
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
            'data_class' => $this->parameters->get('umbrella_admin.user.user_crud.class')
        ]);
    }
}
