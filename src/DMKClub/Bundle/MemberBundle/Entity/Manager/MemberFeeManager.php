<?php
namespace DMKClub\Bundle\MemberBundle\Entity\Manager;

use Doctrine\ORM\EntityManagerInterface;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;

class MemberFeeManager
{

    /**
     *
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Set correction status for MemberFee
     *
     * @param MemberFee $entity
     * @param bool $isSeen
     * @param bool $flush
     *            - if true then method executes flush
     */
    public function setFeeCorrectionStatus(MemberFee $entity, $enableCorrection = true, $flush = false)
    {
        if ($entity->getCorrectionStatus() !== $enableCorrection) {
            $entity->setCorrectionStatus($enableCorrection ? MemberFee::CORRECTION_STATUS_OPEN : MemberFee::CORRECTION_STATUS_NONE);
            if ($flush) {
                $this->em->flush();
            }
        }
    }

    /**
     *
     * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository
     */
    public function getMemberRepository()
    {
        return $this->em->getRepository(Member::class);
    }

    /**
     *
     * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberFeeRepository
     */
    public function getMemberFeeRepository()
    {
        return $this->em->getRepository(MemberFee::class);
    }

    /**
     *
     * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberBillingRepository
     */
    public function getMemberBillingRepository()
    {
        return $this->em->getRepository(MemberBilling::class);
    }
}
