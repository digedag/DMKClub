<?php

namespace DMKClub\Bundle\SponsorBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Form\Handler\ApiFormHandler;

/**
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class SponsorController extends RestController
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'dmkclub_sponsor.sponsor.manager.api' => ApiEntityManager::class,
        ]);
    }
    /**
     * REST GET list
     *
     * 
     * 
     * 
     * 
     * 
     *
     * @throws \Exception
     * @return Response
     */
    #[AclAncestor('dmkclub_sponsor_view')]
    public function cgetAction()
    {
        $page  = (int)$this->getRequest()->get('page', 1);
        $limit = (int)$this->getRequest()->get('limit', self::ITEMS_PER_PAGE);

        $dateClosure = function ($value) {
            // datetime value hack due to the fact that some clients pass + encoded as %20 and not %2B,
            // so it becomes space on symfony side due to parse_str php function in HttpFoundation\Request
            $value = str_replace(' ', '+', $value);

            // The timezone is ignored when DateTime value specifies a timezone (e.g. 2010-01-28T15:00:00+02:00)
            return new \DateTime($value, new \DateTimeZone('UTC'));
        };

        $filterParameters = [
            'createdAt' => [
                'closure' => $dateClosure,
            ],
            'updatedAt' => [
                'closure' => $dateClosure,
            ],
        ];

        $criteria = $this->getFilterCriteria($this->getSupportedQueryParameters('cgetAction'), $filterParameters);

        return $this->handleGetListRequest($page, $limit, $criteria);
    }

    /**
     * REST GET item
     *
     * @param string $id
     *
     * 
     * @return Response
     */
    #[AclAncestor('dmkclub_sponsor_view')]
    public function getAction($id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * REST PUT
     *
     * @param int $id Sponsor item id
     *
     * 
     * @return Response
     */
    #[AclAncestor('dmkclub_sponsor_update')]
    public function putAction($id)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * Create new sponsor
     *
     * 
     */
    #[AclAncestor('dmkclub_sponsor_create')]
    public function postAction()
    {
        return $this->handleCreateRequest();
    }

    /**
     * REST DELETE
     *
     * @param int $id
     *
     * 
     * @return Response
     */
    #[Acl(id: 'dmkclub_sponsor_delete', type: 'entity', permission: 'DELETE', class: \DMKClub\Bundle\SponsorBundle\Entity\Sponsor::class)]
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
        return $this->container->get('dmkclub_sponsor.sponsor.manager.api');
    	//return $this->container->get('orocrm_contact.contact.manager.api');
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
    	throw new \Exception('Not implemented');
    	// return $this->container->get('orocrm_call.call.form.api');
    }

    /**
     * @return ApiFormHandler
     */
    public function getFormHandler()
    {
    	throw new \Exception('Not implemented');
    	// return $this->container->get('orocrm_call.call.form.handler.api');
    }

}
