<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class UserGame
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     */
    protected $steamid;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    protected $gameid;

    /**
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="boolean", name="is_being_handled")
     */
    protected $isBeingHandled = false;

    /**
     * @ORM\Column(type="datetime", name="checked_at")
     */
    protected $checkedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="games")
     * @ORM\JoinColumn(name="steamid", referencedColumnName="steamid", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Game")
     * @ORM\JoinColumn(name="gameid", referencedColumnName="gameid")
     */
    protected $game;

    /**
     * Set steamid
     *
     * @param integer $steamid
     *
     * @return UserGame
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
     * Set gameid
     *
     * @param integer $gameid
     *
     * @return UserGame
     */
    public function setGameid($gameid)
    {
        $this->gameid = $gameid;

        return $this;
    }

    /**
     * Get gameid
     *
     * @return integer
     */
    public function getGameid()
    {
        return $this->gameid;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return UserGame
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
     * Set isBeingHandled
     *
     * @param boolean $isBeingHandled
     *
     * @return UserGame
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
     * Set checkedAt
     *
     * @param \DateTime $checkedAt
     *
     * @return UserGame
     */
    public function setCheckedAt($checkedAt)
    {
        $this->checkedAt = $checkedAt;

        return $this;
    }

    /**
     * Get checkedAt
     *
     * @return \DateTime
     */
    public function getCheckedAt()
    {
        return $this->checkedAt;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return UserGame
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set game
     *
     * @param Game $game
     *
     * @return UserGame
     */
    public function setGame(Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }
}
