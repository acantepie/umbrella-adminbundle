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
    const TAG = 'file_writer';

    const MODE_SYNC = 'sync';
    const MODE_ASYNC = 'async';

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    public $fwMode = self::MODE_SYNC;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default"=false})
     */
    public $fwDisplayAsNotification = false;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    public $fwLabel;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    public $fwHandlerAlias;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    public $fwOutputFilename;

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
        $this->tag = self::TAG;
        parent::__construct($taskHandlerAlias);
    }
}
