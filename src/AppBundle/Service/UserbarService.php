<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;

class UserbarService
{
    const IMAGE_WIDTH   = 538;
    const IMAGE_HEIGHT  = 42;
    const IMAGE_PADDING = 5;

    const FSIZE_COMMON = 14;
    const FSIZE_BIG    = 26;
    const FSIZE_SMALL  = 10;

    const AVATAR_SIZE = 32;

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
        $this->steamData  = $steamData;
        $this->webPath    = $webPath;
        $this->imagesPath = $webPath .'/userbars';
        $this->fontR      = $webPath .'/bundles/app/fonts/regular.ttf';
        $this->fontB      = $webPath .'/bundles/app/fonts/bold.ttf';
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
     * @param array $colors
     *
     * @return UserbarService
     */
    protected function writeRating(&$image, User $user, $x, $y, $colors)
    {
        $x = $this->writeText($image, static::FSIZE_BIG, $x, $y, $colors['green'], $this->fontB, number_format($user->getRatingInteger()));
        $x = $this->writeText($image, static::FSIZE_COMMON, $x, $y, $colors['white'], $this->fontR, '.'. $user->getRatingFraction() .' s');
        $x = $this->writeText($image, static::FSIZE_COMMON, $x, $y, $colors['green'], $this->fontR, 'm');
        $x = $this->writeText($image, static::FSIZE_COMMON, $x, $y, $colors['white'], $this->fontR, '.');

        return $this;
    }

    /**
     * @param resource $image
     * @param int $x
     * @param int $y
     * @param array $colors
     *
     * @return UserbarService
     */
    protected function placeWatermark(&$image, $x, $y, $colors)
    {
        $x = $this->writeText($image, static::FSIZE_SMALL, $x, $y, $colors['white'], $this->fontR, 'stea');
        $x = $this->writeText($image, static::FSIZE_SMALL, $x, $y, $colors['green'], $this->fontR, 'meter');
        $x = $this->writeText($image, static::FSIZE_SMALL, $x, $y, $colors['white'], $this->fontR, '.com');

        return $this;
    }

    /**
     * @param User $user
     * @param string $path
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function createImage(User $user, $path)
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

        return $path;
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
    public function getImage(User $user)
    {
        $path = $this->imagesPath .'/'. $user->getHash() .'.png';

        if ($user->isOutdated()) {
            $this->steamData->updateUser($user);
        }

        if ($this->isImageOutdated($user, $path)) {
            $this->createImage($user, $path);
        }

        return $path;
    }
}