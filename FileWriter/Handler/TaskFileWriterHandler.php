<?php

namespace Umbrella\AdminBundle\FileWriter\Handler;

use Umbrella\AdminBundle\FileWriter\FileWriterManager;
use Umbrella\CoreBundle\Component\Task\Handler\AbstractTaskHandler;
use Umbrella\CoreBundle\Entity\BaseTaskConfig;

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
     * TaskFileWriterHandler constructor.
     *
     * @param FileWriterManager $manager
     */
    public function __construct(FileWriterManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BaseTaskConfig $config)
    {
        $this->manager->run($config);
    }
}
