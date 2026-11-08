<?php
namespace DMKClub\Bundle\BasicsBundle\Async\Topic;

use Oro\Component\MessageQueue\Topic\AbstractTopic;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Erzeugt die PDF-Datei für eine einzelne Entität als verzögerten Job.
 */
class ExportPdfDelayedTopic extends AbstractTopic
{
    public static function getName(): string
    {
        return 'dmkclub.basics.export_pdf.delayed';
    }

    public static function getDescription(): string
    {
        return 'Export a PDF file for a single entity';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['entity_id', 'entity_name', 'jobId']);
    }
}
