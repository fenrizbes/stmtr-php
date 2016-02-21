<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class GameAchievement
{
    /**
     * @ORM\Column
     * @ORM\Id
     */
    protected $key;

    /**
     * @ORM\Column(type="integer")
     */
    protected $gameid;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=4)
     */
    protected $percentage;

    /**
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="achievements")
     * @ORM\JoinColumn(name="gameid", referencedColumnName="gameid", onDelete="CASCADE")
     */
    protected $game;

    /**
     * Set key
     *
     * @param string $key
     *
     * @return GameAchievement
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set gameid
     *
     * @param integer $gameid
     *
     * @return GameAchievement
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
     * Set percentage
     *
     * @param string $percentage
     *
     * @return GameAchievement
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * Get percentage
     *
     * @return string
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * Set game
     *
     * @param Game $game
     *
     * @return GameAchievement
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
