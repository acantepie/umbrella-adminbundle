<?php

namespace Umbrella\AdminBundle\FileWriter;

use Doctrine\ORM\EntityManagerInterface;
use Umbrella\CoreBundle\Component\Task\TaskManager;
use Umbrella\AdminBundle\Entity\FileWriterTaskConfig;
use Umbrella\AdminBundle\FileWriter\Handler\FileWriterHandlerFactory;

/**
 * Class FileWriterManager
 */
class FileWriterManager
{
    /**
     * @var TaskManager
     */
    private $taskManager;

    /**
     * @var FileWriterHandlerFactory
     */
    private $handlerFactory;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * FileWriterService constructor.
     * @param TaskManager              $taskManager
     * @param FileWriterHandlerFactory $handlerFactory
     * @param EntityManagerInterface   $em
     */
    public function __construct(TaskManager $taskManager, FileWriterHandlerFactory $handlerFactory, EntityManagerInterface $em)
    {
        $this->taskManager = $taskManager;
        $this->handlerFactory = $handlerFactory;
        $this->em = $em;
    }

    /**
     * @param FileWriterTaskConfig $config
     */
    public function schedule(FileWriterTaskConfig $config)
    {
        $this->taskManager->register($config);
    }

    /**
     * @param FileWriterTaskConfig $config
     */
    public function run(FileWriterTaskConfig $config)
    {
        $handler = $this->handlerFactory->create($config);
        $handler->initialize($config);
        $handler->execute($config);
        
        if (null === $config->fwOutputFilePath) {
            throw new \RuntimeException(sprintf('You must set FileWriterTaskConfig::fwOutputFilePath on handler %s', $config->fwHandlerAlias));
        }
    }
}
