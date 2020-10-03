<?php

namespace Umbrella\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Umbrella\CoreBundle\Entity\Task;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Umbrella\CoreBundle\Controller\BaseController;
use Umbrella\AdminBundle\Entity\FileWriterTaskConfig;
use Umbrella\AdminBundle\FileWriter\FileWriterManager;
use Umbrella\CoreBundle\Component\DateTime\DateTimeHelper;

/**
 * @Route("/notification")
 */
class NotificationController extends BaseController
{
    /**
     * @Route("/list")
     */
    public function listAction(FileWriterManager $fileWriterManager, DateTimeHelper $dateTimeHelper, ParameterBagInterface  $parameterBag, Request $request)
    {
        if (!$parameterBag->get('umbrella_admin.filewriter.notification_enable')) {
            throw new BadRequestHttpException('Notification are disabled');
        }

        $tasks = $fileWriterManager->searchTask(true, $parameterBag->get('umbrella_admin.filewriter.notification_max_result'));

        $data = [];
        foreach ($tasks as $task) {

            /** @var FileWriterTaskConfig $config */
            $config = $task->config;

            $data[] = [
                'label' => empty($config->fwLabel)
                    ? '-'
                    : $config->fwLabel,
                'state' => $task->state,
                'date' => $dateTimeHelper->diff($task->createdAt),
                'ended_at' => $task->endedAt
                    ? $dateTimeHelper->diff($task->endedAt)
                    : null,
                'runtime' => $task->runtime(),
                'url' => $task->state === Task::STATE_FINISHED
                    ? $fileWriterManager->getDownloadUrl($config)
                    : null
            ];
        }

        return new JsonResponse($data);
    }
}
