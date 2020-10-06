<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 08/05/17
 * Time: 18:39.
 */

namespace Umbrella\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Umbrella\CoreBundle\Annotation\Searchable;
use Umbrella\CoreBundle\Annotation\SearchableField;
use Symfony\Bridge\Doctrine\Validator\Constraints as CT;
use Umbrella\CoreBundle\Model\IdTrait;
use Umbrella\CoreBundle\Model\SearchTrait;
use Umbrella\CoreBundle\Model\TimestampTrait;

/**
 * Class UserGroup.
 *
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks()
 *
 * @Searchable()
 *
 * @CT\UniqueEntity("title")
 */
class BaseUserGroup
{
    use IdTrait;
    use TimestampTrait;
    use SearchTrait;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     *
     * @SearchableField()
     */
    public $title;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    public $roles = [];

    /**
     * @var ArrayCollection|BaseUser[]
     */
    public $users;

    /**
     * UserGroup constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->title;
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }
}
