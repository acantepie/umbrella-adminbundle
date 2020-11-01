<?php

namespace Umbrella\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Umbrella\AdminBundle\Entity\UmbrellaFileWriterConfig;
use Umbrella\AdminBundle\FileWriter\FileWriterManager;
use Umbrella\AdminBundle\Model\AdminUserInterface;
use Umbrella\CoreBundle\Component\DateTime\DateTimeHelper;
use Umbrella\CoreBundle\Controller\BaseController;
use Umbrella\CoreBundle\Entity\Task;

/**
 * @Route("/notification")
 */
class NotificationController extends BaseController
{
    /**
     * @Route("/list")
     */
    public function listAction(FileWriterManager $fileWriterManager, DateTimeHelper $dateTimeHelper, ParameterBagInterface $parameterBag, Request $request)
    {
        if (!$parameterBag->get('umbrella_admin.filewriter.notification_enable')) {
            throw new BadRequestHttpException('Notification are disabled');
        }

        $author = $this->getUser() instanceof AdminUserInterface ? $this->getUser() : null;
        $tasks = $fileWriterManager->searchTask(true, $parameterBag->get('umbrella_admin.filewriter.notification_max_result'), $author);

        $data = [];
        foreach ($tasks as $task) {
            /** @var UmbrellaFileWriterConfig $config */
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
                'url' => Task::STATE_FINISHED === $task->state
                    ? $fileWriterManager->getDownloadUrl($config)
                    : null,
            ];
        }

        return new JsonResponse($data);
    }
}
