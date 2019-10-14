<?php namespace Model\GoogleLogin\Controllers;

use Model\Core\Controller;

class GoogleLoginController extends Controller
{
	public function index()
	{
		$this->model->viewOptions['template'] = null;

		try {
			switch ($this->model->getRequest(1)) {
				case 'login':
					$this->model->_GoogleLogin->redirect();
					break;
				case 'response':
					if (!isset($_GET['code']))
						throw new \Exception('Unauthorized');

					$config = $this->model->_GoogleLogin->retrieveConfig();

					$user = $this->model->_GoogleLogin->getUser($_GET['code']);

					$this->model->getModule($config['signup-module'])->{$config['signup-method']}($user);
					break;
				default:
					echo 'Unknown action';
					break;
			}
		} catch (\Exception $e) {
			$this->model->viewOptions['errors'][] = getErr($e);
		}
	}
}
