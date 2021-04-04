<?php

class map extends krn_abstract{	

	function __construct() {
		parent::__construct();
		$this->page = $this->db->getRow('SELECT Title, Header, Code, Content, SeoTitle, SeoKeywords, SeoDescription, TemplateCode FROM static_pages WHERE Code = ?s', 'map');
		
		$this->pageTitle = $this->page['Title'];
		$this->breadCrumbs = GetBreadCrumbs(array(
			'Главная' => ''),
			$this->pageTitle);
	}	

	function GetResult(){
		$this->content = $this->GetMap();

		$result = krnLoadPageByTemplate($this->page['TemplateCode'] ?: 'base_static');
		$result = strtr($result, array(
			'<%META_KEYWORDS%>'		=> $this->page['SeoKeywords'],
    		'<%META_DESCRIPTION%>'	=> $this->page['SeoDescription'],
    		'<%PAGE_TITLE%>'		=> $this->pageTitle,
    		'<%BREAD_CRUMBS%>'		=> $this->breadCrumbs,
    		'<%TITLE%>'				=> $this->page['Header'] ?: $this->page['Title'],
			'<%TEXT%>'				=> $this->content,
			'<%CONTENT%>'			=> ''
		));
		return $result;
	}
	
	function GetMap() {
		$arr = $this->db->getAll('SELECT Title, Code FROM static_pages ORDER BY IF(`Order`,-1000/`Order`,0)');
		foreach ($arr as $static_page){
			$static_pages[$static_page['Code']] = $static_page;
		}

		$tree = array();

		$servicesCodes = $this->db->getCol('SELECT Code FROM services');

		//statics
		$items =$this->db->getAll('SELECT Title, Code FROM static_pages ORDER BY IF(`Order`,-1000/`Order`,0)');
		foreach ($items as $item) {
			if ($item['Code'] != 'services' && $item['Code'] != 'news' && $item['Code'] != 'works') {
				if (!in_array($item['Code'], $servicesCodes)) {					
					if ($item['Code'] == 'main') $item['Code'] = '/';
					elseif ($item['Code'] == 'mission' ||
						$item['Code'] == 'certs' ||
						$item['Code'] == 'direction' ||
						$item['Code'] == 'vacancies') {

						$tree['company']['pages'][$item['Code']] = $item;

					} else {
						$tree[$item['Code']] = $item;
					}
				}
			}
		}
		
		// services
		$tree['services'] = $static_pages['services'];
		$items = $this->db->getAll('SELECT Title, Code FROM services ORDER BY IF(`Order`,-1000/`Order`,0)');
		foreach ($items as $item) {
			$tree['services']['pages'][$item['Code']] = $item;
		}

		// news
		$tree['articles'] = $static_pages['articles'];
		$items = $this->db->getAll('SELECT Title, Code FROM news ORDER BY Date DESC');
		foreach ($items as $item) {
			$tree['articles']['pages'][$item['Code']] = $item;
		}

		// projects
		$tree['projects'] = $static_pages['projects'];
		$items = $this->db->getAll('SELECT Title, Code FROM works ORDER BY IF(`Order`,-100/`Order`,0)');
		foreach ($items as $item) {
			$tree['projects']['pages'][$item['Code']] = $item;
		}
		
		$content = '';
		foreach ($tree as $code => $item) {
			$sub = '';
			if ($item['pages']) {
				foreach ($item['pages'] as $sub_code => $sub_item) {
					$sub .= '<li><a href="/'.$code.'/'.$sub_code.'/">' . $sub_item['Title'] . '</a></li>';
				}
				$sub = '<ul>' . $sub . '</ul>';
			}

			if ($code != '/') {
				$content .= '<li><a href="/'.$item['Code'].'/">'.$item['Title'].'</a>'.$sub.'</li>';
			} else {
				$content .= '<li><a href="'.$item['Code'].'">'.$item['Title'].'</a>'.$sub.'</li>';
			}
		}

		$result = SetContent(LoadTemplate('map'), $content);
		return $result;
	}

}

?>