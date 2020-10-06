<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 19/01/19
 * Time: 17:06
 */

namespace Umbrella\AdminBundle\DataTable;

use Doctrine\ORM\QueryBuilder;
use Umbrella\CoreBundle\Entity\Task;
use Umbrella\CoreBundle\Form\SearchType;
use Umbrella\CoreBundle\Form\Choice2Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Umbrella\CoreBundle\Component\Column\Type\ColumnType;
use Umbrella\CoreBundle\Component\Toolbar\ToolbarBuilder;
use Umbrella\CoreBundle\Component\Task\Extension\TaskHelper;
use Umbrella\CoreBundle\Component\Column\Type\DateColumnType;
use Umbrella\CoreBundle\Component\DataTable\DataTableBuilder;
use Umbrella\CoreBundle\Component\Column\Type\ActionColumnType;
use Umbrella\CoreBundle\Component\DataTable\Type\DataTableType;
use Umbrella\CoreBundle\Component\Column\Type\PropertyColumnType;
use Umbrella\CoreBundle\Component\DataTable\RowAction\RowActionBuilder;
use Umbrella\CoreBundle\Component\DataTable\Source\Modifier\EntitySearchModifier;

/**
 * Class TaskTableType
 */
class TaskTableType extends DataTableType
{
    /**
     * @var TaskHelper
     */
    private $taskHelper;

    /**
     * TaskTableType constructor.
     * @param TaskHelper $taskHelper
     */
    public function __construct(TaskHelper $taskHelper)
    {
        $this->taskHelper = $taskHelper;
    }

    /**
     * @inheritDoc
     */
    public function buildToolbar(ToolbarBuilder $builder, array $options = [])
    {
        $builder->addFilter('search', SearchType::class);
        $builder->addFilter('states', Choice2Type::class, [
            'label' => false,
            'choices' => [
                Task::STATE_PENDING,
                Task::STATE_RUNNING,
                Task::STATE_FINISHED,
                Task::STATE_TERMINATED,
                Task::STATE_FAILED
            ],
            'choice_label' => function ($state) {
                return $state ? $this->taskHelper->getStateLabel($state) : null;
            },
            'multiple' => true,
            'translation_domain' => false,
            'attr' => [
                'class' => 'form-check-horizontal'
            ],
            'placeholder' => 'form.placeholder.states'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function buildTable(DataTableBuilder $builder, array $options = [])
    {
        $builder->add('state', PropertyColumnType::class, [
            'label' => 'task_state',
            'renderer' => function (Task $entity) {
                return $this->taskHelper->renderState($entity->state);
            }
        ]);

        $builder->add('id', PropertyColumnType::class, [
            'label' => 'task_id'
        ]);

        $builder->add('config', ColumnType::class, [
            'renderer' => function (Task $task) {
                $config = $task->config;

                if (null === $config) {
                    return '';
                }

                $html = sprintf('<div>Handler alias : <span class="text-muted">%s</span></div>', $config->handlerAlias);
                if (!empty($config->tag)) {
                    $html .= sprintf('<div>Tag : <span class="text-muted">%s</span></div>', $config->tag);
                }

                return $html;
            }
        ]);

        $builder->add('createdAt', DateColumnType::class, [
            'default_order' => 'DESC',
            'format' => 'd/m/Y H:i'
        ]);

        $builder->add('startedAt', PropertyColumnType::class, [
            'label' => 'task_runtime',
            'renderer' => function (Task $entity) {
                return $this->taskHelper->renderRuntime($entity);
            }
        ]);

        $builder->add('actions', ActionColumnType::class, [
            'action_builder' => function (RowActionBuilder $builder, Task $entity) {
                $builder->createXhrShow('umbrella_admin_task_show', ['id' => $entity->id]);

                if ($entity->canCancel()) {
                    $builder->create()
                        ->setRoute('umbrella_admin_task_cancel')
                        ->addRouteParam('id', $entity->id)
                        ->setIcon('mdi mdi-cancel')
                        ->setConfirm('message.task_cancel_confirm')
                        ->setXhr(true);
                }
            }
        ]);

        $builder->addEntityCallbackSourceModifier(function (QueryBuilder $qb, array $formData) {
            $qb->innerJoin('e.config', 'c');

            if ($formData['search']) {
                $qb->andWhere('LOWER(c.handlerAlias) LIKE :search OR LOWER(c.tag) LIKE :search');
                $qb->setParameter('search', '%' . $formData['search'] . '%');
            }

            if ($formData['states']) {
                $qb->andWhere('e.state IN (:states)');
                $qb->setParameter('states', $formData['states']);
            }
        });
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'poll_interval' => 10
        ]);
    }
}
