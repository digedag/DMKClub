<?php

namespace DMKClub\Bundle\BasicsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\ImportExportBundle\Handler\ExportHandler;

#[Route('/export')]
class ExportController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class,
                ExportHandler::class,
            ]
        );
    }

    /**
     * @param string $fileName
     * @return Response
     */
    #[Route('/download/{fileName}', name: 'dmkclub_basics_export_download')]
    public function downloadExportResultAction($fileName)
    {
        if (!$this->isGranted('dmkclub_basics_export_download')) {
            throw new AccessDeniedException('Insufficient permission');
        }

        return $this->getExportHandler()->handleDownloadExportResult($fileName);
    }

    /**
     * @return ExportHandler
     */
    protected function getExportHandler()
    {
        return $this->container->get(ExportHandler::class);
    }
}
