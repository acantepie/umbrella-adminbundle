<?php

namespace Umbrella\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Umbrella\CoreBundle\Entity\BaseTaskConfig;
use Umbrella\AdminBundle\FileWriter\Handler\TaskFileWriterHandler;

/**
 * @ORM\Entity
 */
class FileWriterTaskConfig extends BaseTaskConfig
{
    const MODE_DIRECT = 'direct';
    const MODE_SCHEDULE = 'schedule';

    /**
     * Only schedule mode if persisted
     * @var string
     */
    public $fwMode;

    /**
     * Stored on task if persisted
     * @var string
     */
    public $fwLabel;

    /**
     * Only configurable for schedule mode - stored on task
     * @var bool
     */
    public $fwDisplayAsNotification = false;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    public $fwHandlerAlias;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    public $fwOutputFilePath;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    public $fwOutputPrettyFilename;

    /**
     * FileWriterTaskConfig constructor.
     *
     * @param $fileWriterHandlerAlias
     * @param string $taskHandlerAlias
     */
    public function __construct($fileWriterHandlerAlias, $taskHandlerAlias = TaskFileWriterHandler::class)
    {
        $this->fwHandlerAlias = $fileWriterHandlerAlias;
        parent::__construct($taskHandlerAlias);
    }
}
