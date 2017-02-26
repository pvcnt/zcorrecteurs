<?php

namespace Zco\Bundle\UserBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

class AddAutoValidateFieldSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data === null) {
            return;
        }

        if ($data->getUser()->getId() == $_SESSION['id'] && (verifier('membres_editer_pseudos') || verifier('membres_valider_ch_pseudos'))) {
            $form->add($this->factory->createNamed('checkbox', 'autoValidated', null, array(
                'label' => 'Valider le changement immédiatement',
                'required' => false,
            )));
        }
    }
}