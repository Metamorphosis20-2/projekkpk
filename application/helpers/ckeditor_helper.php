<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * CKEditor helper for CodeIgniter
 * 
 * @author Samuel Sanchez <samuel.sanchez.work@gmail.com> - http://kromack.com/
 * @package CodeIgniter
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @tutorial http://kromack.com/developpement-php/codeigniter/ckeditor-helper-for-codeigniter/
 * @see http://codeigniter.com/forums/viewthread/127374/
 * @version 2010-08-28
 * 
 */

/**
 * This function adds once the CKEditor's config vars
 * @author Samuel Sanchez 
 * @access private
 * @param array $data (default: array())
 * @return string
 */
function cke_initialize($data = array()) {
		$return =  "<script>
		if (window.CKEDITOR === undefined) {
			var hc = $(\"<script type='text/javascript' src='". base_url(PLUGINS."ckeditor/ckeditor.js") ."'>\");
			$(\"head\").append(hc);
			CKEDITOR_BASEPATH = '" . base_url() . $data['path'] ."';
			};
		</script>
		";
	return $return;
}

/**
 * This function create JavaScript instances of CKEditor
 * @author Samuel Sanchez 
 * @access private
 * @param array $data (default: array())
 * @return string
 */
function cke_create_instance($data = array()) {
	
	$toolbar = "";
	$return = "<script type=\"text/javascript\">";
	$return .= "CKEDITOR.replace('" . $data['id'] . "', {";

	if(isset($data['toolbar'])){
		$toolbar = $data['toolbar'];
	}
	if(isset($data['toolbar'])){
		if(strtoupper($data['toolbar'])!="full"){
			$toolbar = ", customConfig: '".base_url(). $data['path'] . "/config_".$toolbar.".js'";
		}
	}
	if(isset($data["readonly"])){
		if($data["readonly"]){
			$toolbar = ",toolbar: [], removePlugins: 'elementspath',resize_enabled: false";
		}
	}	
	
  	if(isset($data['config'])) {
		$rc = false;
		foreach($data['config'] as $k=>$v) {
			if($rc) $return .= ",";
			if (is_array($v)) {
				$return .= $k . " : [";
				$return .= config_data($v);
				$return .= "]";
			}else{
  			$return .= $k . " : '" . $v . "'";
  		}	
			$rc = true;  		
		}
	}
	$return .= $toolbar . "
	 });
	</script>";
	// debug_array($return);
  return $return;

}
function cke_create_instancse($data = array()) {
  $return = "
  	<script type=\"text/javascript\">
     	CKEDITOR.replace('" . $data['id'] . "', {";
	//Adding config values
	if(isset($data['config'])) {
		$rc = false;
 		foreach($data['config'] as $k=>$v) {
 			if($rc) $return .= ",";

 			// Support for extra config parameters
			if (is_array($v)) {
				$return .= $k . " : [";
				$return .= config_data($v);
				$return .= "]";
			}else{
  			$return .= $k . " : '" . $v . "'";
  		}
			// $file_extension = end((explode('.', $file_name)));
  			// 	if($k !== end((array_keys($data['config'])))) {
			// 	$return .= ",";
			// }
			$rc = true;    			
  	} 
	}   			
  $return .= '});</script>';	
  return $return;
}

/**
 * This function displays an instance of CKEditor inside a view
 * @author Samuel Sanchez 
 * @access public
 * @param array $data (default: array())
 * @return string
 */
function display_ckeditor($data = array())
{
	// Initialization
	$return = cke_initialize($data);
	
  // Creating a Ckeditor instance
  $return .= cke_create_instance($data);
	
  //   // Adding styles values
  // if(isset($data['styles'])) {
		// $rc = false;
  // 	$return .= "<script type=\"text/javascript\">CKEDITOR.addStylesSet( 'my_styles_" . $data['id'] . "', [";
  //   foreach($data['styles'] as $k=>$v) {
  //   	if($rc) $return .= ",";
  //   	$return .= "{ name : '" . $k . "', element : '" . $v['element'] . "', styles : { ";
  //   	if(isset($v['styles'])) {
		// 		$rcin = false;
  //   		foreach($v['styles'] as $k2=>$v2) {  			
  //   			if($rcin) $return .= ",";
	 //    		$return .= "'" . $k2 . "' : '" . $v2 . "'";	    			
		// 			// if($k2 !== end(array_keys($v['styles']))) {
		// 			// 	$return .= ",";
		// 			// }
		// 			$rcin = true;
	 //    	} 
  //   	} 
  //   	$return .= '} }';
  //  //  	if($k !== end(array_keys($data['styles']))) {
		// 	// 	$return .= ',';
		// 	// }
		// 	$rc = true;  	
	 //  } 
	    
	 //  $return .= ']);';
		// $return .= "CKEDITOR.instances['" . $data['id'] . "'].config.stylesCombo_stylesSet = 'my_styles_" . $data['id'] . "';
		// </script>";		
  // }
  return $return;
}

/**
 * config_data function.
 * This function look for extra config data
 *
 * @author ronan
 * @link http://kromack.com/developpement-php/codeigniter/ckeditor-helper-for-codeigniter/comment-page-5/#comment-545
 * @access public
 * @param array $data. (default: array())
 * @return String
 */
function config_data($data = array())
{
	$return = '';
	foreach ($data as $key)
	{
		if (is_array($key)) {
			$return .= "[";
			foreach ($key as $string) {
				$return .= "'" . $string . "'";
				if ($string != end(array_values($key))) $return .= ",";
			}
			$return .= "]";
		}
		else {
			$return .= "'".$key."'";
		}
		if ($key != end(array_values($data))) $return .= ",";

	}
	return $return;
}