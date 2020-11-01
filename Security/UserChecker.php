<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 24/04/20
 * Time: 21:34
 */

namespace Umbrella\AdminBundle\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Umbrella\AdminBundle\Entity\BaseUser;

/**
 * Class UserChecker
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * Checks the user account before authentication.
     *
     * @throws AccountStatusException
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof BaseUser) {
            return;
        }

        if (!$user->active) {
            throw new AccountDisabledException();
        }
    }

    /**
     * Checks the user account after authentication.
     *
     * @throws AccountStatusException
     */
    public function checkPostAuth(UserInterface $user)
    {
    }
}
