<?php

namespace AppBundle\Service;

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
     * @param int|array $steamids
     *
     * @return array
     */
	public function getUserData($steamids)
	{
		if (is_array($steamids)) {
			$steamids = implode(',', $steamids);
		}

		$data = $this->sendRequest(static::SCOPE_USER, 'GetPlayerSummaries', 2, array(
			'steamids' => $steamids
		));

        if (!array_key_exists('players', $data) || !count($data['players'])) {
            // TO DO
        }

        if (count($data['players']) == 1) {
            $data = $data['players'][0];
        }

        return $data;
    }

    /**
     * Return a list of user's games
     *
     * @param int $steamid
     *
     * @return array
     */
    public function getUserGames($steamid)
    {
        return $this->sendRequest(static::SCOPE_PLAYER, 'GetOwnedGames', 1, array(
            'steamid' => $steamid
        ));
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
        return $this->sendRequest(static::SCOPE_USER_STATS, 'GetPlayerAchievements', 1, array(
            'steamid' => $steamid,
            'appid'   => $appid
        ));
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
        return $this->sendRequest(static::SCOPE_USER_STATS, 'GetGlobalAchievementPercentagesForApp', 2, array(
            'gameid' => $gameid
        ));
    }

    /**
     * Send request to Steam API and return array with data
     *
     * @param string $scope
     * @param string $action
     * @param int $version
     * @param array $parameters
     *
     * @return array
     */
	protected function sendRequest($scope, $action, $version, $parameters = array())
	{
		$parameters = array_merge(array(
			'key' => $this->apiKey
		), $parameters);

		$parameters = http_build_query($parameters);
		$version    = sprintf('v%04d', $version);

		$url = $this->apiUrl .'/'. $scope .'/'. $action .'/'. $version .'/?'. $parameters;

        // TO DO: Handle errors and throw exceptions

        $response = @file_get_contents($url);
        if (false === $response) {
            #throw exception
        }

		$data = @json_decode($response, true);
        if (!is_array($data) || !array_key_exists('response', $data)) {
            #throw exception
        }

        return $data['response'];
	}
}