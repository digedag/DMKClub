<?php
namespace DMKClub\Bundle\MemberBundle\Async\Topic;

use Oro\Component\MessageQueue\Topic\AbstractTopic;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Berechnet den Beitrag für ein einzelnes Mitglied.
 */
class FeeCalculationTopic extends AbstractTopic
{
    public static function getName(): string
    {
        return 'dmkfeeaccounting';
    }

    public static function getDescription(): string
    {
        return 'Calculate the member fee for a single member';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['member_id', 'memberbilling_id', 'bill_date', 'due_date', 'jobId']);
    }
}
