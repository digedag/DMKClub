<?php

namespace DMKClub\Bundle\MemberBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use DMKClub\Bundle\MemberBundle\Model\ExtendMemberProposalAddress;

/**
 * With immutable=true the annotated features will be disabled even in GUI
 */
#[ORM\Table(name: 'dmkclub_member_proposal_addr')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity]
#[Config(
    defaultValues: [
        'entity' => [
            'icon' => 'fa-map-marker',
        ],
        'activity' => [
            'immutable' => true,
        ],
        'attachment' => [
            'immutable' => true,
        ],
    ],
)]
class MemberProposalAddress extends ExtendMemberProposalAddress
{

    public function __construct()
    {
        parent::__construct();
    }

}
