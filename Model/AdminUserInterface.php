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
     * Avatar of user
     *
     * @return UmbrellaFile|null
     */
    public function getAvatar();

    /**
     * Username of user (used to login)
     *
     * @return string
     */
    public function getUsername();

    /**
     * Full name of user
     *
     * @return string
     */
    public function getFullName();



}