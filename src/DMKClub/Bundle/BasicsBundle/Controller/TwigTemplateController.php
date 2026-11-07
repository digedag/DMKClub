<?php
namespace DMKClub\Bundle\BasicsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use DMKClub\Bundle\BasicsBundle\Form\Handler\TwigTemplateHandler;
use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;

/**
 *
 * @Route("/twigtemplate")
 */
class TwigTemplateController extends AbstractController
{

    /**
     *
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class,
            TwigTemplateHandler::class,
            'dmkclub_basics.twigtemplate.form' => Form::class,
            UpdateHandlerFacade::class
        ]);
    }

    /**
     *
     * @Route("/", name="dmkclub_basics_twigtemplate_index")
     * @Template
     */
    #[AclAncestor('dmkclub_basics_twigtemplate_view')]
    public function indexAction()
    {
        return [
            'entity_class' => TwigTemplate::class
        ];
    }

    /**
     * Create template form
     *
     * @Route("/create", name="dmkclub_basics_twigtemplate_create")
     * @Template("DMKClubBasicsBundle:TwigTemplate:update.html.twig")
     */
    #[Acl(id: 'dmkclub_basics_twigtemplate_create', type: 'entity', permission: 'CREATE', class: \DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate::class)]
    public function createAction(Request $request)
    {
        return $this->update(new TwigTemplate(), $request);
    }

    /**
     * Update twigtemplate form
     *
     * @Route("/update/{id}", name="dmkclub_basics_twigtemplate_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     */
    #[Acl(id: 'dmkclub_basics_twigtemplate_update', type: 'entity', permission: 'EDIT', class: \DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate::class)]
    public function updateAction(TwigTemplate $entity, Request $request)
    {
        return $this->update($entity, $request);
    }

    /**
     *
     * @param TwigTemplate $entity
     *
     * @return array
     */
    protected function update(TwigTemplate $entity, Request $request)
    {
        return $this->container->get(UpdateHandlerFacade::class)->update(
            $entity,
            $this->container->get('dmkclub_basics.twigtemplate.form'),
            $this->container->get(TranslatorInterface::class)->trans('dmkclub.basics.twigtemplate.message.saved'),
            $request,
            $this->container->get(TwigTemplateHandler::class)
        );
    }

    /**
     *
     * @Route("/view/{id}", name="dmkclub_basics_twigtemplate_view", requirements={"id"="\d+"}))
     * @Template
     */
    #[Acl(id: 'dmkclub_basics_twigtemplate_view', type: 'entity', permission: 'VIEW', class: \DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate::class)]
    public function viewAction(TwigTemplate $entity)
    {
        return [
            'entity' => $entity
        ];
    }

    /**
     * Wird zur Anzeige des Info-Widgets in der Detailansicht verwendet
     *
     * @Route("/widget/info/{id}", name="dmkclub_basics_twigtemplate_widget_info", requirements={"id"="\d+"}))
     * @Template
     */
    #[AclAncestor('dmkclub_basics_twigtemplate_view')]
    public function infoAction(TwigTemplate $entity)
    {
        return [
            'entity' => $entity
        ];
    }
}
