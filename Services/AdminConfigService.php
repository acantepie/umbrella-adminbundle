<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 08/05/17
 * Time: 16:29.
 */

namespace Umbrella\AdminBundle\Services;
use Umbrella\CoreBundle\Utils\ArrayUtils;

/**
 * Class AdminConfigService
 */
class AdminConfigService
{
    /**
     * @var array
     */
    private $config = array();

    /**
     * @param $key
     * @return mixed
     */
    public function getValue($key)
    {
        return ArrayUtils::get_with_dot_keys($this->config, $key);
    }


    /* Call by Bundle configurator */

    public function loadConfig(array $config)
    {
        $this->config = $config;
    }
}
