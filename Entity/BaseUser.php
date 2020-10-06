<?php

/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 07/05/17
 * Time: 18:50.
 */

namespace Umbrella\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Umbrella\CoreBundle\Annotation\Searchable;
use Umbrella\CoreBundle\Entity\UmbrellaFile;
use Doctrine\Common\Collections\ArrayCollection;
use Umbrella\AdminBundle\Model\AdminUserInterface;
use Umbrella\CoreBundle\Annotation\SearchableField;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Umbrella\CoreBundle\Model\ActiveTrait;
use Umbrella\CoreBundle\Model\IdTrait;
use Umbrella\CoreBundle\Model\SearchTrait;
use Umbrella\CoreBundle\Model\TimestampTrait;

/**
 * Class User.
 *
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks
 *
 * @Searchable()
 */
class BaseUser implements UserInterface, EquatableInterface, \Serializable, AdminUserInterface
{
    use IdTrait;
    use TimestampTrait;
    use SearchTrait;
    use ActiveTrait;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @SearchableField()
     */
    public $firstname;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @SearchableField()
     */
    public $lastname;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    public $password;

    /**
     * Used only by form.
     *
     * @var string
     */
    public $plainPassword;

    /**
     * @ORM\Column(type="string", length=32)
     */
    public $salt;

    /**
     * @var string
     * @ORM\Column(type="string", length=60, unique=true)
     *
     * @SearchableField()
     */
    public $email;

    /**
     * Random string sent to the user email address to verify it.
     *
     * @var string|null
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     */
    public $confirmationToken;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $passwordRequestedAt;

    /**
     * @var UmbrellaFile
     */
    public $avatar;

    /**
     * @var ArrayCollection|BaseUserGroup[]
     */
    public $groups;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->salt = md5(uniqid(null, true));
        $this->groups = new ArrayCollection();
    }

    /**
     * @param BaseUserGroup $group
     */
    public function addGroup(BaseUserGroup $group)
    {
        if (!$this->groups->contains($group)) {
            $group->users->add($this);
            $this->groups->add($group);
        }
    }

    /**
     * @param BaseUserGroup $group
     */
    public function removeGroup(BaseUserGroup $group)
    {
        if ($this->groups->contains($group)) {
            $group->users->removeElement($this);
            $this->groups->removeElement($group);
        }
    }

    /**
     * @param $ttl
     * @return bool
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->passwordRequestedAt instanceof \DateTime &&
            $this->passwordRequestedAt->getTimestamp() + $ttl > time();
    }

    /**
     * Generate new confirmation token for resetting password
     */
    public function generateConfirmationToken()
    {
        $this->confirmationToken = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    // UserInterface implementation

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        $roles = [];
        foreach ($this->groups as $group) {
            $roles = array_merge($roles, $group->roles);
        }

        return empty($roles) ? ['ROLE_USER'] : array_unique($roles);
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    // Equatable implementation

    /**
     * @inheritdoc
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof BaseUser) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->email !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    // Serializable implementation

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->password,
            $this->salt,
            $this->email
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->password,
            $this->salt,
            $this->email
            ) = unserialize($serialized);
    }

    // AdminUserInterface implementation

    /**
     * @inheritdoc
     */
    public function getFullName()
    {
        return sprintf('%s %s', $this->firstname, $this->lastname);
    }

    /**
     * @inheritdoc
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
}
