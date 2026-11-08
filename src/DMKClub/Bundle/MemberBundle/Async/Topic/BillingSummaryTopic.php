<?php
namespace DMKClub\Bundle\MemberBundle\Async\Topic;

use Oro\Component\MessageQueue\Topic\AbstractTopic;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Aktualisiert die Summen eines Abrechnungslaufs.
 */
class BillingSummaryTopic extends AbstractTopic
{
    public static function getName(): string
    {
        return 'dmk.member.accounting.summary';
    }

    public static function getDescription(): string
    {
        return 'Update the summary of a member billing';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['memberbilling_id', 'jobId']);
    }
}
