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

    /**
     * Check if the game was updated more than 1 day ago or never
     *
     * @return bool
     */
    public function isOutdated()
    {
        if (!$this->updatedAt instanceof \DateTime) {
            return true;
        }

        if ($this->updatedAt < new \DateTime('-1 day')) {
            return true;
        }

        return false;
    }
}
