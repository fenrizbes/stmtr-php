<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class UserAchievement
{
    /**
     * @ORM\Column
     * @ORM\Id
     */
    protected $key;

    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     */
    protected $steamid;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="achievements")
     * @ORM\JoinColumn(name="steamid", referencedColumnName="steamid", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="GameAchievement")
     * @ORM\JoinColumn(name="key", referencedColumnName="key")
     */
    protected $gameAchievement;

    /**
     * Set key
     *
     * @param string $key
     *
     * @return UserAchievement
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
     * Set steamid
     *
     * @param integer $steamid
     *
     * @return UserAchievement
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
     * Set user
     *
     * @param User $user
     *
     * @return UserAchievement
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
     * Set gameAchievement
     *
     * @param GameAchievement $gameAchievement
     *
     * @return UserAchievement
     */
    public function setGameAchievement(GameAchievement $gameAchievement = null)
    {
        $this->gameAchievement = $gameAchievement;

        return $this;
    }

    /**
     * Get gameAchievement
     *
     * @return GameAchievement
     */
    public function getGameAchievement()
    {
        return $this->gameAchievement;
    }
}
