<?php

namespace Umbrella\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Umbrella\CoreBundle\Controller\BaseController;
use Umbrella\AdminBundle\Entity\FileWriterTaskConfig;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class FileWriterController
 *
 * @Route("/file-writer")
 */
class FileWriterController extends BaseController
{
    /**
     * @Route("/download/{id}", requirements={"id"="\d+"})
     * @param mixed $id
     */
    public function downloadAction(ParameterBagInterface $parameterBag, $id)
    {
        /** @var FileWriterTaskConfig $config */
        $config = $this->findOrNotFound(FileWriterTaskConfig::class, $id);

        $outputFilePath = sprintf('%s/%s', rtrim($parameterBag->get('umbrella_admin.filewriter.output_path'), '/'), $config->fwOutputFilename);

        if (empty($config->fwOutputFilename) || !file_exists($outputFilePath) || !is_readable($outputFilePath)) {
            return new Response('', 404);
        }

        $response = new BinaryFileResponse($outputFilePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $config->fwOutputPrettyFilename);
        return $response;
    }
}
