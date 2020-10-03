<?php

namespace Umbrella\AdminBundle\FileWriter\Handler;

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
     * TaskFileWriterHandler constructor.
     * @param FileWriterManager $manager
     */
    public function __construct(FileWriterManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @inheritDoc
     */
    public function execute(BaseTaskConfig $config)
    {
        $this->manager->run($config);
    }
}
