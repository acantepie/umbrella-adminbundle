<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 24/04/20
 * Time: 21:39
 */

namespace Umbrella\AdminBundle\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Class AccountDisabledException
 */
class AccountDisabledException extends AccountStatusException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'account_disabled';
    }
}
