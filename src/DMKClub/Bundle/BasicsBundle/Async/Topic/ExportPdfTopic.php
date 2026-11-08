<?php
namespace DMKClub\Bundle\BasicsBundle\Async\Topic;

use Oro\Component\MessageQueue\Topic\AbstractTopic;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Startet den PDF-Export für eine Liste von Entitäten.
 */
class ExportPdfTopic extends AbstractTopic
{
    public static function getName(): string
    {
        return 'dmkclub.basics.export_pdf';
    }

    public static function getDescription(): string
    {
        return 'Export PDF files for the selected entities';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['entity_name', 'entity_ids']);
    }
}
