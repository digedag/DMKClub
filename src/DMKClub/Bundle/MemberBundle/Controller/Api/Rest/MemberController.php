<?php

namespace DMKClub\Bundle\MemberBundle\Controller\Api\Rest;

use Symfony\Component\HttpFoundation\JsonResponse;


use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Symfony\Component\HttpFoundation\Response;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberManager;

/**
 */
class MemberController extends RestController
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'dmkclub_member.member.manager.api' => ApiEntityManager::class,
        ]);
    }
    /**
     * REST DELETE
     *
     * @param int $id
     *
     * @ApiDoc(
     *      description="Delete Member",
     *      resource=true
     * )
     * @return Response
     */
    #[Acl(id: 'dmkclub_member_delete', type: 'entity', permission: 'DELETE', class: \DMKClub\Bundle\MemberBundle\Entity\Member::class)]
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * Get entity Manager
     *
     * @return MemberManager
     */
    public function getManager()
    {
    	// FIXME: missing
    	return $this->container->get('dmkclub_member.member.manager.api');
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
