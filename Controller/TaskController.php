<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 19/01/19
 * Time: 17:05
 */

namespace Umbrella\AdminBundle\Controller;

use Umbrella\CoreBundle\Entity\UmbrellaTask;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Umbrella\AdminBundle\DataTable\TaskTableType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Umbrella\CoreBundle\Controller\BaseController;
use Umbrella\CoreBundle\Component\Task\TaskManager;

/**
 * Class TaskController
 * @Route("/task")
 */
class TaskController extends BaseController
{
    /**
     * @Route(path="")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $table = $this->createTable(TaskTableType::class);
        $table->handleRequest($request);

        if ($table->isCallback()) {
            return new JsonResponse($table->getApiResults());
        }

        return $this->render('@UmbrellaAdmin/DataTable/index.html.twig', [
            'table' => $table
        ]);
    }

    /**
     * @Route(path="/show/{id}", requirements={"id": "\d+"})
     *
     * @param Request $request
     * @param $id
     * @return AppMessageBuilder
     */
    public function showAction(Request $request, $id)
    {
        $entity = $this->findOrNotFound(UmbrellaTask::class, $id);
        return $this->jsResponseBuilder()
            ->openModalView('@UmbrellaAdmin/Task/show.html.twig', [
                'entity' => $entity
            ]);
    }

    /**
     * @Route(path="/cancel/{id}", requirements={"id": "\d+"})
     *
     * @param Request $request
     * @param $id
     * @return AppMessageBuilder
     */
    public function cancelAction(TaskManager  $taskManager, Request $request, $id)
    {
        $entity = $this->findOrNotFound(UmbrellaTask::class, $id);
        $taskManager->cancel($entity);

        return $this->jsResponseBuilder()
            ->toastSuccess('message.task_canceled')
            ->reloadTable();
    }
}
