<?php

namespace DMKClub\Bundle\PaymentBundle\Controller\Api\Rest;

use BadMethodCallException;



use Symfony\Component\HttpFoundation\Response;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;

class SepaCreditorController extends RestController
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'dmkclub.payment.sepacreditor.manager.api' => ApiEntityManager::class,
        ]);
    }
    /**
     * @param int $id
     *
     * 
     * @return Response
     */
    #[Acl(id: 'dmkclub_payment_sepacreditor_delete', type: 'entity', permission: 'DELETE', class: \DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor::class)]
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormHandler()
    {
        throw new BadMethodCallException('This method is unsupported.');
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->container->get('dmkclub.payment.sepacreditor.manager.api');
    }
}
