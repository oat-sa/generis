<?php
/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage action
 *
 */
class generis_actions_Portal extends Module {

	/**
	 * Constructor performs initializations actions
	 * @return void
	 */
	public function __construct(){
		
	}
	
	/**
	 * Authentication form, 
	 * default page, main entry point to the user
	 * @return void
	 */
	public function login(){
		
		if($this->getData('errorMessage')){
			session_destroy();
		}
		$this->setView('Portal/login.tpl');
	}

	/**
	 * Logout, destroy the session and back to the login page
	 * @return 
	 */
	public function logout(){
		session_destroy();
		$this->redirect(_url('login', 'Main', 'Portal'));	
	}
}
?>