<?php
    
    class Site {

    	protected $db;
		protected $settings;

		protected $modals;

		public function __construct() {
			global $Params;
			global $Settings;
			$this->db = $Params['Db']['Link'];
			$this->settings = $Settings;
		}

    	public function GetCurrentPage() {
			$page = false;
			if (preg_match('/\/([a-zA-Z0-9_\-\-]+)\/?$/', $_SERVER['REQUEST_URI'], $match)) {
				$page = $match[1];
			} elseif (preg_match('/\/$/', $_SERVER['REQUEST_URI'])) {
				$page = '/';
			}
			return $page;
		}

		public function GetPageFromLink($link) {
			$page = false;
			if (preg_match('/\/([a-zA-Z0-9_\-]+)\/?$/', $link, $match)) {
				$page = $match[1];
			} elseif (preg_match('/\/$/', $link)) {
				$page = '/';
			}
			return $page;
		}

		public function SetLinks($html) {
			$result = preg_replace('~<a +href="(?!http[s]?://)([^\>]+)~i', '<a href="/$1', $html);
			return strtr($result, array(
				'<a href="//'		=> '<a href="/',
				'<a href="/#'		=> '<a href="#',
				'<a href="/tel:'	=> '<a href="tel:',
				'<a href="/mailto'	=> '<a href="mailto' 
			));
		}

		public function AddModal($html) {
			$this->modals .= $html;
		}

		public function GetModals() {
			return $this->modals;
		}

		public function GetPage() {
			krnLoadLib('define');
			krnLoadLib('menu');
			krnLoadLib('modal');
			global $krnModule;

			$Blocks = krnLoadModuleByName('blocks');
			$Main = krnLoadModuleByName('main');

			// menus
			$menuMain = new Menu([
				'menuDb'			=> 'menu_items',
				'template'			=> 'mn_main',
				'templateEl'		=> 'mn_main_el',
			]);

			$menuSoc = new Menu([
				'menuDb'			=> 'menu_bottom_items',
				'template'			=> 'mn_bottom',
				'templateEl'		=> 'mn_bottom_el',
			]);

			// settings
			$siteTitle = $this->settings->GetSetting('SiteTitle', $Config['Site']['Title']);
			$email = $this->settings->GetSetting('SiteEmail');
			$tel = $this->settings->GetSetting('Tel');

			// feedback
			$feedbackForm = $Blocks->BlockForm(array(
				'Code'	=> 'feedback',
				'Class'	=> $this->GetCurrentPage() == '/' || $this->GetCurrentPage() == 'moidoc' ? false : 'hidden',
			));

			$result = strtr($krnModule->GetResult(), array(
				'<%VERSION%>'				=> $this->settings->GetSetting('AssetsVersion') ? '?v' . $this->settings->GetSetting('AssetsVersion') : '',
		    	'<%META_KEYWORDS%>'			=> $Config['Site']['Keywords'],
		    	'<%META_DESCRIPTION%>'		=> $Config['Site']['Description'],
		    	'<%META_IMAGE%>'			=> '',
		    	'<%PAGE_TITLE%>'			=> $siteTitle,
		    	'<%SITE_TITLE%>'			=> $siteTitle,
		    	'<%SITE_TITLE_ALT%>'		=> htmlspecialchars($siteTitle, ENT_QUOTES),
		    	'<%SITE_URL%>'				=> $this->settings->GetSetting('SiteProtocol') . $_SERVER['HTTP_HOST'],
		    	'<%TEL%>'					=> $tel,
		    	'<%TELLINK%>'				=> $tellink,
		    	'&lt;%TEL%&gt;'				=> $tel,
		    	'&lt;%TELLINK%&gt;'			=> $tellink,
		    	'<%EMAIL%>'					=> $email,
		    	'&lt;%EMAIL%&gt;'			=> $email,
		    	'<%ADDRESS%>'				=> $address,
		    	'&lt;%ADDRESS%&gt;'			=> $address,
		    	'<%META_VERIFICATION%>'		=> $this->settings->GetSetting('MetaVerification'),
		    	'<%METRIKA%>'				=> $this->settings->GetSetting('Metrika'),
		    	'<%MN_MAIN%>'				=> $menuMain->GetMenu(),
		    	'<%MN_SOC%>'				=> $Blocks->BlockSocial(),
		    	'<%BREAD_CRUMBS%>'			=> '',
		    	'<%COPYRIGHT%>'				=> $this->settings->GetSetting('Copyright'),
		    	'<%CONSULTANT%>'			=> $this->settings->GetSetting('ConsultantCode'),
		    	'<%SCRIPTS%>'				=> $this->settings->GetSetting('EndingScripts'),
		    	'<%ANALYTICS%>'				=> $this->settings->GetSetting('AnalyticsCode'),
		    	//'<%BL_FEEDBACK%>'			=> $feedbackForm,
		    	'<%BL_FEEDBACK%>'			=> '',
		    	'<%BLOCK1%>'				=> '',
		    	'<%BLOCK2%>'				=> '',
		    	'<%BLOCK3%>'				=> '',
		    	'<%BLOCK4%>'				=> '',
		    	'<%BLOCK5%>'				=> '',
		    	'<%BLOCK6%>'				=> '',
		    	'<%BLOCK7%>'				=> '',
		    	'<%BLOCK8%>'				=> '',
		    	'<%BLOCK9%>'				=> '',
		    	'<%BLOCK10%>'				=> '',
			));

			return $this->SetLinks($result);
		}	

	}
	
?>