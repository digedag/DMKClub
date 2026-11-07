<?php
namespace DMKClub\Bundle\PaymentBundle\Form\Handler;

use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor;

class SepaCreditorHandler implements FormHandlerInterface
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
     * @param SepaCreditor $entity
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
     * @param SepaCreditor $entity
     */
    protected function onSuccess(SepaCreditor $entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
