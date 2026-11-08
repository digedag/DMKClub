<?php
namespace DMKClub\Bundle\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use DMKClub\Bundle\PaymentBundle\Form\Handler\SepaCreditorHandler;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/sepacreditor')]
class SepaCreditorController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class,
            SepaCreditorHandler::class,
            'dmkclub_payment.sepacreditor.form' => Form::class,
            UpdateHandlerFacade::class,
        ]);
    }

    #[Route('/', name: 'dmkclub_sepacreditor_index')]
    #[Template]
    #[AclAncestor('dmkclub_sepacreditor_view')]
    public function indexAction()
    {
        return [
            'entity_class' => SepaCreditor::class,
        ];
    }

    /**
     * Create sepacreditor form
     */
    #[Route('/create', name: 'dmkclub_sepacreditor_create')]
    #[Template('DMKClubPaymentBundle:SepaCreditor:update.html.twig')]
    #[Acl(id: 'dmkclub_sepacreditor_create', type: 'entity', permission: 'CREATE', class: \DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor::class)]
    public function createAction(Request $request)
    {
        return $this->update(new SepaCreditor(), $request);
    }

    /**
     * Update sepacreditor form
     */
    #[Route('/update/{id}', name: 'dmkclub_sepacreditor_update', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    #[Template]
    #[Acl(id: 'dmkclub_sepacreditor_update', type: 'entity', permission: 'EDIT', class: \DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor::class)]
    public function updateAction(SepaCreditor $entity, Request $request)
    {
        return $this->update($entity, $request);
    }

    /**
     *
     * @param SepaCreditor $entity
     *
     * @return array
     */
    protected function update(SepaCreditor $entity, Request $request)
    {
        return $this->container->get(UpdateHandlerFacade::class)->update(
            $entity,
            $this->container->get('dmkclub_payment.sepacreditor.form'),
            $this->container->get(TranslatorInterface::class)->trans('dmkclub.payment.sepacreditor.saved.message'),
            $request,
            $this->container->get(SepaCreditorHandler::class)
        );
    }

    #[Route('/view/{id}', name: 'dmkclub_sepacreditor_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'dmkclub_sepacreditor_view', type: 'entity', permission: 'VIEW', class: \DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor::class)]
    public function viewAction(SepaCreditor $entity)
    {
        return [
            'entity' => $entity
        ];
    }

    #[Route('/widget/info/{id}', name: 'dmkclub_sepacreditor_widget_info', requirements: ['id' => '\d+'])]
    #[Template]
    #[AclAncestor('dmkclub_sepacreditor_view')]
    public function infoAction(SepaCreditor $entity)
    {
        return [
            'entity' => $entity
        ];
    }
}
