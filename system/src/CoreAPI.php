<?php

namespace Gozer\Core;

/**
 * Base class for all api controllers.
 * 
 * Has built in OAuth2 authentication via the bshaffer/oauth2-server-php library. 
 * Pass true to the constructor to enable OAuth2.
 * 
 * TODO: Need to add config options for OAth2 (storage, grant_type, etc.).
 * TODO: Use Doctrine storage?
 * TODO: Set expiration for access tokens. Make it configurable.
 * TODO: How does a client check for expired tokens?
 *
 * @author Jim McGowen
 *
 */
abstract class CoreAPI extends Core
{
	protected $oauthServer;
	private $useOAuth2 = false;
	
	public function __construct($useOAuth2 = false) {
		$this->useOAuth2 = $useOAuth2;
		
		if ($this->useOAuth2) {
			$this->initOAth2();

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
	
	private function initOAth2() {
		$storage = new \OAuth2\Storage\Pdo(array(
			'dsn' => 'mysql:dbname=' . DOCTRINE_DB_NAME . ';host=localhost',
			'username' => DOCTRINE_DB_USER,
			'password' => DOCTRINE_DB_PASSWORD
		));

		$this->oauthServer = new \OAuth2\Server($storage);
		$this->oauthServer->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
		$this->oauthServer->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
	}
	
	public function getOAuth2Token() {
		// Respond with a new token
		$this->oauthServer->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
	}
}