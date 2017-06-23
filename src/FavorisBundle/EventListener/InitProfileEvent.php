<?php
/**
 * Created by PhpStorm.
 * User: clement
 * Date: 23/06/2017
 * Time: 16:19
 */

namespace FavorisBundle\EventListener;

use Doctrine\ORM\EntityManager;
use FavorisBundle\Entity\Directory;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class InitProfileEvent implements EventSubscriberInterface
{
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;

    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationGenerateProfile',
        );
    }

    public function onRegistrationGenerateProfile(UserEvent $event)
    {
        $user = $event->getUser();
        $collection = array(1=>'Réseaux sociaux',2=>'Divertissement',3=>'News');
        foreach ($collection as $position=>$name){
            $dir = new Directory();
            $dir->setUser_dir($user);
            $dir->setTitle($name);
            $dir->setPosition($position);
            $this->em->persist($dir);
        }
        $this->em->flush();

    }
}