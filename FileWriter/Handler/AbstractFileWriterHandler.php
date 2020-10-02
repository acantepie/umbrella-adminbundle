<?php

namespace Umbrella\AdminBundle\FileWriter\Handler;

use Symfony\Component\Filesystem\Filesystem;
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
     * @var string
     */
    protected $outputFileName;

    /**
     * @param FileWriterTaskConfig $config
     */
    protected $config;

    /**
     * Call on service build
     *
     * @param $outputDirPath
     */
    final private function __initializeService($outputDirPath)
    {
        $this->outputDirPath = $outputDirPath;
    }

    /**
     * @param FileWriterTaskConfig $config
     */
    public function initialize(FileWriterTaskConfig $config)
    {
        $this->outputFileName = md5(uniqid(time(), true));

        $fs = new Filesystem();
        if (!$fs->exists($this->outpoutDirPath)) {
            $fs->mkdir($this->outpoutDirPath);
        }
    }

    /**
     * @param FileWriterTaskConfig $config
     */
    public function execute(FileWriterTaskConfig $config)
    {
    }

    // helper

    /**
     * @return string
     */
    protected function getOutputFilePath()
    {
        return sprintf('%s%s', $this->outputDirPath, $this->outputFileName);
    }
}
