<?php

namespace Umbrella\AdminBundle\FileWriter\Handler;

use Umbrella\AdminBundle\Entity\UmbrellaFileWriterConfig;

/**
 * Class FileWriterProvider
 */
class FileWriterHandlerFactory
{
    /**
     * @var AbstractFileWriterHandler[]
     */
    private $handlers = [];

    /**
     * @param $id
     * @param ActionType $actionType
     */
    public function registerHandler($id, AbstractFileWriterHandler $handler)
    {
        $this->handlers[$id] = $handler;
    }

    /**
     * @param  UmbrellaFileWriterConfig  $config
     * @return AbstractFileWriterHandler
     */
    public function create(UmbrellaFileWriterConfig $config)
    {
        if (isset($this->handlers[$config->fwHandlerAlias])) {
            $handler = $this->handlers[$config->fwHandlerAlias];
            return $handler;
        } else {
            throw new \InvalidArgumentException(sprintf(
                "No filewriter handler found with alias '%s', alias registered are %s.",
                $config->handlerAlias,
                implode(', ', array_keys($this->handlers))
            ));
        }
    }
}
