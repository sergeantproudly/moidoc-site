<?php

	class PreActions{

		private $db;
		
		public function __construct(){
			$this->DoActions();
		}
		
		private function DoActions(){
			global $Params;
			global $Config;
			global $Settings;
			global $Routing;
			global $Lang;
			global $Site;
			global $_LEVEL;
			
			$Params['Db']['Link'] = new SafeMySQL(array(
		   		'host'		=> $Config['Db']['Host'],
		   		'user'		=> $Config['Db']['Login'],
		   		'pass'		=> $Config['Db']['Pswd'],
		   		'db'		=> $Config['Db']['DbName'],
		   		'charset'	=> 'utf8'
		 	));
		 	$this->db = $Params['Db']['Link'];

		 	if ($_GET['p_code'] == 'set-lang') {
		 		setcookie('lang_selected_by_user', '1', time() + WEEK_IN_SEC*24, '/', $_SERVER['HTTP_HOST'], true);
		 		$_COOKIE['lang_selected_by_user'] = 1;
				__Redirect('/');
		 	}

		 	krnLoadLib('lang');
		 	$Lang = new Lang();

			krnLoadLib('settings');
		 	$Settings = new Settings();

		 	krnLoadLib('routing');
		 	$Routing = new Routing();

		 	$Site = new Site();

		 	$_LEVEL[1] = $_GET['p_code'];
		 	$_LEVEL[2] = $_GET['p_code2'];
		 	$_LEVEL[3] = $_GET['p_code3'];
		 	$_LEVEL[4] = $_GET['p_code4'];
		} 
		
	}
	
	$PreActions = new PreActions();

?>