<?php

namespace DMKClub\Bundle\MemberBundle\Controller\Api\Rest;

use BadMethodCallException;



use Symfony\Component\HttpFoundation\Response;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;

class MemberFeeDiscountController extends RestController
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'dmkclub_member.memberfeediscount.manager.api' => ApiEntityManager::class,
        ]);
    }
    /**
     * @param int $id
     *
     * 
     * Der Discount hat keine Entity-ACL. Wir nutzen die ACL des MemberFee
     * @return Response
     */
    #[Acl(id: 'dmkclub_member_memberfee_delete', type: 'entity', permission: 'DELETE', class: \DMKClub\Bundle\MemberBundle\Entity\MemberFee::class)]
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
        return $this->container->get('dmkclub_member.memberfeediscount.manager.api');
    }
}
