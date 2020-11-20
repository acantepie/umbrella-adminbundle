<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 19/01/19
 * Time: 17:06
 */

namespace Umbrella\AdminBundle\DataTable;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Umbrella\CoreBundle\Component\Action\Type\ActionType;
use Umbrella\CoreBundle\Component\Column\Type\ActionColumnType;
use Umbrella\CoreBundle\Component\Column\Type\DateColumnType;
use Umbrella\CoreBundle\Component\Column\Type\PropertyColumnType;
use Umbrella\CoreBundle\Component\DataTable\DataTableBuilder;
use Umbrella\CoreBundle\Component\DataTable\RowAction\RowActionBuilder;
use Umbrella\CoreBundle\Component\DataTable\Type\DataTableType;
use Umbrella\CoreBundle\Component\Schedule\JobHelper;
use Umbrella\CoreBundle\Component\Toolbar\ToolbarBuilder;
use Umbrella\CoreBundle\Entity\Job;
use Umbrella\CoreBundle\Form\Choice2Type;
use Umbrella\CoreBundle\Form\SearchType;

/**
 * Class JobTableType
 */
class JobTableType extends DataTableType
{
    /**
     * @var JobHelper
     */
    private $jobHelper;

    /**
     * JobTableType constructor.
     *
     * @param JobHelper $jobHelper
     */
    public function __construct(JobHelper $jobHelper)
    {
        $this->jobHelper = $jobHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildToolbar(ToolbarBuilder $builder, array $options = [])
    {
        $builder->addFilter('search', SearchType::class);
        $builder->addFilter('states', Choice2Type::class, [
            'label' => false,
            'choices' => [
                Job::STATE_PENDING,
                Job::STATE_RUNNING,
                Job::STATE_FINISHED,
                Job::STATE_TERMINATED,
                Job::STATE_FAILED,
            ],
            'choice_label' => function ($state) {
                return $state ? $this->jobHelper->getStateLabel($state) : null;
            },
            'multiple' => true,
            'translation_domain' => false,
            'attr' => [
                'class' => 'form-check-horizontal',
            ],
            'placeholder' => 'form.placeholder.states',
        ]);

        $builder->addAction('delete_all', ActionType::class, [
            'route' => 'umbrella_admin_job_deleteall',
            'xhr' => true,
            'confirm' => 'message.delete_all_job',
            'class' => 'btn btn-secondary',
            'icon' => 'mdi mdi-delete',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildTable(DataTableBuilder $builder, array $options = [])
    {
        $builder->add('state', PropertyColumnType::class, [
            'label' => 'job_state',
            'renderer' => function (Job $entity) {
                return $this->jobHelper->renderState($entity->state);
            },
        ]);
        $builder->add('id', PropertyColumnType::class);
        $builder->add('description', PropertyColumnType::class);
        $builder->add('createdAt', DateColumnType::class, [
            'order' => 'DESC',
            'format' => 'd/m/Y H:i',
        ]);

        $builder->add('runtime', PropertyColumnType::class, [
            'label' => 'job_runtime',
            'order' => false,
        ]);

        $builder->add('actions', ActionColumnType::class, [
            'action_builder' => function (RowActionBuilder $builder, Job $entity) {
                $builder->createXhrShow('umbrella_admin_job_show', ['id' => $entity->id]);

                if (!$entity->isRunning()) {
                    $builder->createXhrDelete('umbrella_admin_job_delete', ['id' => $entity->id]);
                }
            },
        ]);

        $builder->addEntityCallbackSourceModifier(function (QueryBuilder $qb, array $formData) {
            if ($formData['search']) {
                $qb->andWhere('LOWER(e.description) LIKE :search');
                $qb->setParameter('search', '%' . $formData['search'] . '%');
            }

            if ($formData['states']) {
                $qb->andWhere('e.state IN (:states)');
                $qb->setParameter('states', $formData['states']);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
            'poll_interval' => 10,
        ]);
    }
}
