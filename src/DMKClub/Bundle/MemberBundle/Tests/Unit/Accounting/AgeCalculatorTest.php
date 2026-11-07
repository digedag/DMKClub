<?php
namespace DMKClub\Bundle\MemberBundle\Tests\Unit\Accounting;

use DMKClub\Bundle\MemberBundle\Accounting\DefaultProcessor;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Psr\Log\NullLogger;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use DMKClub\Bundle\MemberBundle\Accounting\AgeCalculator;

class AgeCalculatorTest extends TestCase
{

    private $calculator;

    public function setUp(): void
    {
        $this->calculator = new AgeCalculator();
    }

    #[DataProvider('dataProvider')]
    public function testGetAgeInMonth($currentMonth, $birthday, $raiseOnBirthday, $expected)
    {
        $age = $this->calculator->getAgeInMonth($currentMonth, $birthday, $raiseOnBirthday);

        $this->assertEquals($expected, $age);
    }

    public static function dataProvider()
    {
        return [
            [ new \DateTime('2016-07-01'), new \DateTime('2016-05-13'), false, 0],
            [ new \DateTime('2016-07-01'), new \DateTime('1970-05-13'), false, 46],
            [ new \DateTime('2016-07-01'), new \DateTime('1970-07-01'), false, 46],
            [ new \DateTime('2016-07-01'), new \DateTime('1970-07-02'), false, 45],
            [ new \DateTime('2016-08-01'), new \DateTime('1970-07-01'), false, 46],
            [ new \DateTime('2016-08-01'), new \DateTime('1970-07-02'), false, 46],

            [ new \DateTime('2016-07-01'), new \DateTime('1970-07-01'), true, 46],
            [ new \DateTime('2016-07-01'), new \DateTime('1970-07-02'), true, 46],

        ];
    }

    /**
     *
     * @param string $start
     *            Eintrittsdatum in den Verein
     * @param string $end
     *            Austrittsdatum aus dem Verein
     * @param string $birthday
     *            Geburtstag
     * @param array[MemberFeeDiscount] $discounts
     *            Zeiträume
     * @return
     */
    protected function buildMember($start, $end, $birthday, array $discounts = array())
    {
        $contact = new Contact();
        $contact->setBirthday(new \DateTime($birthday));
        $member = new Member();
        $member->setContact($contact);
        $member->setStartDate(new \DateTime($start));
        $member->setEndDate($end ? new \DateTime($end) : NULL);

        if (is_array($discounts)) {
            foreach ($discounts as $discount) {
                $member->addMemberFeeDiscount($discount);
            }
        }
        return $member;
    }

    /**
     * Erstellt Instanzen von MemberFeeDiscount
     *
     * @param string $start
     *            Startzeitpunkt der Ermäßigung
     * @param string $end
     *            Ende der Ermäßigung oder NULL
     */
    protected function buildMemberFeeDiscount($start, $end)
    {
        $discount = new MemberFeeDiscount();
        $discount->setStartDate(new \DateTime($start));
        $discount->setEndDate($end ? new \DateTime($end) : NULL);
        return $discount;
    }

    protected function getEMMockBuilder()
    {
        return $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor();
    }
}
