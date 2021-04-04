<?php

krnLoadLib('define');
krnLoadLib('settings');

class sitemap extends krn_abstract{
	
	function __construct() {
		parent::__construct();
	}
	
	function GetResult() {
		$pages = $this->GetPages();
		echo $this->GetSitemap($pages);
		exit;
	}
	
	function GetPages() {
		$time = time();

		$servicesCodes = $this->db->getCol('SELECT Code FROM services');
		
		//statics
		$items = $this->db->getAll('SELECT Code, LastModTime FROM static_pages ORDER BY IF(`Order`,-1000/`Order`,0)');
		foreach ($items as $item) {
			if (!in_array($item['Code'], $servicesCodes)) {
				$pages[$item['Code']] = $item['LastModTime'];	
			}
		}

		// services
		$items = $this->db->getAll('SELECT Code, LastModTime FROM services ORDER BY IF(`Order`,-1000/`Order`,0)');
		foreach ($items as $item) {
			$pages['services/' . $item['Code']] = $item['LastModTime'];
		}

		// news
		$items = $this->db->getAll('SELECT Code, LastModTime FROM news ORDER BY Date DESC');
		foreach ($items as $item) {
			$pages['articles/' . $item['Code']] = $item['LastModTime'];
		}
		
		// works
		$items = $this->db->getAll('SELECT Code, LastModTime FROM works ORDER BY IF(`Order`,-100/`Order`,0)');
		foreach ($items as $item) {
			$pages['projects/' . $item['Code']] = $item['LastModTime'];
		}
		return $pages;
	}
	
	function GetSitemap($pages) {
		global $Settings;
		$siteUrl = $Settings->GetSetting('SiteProtocol') . $Settings->GetSetting('SiteDomain');
		
		$xml = new DomDocument('1.0', 'utf8');
		
		$urlset = $xml->appendChild($xml->createElement('urlset'));
		$urlset->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$urlset->setAttribute('xsi:schemaLocation','http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
		$urlset->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
		
		foreach ($pages as $page => $lastmodtime) {
			$url=$urlset->appendChild($xml->createElement('url'));
			$loc=$url->appendChild($xml->createElement('loc'));
			if ($page == 'main') $loc->appendChild($xml->createTextNode($siteUrl . (substr($siteUrl,-1)!='/'?'/':'')));
			else $loc->appendChild($xml->createTextNode($siteUrl . (substr($siteUrl,-1)!='/'?'/':'') . $page . '/'));
			$lastmod=$url->appendChild($xml->createElement('lastmod'));
			$lastmod->appendChild($xml->createTextNode(date('c',$lastmodtime?$lastmodtime:time())));
			$changefreq=$url->appendChild($xml->createElement('changefreq'));
			$changefreq->appendChild($xml->createTextNode('daily'));
			$priority=$url->appendChild($xml->createElement('priority'));
			$priority->appendChild($xml->createTextNode('0.5'));
		}
		
		$xml->formatOutput=true;
		$xml->save('sitemap.xml');
	}
	
	function Generate(){
		$pages = $this->GetPages();
		$this->GetSitemap($pages);
	}
	
}

?>