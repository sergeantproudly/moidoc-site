<?php

krnLoadLib('settings');
krnLoadLib('define');

class Modal {

	protected $db;
	protected $settings;
	
	protected $code;
	protected $params = [];

	protected $template = '';
	protected $result = false;
	
	public function __construct($code, $params = []) {
		global $Params;
		global $Settings;
		$this->db = $Params['Db']['Link'];
		$this->settings = $Settings;

		$this->code = $code;
		if (!empty($params)) $this->params = $params;
		else $this->params = $_POST;
		
		$this->template = SetAtribs(LoadTemplate('modal_base'), [
			'Id'		=> $this->params['Id'] ? $this->params['Id'] : $this->code,
			'Code'		=> $this->code,
			'Content' 	=> LoadTemplate('modal_'.$this->code)
		]);
		$func = 'Modal';
		$r = explode('_', $this->code);
		foreach ($r as $k) {
			$k{0} = strtoupper($k{0});
			$func .= $k;
		}
		if (method_exists($this, $func)) $this->result = $this->$func();		
	}
	
	public function GetModal() {
		return $this->result;
	}

	public function ModalDone() {
		return $this->template;
	}

	public function ModalVideo() {
		$videoCode = $this->settings->GetSetting((isset($this->params['Full']) && $this->params['Full'] ? 'Full' : '') . 'VideoCode');
		$template = strtr($this->template, array(
			'<%CONTENT%>'		=> $videoCode,
		));
		return $template;
	}
	
}

?>