<?php

namespace Umbrella\AdminBundle\FileWriter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Umbrella\AdminBundle\Entity\FileWrite;
use Umbrella\AdminBundle\FileWriter\Handler\FileWriterHandlerFactory;
use Umbrella\AdminBundle\Model\AdminUserInterface;
use Umbrella\CoreBundle\Component\JsResponse\JsResponseBuilder;
use Umbrella\CoreBundle\Component\Schedule\Runner\Runner;
use Umbrella\CoreBundle\Component\Schedule\Scheduler;
use Umbrella\CoreBundle\Entity\Task;
use Umbrella\CoreBundle\Utils\FileUtils;

/**
 * Class FileWriterManager
 */
class FileWriter
{
    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var Runner
     */
    private $runner;

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
     * @var int
     */
    private $maxFileWrite;

    /**
     * FileWriter constructor.
     * @param Scheduler $scheduler
     * @param RouterInterface $router
     * @param Security $security
     * @param JsResponseBuilder $jsResponseBuilder
     * @param string $outputDirPath
     * @param int $maxFileWrite
     */
    public function __construct(
        Scheduler $scheduler,
        RouterInterface $router,
        Security $security,
        JsResponseBuilder $jsResponseBuilder,
        string $outputDirPath,
        int $maxFileWrite
    )
    {
        $this->scheduler = $scheduler;
        $this->router = $router;
        $this->security = $security;
        $this->jsResponseBuilder = $jsResponseBuilder;
        $this->outputDirPath = $outputDirPath;
        $this->maxFileWrite = $maxFileWrite;
    }


    /**
     * @param FileWrite $fileWrite
     * @return RedirectResponse
     */
    public function syncDownloadResponse(FileWrite $fileWrite)
    {
        $fileWrite = $this->scheduleAndRun($fileWrite);
        return new RedirectResponse($this->getDownloadUrl($fileWrite));
    }

    /**
     * @param FileWrite $fileWrite
     * @return JsResponseBuilder
     */
    public function asyncJsResponse(FileWrite $fileWrite)
    {
        $jobId = $this->schedule($fileWrite);
        return $this->jsResponseBuilder
            ->openModalView('@UmbrellaAdmin/FileWriter/register_async_success.html.twig', [
                'file_write' => $fileWrite,
            ]);
    }

    /**
     * @param FileWrite $fileWrite
     * @return string
     */
    public function getDownloadUrl(FileWrite $fileWrite)
    {
        return $this->router->generate('umbrella_admin_filewriter_download', [
            'id' => $fileWrite->id,
        ]);
    }

    /**
     * @param $taskId
     * @param FileWrite $fileWrite
     * @param int $timeout
     * @return int
     */
    public function schedule($taskId, FileWrite $fileWrite, $timeout = 0)
    {
        $user = $this->security->getUser();
        if (null !== $user && $user instanceof AdminUserInterface) { // set author
            $fileWrite->author = $this->security->getUser();
        }

        return $this->scheduler->create()
            ->setTask($taskId, $fileWrite)
            ->setDescription(sprintf('File write : %s', $fileWrite->description))
            ->disableOutput(true)
            ->register();
    }

    /**
     * @param $taskId
     * @param FileWrite $fileWrite
     * @param int $timeout
     * @return FileWrite
     */
    public function scheduleAndRun($taskId, FileWrite $fileWrite, $timeout = 0)
    {
        $jobId = $this->schedule($taskId, $fileWrite, $timeout);
        $this->runner->run($jobId);

        return $this->em->find(FileWrite::class, $id);
    }

    /**
     * @param false $onlyNotification
     * @param int $maxResults
     *
     * @return Task(]
     */
    public function searchTask($onlyNotification = false, $maxResults = 10, AdminUserInterface $author = null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('e');
        $qb->from(Task::class, 'e');
        $qb->innerJoin(UmbrellaFileWriterConfig::class, 'c', Join::WITH, 'c = e.config');

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
     *
     * @return bool
     */
    private function canRegisterTask()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('COUNT(e)');
        $qb->from(Task::class, 'e');
        $qb->innerJoin(UmbrellaFileWriterConfig::class, 'c', Join::WITH, 'c = e.config');
        $qb->andWhere('e.state IN (:states)');
        $qb->setParameter('states', [Task::STATE_PENDING, Task::STATE_RUNNING]);

        $user = $this->security->getUser();
        if (null !== $user && $user instanceof AdminUserInterface) {
            $qb->andWhere('c.fwAuthor = :user');
            $qb->setParameter('user', $user);
        } else {
            $qb->andWhere('c.fwAuthor IS NULL');
        }

        return $qb->getQuery()->getSingleScalarResult() < $this->maxFileWrite;
    }
}
