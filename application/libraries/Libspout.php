<?php
//Set Execution Time
ini_set('max_execution_time', 30000);
ini_set('memory_limit','12288M');
//Display Error Messages
error_reporting(-1);
ini_set('display_errors', 'On');

require_once APPPATH."/third_party/spout/vendor/autoload.php"; 

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Libspout {
	function __construct() {
		$this->CI =& get_instance();
	}
	public function readsheet($parameter){
		ini_set('memory_limit', '-1'); 
		foreach($parameter as $key=>$value){
			${$key} = $value;
		}
        $reader = ReaderEntityFactory::createReaderFromFile($file);
        $reader->setShouldFormatDates(false);
        $reader->open($file);
		return $reader;
	}
            // foreach ($sheet->getRowIterator() as $row) {
            //     // $cells = $row->getCells();
            //     $spreadsheet[$loop] = $row->toArray();
            //     // $this->CI->common->debug_array($spreadsheet, false);
            //     // if($loop==45){
            //     //     die();
            //     // }
            //     $loop++;
            // }
        // $this->CI->common->debug_array($spreadsheet);
        // $reader->close();

}