<?php

namespace Umbrella\AdminBundle\FileWriter\Handler;

use Umbrella\AdminBundle\Entity\FileWriterTaskConfig;

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
     * @param FileWriterTaskConfig $config
     */
    public function initialize(FileWriterTaskConfig $config)
    {
    }

    /**
     * @param FileWriterTaskConfig $config
     */
    public function execute(FileWriterTaskConfig $config)
    {
    }

    /**
     * @param  FileWriterTaskConfig $config
     * @return string
     */
    final protected function getOuputFilePath(FileWriterTaskConfig $config)
    {
        return sprintf('%s%s', $this->outputDirPath, $config->fwOutputFilename);
    }
}
