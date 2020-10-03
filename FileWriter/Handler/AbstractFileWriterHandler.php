<?php

namespace Umbrella\AdminBundle\FileWriter\Handler;

use Umbrella\AdminBundle\Entity\UmbrellaFileWriterConfig;

/**
 * Class AbstractFileWriterHandler
 */
abstract class AbstractFileWriterHandler
{
    /**
     * @var string
     */
    protected $outputDirPath;

    /**
     * @param string $outputDirPath
     */
    final public function setOutputDirPath($outputDirPath)
    {
        $this->outputDirPath = $outputDirPath;
    }

    /**
     * @param UmbrellaFileWriterConfig $config
     */
    public function initialize(UmbrellaFileWriterConfig $config)
    {
    }

    /**
     * @param UmbrellaFileWriterConfig $config
     */
    public function execute(UmbrellaFileWriterConfig $config)
    {
    }

    /**
     * @param  UmbrellaFileWriterConfig $config
     * @return string
     */
    final protected function getOuputFilePath(UmbrellaFileWriterConfig $config)
    {
        return sprintf('%s%s', $this->outputDirPath, $config->fwOutputFilename);
    }
}
