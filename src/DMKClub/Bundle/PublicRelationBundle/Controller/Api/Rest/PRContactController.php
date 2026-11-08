<?php

namespace DMKClub\Bundle\PublicRelationBundle\Controller\Api\Rest;

use Symfony\Component\HttpFoundation\JsonResponse;



use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Symfony\Component\HttpFoundation\Response;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;

class PRContactController extends RestController
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'dmkclub.prcontact.manager.api' => ApiEntityManager::class,
        ]);
    }
    /**
     * REST DELETE
     *
     * @param int $id
     *
     * 
     * @return Response
     */
    #[Acl(id: 'dmkclub_prcontact_delete', type: 'entity', permission: 'DELETE', class: \DMKClub\Bundle\PublicRelationBundle\Entity\PRContact::class)]
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * Get entity Manager
     *
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->container->get('dmkclub.prcontact.manager.api');
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        throw new \BadMethodCallException('FormInterface is not available.');
    }

    /**
     * {@inheritdoc}
     */
    public function getFormHandler()
    {
        throw new \BadMethodCallException('FormHandler is not available.');
    }
}
