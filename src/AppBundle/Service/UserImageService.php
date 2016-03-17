<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;

class UserImageService
{
    const IMAGE_WIDTH   = 538;
    const IMAGE_HEIGHT  = 42;
    const IMAGE_PADDING = 5;

    const SHARE_WIDTH   = 968;
    const SHARE_HEIGHT  = 504;
    const SHARE_PADDING = 60;

    const FSIZE_SMALL  = 10;
    const FSIZE_COMMON = 14;
    const FSIZE_BIG    = 26;
    const FSIZE_GIANT  = 52;

    const AVATAR_SIZE       = 32;
    const AVATARMEDIUM_SIZE = 64;

    /**
     * @var SteamDataService
     */
    protected $steamData;

    /**
     * @var string
     */
    protected $webPath;

    /**
     * @var string
     */
    protected $imagesPath;

    /**
     * @var string
     */
    protected $sharingPath;

    /**
     * @var string
     */
    protected $fontR;

    /**
     * @var string
     */
    protected $fontB;

    /**
     * @param SteamDataService $steamData
     * @param string $webPath
     */
    public function __construct(SteamDataService $steamData, $webPath)
    {
        $this->steamData   = $steamData;
        $this->webPath     = $webPath;
        $this->imagesPath  = $webPath .'/userbars';
        $this->sharingPath = $webPath .'/sharing';
        $this->fontR       = $webPath .'/bundles/app/fonts/regular.ttf';
        $this->fontB       = $webPath .'/bundles/app/fonts/bold.ttf';
    }

    /**
     * @param resource $image
     *
     * @return array
     */
    protected function allocateColors(&$image)
    {
        return [
            'dark'  => imagecolorallocate($image, 42, 48, 60),
            'white' => imagecolorallocate($image, 255, 255, 255),
            'green' => imagecolorallocate($image, 164, 208, 7)
        ];
    }

    /**
     * @param resource $image
     * @param int $size
     * @param int $x
     * @param int $y
     * @param int $color
     * @param string $font
     * @param string $text
     *
     * @return int
     */
    protected function writeText(&$image, $size, $x, $y, $color, $font, $text)
    {
        imagettftext($image, $size, 0, $x, $y, $color, $font, $text);

        $box = imagettfbbox($size, 0, $font, $text);

        return $x + $box[2];
    }

    /**
     * @param resource $image
     * @param array $colors
     *
     * @return UserbarService
     */
    protected function fillImage(&$image, $colors)
    {
        imagefill($image, 0, 0, $colors['dark']);

        return $this;
    }

    /**
     * @param resource $image
     * @param User $user
     * @param int $x
     * @param int $y
     *
     * @return UserbarService
     */
    protected function copyAvatar(&$image, User $user, $x, $y)
    {
        $avatar = imagecreatefromjpeg($user->getAvatar());

        imagecopy($image, $avatar, $x, $y, 0, 0, static::AVATAR_SIZE, static::AVATAR_SIZE);

        return $this;
    }

    /**
     * @param resource $image
     * @param User $user
     * @param int $x
     * @param int $y
     *
     * @return UserbarService
     */
    protected function copyAvatarmedium(&$image, User $user, $x, $y)
    {
        $avatar = imagecreatefromjpeg($user->getAvatarmedium());

        imagecopy($image, $avatar, $x, $y, 0, 0, static::AVATARMEDIUM_SIZE, static::AVATARMEDIUM_SIZE);

        return $this;
    }

    /**
     * @param resource $image
     * @param User $user
     * @param int $x
     * @param int $y
     * @param array $colors
     * @param int $fbig
     * @param int $fsmall
     *
     * @return UserbarService
     */
    protected function writeRating(&$image, User $user, $x, $y, $colors, $fbig = self::FSIZE_BIG, $fsmall = self::FSIZE_COMMON)
    {
        $x = $this->writeText($image, $fbig, $x, $y, $colors['green'], $this->fontB, number_format($user->getRatingInteger()));
        $x = $this->writeText($image, $fsmall, $x, $y, $colors['white'], $this->fontR, '.'. $user->getRatingFraction() .' s');
        $x = $this->writeText($image, $fsmall, $x, $y, $colors['green'], $this->fontR, 'm');
        $x = $this->writeText($image, $fsmall, $x, $y, $colors['white'], $this->fontR, '.');

        return $this;
    }

    /**
     * @param resource $image
     * @param int $x
     * @param int $y
     * @param array $colors
     * @param int $fsize
     *
     * @return UserbarService
     */
    protected function placeWatermark(&$image, $x, $y, $colors, $fsize = self::FSIZE_SMALL)
    {
        $x = $this->writeText($image, $fsize, $x, $y, $colors['white'], $this->fontR, 'stea');
        $x = $this->writeText($image, $fsize, $x, $y, $colors['green'], $this->fontR, 'meter');
        $x = $this->writeText($image, $fsize, $x, $y, $colors['white'], $this->fontR, '.com');

        return $this;
    }

    /**
     * @param resource $image
     * @param User $user
     * @param int $x
     * @param int $y
     * @param array $colors
     * @param int $fsize
     *
     * @return UserbarService
     */
    protected function writeStatistics(&$image, User $user, $x, $y, $colors, $fsize = self::FSIZE_BIG)
    {
        $y = $this->writeStat($image, $x, $y, $colors, $fsize, 'games  owned: ', $user->getGamesOwned());
        $y = $this->writeStat($image, $x, $y, $colors, $fsize, 'hours played: ', $user->getHoursPlayed());
        $y = $this->writeStat($image, $x, $y, $colors, $fsize, 'achievements: ', $user->getAchievements()->count());

        return $this;
    }

    /**
     * @param resource $image
     * @param int $x
     * @param int $y
     * @param array $colors
     * @param int $fsize
     *
     * @return int
     */
    protected function writeStat(&$image, $x, $y, $colors, $fsize, $name, $value) {
        $value = number_format((int) $value);

        $x = $this->writeText($image, $fsize, $x, $y, $colors['white'], $this->fontR, $name);
        $x = $this->writeText($image, $fsize, $x, $y, $colors['green'], $this->fontR, $value);

        $box = imagettfbbox($fsize, 0, $this->fontR, $name);

        return $y + $fsize + $box[3] + round($fsize / 2);
    }

    /**
     * @param User $user
     * @param string $path
     *
     * @throws \Exception
     */
    protected function createUserbar(User $user, $path)
    {
        $image   = imagecreatetruecolor(static::IMAGE_WIDTH, static::IMAGE_HEIGHT);
        $markBox = imagettfbbox(static::FSIZE_SMALL, 0, $this->fontR, 'steameter.com');
        $colors  = $this->allocateColors($image);

        $this
            ->fillImage($image, $colors)
            ->copyAvatar($image, $user, static::IMAGE_PADDING, static::IMAGE_PADDING)
            ->writeRating(
                $image,
                $user,
                static::IMAGE_PADDING * 2 + static::AVATAR_SIZE,
                static::IMAGE_PADDING + (static::AVATAR_SIZE + static::FSIZE_BIG) / 2,
                $colors
            )
            ->placeWatermark(
                $image,
                static::IMAGE_WIDTH - static::IMAGE_PADDING - $markBox[2],
                static::IMAGE_PADDING + static::FSIZE_SMALL,
                $colors
            )
        ;

        if (!@imagepng($image, $path)) {
            throw new \Exception('Cannot save file');
        }

        imagedestroy($image);
    }

    /**
     * @param User $user
     * @param string $path
     *
     * @return bool
     */
    protected function isImageOutdated(User $user, $path)
    {
        if (!file_exists($path)) {
            return true;
        }

        $mtime = new \DateTime();
        $mtime->setTimestamp(filemtime($path));

        if ($mtime <= $user->getUpdatedAt()) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function getUserbar(User $user)
    {
        $path = $this->imagesPath .'/'. $user->getHash() .'.png';

        if ($user->isOutdated()) {
            $this->steamData->updateUser($user);
        }

        if ($this->isImageOutdated($user, $path)) {
            $this->createUserbar($user, $path);
        }

        return $path;
    }

    /**
     * @param User $user
     * @param string $path
     *
     * @throws \Exception
     */
    protected function createShareImage(User $user, $path)
    {
        $image  = imagecreatetruecolor(static::SHARE_WIDTH, static::SHARE_HEIGHT);
        $colors = $this->allocateColors($image);

        $this
            ->fillImage($image, $colors)
            ->placeWatermark(
                $image,
                static::SHARE_PADDING,
                static::SHARE_PADDING + static::FSIZE_COMMON,
                $colors,
                static::FSIZE_COMMON
            )
            ->copyAvatarmedium(
                $image,
                $user,
                static::SHARE_PADDING,
                static::SHARE_PADDING * 2 + static::FSIZE_COMMON
            )
            ->writeRating(
                $image,
                $user,
                static::SHARE_PADDING + static::AVATARMEDIUM_SIZE + 20,
                static::SHARE_PADDING * 2 + static::FSIZE_COMMON + (static::AVATARMEDIUM_SIZE + static::FSIZE_GIANT) / 2,
                $colors,
                static::FSIZE_GIANT,
                static::FSIZE_BIG
            )
            ->writeStatistics(
                $image,
                $user,
                static::SHARE_PADDING,
                static::SHARE_PADDING * 3 + static::AVATARMEDIUM_SIZE + static::FSIZE_COMMON + static::FSIZE_BIG,
                $colors
            )
            ->writeText(
                $image,
                static::FSIZE_COMMON,
                static::SHARE_PADDING,
                static::SHARE_HEIGHT - static::SHARE_PADDING,
                $colors['white'],
                $this->fontR,
                'and what have you achieved? measure you coolness on steameter.com.'
            )
        ;

        if (!@imagepng($image, $path)) {
            throw new \Exception('Cannot save file');
        }

        imagedestroy($image);
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function getShareImage(User $user)
    {
        $path = $this->sharingPath .'/'. $user->getHash() .'.png';

        if ($user->isOutdated()) {
            $this->steamData->updateUser($user);
        }

        if ($this->isImageOutdated($user, $path)) {
            $this->createShareImage($user, $path);
        }

        return $path;
    }
}