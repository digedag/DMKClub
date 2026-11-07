<?php

namespace DMKClub\Bundle\MemberBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use DMKClub\Bundle\MemberBundle\Form\Handler\MemberFeeDiscountHandler;
use Symfony\Component\Form\Form;

class MemberFeeDiscountController extends AbstractController
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'doctrine' => ManagerRegistry::class,
            TranslatorInterface::class,
            MemberFeeDiscountHandler::class,
            'dmkclub_member.memberfeediscount.form' => Form::class,
        ]);
    }

	/**
	 * @Route("/info-block/{id}", name="dmkclub_member_memberfeediscount_infoblock", requirements={"id"="\d+"})
	 * @Template
	 */
	#[AclAncestor('dmkclub_member_view')]
	public function infoBlockAction(Member $contact)
	{
		return [
			'entity' => $contact,
			'memberfeediscount_edit_acl_resource' => 'dmkclub_member_update'
		];
	}

	/**
	 * @Route(
	 *      "/{memberId}/memberfeediscount-create",
	 *      name="dmkclub_member_memberfeediscount_create",
	 *      requirements={"memberId"="\d+"}
	 * )
	 * @Template("DMKClubMemberBundle:MemberFeeDiscount:update.html.twig")
	 * @ParamConverter("member", options={"id" = "memberId"})
	 */
	#[AclAncestor('dmkclub_member_create')]
	public function createAction(Member $member)
	{
		$discount = new MemberFeeDiscount();
		$member->addMemberFeeDiscount($discount);

		// Update member's modification date when an address is changed
		$member->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));

		return $this->update($member, $discount);
	}

	/**
	 * @Route(
	 *      "/{memberId}/memberfeediscount-update/{id}",
	 *      name="dmkclub_member_memberfeediscount_update",
	 *      requirements={"memberId"="\d+","id"="\d+"},defaults={"id"=0}
	 * )
	 * @Template
	 * @ParamConverter("member", options={"id" = "memberId"})
	 */
	#[AclAncestor('dmkclub_member_update')]
	public function updateAction(Member $member, MemberFeeDiscount $discount)
	{
		return $this->update($member, $discount);
	}

	/**
	 * @param Member $member
	 * @param MemberFeeDiscount $discount
	 * @return array
	 * @throws BadRequestHttpException
	 */
	protected function update(Member $member, MemberFeeDiscount $discount)
	{
		$responseData = [
			'saved' => false,
			'member' => $member
		];


		if ($this->container->get(MemberFeeDiscountHandler::class)->process($discount)) {
			$this->container->get('doctrine')->getManager()->flush();
			$responseData['entity'] = $discount;
			$responseData['saved'] = true;
		}
		/* @var $form \Symfony\Component\Form\Form */
		$form = $this->container->get('dmkclub_member.memberfeediscount.form');
//		$form->setData($discount);
		$responseData['form'] = $form->createView();
		return $responseData;
	}
}
