<?php

namespace Umbrella\AdminBundle\Controller;

use Umbrella\CoreBundle\Entity\BaseTask;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Umbrella\CoreBundle\Controller\BaseController;
use Umbrella\CoreBundle\Component\Task\TaskManager;
use Umbrella\CoreBundle\Component\DateTime\DateTimeHelper;
use Umbrella\CoreBundle\Component\Task\SearchTaskCriteria;

/**
 * @Route("/notification")
 */
class NotificationController extends BaseController
{
    /**
     * @Route("/list")
     */
    public function listAction(TaskManager  $taskManager, DateTimeHelper $dateTimeHelper, Request $request)
    {
        $criteria = new SearchTaskCriteria();
        $criteria->types = [BaseTask::TYPE_FILEWRITER];
        $criteria->maxResults = 10;
        $criteria->onlyNotifiable = true;
        
        $tasks = $taskManager->search($criteria);

        $data = [];
        foreach ($tasks as $task) {
            $data[] = [
                'label' => $task->fileWriterConfig->outputPrettyFilename,
                'state' => $task->state,
                'date' => $dateTimeHelper->diff($task->createdAt),
                'ended_at' => $task->endedAt ? $dateTimeHelper->diff($task->endedAt) : null,
                'runtime' => $task->runtime(),
                'url' => '#'
            ];
        }
        
        return new JsonResponse($data);
    }
}
