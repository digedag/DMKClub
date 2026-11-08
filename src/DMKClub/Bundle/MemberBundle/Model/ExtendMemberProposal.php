<?php

namespace DMKClub\Bundle\MemberBundle\Model;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

/**
 *
 * @method \Oro\Bundle\EntityExtendBundle\Entity\EnumOptionInterface getStatus()
 * @method ExtendMember setStatus(\Oro\Bundle\EntityExtendBundle\Entity\EnumOptionInterface $status)
 * @method \Oro\Bundle\EntityExtendBundle\Entity\EnumOptionInterface getPaymentOption()
 * @method ExtendMember setPaymentOption(\Oro\Bundle\EntityExtendBundle\Entity\EnumOptionInterface $status)
 * @method \Oro\Bundle\EntityExtendBundle\Entity\EnumOptionInterface getPaymentInterval()
 * @method ExtendMember setPaymentInterval(\Oro\Bundle\EntityExtendBundle\Entity\EnumOptionInterface $status)
 */
class ExtendMemberProposal implements ExtendEntityInterface 
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
