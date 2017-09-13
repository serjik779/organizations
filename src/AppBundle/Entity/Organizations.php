<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Organizations
 *
 * @ORM\Table(name="Organizations")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrganizationsRepository")
 */
class Organizations
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="ogrn", type="bigint", nullable=false)
     */
    private $ogrn;

    /**
     * @var integer
     *
     * @ORM\Column(name="oktmo", type="integer", nullable=false)
     */
    private $oktmo;

    /**
     * @var ArrayCollection|Users
     *
     * @ORM\OneToMany(targetEntity="Users", mappedBy="organization", cascade={"remove"})
     */
    private $users;


    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|Users
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param Users $users
     * @return Organizations
     */
    public function setUsers($users)
    {
        $this->users[] = $users;
        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Organizations
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
     * Set ogrn
     *
     * @param integer $ogrn
     * @return Organizations
     */
    public function setOgrn($ogrn)
    {
        $this->ogrn = $ogrn;

        return $this;
    }

    /**
     * Get ogrn
     *
     * @return integer 
     */
    public function getOgrn()
    {
        return $this->ogrn;
    }

    /**
     * Set oktmo
     *
     * @param integer $oktmo
     * @return Organizations
     */
    public function setOktmo($oktmo)
    {
        $this->oktmo = $oktmo;

        return $this;
    }

    /**
     * Get oktmo
     *
     * @return integer 
     */
    public function getOktmo()
    {
        return $this->oktmo;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Need for show data for field 'organization' in Create user
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle()?: 'Нет данных';
    }
}
