<?php

krnLoadLib('define');
krnLoadLib('settings');

class blocks extends krn_abstract{
	
	private $page_id;
	private $blocks_sequence = array();
	private $blocks_info = array();
	private $forms_info = array();
	private $rel_codes_methods = array(
	);
	private $flag = false;

	public function __construct($pageId = false) {
		parent::__construct();

		global $Params;
		$this->page_id = $pageId ?: $Params['Site']['Page']['Id'];
		
		$query = 'SELECT p2b.*, b.Code '
				.'FROM `rel_pages_blocks` AS p2b '
				.'LEFT JOIN `blocks` AS b ON p2b.BlockId = b.Id '
				.'LEFT JOIN `static_pages` AS s ON p2b.PageId = s.Id '
				.'WHERE s.Id = ?s '
				.'ORDER BY IF(p2b.`Order`,-30/p2b.`Order`,0)';
		$blocks = $this->db->getAll($query, $this->page_id);
		foreach ($blocks as $block) {
			$this->blocks_sequence[] = $block['Code'];
			$this->blocks_info[$block['Code']][] = $block;
		}
		
		$forms = $this->db->getAll('SELECT * FROM `forms`');
		foreach ($forms as $form) {
			$this->forms_info[$form['Code']] = $form;
		}
	}
	
	public function GetResult() {}

	public function GetPageBlocks($data = array()) {
		$html = [];
		$counter = [];
		foreach ($this->blocks_sequence as $code) {
			if (!isset($counter[$code])) $counter[$code] = 0;

			if (isset($this->rel_codes_methods[$code])) {
				$func = $this->rel_codes_methods[$code];
				$code_param = $code;
			} else {
				$func = 'Block';
				foreach (explode('_', $code) as $fragments) {
					$func .= ucfirst($fragments);
				}
			}

			$info['Index'] = $counter[$code];
			$info['Code'] = isset($code_param) ? $code_param : $code;
			if (method_exists($this, $func)) {
				$html[] = $this->$func($info, $data);
			} else {
				$html[] = $this->BlockText($info, $data);
			}

			$counter[$code]++;
		}
		return $html;
	}

	public function GetBlockParams($blockCode, $index = 0) {
		$params = [];
		foreach (explode(';', $this->blocks_info[$blockCode][$index]['Params']) as $line) {
			list($param, $value) = explode(':', $line, 2);
			$params[ucfirst(trim($param))] = trim($value);
		}
		return $params;
	}
	
	/** Блок - Текстовый */
	public function BlockText($data = array()) {
		$code = $data['Code'];
		$index = $data['Index'];
		$params = $this->GetBlockParams($code, $index);

		$result = LoadTemplate($code ? 'bl_'.$code : 'bl_text');
		$result = strtr($result, array(
			'<%CLASS%>'		=> $params['Class'] ?: '',
			'<%HEADER%>'	=> $this->blocks_info[$code][$index]['Header'] ? '<h2>'.$this->blocks_info[$code][$index]['Header'].'</h2>' : '',
			'<%TITLE%>'		=> $this->blocks_info[$code][$index]['Header'] ? '<h2>'.$this->blocks_info[$code][$index]['Header'].'</h2>' : '',
			'<%CONTENT%>'	=> $this->blocks_info[$code][$index]['Content'],
			'<%HR%>'		=> $params['Hr'] == 1 ? '<hr class="hr">' : '',
		));
		if ($params['ExcludedClass']) $result = str_replace($params['ExcludedClass'], '', $result);
		return $result;
	}

	/** Блок - Форма */
	public function BlockForm($data = array()) {
		$code = $data['Code'];
		$class = $data['Class'];
		//$index = $data['Index'];
		$result = LoadTemplate($code);
		$result = strtr($result, array(
			'<%TITLE%>'	=> $this->forms_info[$code]['Title'],
			'<%TEXT%>'	=> trim($this->forms_info[$code]['Text']) ? $this->forms_info[$code]['Text'] : '',
			'<%CODE%>'	=> $this->forms_info[$code]['Code'],
			'<%CLASS%>'	=> $class ? ' class="' . $class . '"' : ''
		));
		return $result;
	}

	/** Блок - Меню соц сетей */
	public function BlockSocial() {
		$content = '';
		$element = LoadTemplate('mn_social_el');

		$items = $this->db->getAll('SELECT * FROM `social` ORDER BY IF(`Order`, -100/`Order`, 0) ASC');
		foreach ($items as $item) {
			$item['Alt'] = htmlspecialchars($item['Title'], ENT_QUOTES);
			$content .= SetAtribs($element, $item);
		}

		$result = SetContent(LoadTemplate('mn_social'), $content);
		return $result;
	}

	/** Блок - Блог */
	public function BlockStatues() {
		$content = '';
		$element = LoadTemplate('bl_statues_el');

		$items = $this->db->getAll('SELECT Title, Code, Image1146_877 AS Image FROM `news` ORDER BY Date DESC');
		foreach ($items as $item) {
			$item['Alt'] = htmlspecialchars($item['Title'], ENT_QUOTES);
			$item['Link'] = '/blog/' . $item['Code'] . '/';
			$content .= SetAtribs($element, $item);
		}

		$result = SetContent(LoadTemplate('bl_statues'), $content);
		return $result;
	}
}
?>