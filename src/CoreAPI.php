<?php

namespace Gozer\Core;

/**
 * Base class for all api controllers.
 * 
 * Has built in OAuth2 authentication via the bshaffer/oauth2-server-php library. 
 * 
 * Set API_USE_OAUTH to true in the site config file to enable OAuth2.
 * 
 * TODO: Add JWT Bearer support. See http://bshaffer.github.io/oauth2-server-php-docs/grant-types/jwt-bearer/
 * 
 * @author Jim McGowen
 *
 */
abstract class CoreAPI extends Core
{
	/**
	 * @var \OAuth2\Server
	 */
	protected $oauthServer = null;
	private $useOAuth2 = false;

	/**
	 * @var CoreAPIResponse
	 */
	private $responder = null;
	
	/**
	 * CoreAPI constructor.
	 * 
	 * Initializes OAuth2 if API_USE_OAUTH = true.
	 *
	 * @param array $bypassPaths
	 * @param bool $bypassAuth
	 */
	public function __construct($bypassPaths = array(), $bypassAuth = false) {
		
		$this->useOAuth2 = API_USE_OAUTH;
		
		if ($this->useOAuth2) {
			$this->initOAth2();
			
			// Don't check for authorization when requesting a token or docs
			$temp = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
			$lastPath = str_replace($_SERVER['QUERY_STRING'], '', $temp[count($temp) - 1]);
			$lastPath = str_replace('?', '', $lastPath);
			if ($bypassAuth == false && $lastPath != 'authorize' && $lastPath != 'docs') {
				$continue = true;
				foreach ($bypassPaths as $path) {
					if ($lastPath == $path) {
						$continue = false;
					}
				}
				
				if ($continue) {
					// Check for a valid token
					if (!$this->oauthServer->verifyResourceRequest(\OAuth2\Request::createFromGlobals())) {
						// Not authorized!
						$this->oauthServer->getResponse()->send();
						die;
					}
				}
			}
		}
	}
	
	/**
	 * Sets the API responder.
	 * 
	 * @param CoreAPIResponse $responder
	 *
	 * @return CoreAPIResponse
	 */
	protected function setResponder(CoreAPIResponse $responder) {
		$this->responder = $responder;
		return $this->responder;
	}
	
	/**
	 * Responds with data according the API responder.
	 * 
	 * @param $data
	 *
	 * @return mixed
	 */
	protected function respond($data) {
		return $this->responder->respond($data);
	}
	
	/**
	 * Responds with the error message and code according the API responder.
	 * 
	 * @param $msg
	 * @param int $code
	 *
	 * @return mixed
	 */
	protected function respondError($msg, $code = 500) {
		return $this->responder->respondError($msg, $code);
	}
	
	/**
	 * Helper function to get data from a POST either from the $_POST global 
	 * or from php://input.
	 *
	 * @return array|null
	 * @documen nodoc
	 */
	public function getPostDataJSON() {
		$data = null;
		
		// Get from the POST global first
		if (empty($_POST)) {
			// For API calls we need to look at php://input
			if (!empty(file_get_contents('php://input'))) {
				$data = @json_decode(file_get_contents('php://input'), true);
				if ($data === false || $data === null) {
					return null;
				}
			}
			else {
				// No data was passed
				$data = array();
			}
		}
		else {
			$data = $_POST;
		}
		
		// Normalize boolean values
		foreach ($data as $name => &$value) {
			if ($value === 'true') {
				$value = true;
			}
			else if ($value === 'false') {
				$value = false;
			}
		}
		
		return $data;
	}

	/**
	 * Only the client_credentials and password grant_types are currently supported.
	 * Will add support for authorization_code and implicit when needed.
	 * 
	 * # Grant types:
	 * authorization_code (future support)
	 *      3-legged authentication. See http://bshaffer.github.io/oauth2-server-php-docs/grant-types/authorization-code/
	 * client_credentials
	 *      data: client_id and client_secret
	 * password
	 *      data: client_id, username and password
	 * implicit (future support)
	 *      Allows implicit 
	 */
	protected function initOAth2() {
		// TODO: This needs to allow for other db types such as MongoDB.
		$storage = new \OAuth2\Storage\Pdo(array(
			'dsn' => 'mysql:dbname=' . OAUTH_DB_NAME . ';host=' . OAUTH_DB_HOST,
			'username' => OAUTH_DB_USER,
			'password' => OAUTH_DB_PASSWORD
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
		
		$options['access_lifetime'] = OAUTH_TOKEN_LIFETIME;

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
	 * 
	 * @see initOAth2
	 * @documen nodoc
	 */
	public function getOAuth2Token() {
		if ($this->oauthServer === null) {
			$this->respondError("OAuth2 is not enabled for this web service.");
		}
		else {
			// Respond with a new token
			$this->oauthServer->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
		}
	}

	/**
	 * Returns the client id associated with the access token.
	 * If the token is expired null will be returned.
	 * 
	 * @param $token
	 */
	protected function getClientIdFromAccessToken($token) {
		$db = new \mysqli(OAUTH_DB_HOST, OAUTH_DB_USER, OAUTH_DB_PASSWORD, OAUTH_DB_NAME);
		$result = $db->query("SELECT client_id from oauth_access_tokens where access_token='$token'");
		
		if ($result !== false && $result->num_rows > 0) {
			$row = $result->fetch_assoc();
			return $row['client_id'];
		}
		else {
			$this->respondError('Invalid access token.', 401);
		}
	}
}