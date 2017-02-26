<?php

namespace Zco\Bundle\UserBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;

class AddUserFieldSubscriber implements EventSubscriberInterface
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

		if ($data === null)
		{
			return;
		}
		
		if (!$data->getUserId())
		{
			$form->add($this->factory->createNamed('entity', 'User', null, array(
				'label' => 'Pseudonyme',
				'class' => 'Utilisateur',
			)));
		}
	}
}