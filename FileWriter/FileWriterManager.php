<?php

namespace Umbrella\AdminBundle\FileWriter;

use Doctrine\ORM\Query\Expr\Join;
use Umbrella\CoreBundle\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Umbrella\CoreBundle\Utils\FileUtils;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;
use Umbrella\CoreBundle\Component\Task\TaskManager;
use Umbrella\AdminBundle\Entity\FileWriterTaskConfig;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Umbrella\AdminBundle\FileWriter\Handler\FileWriterHandlerFactory;

/**
 * Class FileWriterManager
 */
class FileWriterManager
{
    const TAG = 'file_writer';

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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $outputDirPath;

    /**
     * FileWriterManager constructor.
     *
     * @param TaskManager              $taskManager
     * @param FileWriterHandlerFactory $handlerFactory
     * @param EntityManagerInterface   $em
     * @param RouterInterface          $router
     * @param $outputDirPath
     */
    public function __construct(TaskManager $taskManager, FileWriterHandlerFactory $handlerFactory, EntityManagerInterface $em, RouterInterface $router, $outputDirPath)
    {
        $this->taskManager = $taskManager;
        $this->handlerFactory = $handlerFactory;
        $this->em = $em;
        $this->router = $router;
        $this->outputDirPath = rtrim($outputDirPath, '/') . '/';
    }

    /**
     * Generate a response for a sync file writer config
     *
     * @param $config
     * @retrun Response
     */
    public function syncDownloadResponse(FileWriterTaskConfig $config)
    {
        $config->fwMode = FileWriterTaskConfig::MODE_SYNC;
        $this->run($config);

        return new RedirectResponse($this->getDownloadUrl($config));
    }

    /**
     * @param  FileWriterTaskConfig $config
     * @return string
     */
    public function getDownloadUrl(FileWriterTaskConfig $config)
    {
        return $this->router->generate('umbrella_admin_filewriter_download', [
            'id' => $config->id
        ]);
    }

    /**
     * Schedule an ASYNC config
     *
     * @param  FileWriterTaskConfig $config
     * @return Task
     */
    public function schedule(FileWriterTaskConfig $config)
    {
        $config->fwMode = FileWriterTaskConfig::MODE_ASYNC;
        return $this->taskManager->register($config);
    }

    /**
     * Run a config (don't care if async or sync)
     *
     * @param  FileWriterTaskConfig $config
     * @return FileWriterTaskConfig
     */
    public function run(FileWriterTaskConfig $config)
    {
        $this->em->persist($config);

        $handler = $this->handlerFactory->create($config);
        $handler->setOutputDirPath($this->outputDirPath);

        // set a default outpout file path if not setted
        if (empty($config->fwOutputFilename)) {
            $config->fwOutputFilename = md5(uniqid(time(), true));
        }

        // create directory for output file (if needed)
        $fs = new Filesystem();
        if (!$fs->exists($this->outputDirPath)) {
            $fs->mkdir($this->outputDirPath);
        }

        // run
        $handler->initialize($config);
        $handler->execute($config);

        // if prettyFilename was empty - generate one
        if (empty($config->fwOutputPrettyFilename)) {
            $outputFilePath = sprintf('%s%s', $this->outputDirPath, $config->fwOutputFilename);

            if (is_readable($outputFilePath)) {
                $ext = FileUtils::mime_to_ext(mime_content_type($outputFilePath));
                $config->fwOutputPrettyFilename = false === $ext
                    ? 'file.txt'
                    : sprintf('file.%s', $ext);
            } else {
                $config->fwOutputPrettyFilename = 'file.txt';
            }
        }

        $this->em->flush();

        return $config;
    }

    /**
     * @param  false  $onlyNotification
     * @param  int    $maxResults
     * @return Task(]
     */
    public function searchTask($onlyNotification = false, $maxResults = 10)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('e');
        $qb->from(Task::class, 'e');
        $qb->innerJoin(FileWriterTaskConfig::class, 'c', Join::WITH, 'c = e.config');

        if ($onlyNotification) {
            $qb->andWhere('c.fwDisplayAsNotification = TRUE');
        }

        if (null !== $maxResults) {
            $qb->setMaxResults($maxResults);
        }

        return $qb->getQuery()->getResult();
    }
}
