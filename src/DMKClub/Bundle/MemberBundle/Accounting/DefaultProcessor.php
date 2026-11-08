<?php
namespace DMKClub\Bundle\MemberBundle\Accounting;

use DateTime;
use DMKClub\Bundle\MemberBundle\Accounting\Time\TimeCalculator;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition;
use DMKClub\Bundle\MemberBundle\Form\Type\DefaultProcessorSettingsType;
use DMKClub\Bundle\MemberBundle\Model\AgePrice;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 */
class DefaultProcessor extends AbstractProcessor
{

    const NAME = 'default';

    const OPTION_FEE = 'fee';
    const OPTION_FEE_ADMISSION = 'fee_admission';
    const OPTION_FEE_DISCOUNT = 'fee_discount';

    /** @deprecated */
    const OPTION_FEE_CHILD = 'fee_child';
    /** @deprecated */
    const OPTION_AGE_CHILD = 'age_child';

    const OPTION_FEE_AGES = 'fee_ages';
    const OPTION_FEE_AGE_FROM = 'fee_age_from';
    const OPTION_FEE_AGE_TO = 'fee_age_to';
    const OPTION_FEE_AGE_VALUE = 'fee_age_value';
    /* Höherer Beitrag im Monat des Geburtstags */
    const OPTION_FEE_AGE_RAISE_ON_BIRTHDAY = 'fee_age_raise_on_birthday';

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    private $ageCalculator;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $em, AgeCalculator $ageCalculator)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->ageCalculator = $ageCalculator;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::getFields()
     */
    public function getFields()
    {
        return [
            self::OPTION_FEE,
            self::OPTION_FEE_DISCOUNT,
            self::OPTION_FEE_ADMISSION,
            self::OPTION_FEE_AGE_RAISE_ON_BIRTHDAY,
            self::OPTION_FEE_AGES,
        ];
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getSettingsFormType()
    {
        return DefaultProcessorSettingsType::class;
    }

    /*
     * (non-PHPdoc)
     * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::execute()
     */
    public function execute(Member $member)
    {
        $this->assertMember($member);

        $memberBilling = $this->getMemberBilling();
        $labelMap = $memberBilling->getPositionLabelMap();
        // Monate ermitteln
        $startDate = $memberBilling->getStartDate();

        $endDate = $memberBilling->getEndDate();
        $calculator = new TimeCalculator();
        $months = $calculator->calculateTimePeriods($startDate, $endDate);

        $feeFull = (int) $this->getOption(self::OPTION_FEE);
        $feeDiscount = (int) $this->getOption(self::OPTION_FEE_DISCOUNT);
        $feeAdmission = (int) $this->getOption(self::OPTION_FEE_ADMISSION);
        $feeAgeRaiseOnBirthday = (bool) $this->getOption(self::OPTION_FEE_AGE_RAISE_ON_BIRTHDAY);
        $agePrices = $this->getAgePrices();

        $fee = 0;
        // Über jeden Monat iterieren, den erste und den letzten Monat ?merken $firstMonth2Pay = null;
        $lastMonth2Pay = null;
        /* @var $currentMonth \DateTime */
        $currentMonthFirstDay = $this->newDate($startDate->format('Y-m-d'));
        // $currentMonthLastDay = $calculator->getLastDayInMonth($currentMonthFirstDay);
        foreach ($months as $interval) {

            // War das Mitglied in dem Monat Mitglied?
            /* @var $interval \DateInterval */
            if ($this->isMembershipActive($member, $currentMonthFirstDay)) {
                if ($firstMonth2Pay === null) {
                    $firstMonth2Pay = clone $currentMonthFirstDay;
                }
                $lastMonth2Pay = clone $currentMonthFirstDay;
                $periodFee = $feeFull;
                $agePrice = $this->lookupAgePrice($member, $currentMonthFirstDay, $agePrices, $feeAgeRaiseOnBirthday);
                $hasDiscount = $this->isMembershipDiscount($member, $currentMonthFirstDay);

                if ($agePrice && (!$hasDiscount || $agePrice->getFeeAgeValue() < $feeDiscount )) {
                    $periodFee = $agePrice->getFeeAgeValue();
                }
                elseif ($hasDiscount) {
                    $periodFee = $feeDiscount;
                }
                $fee += $periodFee;
            }
            $currentMonthFirstDay = $currentMonthFirstDay->add($interval);
            // $currentMonthLastDay = $calculator->getLastDayInMonth($currentMonthFirstDay);
        }
        // Enddatum auf den Monatsletzten setzen
        if ($lastMonth2Pay) {
            $lastMonth2Pay->add($interval);
            $lastMonth2Pay->sub(new \DateInterval('P1D'));
        }

        $this->writeLog("Fee: " . $fee . " from " . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'));

        // Bei unterjährigem Ein- und Austritt das passende Datum verwenden
        $labelStartDate = $firstMonth2Pay ? $firstMonth2Pay : $startDate;
        $labelEndDate = $lastMonth2Pay ? $lastMonth2Pay : $endDate;
        // $descriptionFeePosition = 'Beitrag vom [STARTDATE] bis [ENDDATE]';
        $descriptionFeePosition = isset($labelMap[MemberFeePosition::FLAG_FEE]) ? $labelMap[MemberFeePosition::FLAG_FEE] : 'MemberFeePosition::FLAG_FEE';
        $descriptionFeePosition = $this->prepareDescriptionFeePosition($descriptionFeePosition, $labelStartDate, $labelEndDate);

        $memberFee = $this->createMemberFee($member, $memberBilling, $labelStartDate, $labelEndDate);

        $position = new MemberFeePosition();
        $memberFee->addPosition($position);

        $position->setDescription($descriptionFeePosition);
        $position->setQuantity(1);
        $position->setPriceSingle($fee);
        $position->setPriceTotal($fee);
        $position->setFlag(MemberFeePosition::FLAG_FEE);

        // Aufnahmegebühr
        if ($feeAdmission > 0 && $this->isNewMembership($member, $startDate, $endDate)) {
            // Ist das Mitglied im Berechnungszeitraum neu eingetreten
            $label = isset($labelMap[MemberFeePosition::FLAG_ADMISSON]) ? $labelMap[MemberFeePosition::FLAG_ADMISSON] : 'MemberFeePosition::FLAG_ADMISSON';
            $position = new MemberFeePosition();
            $memberFee->addPosition($position);
            $position->setDescription($label);
            $position->setQuantity(1);
            $position->setPriceSingle($feeAdmission);
            $position->setPriceTotal($feeAdmission);
            $position->setFlag(MemberFeePosition::FLAG_ADMISSON);
        }

        $memberFee->updatePriceTotal();

        return $memberFee;
    }

    /**
     *
     * @return AgePrice[]
     */
    private function getAgePrices(): array
    {
        $agePrices = [];
        $pricesData = (array) $this->getOption(self::OPTION_FEE_AGES);
        foreach ($pricesData as $priceData) {
            $agePrices[] = $priceData instanceof AgePrice ? $priceData : new AgePrice($priceData);
        }
        return $agePrices;
    }

    private function isNewMembership($member, $startDate, $endDate)
    {
        return $member->getStartDate() >= $startDate && $member->getStartDate() <= $endDate;
    }

    /**
     *
     * @param Member $member
     * @param DateTime $currentMonth
     * @param AgePrice[] $agePrices
     * @param bool $feeAgeRaiseOnBirthday
     * @return AgePrice|null
     */
    private function lookupAgePrice(Member $member, DateTime $currentMonth, array $agePrices, $feeAgeRaiseOnBirthday): ?AgePrice
    {
        if (empty($agePrices)) {
            return null;
        }
        // currentMonth steht immer auf dem 1. des Monats. Wer in dem
        // Monat 18 wird, ist also am 1. noch 17 Jahre alt.
        // Der volle Beitrag gilt erst im Folgemonat
        if (! $member->getContact()) {
            return null;
        }
        $birthday = $member->getContact()->getBirthday();
        if (! $birthday) {
            return null;
        }
        $age = $this->ageCalculator->getAgeInMonth($currentMonth, $birthday, $feeAgeRaiseOnBirthday);

        foreach ($agePrices as $agePrice) {
            if ($age >= $agePrice->getFeeAgeFrom() && $age <= $agePrice->getFeeAgeTo()) {
                return $agePrice;
            }
        }
        return null;
    }

    /**
     * Is member discount active in current month
     *
     * @param Member $member
     * @param \DateTime $currentMonthLastDay
     */
    protected function isMembershipDiscount(Member $member, $currentMonthLastDay)
    {
        foreach ($member->getMemberFeeDiscounts() as $feeDiscount) {
            /* @var $feeDiscount \DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount */
            // Ist das Datum in $month innerhalb der Discount-Zeit?
            if ($feeDiscount->contains($currentMonthLastDay)) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository
     */
    public function getMemberRepository()
    {
        return $this->em->getRepository(Member::class);
    }

    public function formatSettings(array $options)
    {
        $ret = [];
        foreach ($options as $key => $value) {
            if ($key == self::OPTION_FEE || $key == self::OPTION_FEE_CHILD || $key == self::OPTION_FEE_DISCOUNT || $key == self::OPTION_FEE_ADMISSION) {
                $value = number_format($value / 100, 2);
            } elseif ($key === self::OPTION_FEE_AGES) {
                $data = [];
                foreach ($value as $agePrice) {
                    /* @var $agePrice AgePrice */
                    $price = number_format($agePrice->getFeeAgeValue() / 100, 2);
                    $data[] = sprintf('%02d - %02d: %s', $agePrice->getFeeAgeFrom(), $agePrice->getFeeAgeTo(), $price);
                }
                $value = $data;
            }
            $ret[$key] = $value;
        }
        return $ret;
    }

    /**
     *
     * {@inheritDoc}
     * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::prepareFormData()
     */
    public function prepareFormData(array $storedData): array
    {
        $result = parent::prepareFormData($storedData);
        if (!empty($result[self::OPTION_FEE_AGES])) {
            $agePrices = [];
            foreach ($result[self::OPTION_FEE_AGES] as $agePriceData) {
                $agePrice = new AgePrice();
                $agePrice->setFeeAgeFrom($agePriceData[self::OPTION_FEE_AGE_FROM]);
                $agePrice->setFeeAgeTo($agePriceData[self::OPTION_FEE_AGE_TO]);
                $agePrice->setFeeAgeValue($agePriceData[self::OPTION_FEE_AGE_VALUE]);
                $agePrices[] = $agePrice;
            }
            $result[self::OPTION_FEE_AGES] = $agePrices;
        }

        // Altdaten konvertieren
        if (array_key_exists(self::OPTION_FEE_CHILD, $storedData)) {
            $agePrice = new AgePrice();
            $agePrice->setFeeAgeValue(array_key_exists(self::OPTION_FEE_CHILD, $storedData) ? $storedData[self::OPTION_FEE_CHILD] : 0);
            $agePrice->setFeeAgeFrom(0);
            $agePrice->setFeeAgeTo(array_key_exists(self::OPTION_AGE_CHILD, $storedData) ? $storedData[self::OPTION_AGE_CHILD] - 1 : 13);
            $result[self::OPTION_FEE_AGES][] = $agePrice;
        }

        return $result;
    }

    public function prepareStoredData(array $formData): array
    {
        $ageGroups = [];
        $ageGroupsForm = $formData[self::OPTION_FEE_AGES];
        foreach ($ageGroupsForm as $ageGroupForm) {
            /* @var $ageGroupForm AgePrice */
            if (!$ageGroupForm->isEmpty()) {
                $ageGroups[] = [
                    self::OPTION_FEE_AGE_FROM => (int) $ageGroupForm->getFeeAgeFrom(),
                    self::OPTION_FEE_AGE_TO => (int) $ageGroupForm->getFeeAgeTo(),
                    self::OPTION_FEE_AGE_VALUE => (int) $ageGroupForm->getFeeAgeValue(),
                ];
            }
        }
        $formData[self::OPTION_FEE_AGES] = $ageGroups;
        return $formData;
    }

    private function writeLog($message)
    {
        $this->logger->info($message);
    }
}
