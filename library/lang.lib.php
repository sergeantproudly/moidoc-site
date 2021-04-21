<?php

krnLoadLib('settings');
krnLoadLib('define');

class Lang {
	protected $db;
	protected $settings;

	protected $langs = ['ru', 'en'];
	protected $lang;

	public function __construct() {
		global $Params;
		global $Settings;
		$this->db = $Params['Db']['Link'];
		$this->settings = $Settings;

		$this->lang = $this->langs[1];
	}

	public function GetLangs() {
		return $this->langs;
	}

	public function GetLang() {
		return $this->lang;
	}

	public function SetLang($value) {
		$this->lang = $value;
	}

	public function IsEng() {
		return $this->lang == 'en';
	}

	public function LoadTemplate($name, $dir='') {
		return LoadTemplate($name, ($this->IsEng() ? EN_DIR : '') . $dir);
	}
}

?>