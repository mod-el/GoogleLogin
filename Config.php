<?php namespace Model\GoogleLogin;

use Model\Core\Module_Config;

class Config extends Module_Config
{
	/**
	 */
	protected function assetsList()
	{
		$this->addAsset('config', 'config.php', function () {
			return '<?php
$config = [
	\'path\' => \'google\',
	\'signup-module\' => \'Signup\',
	\'signup-method\' => \'googleSignup\',
	// Put "client_secret.json" file, downloaded from online Google OAuth console, inside this folder
];
';
		});
	}

	/**
	 * @return bool
	 */
	public function makeCache(): bool
	{
		if ($this->model->moduleExists('Composer'))
			$this->model->_Composer->addToJson('google/apiclient');
		return true;
	}

	/**
	 * @return array
	 */
	public function getRules(): array
	{
		$config = $this->retrieveConfig();

		return [
			'rules' => [
				'google-login' => $config['path'] ?? '',
			],
			'controllers' => [
				'GoogleLogin',
			],
		];
	}
}
