<?php

namespace Umbrella\AdminBundle\FileWriter\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Umbrella\CoreBundle\Entity\BaseTaskConfig;
use Umbrella\AdminBundle\FileWriter\FileWriterManager;
use Umbrella\CoreBundle\Component\Task\Handler\AbstractTaskHandler;

/**
 * Class TaskFileWriterHandler
 */
class TaskFileWriterHandler extends AbstractTaskHandler
{
    /**
     * @var FileWriterManager
     */
    private $manager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * TaskFileWriterHandler constructor.
     * @param FileWriterManager      $manager
     * @param EntityManagerInterface $em
     */
    public function __construct(FileWriterManager $manager, EntityManagerInterface $em)
    {
        $this->manager = $manager;
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    public function execute(BaseTaskConfig $config)
    {
        $task = $this->taskHelper->getTask();
        $this->manager->run($task->fileWriterConfig);
        $this->em->flush();
    }
}
