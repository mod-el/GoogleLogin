<?php namespace Model\GoogleLogin;

use Model\Core\Module;

class GoogleLogin extends Module
{
	/**
	 * @return \Google_Client
	 */
	private function getMain(): \Google_Client
	{
		$config = $this->retrieveConfig();

		$client = new \Google_Client();
		$client->setAuthConfig(INCLUDE_PATH . 'app/config/GoogleLogin/client_secret.json');
		$client->addScope(\Google_Service_Oauth2::USERINFO_EMAIL);
		$client->addScope(\Google_Service_Oauth2::USERINFO_PROFILE);
		$client->setRedirectUri(BASE_HOST . PATH . $config['path'] . '/response');

		return $client;
	}

	/**
	 * @return string
	 */
	public function getLoginUrl(): string
	{
		$config = $this->retrieveConfig();

		return PATH . $config['path'] . '/login';
	}

	/**
	 */
	public function redirect()
	{
		$client = $this->getMain();

		$auth_url = $client->createAuthUrl();
		$this->model->redirect(filter_var($auth_url, FILTER_SANITIZE_URL));
	}

	/**
	 * @param string $authCode
	 * @return array
	 */
	public function getUser(string $authCode): array
	{
		$client = $this->getMain();

		$token = $client->fetchAccessTokenWithAuthCode($authCode);
		if (!isset($token['access_token']))
			throw new \Exception(isset($token['error'], $token['error_description']) ? $token['error'] . ' - ' . $token['error_description'] : 'Errore durante l\'acquisizione del token');

		$client->setAccessToken($token['access_token']);

		$google_oauth = new \Google_Service_Oauth2($client);
		$google_account_info = $google_oauth->userinfo->get();

		return [
			'id' => $google_account_info->id,
			'name' => $google_account_info->givenName,
			'surname' => $google_account_info->familyName,
			'gender' => $google_account_info->gender,
			'email' => $google_account_info->email,
			'picture' => $google_account_info->picture,
			'lang' => $google_account_info->locale,
		];
	}

	/**
	 * @param array $request
	 * @param string $rule
	 * @return array|null
	 */
	public function getController(array $request, string $rule): ?array
	{
		return $rule === 'google-login' ? [
			'controller' => 'GoogleLogin',
		] : null;
	}
}
