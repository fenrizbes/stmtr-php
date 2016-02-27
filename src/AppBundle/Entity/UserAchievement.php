<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class UserAchievement
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="checked_at", nullable=true)
     */
    protected $checkedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="achievements")
     * @ORM\JoinColumn(name="steamid", referencedColumnName="steamid", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="GameAchievement")
     * @ORM\JoinColumn(name="`key`", referencedColumnName="`key`")
     */
    protected $gameAchievement;

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
     * Set checkedAt
     *
     * @param \DateTime $checkedAt
     *
     * @return UserAchievement
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
