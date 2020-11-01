<?php

namespace Umbrella\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Umbrella\AdminBundle\FileWriter\Handler\TaskFileWriterHandler;
use Umbrella\CoreBundle\Entity\BaseTaskConfig;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class UmbrellaFileWriterConfig extends BaseTaskConfig
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
     * Author
     *
     * @var BaseUser
     *
     * @ORM\ManyToOne(targetEntity="Umbrella\AdminBundle\Model\AdminUserInterface")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    public $fwAuthor;

    /**
     * Will display task on notification view on ASYNC mode
     *
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
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
     * UmbrellaFileWriterConfig constructor.
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
