<?php

namespace DMKClub\Bundle\BasicsBundle\Controller\Api\Rest;

use Symfony\Component\HttpFoundation\JsonResponse;


use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\AddressBundle\Entity\AbstractTypedAddress;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Symfony\Component\HttpFoundation\Response;

/**
 */
class TwigTemplateController extends RestController
{
    /**
     * REST DELETE
     *
     * @param int $id
     *
     * @ApiDoc(
     *      description="Delete TwigTemplate",
     *      resource=true
     * )
     * @return Response
     */
    #[Acl(id: 'dmkclub_basics_twigtemplate_delete', type: 'entity', permission: 'DELETE', class: \DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate::class)]
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
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

    public function getManager()
    {
        throw new \BadMethodCallException('Manager is not available.');
    }
}
