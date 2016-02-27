<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(indexes={
 *      @ORM\Index(name="achievement_key_index", columns={"`key`"})
 * })
 */
class GameAchievement
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="`key`")
     */
    protected $key;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=4)
     */
    protected $percentage;

    /**
     * @ORM\Column(type="datetime", name="checked_at", nullable=true)
     */
    protected $checkedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="achievements")
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
     * Set checkedAt
     *
     * @param \DateTime $checkedAt
     *
     * @return GameAchievement
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
