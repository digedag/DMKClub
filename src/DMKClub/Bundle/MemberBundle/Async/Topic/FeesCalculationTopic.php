<?php
namespace DMKClub\Bundle\MemberBundle\Async\Topic;

use Oro\Component\MessageQueue\Topic\AbstractTopic;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Startet die Beitragsberechnung für einen Abrechnungslauf.
 */
class FeesCalculationTopic extends AbstractTopic
{
    public static function getName(): string
    {
        return 'dmkfeesaccounting';
    }

    public static function getDescription(): string
    {
        return 'Calculate member fees for a member billing';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['memberbilling_id', 'entity_ids', 'bill_date', 'due_date']);
    }
}
