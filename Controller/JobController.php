<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 19/01/19
 * Time: 17:05
 */

namespace Umbrella\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Umbrella\AdminBundle\DataTable\JobTableType;
use Umbrella\CoreBundle\Component\Schedule\JobManager;
use Umbrella\CoreBundle\Controller\BaseController;
use Umbrella\CoreBundle\Entity\Job;

/**
 * Class JobController
 *
 * @Route("/job")
 */
class JobController extends BaseController
{
    /**
     * @var JobManager
     */
    private $manager;

    /**
     * JobController constructor.
     *
     * @param JobManager $manager
     */
    public function __construct(JobManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route(path="")
     */
    public function indexAction(Request $request)
    {
        $table = $this->createTable(JobTableType::class);
        $table->handleRequest($request);

        if ($table->isCallback()) {
            return new JsonResponse($table->getApiResults());
        }

        return $this->render('@UmbrellaAdmin/DataTable/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route(path="/show/{id}", requirements={"id": "\d+"})
     */
    public function showAction(Request $request, $id)
    {
        $entity = $this->findOrNotFound(Job::class, $id);

        return $this->jsResponseBuilder()
            ->openModalView('@UmbrellaAdmin/Job/show.html.twig', [
                'entity' => $entity,
            ]);
    }

    /**
     * @Route(path="/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->findOrNotFound(Job::class, $id);

        if (!$entity->isRunning()) {
            $this->manager->deleteJob($entity);
        }

        return $this->jsResponseBuilder()
            ->reloadTable()
            ->toastSuccess('message.entity_deleted');
    }

    /**
     * @Route(path="/delete-all")
     */
    public function deleteAllAction(Request $request)
    {
        $this->manager->deleteJobsNotRunning();

        return $this->jsResponseBuilder()
            ->reloadTable()
            ->toastSuccess('message.entity_deleted');
    }
}
