<?php
namespace DMKClub\Bundle\MemberBundle\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Form;
use Symfony\Contracts\Translation\TranslatorInterface;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Form\Handler\CreateBillsHandler;
use DMKClub\Bundle\MemberBundle\Form\Handler\MemberBillingHandler;

#[Route('/memberbilling')]
class MemberBillingController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class,
            MemberBillingManager::class,
            MemberBillingHandler::class,
            CreateBillsHandler::class,
            'dmkclub_member.createbills.form' => Form::class,
            'dmkclub_member.memberbilling.form' => Form::class,
            UpdateHandlerFacade::class,
        ]);
    }

    #[Route('/', name: 'dmkclub_memberbilling_index')]
    #[Template]
    #[AclAncestor('dmkclub_memberbilling_view')]
    public function indexAction()
    {
        return [
            'entity_class' => MemberBilling::class,
        ];
    }

    /**
     * Create member form
     */
    #[Route('/create', name: 'dmkclub_memberbilling_create')]
    #[Template('DMKClubMemberBundle:MemberBilling:update.html.twig')]
    #[Acl(id: 'dmkclub_memberbilling_create', type: 'entity', permission: 'CREATE', class: \DMKClub\Bundle\MemberBundle\Entity\MemberBilling::class)]
    public function createAction()
    {
        $billing = new MemberBilling();
        $billing->setPositionLabels($this->container->get(TranslatorInterface::class)->trans('dmkclub.member.memberbilling.position_labels.default'));
        return $this->update($billing);
    }

    /**
     * Update memberbilling form
     */
    #[Route('/update/{id}', name: 'dmkclub_memberbilling_update', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    #[Template]
    #[Acl(id: 'dmkclub_memberbilling_update', type: 'entity', permission: 'EDIT', class: \DMKClub\Bundle\MemberBundle\Entity\MemberBilling::class)]
    public function updateAction(MemberBilling $entity)
    {
        return $this->update($entity);
    }

    /**
     *
     * @param MemberBilling $entity
     *
     * @return array
     */
    protected function update(MemberBilling $entity)
    {
        /* @var $handler  \Oro\Bundle\FormBundle\Model\UpdateHandlerFacade */
        $handler = $this->container->get(UpdateHandlerFacade::class);
        return $handler->update(
            $entity,
            $this->container->get('dmkclub_member.memberbilling.form'),
            $this->container->get(TranslatorInterface::class)->trans('dmkclub.member.memberbilling.message.saved'),
            null,
            $this->container->get(MemberBillingHandler::class)
        );
    }

    #[Route('/view/{id}', name: 'dmkclub_memberbilling_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'dmkclub_memberbilling_view', type: 'entity', permission: 'VIEW', class: \DMKClub\Bundle\MemberBundle\Entity\MemberBilling::class)]
    public function viewAction(MemberBilling $entity)
    {
        $options = $this->getBillingManager()->getProcessorSettings($entity);
        $options = $this->getBillingManager()
            ->getProcessor($entity)
            ->formatSettings($options);
        $entity->setPayedTotal($this->getBillingManager()
            ->getPaidTotal($entity));

        return [
            'entity' => $entity,
            'options' => $options
        ];
    }

    /**
     *
     * @return MemberBillingManager
     */
    protected function getBillingManager()
    {
        return $this->container->get(MemberBillingManager::class);
    }

    /**
     * Wird zur Anzeige des Info-Widgets in der Detailansicht verwendet
     */
    #[Route('/widget/info/{id}', name: 'dmkclub_memberbilling_widget_info', requirements: ['id' => '\d+'])]
    #[Template]
    #[AclAncestor('dmkclub_memberbilling_view')]
    public function infoAction(MemberBilling $entity)
    {
        return [
            'entity' => $entity
        ];
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function getCreateBillsForm()
    {
        return $this->container->get('dmkclub_member.createbills.form');
    }
    #[Route('/{id}/createbills', name: 'dmkclub_memberbilling_createbills', requirements: ['id' => '\d+'])]
    #[Template]
    #[AclAncestor('dmkclub_memberbilling_create')]
    public function createBillsAction(MemberBilling $entity)
    {
        $form = $this->getCreateBillsForm();
        $response = [
            'entity' => $entity,
            'saved' => false,
            'form' => $form->createView(),
        ];

        // Form auswerten
        if ($ret = $this->container->get(CreateBillsHandler::class)->process($entity)) {
            $response['message'] = $this->buildMessage('dmkclub.member.memberbilling.message.accounting' . ($ret['async'] ? '.async' : '') . '.started', $ret);
            $response['saved'] = true;
        }

        return $response;
    }

    #[Route('/{id}/refreshsummary', name: 'dmkclub_memberbilling_refreshsummary', requirements: ['id' => '\d+'])]
    #[Template]
    #[AclAncestor('dmkclub_memberbilling_create')]
    public function refreshSummaryAction(MemberBilling $entity)
    {
        // Info an den Manager übergeben
        $this->getBillingManager()->updateSummary($entity);

        return new RedirectResponse($this->generateUrl('dmkclub_memberbilling_view', [
            'id' => $entity->getId()
        ]));
    }

    #[Route('/recreatecorrections/{id}', name: 'dmkclub_memberbilling_createcorrections', requirements: ['id' => '\d+'])]
    #[Template]
    #[AclAncestor('dmkclub_memberbilling_create')]
    public function createCorrectionsAction(MemberBilling $entity)
    {
        // Info an den Manager übergeben
        $ret = $this->container->get(MemberBillingManager::class)->startCorrections($entity);

        $marked = ((int) $ret['success'] + (int) $ret['skipped']) > 0;
        $msgType = $marked ? 'success' : 'warning';
        $msg = 'dmkclub.member.memberbilling.message.correction.' . ($marked ? 'started' : 'nothingfound');
        $this->addFlash($msgType, $this->buildMessage($msg, $ret));
        return new RedirectResponse($this->generateUrl('dmkclub_memberbilling_view', [
            'id' => $entity->getId()
        ]));
    }

    private function buildMessage($msg, $info)
    {
        $msg = $this->container->get(TranslatorInterface::class)->trans($msg);
        return sprintf($msg, $info['success'], $info['skipped'], $info['errors']);
    }
}
