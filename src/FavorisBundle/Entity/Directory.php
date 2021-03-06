<?php

namespace FavorisBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * File
 *
 * @ORM\Table(name="directory")
 * @ORM\Entity(repositoryClass="FavorisBundle\Repository\DirectoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Directory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="FavorisBundle\Entity\Favoris", mappedBy="directory", cascade={"remove"})
     */
    private $favourites;

    /**
     * @ORM\ManyToOne(targetEntity="FavorisBundle\Entity\User", inversedBy="directory_user")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user_dir;

    public function __construct()
    {
        $this->favourites = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function manageDates()
    {
        if (!$this->getCreatedAt()) $this->setCreatedAt(new \DateTime('now'));
        $this->setUpdatedAt(new \DateTime('now'));
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Directory
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Directory
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Directory
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Directory
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * Add favourite
     *
     * @param \FavorisBundle\Entity\Favoris $favourite
     *
     * @return Directory
     */
    public function addFavourite(\FavorisBundle\Entity\Favoris $favourite)
    {
        $this->favourites[] = $favourite;

        return $this;
    }

    /**
     * Remove favourite
     *
     * @param \FavorisBundle\Entity\Favoris $favourite
     */
    public function removeFavourite(\FavorisBundle\Entity\Favoris $favourite)
    {
        $this->favourites->removeElement($favourite);
    }

    /**
     * Get favourites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavourites()
    {
        return $this->favourites;
    }


    /**
     * Set user
     *
     * @param \FavorisBundle\Entity\User $user
     *
     * @return Directory
     */
    public function setUser_dir(\FavorisBundle\Entity\User $user = null)
    {
        $this->user_dir = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \FavorisBundle\Entity\User
     */
    public function getUser_dir()
    {
        return $this->user_dir;
    }
}
