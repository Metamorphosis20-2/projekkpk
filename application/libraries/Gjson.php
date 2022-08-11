<?php  if (!defined('BASEPATH')) exit(header("Location: /"));
/**
 * Common Library
 *
 * @package		 CodeIgniter
 * @subpackage Library
 * @category	 Commonly Use Function Library
 *
 * @author		 detanto / detanto[at]gmail.com
 * @project		 kemas indah maju
 * Modified 2009 10 11
 */
 
class Gjson {
  
  var $mytable;
  var $CI;
  var $label;
  var $kind;
  
  function __construct() {
    $this->CI =& get_instance();
    $this->label = $this->CI->config->item('label');
    $this->kind = $this->CI->config->item('kind');
  }
  function setTable($table){
    $this->mytable=$table;
  }
  public function nosjforall($table, $col, $jdata, $idnya, $page, $param=null){
    if($col==""){
      $col = generateGrid('col', $table, $this->label, $this->kind);
      //var_dump($col);
      $idnya = substr(key($col),0,3) . "_IDENTS";
    }
    $numrows = $jdata['record_count'];
    $total =  $jdata['record_pages'];
    echo returnjson($param, $idnya, $numrows, $page, $total, $col, $jdata['records']);      
  }
  function returnjsontree($pass){
    $all = false;
    $CI = get_instance();
    foreach ($pass as $param=>$value){
     ${$param}=$value;
    }
    $json = '[';
    if($all){
      $json .= '{"id":"0","text":"SEMUA DATA","parentid":""},';  
    }
    
    $rc = false;
    foreach ($Hasil as $key=>$value){
      if($rc) $json .= ",";
      $rcin = false;
      $json .= "{";
      foreach($value as $val1=>$val2){
        if($rcin) $json .= ",";
        $cValue = trim(preg_replace('/\t+/', '', $val2));
        // $cValue = trim(preg_replace('/ +/', ' ', preg_replace('/[^,.:;\/A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($cValue))))));  
        $cValue = str_replace(array("\n", "\t", "\r"), '', $cValue);
        $json .= '"' . $val1 . '":"' .$cValue . '"';
        $rcin=true;
      }
      $json .= "}";
      $rc = true;
    }
    $json .= ']';
    echo $json;
  }
  function returnjson($pass, $col=null, $processfield=null, $split = null, $model='crud', $function='getEmployee'){  
    $CI = get_instance();
    $TotalRows = 0;
    $type = "grid";
    foreach ($pass as $param=>$value){
     ${$param}=$value;
    }
    switch($type){
      case "grid":
        $json = '[{"TotalRows":"' . $TotalRows . '",';
        $json .= '"Rows":[';
        break;
      case "datatables":
        $json = '{"draw":"' . $draw . '",';
        $json .= '"recordsTotal":"' . $recordsTotal . '",';
        $json .= '"recordsFiltered":"' . $recordsFiltered . '",';
        $json .= '"data":[';
        break;
      case "combo":
        $json = '{"results":[';
        break;
      default:
        $json = '[';
        break;
    }
    $rc = false;
    $coldetail="";
		foreach ($Hasil as $key=>$value){
			if($rc) $json .= ",";
      $rcin = false;
      $json .= "{";
      $loop = 1;
      if(is_array($col)){
        $coldetail = $col;
      }
      foreach($value as $val1=>$val2){
        // if($rcin) $json .= ",";
        $cValue = trim(preg_replace('/\t+/', '', $val2));
        // $cValue = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($cValue))))));
        if(strpos("0" .$val1, "HTMLTG")==0){
          // $cValue = trim(preg_replace('/ +/', ' ', preg_replace('/[^,.:;\/A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($cValue))))));  
          // $cValue = preg_replace('/[^A-Za-z0-9 \-]/', '', urldecode(html_entity_decode(strip_tags($cValue)))); 
          $cValue = str_replace("\"", "'", $cValue);
          $cValue = str_replace("\\", "'", $cValue);
          $cValue = str_replace(array("\n", "\t", "\r"), '', $cValue);
        }else{
          $cValue = str_replace(array("\n", "\t", "\r"), '', $cValue);
        }
        if($loop==1){
          if(is_array($processfield)){
            foreach ($processfield as $key_p => $value_p) {
              if(!isset($value->$key_p)){
                if(is_array($split)){
                  $arrIDENTS = explode("/", $value->$split[0]);
                  // 
                  ${$key_p} = "";
                  if(isset($arrIDENTS[$split[1]])){
                    if(isset($split[2])){
                      // $CI->common->debug_array($arrIDENTS[$split[1]]);
                     $splitnya = trim(str_replace($split[2], "", strtoupper($arrIDENTS[$split[1]]))); 
                    }else{
                      $splitnya = $arrIDENTS[$split[1]];
                    }
                    ${$key_p} = $splitnya;
                  }                  
                }else{
                  ${$key_p} = "";
                }

              }else{
                ${$key_p} = $value->$key_p;
              }
              if(is_array($value_p)){
                $CI->load->model($model);
                $return = $CI->{$model}->{$function}(${$key_p});
                // $CI->common->debug_array(${$key_p});
                foreach ($return as $key_ret => $value_ret) {
                  ${$key_ret}=$value_ret;
                }              
              }else{
                if(isset($arrIDENTS[$value_p])){
                  $aValue = trim(preg_replace('/\t+/', '', $arrIDENTS[$value_p]));
                  $aValue = str_replace(array("\n", "\\", "\t", "\r"), ' ', $aValue);
                  ${$key_p} = $aValue;
                }
              }
            }
          }        
        }
        if(is_array($coldetail)){
          for($x=0;$x<count($coldetail);$x++){
            if(trim($coldetail[$x])==trim($val1)){
              if($rcin) $json .= ",";
              $json .= '"' . $val1 . '":"' .$cValue .'"';
              unset($coldetail[$x]);  
              $coldetail=array_values($coldetail);
              $rcin=true;
            }else{
              if(isset(${$coldetail[$x]})){
                if($rcin) $json .= ",";
                $json .= '"' . $coldetail[$x] . '":"' . ${$coldetail[$x]} . '"';
                $rcin=true;                
                unset(${$coldetail[$x]});               
              }
            }
          }    
        }else{
          if($rcin) $json .= ",";
          $json .= '"' . $val1 . '":"' .$cValue . '"';  
          $rcin=true;
        }

        $loop++;
      }
      $json .= "}";
      $rc=true;
		}
    if($type=="grid"){
        
    }else{
      
    }
    switch($type){
      case "grid":
        $json .= ']}]';
        break;
      case "datatables":
        $json .= ']}';
        break;
      case "combo":
        $json .= ']}';
        break;
      default:
        $json .= ']';
        break;
    }    
    header('Content-Type: application/json');
    echo $json;
  }

  function returnjsontreegrid($pass){
    $CI = get_instance();
    $json = '{"data":[';
    $rc = false;
    foreach ($pass as $key=>$value){
      if($rc) $json .= ",";
      $rcin = false;
      $json .= "{";
      foreach($value as $val1=>$val2){
        if($rcin) $json .= ",";
        $cValue = trim(preg_replace('/\t+/', '', $val2));
        $cValue = str_replace("\"", "'", $cValue);
        $cValue = str_replace("\\", "'", $cValue);
        $cValue = str_replace(array("\n", "\t", "\r"), '', $cValue);
        
        // $cValue = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($cValue))))));
        if(strpos("0" .$val1, "HTMLTG")==0){
          // $cValue = trim(preg_replace('/ +/', ' ', preg_replace('/[^,.:;\/A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($cValue))))));  
        }
        // if(is_numeric($cValue)){
        //   $json .= '"' . $val1 . '":' .$cValue;    
        // }else{
          $json .= '"' . $val1 . '":"' .$cValue . '"';  
        // }
        
        $rcin=true;
      }
      $json .= "}";
      $rc=true;
    }
    $json .= ']}';
    
    echo $json;
  }
  function returnjsoncombo($pass){
    $CI = get_instance();
    $json = '{"result":[';
    $rc = false;

    $json .= ']}';
  }
}
