<?php

namespace Zco\Bundle\UserBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Geocoder\GeocoderInterface;

/**
 * Calcule les coordonnées (latitude et longitude) à partir d'une adresse.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CalculateCoordinatesSubscriber implements EventSubscriberInterface
{
    protected $geocoder;

    /**
     * Constructeur.
     *
     * @param $geocoder GeocoderInterface
     */
    public function __construct(GeocoderInterface $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SUBMIT => 'onBindClientData');
    }

    /**
     * Remplit les coordonnées de l'utilisateur en fonction de son adresse.
     *
     * @param FormEvent $event
     */
    public function onBindClientData(FormEvent $event)
    {
        $data = $event->getData();
        $user = $event->getForm()->getData();

        if (null === $data || null === $user) {
            return;
        }

        if (!empty($data['address']))
        {
            $geocoded = $this->geocoder->using('google_maps')->geocode($data['address']);
            $user->setLatitude($geocoded->getLatitude());
            $user->setLongitude($geocoded->getLongitude());
        }
        else
        {
            $user->setLatitude(0);
            $user->setLongitude(0);
        }
    }
}