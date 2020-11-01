<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 24/04/20
 * Time: 16:36
 */

namespace Umbrella\AdminBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Umbrella\AdminBundle\Entity\BaseUserGroup;

/**
 * Class UserGroupManager
 */
class UserGroupManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ParameterBagInterface
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var EntityRepository
     */
    protected $repo;

    /**
     * UserGroupManager constructor.
     *
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface  $parameters
     */
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $parameters)
    {
        $this->em = $em;
        $this->parameters = $parameters;

        $this->class = $parameters->get('umbrella_admin.user.group_crud.class');
        $this->repo = $this->em->getRepository($this->class);
    }

    /**
     * @return BaseUserGroup
     */
    public function createGroup()
    {
        $user = new $this->class();

        return $user;
    }

    /**
     * @param $id
     *
     * @return BaseUserGroup
     */
    public function find($id)
    {
        return $this->repo->find($id);
    }

    /**
     * @param BaseUserGroup $group
     */
    public function update(BaseUserGroup $group)
    {
        $this->em->persist($group);
        $this->em->flush();
    }

    /**
     * @param BaseUserGroup $group
     */
    public function remove(BaseUserGroup $group)
    {
        $this->em->remove($group);
        $this->em->flush();
    }
}
