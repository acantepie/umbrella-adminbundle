<?php

namespace Umbrella\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Umbrella\CoreBundle\Component\Schedule\Context\AbstractTaskContext;
use Umbrella\CoreBundle\Component\Schedule\RuntimeEnv\AbstractEnvironment;

/**
 * @ORM\Entity()
 */
class FileWrite extends AbstractTaskContext
{
    /**
     * Author
     *
     * @var BaseUser
     *
     * @ORM\ManyToOne(targetEntity="Umbrella\AdminBundle\Model\AdminUserInterface")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    public $author;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    public $displayAsNotification = false;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    public $description;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    public $outputFilename;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    public $outputPrettyFilename;
}
