<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 13/04/20
 * Time: 19:15
 */

namespace Umbrella\AdminBundle\Model;

use Umbrella\CoreBundle\Entity\UmbrellaFile;

/**
 * Interface AdminUserInterface
 * @package Umbrella\AdminBundle\Model
 */
interface AdminUserInterface
{
    /**
     * @return string
     */
    public function getAdminName();

    /**
     * @return string
     */
    public function getAdminLogin();

    /**
     * @return UmbrellaFile|null
     */
    public function getAvatar();

}