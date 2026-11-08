<?php

namespace DMKClub\Bundle\SponsorBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ChannelBundle\Entity\Channel;

use DMKClub\Bundle\SponsorBundle\Entity\Sponsor;
use Symfony\Contracts\Translation\TranslatorInterface;
use DMKClub\Bundle\SponsorBundle\Form\Handler\SponsorHandler;
use Symfony\Component\Form\Form;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;

#[Route('/sponsor')]
class SponsorController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'doctrine' => ManagerRegistry::class,
            TranslatorInterface::class,
            SponsorHandler::class,
            'dmkclub.sponsor.form' => Form::class,
            UpdateHandlerFacade::class,
        ]);
    }

	#[Route('/', name: 'dmkclub_sponsor_index')]
	#[Template]
	#[AclAncestor('dmkclub_sponsor_view')]
	public function indexAction()
	{
		return [
			'entity_class' => Sponsor::class,
		];
	}
    /**
     * Create sponsor form
     */
    #[Route('/create', name: 'dmkclub_sponsor_create')]
    #[Template('@DMKClubSponsor/Sponsor/update.html.twig')]
    #[Acl(id: 'dmkclub_sponsor_create', type: 'entity', permission: 'CREATE', class: \DMKClub\Bundle\SponsorBundle\Entity\Sponsor::class)]
    public function createAction() {
    	return $this->update(new Sponsor());
    }
    /**
     * Update sponsor form
     */
    #[Route('/update/{id}', name: 'dmkclub_sponsor_update', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    #[Template]
    #[Acl(id: 'dmkclub_sponsor_update', type: 'entity', permission: 'EDIT', class: \DMKClub\Bundle\SponsorBundle\Entity\Sponsor::class)]
    public function updateAction(Sponsor $entity)
    {
    	return $this->update($entity);
    }
    /**
     * @param Sponsor $entity
     *
     * @return array
     */
    protected function update(Sponsor $entity)
    {
        return $this->container->get(UpdateHandlerFacade::class)->update(
            $entity,
            $this->container->get('dmkclub.sponsor.form'),
            $this->container->get(TranslatorInterface::class)->trans('dmkclub.controller.sponsor.saved.message'),
    	    null,
            $this->container->get(SponsorHandler::class)
        );
    }

    #[Route('/view/{id}', name: 'dmkclub_sponsor_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'dmkclub_sponsor_view', type: 'entity', permission: 'VIEW', class: \DMKClub\Bundle\SponsorBundle\Entity\Sponsor::class)]
    public function viewAction(Sponsor $entity)
    {
        return ['entity' => $entity];
    }

    #[Route('/widget/info/{id}', name: 'dmkclub_sponsor_widget_info', requirements: ['id' => '\d+'])]
    #[Template]
    #[AclAncestor('dmkclub_sponsor_view')]
    public function infoAction(Sponsor $entity)
    {
        return [
            'entity' => $entity
        ];
    }

    /**
     * Wird aufgerufen, um im Account einen Abschnitt für die Sponsoren
     * einzublenden. Die Einbindung erfolgt über die placeholder.yml
     * Die Methode stellt die Sponsoren-Datensätze des aktuellen Accounts
     * im entsprechenden Channel bereit.
     * Die eigentlichen Datensätze werden dann in der Route
     * dmkclub_sponsor_widget_sponsor_info gerendert.
     */
    #[Route('/widget/sponsor-info/account/{accountId}/channel/{channelId}', name: 'dmkclub_sponsor_widget_account_sponsor_info', requirements: ['accountId' => '\d+', 'channelId' => '\d+'])]
    #[ParamConverter('account', class: \Oro\Bundle\AccountBundle\Entity\Account::class, options: ['id' => 'accountId'])]
    #[ParamConverter('channel', class: \Oro\Bundle\ChannelBundle\Entity\Channel::class, options: ['id' => 'channelId'])]
    #[Template]
    #[AclAncestor('orocrm_sales_b2bcustomer_view')]
    public function accountSponsorInfoAction(Account $account, Channel $channel)
    {
        $entities = $this->container->get('doctrine')->getRepository(Sponsor::class)
            ->findBy(['account' => $account, 'dataChannel' => $channel]);

        return ['account' => $account, 'sponsors' => $entities, 'channel' => $channel];
    }

    #[Route('/widget/sponsor-info/{id}/channel/{channelId}', name: 'dmkclub_sponsor_widget_sponsor_info', requirements: ['id' => '\d+', 'channelId' => '\d+'])]
    #[ParamConverter('channel', class: \Oro\Bundle\ChannelBundle\Entity\Channel::class, options: ['id' => 'channelId'])]
    #[Template]
    #[AclAncestor('orocrm_magento_customer_view')]
    public function sponsorInfoAction(Sponsor $entity, Channel $channel)
    {
        return [
            'sponsor'             => $entity,
            'channel'              => $channel,
//             'leadClassName'        => $this->container->getParameter('orocrm_sales.lead.entity.class'),
//             'opportunityClassName' => $this->container->getParameter('orocrm_sales.opportunity.class'),
        ];
    }
}
