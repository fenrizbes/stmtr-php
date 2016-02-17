<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class User
{
	/**
	 * @ORM\Column(type="bigint")
	 * @ORM\Id
	 */
	protected $steamid;

	/**
	 * @ORM\Column
	 */
	protected $personaname;

	/**
	 * @ORM\Column
	 */
	protected $avatar;

	/**
	 * @ORM\Column(type="datetime", name="created_at")
	 */
	protected $createdAt;

	/**
	 * @ORM\Column(type="datetime", name="updated_at", nullable=true)
	 */
	protected $updatedAt;

	/**
	 * Set current date to `created_at` column
	 */
	public function __construct()
	{
		$this->setCreatedAt(new \DateTime());
	}

    /**
     * Set steamid
     *
     * @param integer $steamid
     *
     * @return User
     */
    public function setSteamid($steamid)
    {
        $this->steamid = $steamid;

        return $this;
    }

    /**
     * Get steamid
     *
     * @return integer
     */
    public function getSteamid()
    {
        return $this->steamid;
    }

    /**
     * Set personaname
     *
     * @param string $personaname
     *
     * @return User
     */
    public function setPersonaname($personaname)
    {
        $this->personaname = $personaname;

        return $this;
    }

    /**
     * Get personaname
     *
     * @return string
     */
    public function getPersonaname()
    {
        return $this->personaname;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
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
     * @return User
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
}
