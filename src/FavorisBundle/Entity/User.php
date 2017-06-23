<?php
// src/AppBundle/Entity/User.php

namespace FavorisBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="FavorisBundle\Entity\Directory", mappedBy="user_dir", cascade={"remove"})
     */
    private $directory_user;

    public function __construct()
    {
        parent::__construct();
        $this->directory_user = new ArrayCollection();
    }

    /**
     * Add directory
     *
     * @param \FavorisBundle\Entity\Directory $directory
     *
     * @return User
     */
    public function addDirectory_user(\FavorisBundle\Entity\Directory $directory)
    {
        $this->directory_user[] = $directory;

        return $this;
    }

    /**
     * Remove Directory
     *
     * @param \FavorisBundle\Entity\Directory $directory
     */
    public function removeDirectory_user(\FavorisBundle\Entity\Favoris $directory)
    {
        $this->directory_user->removeElement($directory);
    }

    /**
     * Get Directory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDirectory_user()
    {
        return $this->directory_user;
    }


}