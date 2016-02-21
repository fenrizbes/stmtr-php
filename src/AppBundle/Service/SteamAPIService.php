<?php

namespace AppBundle\Service;

use AppBundle\Exception\SteamRequestException;
use AppBundle\Exception\SteamResponseException;

class SteamAPIService
{
    /**
     * Scopes' names
     */
	const SCOPE_USER       = 'ISteamUser';
	const SCOPE_USER_STATS = 'ISteamUserStats';
	const SCOPE_PLAYER     = 'IPlayerService';

    /**
     * A base URL of Steam API
     *
     * @var string
     */
	protected $apiUrl;

    /**
     * A key for Steam API
     *
     * @var string
     */
	protected $apiKey;

	/**
	 * @param string $apiUrl
	 * @param string $apiKey
	 */
	public function __construct($apiUrl, $apiKey)
	{
		$this->apiUrl = rtrim($apiUrl, ' /');
		$this->apiKey = $apiKey;
	}

    /**
     * Return user's profile's data
     *
     * @param int|array $steamid
     *
     * @return array
     *
     * @throws SteamResponseException
     */
	public function getUserData($steamid)
	{
		if (is_array($steamid)) {
			$steamids = implode(',', $steamid);
		} else {
            $steamids = $steamid;       
        }

		$data = $this->sendRequest(static::SCOPE_USER, 'GetPlayerSummaries', 2, [
			'steamids' => $steamids
		], [
            'response',
            'players'
        ]);

        if (!count($data)) {
            throw new SteamResponseException('Empty Steam API response');
        }

        if (!is_array($steamid)) {
            $data = $data[0];
        }

        return $data;
    }

    /**
     * Return a list of user's games
     *
     * @param int $steamid
     *
     * @return array
     *
     * @throws SteamResponseException
     *
     * @throws SteamResponseException
     */
    public function getUserGames($steamid)
    {
        return $this->sendRequest(static::SCOPE_PLAYER, 'GetOwnedGames', 1, [
            'steamid' => $steamid
        ], [
            'response',
            'games'
        ]);
	}

    /**
     * Return a list of user's achievements for a specified game
     *
     * @param int $steamid
     * @param int $appid
     *
     * @return array
     */
    public function getUserAchievements($steamid, $appid)
    {
        return $this->sendRequest(static::SCOPE_USER_STATS, 'GetPlayerAchievements', 1, [
            'steamid' => $steamid,
            'appid'   => $appid
        ], [
            'playerstats',
            'achievements'
        ]);
    }

    /**
     * Return a list of game's achievements
     *
     * @param int $gameid
     *
     * @return array
     */
    public function getGameAchievements($gameid)
    {
        return $this->sendRequest(static::SCOPE_USER_STATS, 'GetGlobalAchievementPercentagesForApp', 2, [
            'gameid' => $gameid
        ], [
            'achievementpercentages',
            'achievements'
        ]);
    }

    /**
     * Send request to Steam API and return array with data
     *
     * @param string $scope
     * @param string $action
     * @param int $version
     * @param array $parameters
     * @param array $traverse
     *
     * @return array
     *
     * @throws SteamRequestException
     * @throws SteamResponseException
     */
	protected function sendRequest($scope, $action, $version, $parameters = [], $traverse = [])
	{
        if (!is_array($parameters) || !count($parameters)) {
            throw new SteamRequestException('Insufficient Steam API request parameters');
        }

		$parameters = array_merge([
			'key' => $this->apiKey
		], $parameters);

		$parameters = http_build_query($parameters);
		$version    = sprintf('v%04d', $version);

		$url = $this->apiUrl .'/'. $scope .'/'. $action .'/'. $version .'/?'. $parameters;

        $response = @file_get_contents($url);
        if (false === $response) {
            throw new SteamResponseException('Unable to read Steam API response');
        }

		$data = @json_decode($response, true);
        if (!is_array($data)) {
            throw new SteamResponseException('Unreadable Steam API response');
        }

        if (is_array($traverse)) {
            foreach ($traverse as $name) {
                if (!array_key_exists($name, $data)) {
                    throw new SteamResponseException('Invalid Steam API response');
                }

                $data = $data[$name];
            }
        }

        return $data;
	}
}