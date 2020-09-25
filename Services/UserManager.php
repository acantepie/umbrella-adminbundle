<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 24/04/20
 * Time: 16:36
 */

namespace Umbrella\AdminBundle\Services;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Umbrella\AdminBundle\Entity\BaseUser;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class UserManager
 */
class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

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
     * UserManager constructor.
     * @param EntityManagerInterface       $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ParameterBagInterface        $parameters
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, ParameterBagInterface $parameters)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->parameters = $parameters;

        $this->class = $parameters->get('umbrella_admin.user.user_crud.class');
        $this->repo = $this->em->getRepository($this->class);
    }

    /**
     * @return BaseUser
     */
    public function createUser()
    {
        $user = new $this->class();
        return $user;
    }

    /**
     * @param $id
     * @return BaseUser
     */
    public function find($id)
    {
        return $this->repo->find($id);
    }

    /**
     * @param $email
     * @return BaseUser
     */
    public function findUserByEmail($email)
    {
        return $this->repo->findOneBy([
            'email' => $email
        ]);
    }

    /**
     * @param $confirmationToken
     * @return BaseUser
     */
    public function findUserByConfirmationToken($confirmationToken)
    {
        return $this->repo->findOneBy([
            'confirmationToken' => $confirmationToken
        ]);
    }

    /**
     * @param BaseUser $user
     */
    public function updatePassword(BaseUser $user)
    {
        if (!empty($user->plainPassword)) {
            $user->password = $this->passwordEncoder->encodePassword($user, $user->plainPassword);
            $user->passwordRequestedAt = null;
            $user->confirmationToken = null;
        }
    }

    /**
     * @param BaseUser $user
     */
    public function update(BaseUser $user)
    {
        $this->updatePassword($user);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param BaseUser $user
     */
    public function remove(BaseUser $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}
