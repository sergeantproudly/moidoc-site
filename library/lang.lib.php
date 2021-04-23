<?php

krnLoadLib('settings');
krnLoadLib('define');

class Lang {
	protected $db;
	protected $settings;

	protected $langs = ['ru', 'en'];
	protected $domains = ['moidoc.ru', 'moidoc.com'];
	protected $default = 'ru';
	protected $lang;

	public function __construct() {
		global $Params;
		global $Settings;
		$this->db = $Params['Db']['Link'];
		$this->settings = $Settings;

		$this->lang = $this->DetectLang();
	}

	public function DetectLang() {
		if (!$_POST['ajax']) {
			var_dump($_SESSION['lang']);
		var_dump($_COOKIE['lang']);
		}
		
		if ($_SESSION['lang']) {
			return $_SESSION['lang'];

		} elseif ($_COOKIE['lang']) {
			return $_COOKIE['lang'];

		} else {
			$domain = $_SERVER['HTTP_HOST'];
			if ($domain == $domains[1]) $this->default = $this->langs[1];
			return $this->DetectLangByAcceptLanguage();
		}
	}

	public function DetectLangByAcceptLanguage() {
		$langs = array();
		if (($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
            if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)) {
                $langs = array_combine($list[1], $list[2]);
                foreach ($langs as $n => $v)
                    $langs[$n] = $v ? $v : 1;
                arsort($langs, SORT_NUMERIC);
            }
        } else $langs = array();

        $languages = array();

        foreach ($this->langs as $lang => $alias) {
            if (is_array($alias)) {
                foreach ($alias as $alias_lang) {
                    $languages[strtolower($alias_lang)] = strtolower($lang);
                }
            }else $languages[strtolower($alias)]=strtolower($lang);
        }

        foreach ($langs as $l => $v) {
            $s = strtok($l, '-'); // убираем то что идет после тире в языках вида "en-us, ru-ru"
            if (isset($languages[$s]))
                return $s;
        }
        return $this->default;
	}

	public function CheckDomainByLang($lang) {
		$k = array_search($lang, $this->langs);
		$domain = $_SERVER['HTTP_HOST'];
		if ($domain == $this->domains[$k]) return true;
		else return $this->domains[$k];
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