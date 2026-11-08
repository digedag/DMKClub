<?php
namespace DMKClub\Bundle\MemberBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Form\Form;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberPrivacy;
use DMKClub\Bundle\MemberBundle\Form\Handler\MemberPrivacyHandler;

class MemberPrivacyController extends AbstractController
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'doctrine' => ManagerRegistry::class,
            TranslatorInterface::class,
            MemberPrivacyHandler::class,
            'dmkclub_member.memberprivacy.form' => Form::class,
        ]);
    }

    #[Route('/widget/privacyinfo/{id}', name: 'dmkclub_member_memberprivacy_widget_infoblock', options: ['expose' => true], requirements: ['id' => '\d+'])]
    #[Template]
    #[AclAncestor('dmkclub_member_view')]
    public function privacyInfoAction(Member $member)
    {
        return [
            'entity' => $member->getPrivacy()
        ];
    }

    #[Route('/{memberId}/memberprivacy-update', options: ['expose' => true], name: 'dmkclub_member_memberprivacy_update', requirements: ['memberId' => '\d+', 'id' => '\d+'])]
    #[Template]
    #[ParamConverter('member', options: ['id' => 'memberId'])]
    #[AclAncestor('dmkclub_member_update')]
    public function updateAction(Member $member, MemberPrivacy $privacy)
    {
        return $this->update($member, $privacy);
    }

    /**
     *
     * @param Member $member
     * @param MemberPrivacy $privacy
     * @return array
     * @throws BadRequestHttpException
     */
    protected function update(Member $member, MemberPrivacy $privacy)
    {
        $responseData = [
            'saved' => false,
            'member' => $member
        ];

        if ($this->container->get(MemberPrivacyHandler::class)->process($privacy)) {
            $this->container->get('doctrine')->getManager()->flush();
            $responseData['entity'] = $privacy;
            $responseData['saved'] = true;
        }
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->container->get('dmkclub_member.memberprivacy.form');
        $responseData['form'] = $form->createView();
        return $responseData;
    }
}
