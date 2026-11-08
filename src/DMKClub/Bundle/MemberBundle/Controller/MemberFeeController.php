<?php

namespace DMKClub\Bundle\MemberBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Form\Form;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Form\Handler\MemberFeeHandler;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;

#[Route('/memberfee')]
class MemberFeeController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'doctrine' => ManagerRegistry::class,
            TranslatorInterface::class,
            MemberFeeHandler::class,
            Manager::class,
            'dmkclub_member.memberfee.form' => Form::class,
            RouterInterface::class,
        ]);
    }

	#[Route('/', name: 'dmkclub_memberfee_index')]
	#[Template]
	#[AclAncestor('dmkclub_memberfee_view')]
	public function indexAction()
	{
	    return [
	        'entity_class' => MemberFee::class,
	    ];
	}
	/**
	 * Create member form
	 */
	#[Route('/create', name: 'dmkclub_memberfee_create')]
	#[Template('DMKClubMemberBundle:MemberFee:update.html.twig')]
	#[Acl(id: 'dmkclub_memberfee_create', type: 'entity', permission: 'CREATE', class: \DMKClub\Bundle\MemberBundle\Entity\MemberFee::class)]
	public function createAction()
	{
		return $this->update(new MemberFee());
	}
	/**
	 * Method is called from "dmkgetpdf" datagrid-action.
	 * Is creates a PDF-File for a single MemberFee and returns a download link.
	 * -> nicht notwendig! Template("DMKClubMemberBundle:MemberFee:pdf.html.twig")
	 */
	#[Route('/pdf/{id}', name: 'dmkclub_memberfee_createpdf', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
	#[AclAncestor('dmkclub_memberfee_view')]
	public function createPDFAction(MemberFee $entity) {
		$responseData = [
				'url'  => false
		];

		/* @var $pdfManager \DMKClub\Bundle\BasicsBundle\PDF\Manager */
		$pdfManager = $this->container->get(Manager::class);

		try {
			$file = $pdfManager->buildPdf($entity);
			$url = $this->container->get('router')->generate(
					'dmkclub_basics_export_download',
					['fileName' => basename($file->getKey())]
			);
			$responseData['url'] = $url;
		}
		catch(\Exception $e) {
			$responseData['message'] = $e->getMessage();
		}

		$response = new JsonResponse($responseData, Response::HTTP_OK);

		return $response;
	}

	/**
	 * Update memberfee form
	 */
	#[Route('/update/{id}', name: 'dmkclub_memberfee_update', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
	#[Template]
	#[Acl(id: 'dmkclub_memberfee_update', type: 'entity', permission: 'EDIT', class: \DMKClub\Bundle\MemberBundle\Entity\MemberFee::class)]
	public function updateAction(MemberFee $entity)
	{
		return $this->update($entity);
	}

	/**
	 * @param MemberFee $entity
	 *
	 * @return array
	 */
	protected function update(MemberFee $entity)
	{
	    $responseData = [
	        'saved' => false,
	    ];

	    if ($this->container->get(MemberFeeHandler::class)->process($entity)) {
	        $this->container->get('doctrine')->getManager()->flush();
	        $responseData['entity'] = $entity;
	        $responseData['saved'] = true;
	    }
	    /* @var $form \Symfony\Component\Form\Form */
	    $form = $this->container->get('dmkclub_member.memberfee.form');
	    $responseData['form'] = $form->createView();
	    return $responseData;
	}

	/**
	 * Show positions grid
	 */
	#[Route('/{id}/positions', name: 'dmkclub_memberfee_listpositions', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
	#[Template]
	#[AclAncestor('dmkclub_memberfee_view')]
	public function listPositionsAction(MemberFee $entity)
	{
	    return ['entity' => $entity];
	}

	#[Route('/view/{id}', name: 'dmkclub_memberfee_view', requirements: ['id' => '\d+'])]
	#[Template]
	#[Acl(id: 'dmkclub_memberfee_view', type: 'entity', permission: 'VIEW', class: \DMKClub\Bundle\MemberBundle\Entity\MemberFee::class)]
	public function viewAction(MemberFee $entity) {
	    return ['entity' => $entity];
	}

}
