<?php

namespace DMKClub\Bundle\MemberBundle\Model;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

use DMKClub\Bundle\PaymentBundle\Entity\AbstractBankAccount;

class ExtendMemberProposalBankAccount extends AbstractBankAccount implements ExtendEntityInterface 
{
    use ExtendEntityTrait;

    /**
     * Constructor
     *
     * The real implementation of this method is auto generated.
     *
     * IMPORTANT: If the derived class has own constructor it must call parent constructor.
     */
    public function __construct()
    {
    }
}
