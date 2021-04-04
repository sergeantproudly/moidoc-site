<?php

class _static extends krn_abstract {	

	function __construct() {
		parent::__construct();
		global $Params;
		$this->page = $this->db->getRow('SELECT Id, Code, Title, Header, Content, SeoTitle, SeoKeywords, SeoDescription, TemplateCode FROM static_pages WHERE Code="'.$Params['Site']['Page']['Code'].'"');
		if (!$this->page) {
			header('HTTP/1.1 404 Not Found');
			header('Status: 404 Not Found');
			$this->pageTitle = 'Страница не найдена';
			$this->page['Title'] = '';
			$this->page['Content'] = LoadTemplate('404');
			
		} else {
			$this->page['Title'] = stripslashes($this->page['Title']);
			$this->page['Content'] = stripslashes($this->page['Content']);
			$this->pageTitle = $this->page['SeoTitle'] ? $this->page['SeoTitle'] : $this->page['Title'];
			$this->breadCrumbs = GetBreadCrumbs(array('Главная'=>'/'), $this->pageTitle);
		}
	}	

	function GetResult() {
		$Blocks = krnLoadModuleByName('blocks');

		$this->content = $this->page['Content'] ? '<div class="text content wow fadeInUpSmall fast animated delay-02s">' . $this->page['Content'] . '</div>' : '';		

		$result = krnLoadPageByTemplate($this->page['TemplateCode'] ?: 'base_static');
		$result = strtr($result, array(
			'<%META_KEYWORDS%>'		=> $this->page['SeoKeywords'],
    		'<%META_DESCRIPTION%>'	=> $this->page['SeoDescription'],
    		'<%PAGE_TITLE%>'		=> $this->pageTitle,
    		'<%BREAD_CRUMBS%>'		=> $this->breadCrumbs,
    		'<%TITLE%>'				=> $this->page['Header'] ?: $this->page['Title'],
			'<%CONTENT%>'			=> $this->content,
		));

		$this->blocks = $Blocks->GetPageBlocks($this->page['Id']);
		foreach ($this->blocks as $i => $block) {
			$result = str_replace('<%BLOCK' . ($i + 1) . '%>', $block, $result);
		}
		return $result;
	}

}

?>