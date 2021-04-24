<?php

krnLoadLib('define');
krnLoadLib('settings');

class projects extends krn_abstract {

	function __construct() {
		global $_LEVEL;
		parent::__construct();

		if (!$_LEVEL[1]) $_LEVEL[1] = 'application';
		
		if ($this->code = $_LEVEL[1]) {
			$this->project = $this->db->getRow('SELECT Id, Title, Code, SeoTitle, SeoKeywords, SeoDescription FROM projects WHERE Code = ?s AND Lang = ?s', $this->code, $this->lang->GetLang());
			$this->pageTitle = $this->project['Title'];
			/*
			$this->breadCrumbs = GetBreadCrumbs(array(
				'Главная' => '/',
				$this->page['Title'] => $this->page['Code'] . '/'),
				$this->pageTitle);
				*/

		}	
	}	

	function GetResult() {
		$Blocks = krnLoadModuleByName('blocks');

		if ($this->code) {
			$result = strtr(krnLoadPage(), array(
				'<%META_KEYWORDS%>'		=> $this->project['SeoKeywords'],
	    		'<%META_DESCRIPTION%>'	=> $this->project['SeoDescription'],
	    		'<%PAGE_TITLE%>'		=> $this->project['SeoTitle'] ?: $this->project['Title'],
	    		'<%CONTENT%>'			=> $this->GetProject()
			));

		}
		return $result;
	}

	function GetProject() {
		$projects = $this->db->getCol('SELECT Code FROM projects WHERE Lang = ?s ORDER BY IF(`Order`, -100/`Order`, 0)', $this->lang->GetLang());

		return strtr($this->lang->LoadTemplate($this->code), array(
			'<%LINK1%>'				=> $projects[0] . '/',
			'<%LINK2%>'				=> $projects[1] . '/',
			'<%LINK3%>'				=> $projects[2] . '/',
		));
	}
}

?>