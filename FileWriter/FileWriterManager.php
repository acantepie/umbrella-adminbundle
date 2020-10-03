<?php

namespace Umbrella\AdminBundle\FileWriter;

use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;
use Umbrella\AdminBundle\Model\AdminUserInterface;
use Umbrella\CoreBundle\Component\JsResponse\JsResponseBuilder;
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
     * @var ParameterBagInterface
     */
    private $parameterBag;

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
     * @var Security
     */
    private $security;

    /**
     * @var JsResponseBuilder
     */
    private $jsResponseBuilder;

    /**
     * @var string
     */
    private $outputDirPath;

    /**
     * FileWriterManager constructor.
     *
     * @param ParameterBagInterface $parameterBag
     * @param TaskManager $taskManager
     * @param FileWriterHandlerFactory $handlerFactory
     * @param EntityManagerInterface $em
     * @param RouterInterface $router
     * @param Security $security
     * @param JsResponseBuilder $jsResponseBuilder
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        TaskManager $taskManager,
        FileWriterHandlerFactory $handlerFactory,
        EntityManagerInterface $em,
        RouterInterface $router,
        Security $security,
        JsResponseBuilder $jsResponseBuilder
    )
    {
        $this->parameterBag = $parameterBag;
        $this->taskManager = $taskManager;
        $this->handlerFactory = $handlerFactory;
        $this->em = $em;
        $this->router = $router;
        $this->security = $security;
        $this->jsResponseBuilder = $jsResponseBuilder;
        $this->outputDirPath = rtrim($this->parameterBag->get('umbrella_admin.filewriter.output_path'), '/') . '/';
    }

    /**
     * Generate a response for a sync file writer config (HttpResponse)
     *
     * @param $config
     * @retrun Response
     */
    public function syncDownloadResponse(FileWriterTaskConfig $config)
    {
        $this->registerSync($config);
        $this->run($config);
        return new RedirectResponse($this->getDownloadUrl($config));
    }

    /**
     * Generate a response for an async file writer config (JsResponse)
     *
     * @param FileWriterTaskConfig $config
     */
    public function asyncJsResponse(FileWriterTaskConfig $config)
    {
        try {
            $task = $this->registerASync($config);
            return $this->jsResponseBuilder
                ->openModalView('@UmbrellaAdmin/FileWriter/register_async_success.html.twig', [
                    'task' => $task,
                    'config' => $config
                ]);
        } catch(MaxTaskReachedException $e) {
            return $this->jsResponseBuilder
                ->openModalView('@UmbrellaAdmin/FileWriter/register_async_error.html.twig', [
                    'max_task' => $e->getMaxTask(),
                    'config' => $config
                ]);
        }
    }

    /**
     * @param FileWriterTaskConfig $config
     * @return string
     */
    public function getDownloadUrl(FileWriterTaskConfig $config)
    {
        return $this->router->generate('umbrella_admin_filewriter_download', [
            'id' => $config->id
        ]);
    }

    /**
     * Register a SYNC config
     *
     * @param FileWriterTaskConfig $config
     */
    public function registerSync(FileWriterTaskConfig $config)
    {
        $config->fwMode = FileWriterTaskConfig::MODE_SYNC;

        $user = $this->security->getUser();
        if ($user !== null && $user instanceof AdminUserInterface) { // set author
            $config->fwAuthor = $this->security->getUser();
        }

        $this->em->persist($config);
        $this->em->flush();
    }

    /**
     * Register an ASYNC config
     *
     * @param FileWriterTaskConfig $config
     * @return Task
     */
    public function registerAsync(FileWriterTaskConfig $config)
    {
        $config->fwMode = FileWriterTaskConfig::MODE_ASYNC;

        if (!$this->canRegisterTask()) {
            throw new MaxTaskReachedException($this->parameterBag->get('umbrella_admin.filewriter.max_task'));
        }

        $user = $this->security->getUser();
        if ($user !== null && $user instanceof AdminUserInterface) { // set author
            $config->fwAuthor = $this->security->getUser();
        }

        return $this->taskManager->register($config);
    }

    /**
     * Run a config (don't care if async or sync)
     *
     * @param FileWriterTaskConfig $config
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
     * @param false $onlyNotification
     * @param int $maxResults
     * @return Task(]
     */
    public function searchTask($onlyNotification = false, $maxResults = 10, AdminUserInterface $author = null)
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

        if ($author) {
            $qb->andWhere('c.fwAuthor = :user');
            $qb->setParameter('user', $author);
        } else {
            $qb->andWhere('c.fwAuthor IS NULL');
        }

        $qb->orderBy('e.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Can register filewriter task pending < max_task
     * @return bool
     */
    private function canRegisterTask()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('COUNT(e)');
        $qb->from(Task::class, 'e');
        $qb->innerJoin(FileWriterTaskConfig::class, 'c', Join::WITH, 'c = e.config');
        $qb->andWhere('e.state IN (:states)');
        $qb->setParameter('states', [Task::STATE_PENDING, Task::STATE_RUNNING]);

        $user = $this->security->getUser();
        if ($user !== null && $user instanceof AdminUserInterface) {
            $qb->andWhere('c.fwAuthor = :user');
            $qb->setParameter('user', $user);
        } else {
            $qb->andWhere('c.fwAuthor IS NULL');
        }

        return $qb->getQuery()->getSingleScalarResult() < $this->parameterBag->get('umbrella_admin.filewriter.max_task');
    }
}
