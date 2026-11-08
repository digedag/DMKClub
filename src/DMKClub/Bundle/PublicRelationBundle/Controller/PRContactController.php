<?php

namespace DMKClub\Bundle\PublicRelationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

use DMKClub\Bundle\PublicRelationBundle\Entity\PRContact;
use DMKClub\Bundle\PublicRelationBundle\Form\Handler\PRContactHandler;

#[Route('/prcontact')]
class PRContactController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'oro_form.update_handler' => UpdateHandlerFacade::class,
            'dmkclub.prcontact.form' => Form::class,
            'dmkclub.prcontact.form.handler' => PRContactHandler::class,
            'translator' => TranslatorInterface::class,
        ]);
    }

	#[Route('/', name: 'dmkclub_prcontact_index')]
	#[Template]
	#[AclAncestor('dmkclub_prcontact_view')]
	public function indexAction() {
		return [
				'entity_class' => $this->container->getParameter ( 'dmkclub.prcontact.entity.class' )
		];
	}

	#[Route('/view/{id}', name: 'dmkclub_prcontact_view', requirements: ['id' => '\d+'])]
	#[Template]
	#[Acl(id: 'dmkclub_prcontact_view', type: 'entity', permission: 'VIEW', class: \DMKClub\Bundle\PublicRelationBundle\Entity\PRContact::class)]
	public function viewAction(PRContact $entity) {
		return [
				'entity' => $entity
		];
	}

	#[Route('/widget/info/{id}', name: 'dmkclub_prcontact_widget_info', requirements: ['id' => '\d+'])]
	#[Template]
	#[AclAncestor('dmkclub_prcontact_view')]
	public function infoAction(PRContact $entity)
	{
	    return [
	        'entity' => $entity
	    ];
	}

	/**
	 * Create form
	 */
	#[Route('/create', name: 'dmkclub_prcontact_create')]
	#[Template('@DMKClubPublicRelation/PRContact/update.html.twig')]
	#[Acl(id: 'dmkclub_prcontact_create', type: 'entity', permission: 'CREATE', class: \DMKClub\Bundle\PublicRelationBundle\Entity\PRContact::class)]
	public function createAction(Request $request) {
		return $this->update ( new PRContact(), $request );
	}

    /**
     * Update p/r contact form
     */
    #[Route('/update/{id}', name: 'dmkclub_prcontact_update', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    #[Template]
    #[Acl(id: 'dmkclub_prcontact_update', type: 'entity', permission: 'EDIT', class: \DMKClub\Bundle\PublicRelationBundle\Entity\PRContact::class)]
    public function updateAction(PRContact $entity, Request $request)
    {
    	return $this->update($entity, $request);
    }

    /**
     * @param PRContact $entity
     *
     * @return array
     */
    protected function update(PRContact $entity, Request $request)
    {
    	return $this->container->get('oro_form.update_handler')->update(
            $entity,
            $this->container->get('dmkclub.prcontact.form'),
            $this->container->get('translator')->trans('dmkclub.publicrelation.prcontact.saved.message'),
            $request,
            $this->container->get('dmkclub.prcontact.form.handler')
    	);
    }
}
