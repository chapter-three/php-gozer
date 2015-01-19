<?php

namespace Gozer\Core;

/**
 * Base class for all api controllers.
 * 
 * Has built in OAuth2 authentication via the bshaffer/oauth2-server-php library.
 * (http://bshaffer.github.io/oauth2-server-php-docs/) 
 * Pass true to the constructor to enable OAuth2.
 * 
 * TODO: Use Doctrine storage?
 *
 * @author Jim McGowen
 *
 */
abstract class CoreAPI extends Core
{
	protected $oauthServer;
	
	public function __construct() 
	{
		if (API_USE_OAUTH) 
		{
			$this->initOAuth2();

			// Don't check for authorization when requesting a token
			$temp = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
			if ($temp[count($temp) - 1] != 'authorize') {
				// Check for a valid token
				if (!$this->oauthServer->verifyResourceRequest(\OAuth2\Request::createFromGlobals())) {
					$this->oauthServer->getResponse()->send();
					die;
				}
			}
		}
	}
	
	private function initOAuth2() 
	{
		$storage = null;
		switch (OAUTH_STORAGE_TYPE) {
			case 'pdo':
				$storage = new \OAuth2\Storage\Pdo(array(
					'dsn' => 'mysql:dbname=' . DOCTRINE_DB_NAME . ';host=localhost',
					'username' => DOCTRINE_DB_USER,
					'password' => DOCTRINE_DB_PASSWORD
				));
				break;
			default:
				throw new \Exception('Invalid OAuth2 storage type');
		}
		
		$opts = array(
			'access_lifetime' => OAUTH_TOKEN_LIFETIME
		);

		$this->oauthServer = new \OAuth2\Server($storage, $opts);
		
		foreach (unserialize(OAUTH_GRANT_TYPES) as $grantType) {
			switch ($grantType) {
				case 'client_credentials':
					$this->oauthServer->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
					break;
				case 'authorization_code':
					$this->oauthServer->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
					break;
			}
		}
	}
	
	public function getOAuth2Token() 
	{
		// Respond with a new token
		$this->oauthServer->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
	}
	
	// TODO: Create a helper function for creating a user account in oauth_clients
}