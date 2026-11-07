<?php

namespace DMKClub\Bundle\MemberBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use DMKClub\Bundle\MemberBundle\Model\ExtendMemberProposalAddress;

/**
 * @ORM\Table("dmkclub_member_proposal_addr")
 * @ORM\HasLifecycleCallbacks()
 * With immutable=true the annotated features will be disabled even in GUI
 * @ORM\Entity
 */
#[Config(
    defaultValues: [
        'entity' => [
            'icon' => 'fa-map-marker',
        ],
        'note' => [
            'immutable' => true,
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
