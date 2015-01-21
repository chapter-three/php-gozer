<?php

namespace Gozer\Core;

/**
 * Base class for all api controllers.
 * 
 * Has built in OAuth2 authentication via the bshaffer/oauth2-server-php library. 
 * Pass true to the constructor to enable OAuth2.
 * 
 * TODO: Add JWT Bearer support. See http://bshaffer.github.io/oauth2-server-php-docs/grant-types/jwt-bearer/
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
			$temp = explode('/', trim($_SERVER['PATH_INFO'], '/'));
			if ($temp[count($temp) - 1] != 'authorize') {
				// Check for a valid token
				if (!$this->oauthServer->verifyResourceRequest(\OAuth2\Request::createFromGlobals())) {
					// Not authorized!
					$this->oauthServer->getResponse()->send();
					die;
				}
			}
		}
	}

	/**
	 * Only the client_credentials and password grant_types are currently supported.
	 * Will add support for authorization_code and implicit when needed.
	 * 
	 * authorization_code (future support)
	 *      3-legged authentication. See http://bshaffer.github.io/oauth2-server-php-docs/grant-types/authorization-code/
	 * client_credentials
	 *      data: client_id and client_secret
	 * password
	 *      data: username and password
	 * implicit (future support)
	 *      Allows implicit 
	 */
	private function initOAth2() {
		$storage = new \OAuth2\Storage\Pdo(array(
			'dsn' => 'mysql:dbname=' . DOCTRINE_DB_NAME . ';host=localhost',
			'username' => DOCTRINE_DB_USER,
			'password' => DOCTRINE_DB_PASSWORD
		));

		//$storage->checkRestrictedGrantType()
		
		$grantTypes = unserialize(OAUTH_GRANT_TYPES);

		$options = array();
		
		/*
		if (in_array('implicit', $grantTypes)) {
			// 'implicit' requires 'authorization_code'
			if (!in_array('authorization_code', $grantTypes)) {
				$grantTypes[] = 'authorization_code';
			}
			$options['allow_implicit'] = true;
		}
		*/
		
		if (OAUTH_ISSUE_REFRESH_TOKENS) {
			$options['always_issue_new_refresh_token'] = true;
			$options['refresh_token_lifetime'] = OAUTH_REFRESH_TOKEN_LIFETIME;
		}

		$grants = array();
		
		foreach ($grantTypes as $grantType) {
			switch ($grantType) {
				/*
				case 'authorization_code':
					// 3-legged authentication
					// See http://bshaffer.github.io/oauth2-server-php-docs/grant-types/authorization-code/
					$this->oauthServer->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
					break;
				*/
				case 'client_credentials':
					// Uses client_id and client_secret.
					// Example request:
					//      curl -u TestClient:TestSecret https://api.mysite.com/token -d 'grant_type=client_credentials'
					//   or:
					//      curl https://api.mysite.com/authorize -d 'grant_type=client_credentials&client_id=TestClient&client_secret=TestSecret'
					$grants[] = new \OAuth2\GrantType\ClientCredentials($storage);
					break;
				case 'password':
					// Uses username and password.
					// Example request:
					//      curl -u TestClient:TestSecret https://api.mysite.com/token -d 'grant_type=password&username=bshaffer&password=brent123'
					//   or:
					//      curl https://api.mysite.com/token -d 'grant_type=password&client_id=TestClient&username=bshaffer&password=brent123'
					$grants[] = new \OAuth2\GrantType\UserCredentials($storage);
					break;
			}
		}
		
		if (OAUTH_ISSUE_REFRESH_TOKENS) {
			$grants[] = new \OAuth2\GrantType\RefreshToken($storage);
		}

		$this->oauthServer = new \OAuth2\Server($storage, $options, $grants);
	}
	
	/**
	 * This is the client authorize endpoint.
	 * Requires a route like so:
	 * {
	 *      "url":			"/api/authorize",
	 *      "controller":	"<NameOfYourController>",
	 *      "action":		"getOAuth2Token"
	 * }
	 * 
	 * Then the client would post to http(s)://<yourdomain.com>/api/authorize/ 
	 * with data appropriate for the grant type to get an access_token for use 
	 * in subsequent calls (defined in your controller).
	 */
	public function getOAuth2Token() {
		// Respond with a new token
		$this->oauthServer->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
	}
}