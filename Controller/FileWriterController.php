<?php

namespace Umbrella\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Umbrella\AdminBundle\Entity\UmbrellaFileWriterConfig;
use Umbrella\AdminBundle\Model\AdminUserInterface;
use Umbrella\CoreBundle\Controller\BaseController;

/**
 * Class FileWriterController
 *
 * @Route("/file-writer")
 */
class FileWriterController extends BaseController
{
    /**
     * @Route("/download/{id}", requirements={"id": "\d+"})
     *
     * @param mixed $id
     */
    public function downloadAction(ParameterBagInterface $parameterBag, $id)
    {
        /** @var UmbrellaFileWriterConfig $config */
        $config = $this->findOrNotFound(UmbrellaFileWriterConfig::class, $id);
        $author = $this->getUser() instanceof AdminUserInterface ? $this->getUser() : null;

        if (null !== $author && $config->fwAuthor !== $author) {
            return new Response('', 404);
        }

        $outputFilePath = sprintf('%s/%s', rtrim($parameterBag->get('umbrella_admin.filewriter.output_path'), '/'), $config->fwOutputFilename);

        if (empty($config->fwOutputFilename) || !file_exists($outputFilePath) || !is_readable($outputFilePath)) {
            return new Response('', 404);
        }

        $response = new BinaryFileResponse($outputFilePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $config->fwOutputPrettyFilename);

        return $response;
    }
}
