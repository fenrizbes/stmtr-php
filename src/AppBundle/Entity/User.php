<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class User implements UserInterface, \Serializable
{
	/**
	 * @ORM\Column(type="bigint")
	 * @ORM\Id
	 */
	protected $steamid;

	/**
	 * @ORM\Column(nullable=true)
	 */
	protected $personaname;

	/**
	 * @ORM\Column(nullable=true)
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
     * @ORM\Column(type="decimal", precision=15, scale=4, nullable=true)
     */
    protected $rating;

    /**
     * @ORM\Column(type="boolean", name="is_being_handled")
     */
    protected $isBeingHandled = false;

    /**
     * @ORM\OneToMany(targetEntity="UserGame", mappedBy="user")
     */
    protected $games;

    /**
     * @ORM\OneToMany(targetEntity="UserAchievement", mappedBy="user")
     */
    protected $achievements;

    /**
     * @ORM\Column(length=32)
     */
    protected $hash;

	/**
	 * Initialize default values
	 */
	public function __construct()
	{
		$this->createdAt    = new \DateTime();
        $this->hash         = md5(microtime() .'{'. mt_rand() .'}');
        $this->games        = new ArrayCollection();
        $this->achievements = new ArrayCollection();
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

    /**
     * Set rating
     *
     * @param string $rating
     *
     * @return User
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set isBeingHandled
     *
     * @param boolean $isBeingHandled
     *
     * @return User
     */
    public function setIsBeingHandled($isBeingHandled)
    {
        $this->isBeingHandled = $isBeingHandled;

        return $this;
    }

    /**
     * Get isBeingHandled
     *
     * @return boolean
     */
    public function getIsBeingHandled()
    {
        return $this->isBeingHandled;
    }

    /**
     * Add game
     *
     * @param UserGame $game
     *
     * @return User
     */
    public function addGame(UserGame $game)
    {
        $this->games[] = $game;

        return $this;
    }

    /**
     * Remove game
     *
     * @param UserGame $game
     */
    public function removeGame(UserGame $game)
    {
        $this->games->removeElement($game);
    }

    /**
     * Get games
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Add achievement
     *
     * @param UserAchievement $achievement
     *
     * @return User
     */
    public function addAchievement(UserAchievement $achievement)
    {
        $this->achievements[] = $achievement;

        return $this;
    }

    /**
     * Remove achievement
     *
     * @param UserAchievement $achievement
     */
    public function removeAchievement(UserAchievement $achievement)
    {
        $this->achievements->removeElement($achievement);
    }

    /**
     * Get achievements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAchievements()
    {
        return $this->achievements;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->getSteamid();
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return ['ROLE_STEAM_USER'];
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {}

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {}

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {}

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->steamid
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serizlized)
    {
        list($this->steamid) = unserialize($serizlized);
    }

    /**
     * Check if the user data was updated more than 1 day ago or never been set
     *
     * @return bool
     */
    public function isOutdated()
    {
        if ($this->isBeingHandled) {
            return false;
        }

        if (
            $this->updatedAt instanceof \DateTime
            &&
            $this->updatedAt > new \DateTime('-1 day')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Return the integer part of the rating
     *
     * @return integer
     */
    public function getRatingInteger()
    {
        if (null == $this->rating) {
            return 0;
        }

        return round($this->rating);
    }

    /**
     * Return the fractional part of the rating
     *
     * @return integer
     */
    public function getRatingFraction()
    {
        if (null == $this->rating) {
            return 0;
        }

        $ratingString = (string) $this->rating;

        if (false === strpos($ratingString, '.')) {
            return 0;
        }

        return preg_replace('/^\d+\./', '', $ratingString);
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return User
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
}
