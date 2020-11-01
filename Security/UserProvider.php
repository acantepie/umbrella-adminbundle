<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 11/04/18
 * Time: 14:56
 */

namespace Umbrella\AdminBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 */
class UserProvider implements UserProviderInterface
{
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
     * UserProvider constructor.
     *
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface  $parameters
     */
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
        $this->class = $this->parameters->get('umbrella_admin.user.user_crud.class');
        $this->repo = $em->getRepository($this->class);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        return $this->createQb()
            ->where('e.email = :email')
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof $this->class) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        return $this->createQb()
            ->where('e.id = :id')
            ->setParameter('id', $user->id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->class || is_subclass_of($class, $this->class);
    }

    protected function createQb()
    {
        return $this->repo->createQueryBuilder('e')
            ->addSelect('a, g')
            ->leftJoin('e.avatar', 'a')
            ->leftJoin('e.groups', 'g');
    }
}
