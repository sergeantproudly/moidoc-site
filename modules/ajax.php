<?php

krnLoadLib('mail');
krnLoadLib('settings');
krnLoadLib('define');

class ajax extends krn_abstract{

	function __construct($params=array()) {
		parent::__construct();
	}
	
	function GetResult() {
		if ($_POST['act'] && method_exists($this, $_POST['act'])) {
			echo $this->$_POST['act'];
		}
		exit;
	}	

	/** Модальное окно */
	function GetModal() {
		krnLoadLib('modal');
		$modalCode = $_POST['code'];
		$modal = new Modal($modalCode, $_POST['data']);
		return $modal->GetModal();
	}
	
	/** Загрузчик файлов */
	function GetUploader() {
		krnLoadLib('uploader');
		$uploaderCode = $_POST['code'];
		$func = 'Uploader';
		$r = explode('_',$uploaderCode);
		foreach ($r as $k) {
			$k{0} = strtoupper($k{0});
			$func .= $k;
		}
		if (function_exists($func)) return $func();
		return false;
	}

	/** Обратная связь */
	function Feedback() {
		$name = trim($_POST['name']);
		$tel = trim($_POST['tel']);
		$email = trim($_POST['email']);
		$text = $_POST['text'];
		$code = $_POST['code'];

		$capcha = $_POST['capcha'];

		// проверка на спамбота
		// основывается на сверки user agent-ов
		if ($capcha == $_SERVER['HTTP_USER_AGENT']) {
			if ($tel && $name && $email && $text) {	
			if (!$code)	$code = 'feedback';
				$form = $this->db->getRow('SELECT Title, SuccessHeader, Success FROM forms WHERE Code=?s', $code);				
				$request = '';
				if ($name) $request .= "Имя: $name\r\n";
				if ($email) $request .= "E-mail: $email\r\n";
				if ($tel) $request .= "Телефон: $tel\r\n";
				if ($text) $request .= 'Текст:'."\r\n$text\r\n";
				$this->db->query('INSERT INTO requests SET DateTime=NOW(), Form=?s, Name=?s, Tel=?s, Email=?s, Text=?s, RefererPage=?s, IsSet=0',
					$form ? $form['Title'] : '',
				 	$name,
				 	$tel,
				 	$email,
					str_replace('"', '\"', $request),
					$_SERVER['HTTP_REFERER']
				);
					
				global $Config;
				$siteTitle = strtr(stGetSetting('SiteEmailTitle', $Config['Site']['Title']), array('«'=>'"','»'=>'"','—'=>'-'));
				$siteEmail = stGetSetting('SiteEmail', $Config['Site']['Email']);
				$adminTitle = 'Администратор';
				$adminEmail = stGetSetting('AdminEmail', $Config['Site']['Email']);
					
				$letter['subject'] = $form['Title'].' с сайта "'.$siteTitle.'"';
				$letter['html'] = '<b>'.$form['Title'].'</b><br/><br/>';
				$letter['html'] .= str_replace("\r\n", '<br/>', $request);
				$mail = new Mail();
				$mail->SendMailFromSite($adminEmail, $letter['subject'], $letter['html']);
											
				$json = array(
					'status' => true,
					'header' => $form['SuccessHeader'],
					'message' => strip_tags($form['Success']),
				);

			} else {
				$json = array(
					'status' => false,
					'message' => 'Серверная ошибка. При повторном возникновении, пожалуйста, обратитесь к администратору.'
				);
			}
		} else {
			$json = array(
				'status' => false,
				'message' => 'Сработал антиспам. При повторном возникновении, пожалуйста, обратитесь к администратору.'
			);
		}

		return json_encode($json);
	}

	/** Подписка */
	function Subscribe() {
		$name = trim($_POST['name']);
		$email = trim($_POST['email']);
		$code = $_POST['code'];

		$capcha = $_POST['capcha'];

		// проверка на спамбота
		// основывается на сверки user agent-ов
		if ($capcha == $_SERVER['HTTP_USER_AGENT']) {		
			$form = $this->db->getRow('SELECT Title, SuccessHeader, Success FROM forms WHERE Code=?s', $code);				
			$request = '';
			if ($name) $request .= "Имя: $name\r\n";
			if ($email) $request .= "E-mail: $email\r\n";
			if ($tel) $request .= "Телефон: $tel\r\n";
			if ($text) $request .= 'Текст:'."\r\n$text\r\n";
			$this->db->query('INSERT INTO requests SET DateTime=NOW(), Form=?s, Name=?s, Email=?s, Text=?s, RefererPage=?s, IsSet=0',
				$form ? $form['Title'] : '',
			 	$name,
			 	$email,
				str_replace('"', '\"', $request),
				$_SERVER['HTTP_REFERER']
			);
					
			global $Config;
			$siteTitle = strtr(stGetSetting('SiteEmailTitle', $Config['Site']['Title']), array('«'=>'"','»'=>'"','—'=>'-'));
			$siteEmail = stGetSetting('SiteEmail', $Config['Site']['Email']);
			$adminTitle = 'Администратор';
			$adminEmail = stGetSetting('AdminEmail', $Config['Site']['Email']);
				
			$letter['subject'] = $form['Title'].' с сайта "'.$siteTitle.'"';
			$letter['html'] = '<b>'.$form['Title'].'</b><br/><br/>';
			$letter['html'] .= str_replace("\r\n", '<br/>', $request);
			$mail = new Mail();
			$mail->SendMailFromSite($adminEmail, $letter['subject'], $letter['html']);
										
			$json = array(
				'status' => true,
				'header' => $form['SuccessHeader'],
				'message' => strip_tags($form['Success']),
			);
		} else {
			$json = array(
				'status' => false,
				'message' => 'Сработал антиспам. При повторном возникновении, пожалуйста, обратитесь к администратору.'
			);
		}

		return json_encode($json);
	}

	function Test() {
		global $Config;
		$siteTitle = strtr(stGetSetting('SiteEmailTitle', $Config['Site']['Title']), array('«'=>'"','»'=>'"','—'=>'-'));
		$siteEmail = stGetSetting('SiteEmail', $Config['Site']['Email']);
		$adminTitle = 'Администратор';
		$adminEmail = 'support@proudly.ru';
				
		$letter['subject'] = 'Тест с сайта "'.$siteTitle.'"';
		$letter['html'] = '<b>Тестовая заявка</b><br/><br/>';
		$letter['html'] .= str_replace("\r\n", '<br/>', 'Это тестовое тело заявки');
		$mail = new Mail();
		var_dump($mail->SendMailFromSite($adminEmail, $letter['subject'], $letter['html']));

		die();
	}

}

?>