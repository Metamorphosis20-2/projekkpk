<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('DOMPDF_ENABLE_AUTOLOAD', false);
define("DOMPDF_FONT_HEIGHT_RATIO", 1.1);
// define('DOMPDF_FONT_HEIGHT_RATIO', 2);
// require_once("./vendor/dompdf/dompdf/dompdf_config.inc.php");
include_once APPPATH.'third_party/dompdf/vendor/autoload.php'; 
// use Dompdf\Dompdf;

class Dompdf {
  function __construct(){ 
      $this->CI =& get_instance();
      
  } 
  // public function generate($html, $filename='', $stream=TRUE, $paper = 'A4', $orientation = "portrait", $server)
  public function generate($parameter)
  {
    // 
    $html = "<b>No Data</b>";
    $filename="filepdf";
    $stream=TRUE;
    $path = "";
    $paper = 'A4';
    $orientation = "portrait";
    $server = false;

    foreach($parameter as $key=>$value){
      ${$key} = $value;
    }    
    $dompdf = new Dompdf\Dompdf();

    $dompdf->load_html($html);
    $dompdf->set_paper($paper, $orientation);
    $dompdf->render();
    if ($stream) {
        // $dompdf->stream($filename.".pdf", array("Attachment" => 0));
      $Attachment = 0;
    } else {
      $Attachment = 1;
        // return $dompdf->output();
    }
    if($server){
      $Attachment = 1;
    }
    // $output = $dompdf->stream($filename.".pdf", array("Attachment" => $Attachment));
    if($server){
      // $dompdf->stream($filename.".pdf");
      $output = $dompdf->output();
      $path = $_SERVER["DOCUMENT_ROOT"]."/".$path;
      file_put_contents($path . $filename.".pdf", $output);
    }else{
      $dompdf->stream($filename.".pdf", array("Attachment" => $Attachment));
    }
    // file_put_contents('/temp/filename.pdf', $output);
  }
}