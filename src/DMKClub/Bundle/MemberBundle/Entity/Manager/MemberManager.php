<?php
namespace DMKClub\Bundle\MemberBundle\Entity\Manager;

use Doctrine\ORM\EntityManagerInterface;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberProposal;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\ContactBundle\Entity\ContactEmail;
use Oro\Bundle\ContactBundle\Entity\ContactPhone;
use Oro\Bundle\AddressBundle\Entity\Address;
use DMKClub\Bundle\PaymentBundle\Entity\BankAccount;
use DMKClub\Bundle\MemberBundle\Model\MemberStatus;
use DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount;

class MemberManager
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
     * Update member status to terminated for all members with end date in past.
     *
     * @return number of newly resigned members
     */
    public function checkResignedMembers()
    {
        $now = new \DateTime();
        $qb = $this->getMemberRepository()->createQueryBuilder('m');
        $qb->where('m.endDate IS NOT NULL');
        $qb->andWhere('m.endDate < :today');
        $qb->andWhere('m.status != :status');

        $qb->setParameter('today', $now->format('c'));
        $qb->setParameter('status', 'terminated');
        $q = $qb->getQuery();
        $result = $q->iterate();
        $cnt = 0;
        foreach ($result as $row) {
            /* @var $mbr \DMKClub\Bundle\MemberBundle\Entity\Member */
            $mbr = $row[0];
            $mbr->setStatus(MemberStatus::TERMINATED);
            $this->em->persist($mbr);
            $cnt++;
        }
        $this->em->flush();

        return $cnt;
    }

    /**
     * The next possible memberCode
     *
     * @return number
     */
    public function nextMemberCode()
    {
        $qb = $this->getMemberRepository()->createQueryBuilder('m');
        $qb->select('max(m.memberCodeInt) mc');
        $data = $qb->getQuery()->getArrayResult();
        $data = reset($data);
        return (int) ($data['mc'] + 1);
    }

    /**
     *
     * @param MemberProposal $entity
     * @return \DMKClub\Bundle\MemberBundle\Entity\Member
     */
    public function buildMemberByProposal(MemberProposal $entity)
    {
        $member = new Member();
        $contact = new Contact();
        // contact
        if ($entity->getEmail()) {
            $email = new ContactEmail($entity->getEmail());
            $email->setPrimary(true);
            $contact->addEmail($email);
        }
        if ($entity->getPhone()) {
            $phone = new ContactPhone($entity->getPhone());
            $phone->setPrimary(true);
            $contact->addPhone($phone);
        }
        if ($proposalAddress = $entity->getPostalAddress()) {
            $address = new Address();
            $address->setCity($entity->getPostalAddress()
                ->getCity());
            $address->setCountry($entity->getPostalAddress()
                ->getCountry());

            $useMemberName = (! $proposalAddress->getFirstName() && ! $proposalAddress->getLastName());
            $address->setFirstName($useMemberName ? $entity->getFirstName() : $proposalAddress->getFirstName());
            $address->setLabel($entity->getPostalAddress()
                ->getLabel());
            $address->setLastName($useMemberName ? $entity->getLastName() : $proposalAddress->getLastName());
            $address->setMiddleName($useMemberName ? $entity->getMiddleName() : $proposalAddress->getMiddleName());
            $address->setNamePrefix($useMemberName ? $entity->getNamePrefix() : $proposalAddress->getNamePrefix());
            $address->setNameSuffix($useMemberName ? $entity->getNameSuffix() : $proposalAddress->getNameSuffix());
            $address->setPostalCode($entity->getPostalAddress()
                ->getPostalCode());
            $address->setRegion($entity->getPostalAddress()
                ->getRegion());
            $address->setRegionText($entity->getPostalAddress()
                ->getRegionText());
            $address->setStreet($entity->getPostalAddress()
                ->getStreet());
            $address->setStreet2($entity->getPostalAddress()
                ->getStreet2());
            $address->setCity($entity->getPostalAddress()
                ->getCity());

            $member->setPostalAddress($address);
        }
        if ($entity->getBankAccount()) {
            $bankAccount = new BankAccount();
            $bankAccount->setAccountOwner($entity->getBankAccount()
                ->getAccountOwner());
            $bankAccount->setBankName($entity->getBankAccount()
                ->getBankName());
            $bankAccount->setBic($entity->getBankAccount()
                ->getBic());
            $bankAccount->setIban($entity->getBankAccount()
                ->getIban());
            $bankAccount->setDirectDebitValidFrom($entity->getBankAccount()
                ->getDirectDebitValidFrom());

            $member->setBankAccount($bankAccount);
        }
        if ($entity->getDiscountStartDate()) {
            $feeDiscount = new MemberFeeDiscount();
            $feeDiscount->setStartDate($entity->getDiscountStartDate());
            $feeDiscount->setEndDate($entity->getDiscountEndDate());
            $feeDiscount->setReason($entity->getDiscountReason());

            $member->addMemberFeeDiscount($feeDiscount);
        }
        $contact->setBirthday($entity->getBirthday())
            ->setFirstName($entity->getFirstName())
            ->setLastName($entity->getLastName())
            ->setJobTitle($entity->getJobTitle())
            ->setMiddleName($entity->getMiddleName())
            ->setNamePrefix($entity->getNamePrefix())
            ->setNameSuffix($entity->getNameSuffix());

        $member->setContact($contact);
        $member->setStatus(MemberStatus::ACTIVE);
        $member->setPaymentOption($entity->getPaymentOption());
        $member->setPaymentInterval($entity->getPaymentInterval());
        $member->setIsActive($entity->getIsActive());
        $member->setDataChannel($entity->getDataChannel());
        $member->setName($contact->getFirstName() . ' ' . $contact->getLastName());

        return $member;
    }

    /**
     *
     * @param Contact $contact
     *
     * @return Member|NULL
     */
    public function findMemberByContact(Contact $contact)
    {
        return $this->getMemberRepository()->findOneBy([
            'contact' => $contact->getId()
        ]);
    }

    /**
     *
     * @return MemberRepository
     */
    public function getMemberRepository()
    {
        return $this->em->getRepository(Member::class);
    }

}
