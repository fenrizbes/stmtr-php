<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(indexes={
 *      @ORM\Index(name="updated_at_index", columns={"updated_at"})
 * })
 */
class UserGame
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="previous_playtime")
     */
    protected $previousPlaytime = 0;

    /**
     * @ORM\Column(type="integer", name="current_playtime")
     */
    protected $currentPlaytime = 0;

    /**
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime", name="checked_at", nullable=true)
     */
    protected $checkedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="games")
     * @ORM\JoinColumn(name="steamid", referencedColumnName="steamid", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Game")
     * @ORM\JoinColumn(name="gameid", referencedColumnName="gameid", onDelete="CASCADE")
     */
    protected $game;

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

    /**
     * Set previousPlaytime
     *
     * @param integer $previousPlaytime
     *
     * @return UserGame
     */
    public function setPreviousPlaytime($previousPlaytime)
    {
        $this->previousPlaytime = $previousPlaytime;

        return $this;
    }

    /**
     * Get previousPlaytime
     *
     * @return integer
     */
    public function getPreviousPlaytime()
    {
        return $this->previousPlaytime;
    }

    /**
     * Set currentPlaytime
     *
     * @param integer $currentPlaytime
     *
     * @return UserGame
     */
    public function setCurrentPlaytime($currentPlaytime)
    {
        $this->currentPlaytime = $currentPlaytime;

        return $this;
    }

    /**
     * Get currentPlaytime
     *
     * @return integer
     */
    public function getCurrentPlaytime()
    {
        return $this->currentPlaytime;
    }

    /**
     * Check if the game was played and has to be updated
     *
     * @return bool
     */
    public function isOutdated()
    {
        if ($this->getPreviousPlaytime() == $this->getCurrentPlaytime()) {
            return false;
        }

        return true;
    }
}
