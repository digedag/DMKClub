<?php
namespace DMKClub\Bundle\BasicsBundle\Form\Handler;

use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;

class TwigTemplateHandler implements FormHandlerInterface
{
    /** @var ObjectManager */
    protected $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Process form
     *
     * @param TwigTemplate $entity
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process($entity, FormInterface $form, Request $request)
    {
        $form->setData($entity);

        if (in_array($request->getMethod(), [
            'POST',
            'PUT'
        ])) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->onSuccess($entity);

                return true;
            }
        }

        return false;
    }

    /**
     * "Success" form handler
     *
     * @param TwigTemplate $entity
     */
    protected function onSuccess(TwigTemplate $entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
