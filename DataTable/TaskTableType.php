<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 19/01/19
 * Time: 17:06
 */

namespace Umbrella\AdminBundle\DataTable;

use Doctrine\ORM\QueryBuilder;
use Umbrella\CoreBundle\Form\SearchType;
use Umbrella\CoreBundle\Form\Choice2Type;
use Umbrella\CoreBundle\Entity\UmbrellaTask;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                UmbrellaTask::STATE_PENDING,
                UmbrellaTask::STATE_RUNNING,
                UmbrellaTask::STATE_FINISHED,
                UmbrellaTask::STATE_TERMINATED,
                UmbrellaTask::STATE_FAILED
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
            'renderer' => function (UmbrellaTask $entity) {
                return $this->taskHelper->renderState($entity->state);
            }
        ]);

        $builder->add('handlerAlias', PropertyColumnType::class, [
            'label' => 'task_id',
            'renderer' => function (UmbrellaTask $entity) {
                return $entity->getTaskId();
            }
        ]);

        $builder->add('createdAt', DateColumnType::class, [
            'default_order' => 'DESC',
            'format' => 'd/m/Y H:i'
        ]);

        $builder->add('startedAt', PropertyColumnType::class, [
            'label' => 'task_runtime',
            'renderer' => function (UmbrellaTask $entity) {
                return $this->taskHelper->renderRuntime($entity);
            }
        ]);

        $builder->add('progress', PropertyColumnType::class, [
            'label' => 'task_progress',
            'renderer' => function (UmbrellaTask $entity) {
                return $this->taskHelper->renderProgress($entity);
            },
        ]);

        $builder->add('actions', ActionColumnType::class, [
            'action_builder' => function (RowActionBuilder $builder, UmbrellaTask $entity) {
                $builder->createShow('umbrella_admin_task_show', ['id' => $entity->id]);

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

        $builder->addSourceModifier(new EntitySearchModifier());
        $builder->addEntityCallbackSourceModifier(function (QueryBuilder $qb, array $formData) {
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
            'data_class' => UmbrellaTask::class,
            'poll_interval' => 10
        ]);
    }
}
