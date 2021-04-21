<?php

class actions extends krn_abstract{
	
	function __construct(){
		parent::__construct();
	}
	
	function GetResult(){
	}
	
	/** System */
	function SystemMultiSelect($params){
		$storageTable=$params['storageTable'];
		$storageSelfField=$params['storageSelfField'];
		$storageField=$params['storageField'];
		$selfValue=$params['selfValue'];
		dbDoQuery('DELETE FROM `'.$storageTable.'` WHERE `'.$storageSelfField.'`="'.$selfValue.'"',__FILE__,__LINE__);
		if(isset($params['values'])){
			foreach($params['values'] as $value){
				dbDoQuery('INSERT INTO `'.$storageTable.'` SET `'.$storageSelfField.'`="'.$selfValue.'", `'.$storageField.'`="'.$value.'"',__FILE__,__LINE__);
			}
		}
	}
	
	/** Blog */
	function OnAddNew($newRecord){
		if (!$newRecord['Code']) {
			krnLoadLib('chars');
			$code = mb_strtolower(chrTranslit($newRecord['Title']));
			$code = strtr($code, array(','=>'','.'=>'',' '=>'_','*'=>'','!'=>'','?'=>'','@'=>'','#'=>'','$'=>'','%'=>'','^'=>'','('=>'',')'=>'','+'=>'','-'=>'_','«'=>'','»'=>'','—'=>'',':'=>'',';'=>'','ь'=>''));
			dbDoQuery('UPDATE news SET `Code`="'.$code.'", LastModTime='.time().' WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		} else {
			$code = $newRecord['Code'];
		}

		if ($code) {
			krnLoadLib('routing');
			Routing::SetRouting($code, 'blog');
		}
	}
	
	function OnEditNew($newRecord,$oldRecord){
		if (!$newRecord['Code']) {
			krnLoadLib('chars');
			$code = mb_strtolower(chrTranslit($newRecord['Title']));
			$code = strtr($code,array(','=>'','.'=>'',' '=>'_','*'=>'','!'=>'','?'=>'','@'=>'','#'=>'','$'=>'','%'=>'','^'=>'','('=>'',')'=>'','+'=>'','-'=>'_','«'=>'','»'=>'','—'=>'',':'=>'',';'=>'','ь'=>''));
			dbDoQuery('UPDATE news SET `Code`="'.$code.'", LastModTime='.time().' WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		} else {
			$code = $newRecord['Code'];
		}

		if ($code) {
			krnLoadLib('routing');
			Routing::DeleteRouting($oldRecord['Code'], 'blog');
			Routing::SetRouting($code, 'blog');
		}
	}

	function OnDeleteNew($oldRecord) {
		$code = dbGetValueFromDb('SELECT Code FROM news WHERE Id='.$oldRecord['Id'],__FILE__,__LINE__);

		if ($code) {
			krnLoadLib('routing');
			Routing::DeleteRouting($code, 'blog');
		}
	}

	/** Blog categories */
	function OnAddCategory($newRecord){
		if (!$newRecord['Code']) {
			krnLoadLib('chars');
			$code = mb_strtolower(chrTranslit($newRecord['Title']));
			$code = strtr($code, array(','=>'','.'=>'',' '=>'_','*'=>'','!'=>'','?'=>'','@'=>'','#'=>'','$'=>'','%'=>'','^'=>'','('=>'',')'=>'','+'=>'','-'=>'_','«'=>'','»'=>'','—'=>'',':'=>'',';'=>'','ь'=>''));
			dbDoQuery('UPDATE blog_categories SET `Code`="'.$code.'" WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		} else {
			$code = $newRecord['Code'];
		}

		if ($code) {
			krnLoadLib('routing');
			Routing::SetRouting($code, 'blog');
		}
	}
	
	function OnEditCategory($newRecord,$oldRecord){
		if (!$newRecord['Code']) {
			krnLoadLib('chars');
			$code = mb_strtolower(chrTranslit($newRecord['Title']));
			$code = strtr($code,array(','=>'','.'=>'',' '=>'_','*'=>'','!'=>'','?'=>'','@'=>'','#'=>'','$'=>'','%'=>'','^'=>'','('=>'',')'=>'','+'=>'','-'=>'_','«'=>'','»'=>'','—'=>'',':'=>'',';'=>'','ь'=>''));
			dbDoQuery('UPDATE blog_categories SET `Code`="'.$code.'" WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		} else {
			$code = $newRecord['Code'];
		}

		if ($code) {
			krnLoadLib('routing');
			Routing::DeleteRouting($oldRecord['Code'], 'blog');
			Routing::SetRouting($code, 'blog');
		}
	}

	function OnDeleteCategory($oldRecord) {
		$code = dbGetValueFromDb('SELECT Code FROM blog_categories WHERE Id='.$oldRecord['Id'],__FILE__,__LINE__);

		if ($code) {
			krnLoadLib('routing');
			Routing::DeleteRouting($code, 'blog');
		}
	}

	/** Projects */
	function OnAddProject($newRecord){
		if (!$newRecord['Code']) {
			krnLoadLib('chars');
			$code = mb_strtolower(chrTranslit($newRecord['Title']));
			$code = strtr($code, array(','=>'','.'=>'',' '=>'_','*'=>'','!'=>'','?'=>'','@'=>'','#'=>'','$'=>'','%'=>'','^'=>'','('=>'',')'=>'','+'=>'','-'=>'_','«'=>'','»'=>'','—'=>'',':'=>'',';'=>'','ь'=>''));
			dbDoQuery('UPDATE projects SET `Code`="'.$code.'" WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		} else {
			$code = $newRecord['Code'];
		}

		if ($code) {
			krnLoadLib('routing');
			Routing::SetRouting($code, 'projects');
		}
	}
	
	function OnEditProject($newRecord,$oldRecord){
		if (!$newRecord['Code']) {
			krnLoadLib('chars');
			$code = mb_strtolower(chrTranslit($newRecord['Title']));
			$code = strtr($code,array(','=>'','.'=>'',' '=>'_','*'=>'','!'=>'','?'=>'','@'=>'','#'=>'','$'=>'','%'=>'','^'=>'','('=>'',')'=>'','+'=>'','-'=>'_','«'=>'','»'=>'','—'=>'',':'=>'',';'=>'','ь'=>''));
			dbDoQuery('UPDATE projects SET `Code`="'.$code.'" WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		} else {
			$code = $newRecord['Code'];
		}

		if ($code) {
			krnLoadLib('routing');
			Routing::DeleteRouting($oldRecord['Code'], 'projects');
			Routing::SetRouting($code, 'projects');

			if ($code != $oldRecord['Code']) {
				if (file_exists(ROOT_PATH . 'templates/' . $oldRecord['Code'] . '.htm')) {
					rename(ROOT_PATH . 'templates/' . $oldRecord['Code'] . '.htm', ROOT_PATH . 'templates/' . $code . '.htm');
				}
				if (file_exists(ROOT_PATH . 'templates/' . EN_DIR . $oldRecord['Code'] . '.htm')) {
					rename(ROOT_PATH . 'templates/' . EN_DIR . $oldRecord['Code'] . '.htm', ROOT_PATH . 'templates/' . EN_DIR . $code . '.htm');
				}
			}			
		}
	}

	function OnDeleteProject($oldRecord) {
		$code = dbGetValueFromDb('SELECT Code FROM projects WHERE Id='.$oldRecord['Id'],__FILE__,__LINE__);

		if ($code) {
			krnLoadLib('routing');
			Routing::DeleteRouting($code, 'projects');
		}
	}

	/** Tags */
	function OnAddTag($newRecord){
		if (!$newRecord['Code']) {
			krnLoadLib('chars');
			$code = mb_strtolower(chrTranslit($newRecord['Title']));
			$code = strtr($code, array(','=>'','.'=>'',' '=>'_','*'=>'','!'=>'','?'=>'','@'=>'','#'=>'','$'=>'','%'=>'','^'=>'','('=>'',')'=>'','+'=>'','-'=>'_','«'=>'','»'=>'','—'=>'',':'=>'',';'=>'','ь'=>''));
			dbDoQuery('UPDATE tags SET `Code`="'.$code.'" WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		} else {
			$code = $newRecord['Code'];
		}

		if ($code) {
			krnLoadLib('routing');
			Routing::SetRouting($code, 'blog');
		}
	}
	
	function OnEditTag($newRecord,$oldRecord){
		if (!$newRecord['Code']) {
			krnLoadLib('chars');
			$code = mb_strtolower(chrTranslit($newRecord['Title']));
			$code = strtr($code,array(','=>'','.'=>'',' '=>'_','*'=>'','!'=>'','?'=>'','@'=>'','#'=>'','$'=>'','%'=>'','^'=>'','('=>'',')'=>'','+'=>'','-'=>'_','«'=>'','»'=>'','—'=>'',':'=>'',';'=>'','ь'=>''));
			dbDoQuery('UPDATE tags SET `Code`="'.$code.'" WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		} else {
			$code = $newRecord['Code'];
		}

		if ($code) {
			krnLoadLib('routing');
			Routing::DeleteRouting($oldRecord['Code'], 'blog');
			Routing::SetRouting($code, 'blog');
		}
	}

	function OnDeleteTag($oldRecord) {
		$code = dbGetValueFromDb('SELECT Code FROM tags WHERE Id='.$oldRecord['Id'],__FILE__,__LINE__);

		if ($code) {
			krnLoadLib('routing');
			Routing::DeleteRouting($code, 'blog');
		}
	}
	
	/** Static pages */
	function OnAddStaticPage($newRecord){
		if(!$newRecord['Code']){
			krnLoadLib('chars');
			$code=mb_strtolower(chrTranslit($newRecord['Title']));
			$code=strtr($code,array(','=>'','.'=>'',' '=>'_','*'=>'','!'=>'','?'=>'','@'=>'','#'=>'','$'=>'','%'=>'','^'=>'','('=>'',')'=>'','+'=>'','-'=>'_','«'=>'','»'=>'','—'=>'',':'=>'',';'=>'','ь'=>''));
			dbDoQuery('UPDATE static_pages SET `Code`="'.$code.'", LastModTime='.time().' WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		}else{
			dbDoQuery('UPDATE static_pages SET LastModTime='.time().' WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		}		
	}
	
	function OnEditStaticPage($newRecord,$oldRecord){
		if(!$newRecord['Code']){
			krnLoadLib('chars');
			$code=mb_strtolower(chrTranslit($newRecord['Title']));
			$code=strtr($code,array(','=>'','.'=>'',' '=>'_','*'=>'','!'=>'','?'=>'','@'=>'','#'=>'','$'=>'','%'=>'','^'=>'','('=>'',')'=>'','+'=>'','-'=>'_','«'=>'','»'=>'','—'=>'',':'=>'',';'=>'','ь'=>''));
			dbDoQuery('UPDATE static_pages SET `Code`="'.$code.'", LastModTime='.time().' WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		}else{
			dbDoQuery('UPDATE static_pages SET LastModTime='.time().' WHERE Id='.$newRecord['Id'],__FILE__,__LINE__);
		}	
	}
}

?>