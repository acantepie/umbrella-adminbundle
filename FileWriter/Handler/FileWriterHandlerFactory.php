<?php

namespace Umbrella\AdminBundle\FileWriter\Handler;

use Umbrella\AdminBundle\Entity\FileWriterTaskConfig;

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
     * @param  FileWriterTaskConfig      $config
     * @return AbstractFileWriterHandler
     */
    public function create(FileWriterTaskConfig $config)
    {
        if (isset($this->handlers[$config->handlerAlias])) {
            $handler = $this->handlers[$config->handlerAlias];
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
