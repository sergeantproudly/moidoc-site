<?php

krnLoadLib('settings');

class main extends krn_abstract{	

	public function __construct(){
		parent::__construct();
		$this->page = $this->db->getRow('SELECT Id, Title, Header, Content, SeoTitle, SeoKeywords, SeoDescription FROM static_pages WHERE Code="main"');
		
		global $Config;
		$this->pageTitle = $this->page['Title'] ?: $this->settings->GetSetting('SiteTitle', $Config['Site']['Title'] ?: 'Главная');
	}	

	public function GetResult(){
		global $Config;
		$Projects = krnLoadModuleByName('projects');

		return $Projects->GetResult();
	}

}
?>