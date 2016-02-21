<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class Game
{
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
     * @ORM\OneToMany(targetEntity="GameAchievement", mappedBy="game")
     */
    protected $achievements;

    public function __construct()
    {
        $this->achievements = new ArrayCollection();
    }

    /**
     * Set gameid
     *
     * @param integer $gameid
     *
     * @return Game
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
     * @return Game
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
     * @return Game
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
     * Add achievement
     *
     * @param GameAchievement $achievement
     *
     * @return Game
     */
    public function addAchievement(GameAchievement $achievement)
    {
        $this->achievements[] = $achievement;

        return $this;
    }

    /**
     * Remove achievement
     *
     * @param GameAchievement $achievement
     */
    public function removeAchievement(GameAchievement $achievement)
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
}
