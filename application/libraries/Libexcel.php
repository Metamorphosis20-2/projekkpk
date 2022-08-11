<?php
require_once APPPATH."/third_party/PHPSpreadsheet/vendor/autoload.php"; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Libexcel {
	function __construct() {
		$this->CI =& get_instance();
	}
	public function bangunexcel($parameter){
		$is_html = false;
    	$bentuk = "spreadsheet";
    	$alphas = range("A", "Z");
		$objSpreadsheet = new Spreadsheet();
		$sheetname = "Sheet 1";
		$filename = "exported_to_excel";
		$pCreator = "";
		$pTitle = "";
		$pSubject = "";
		$pDescription = "";
		$pKeywords = "";
		$pCategory = "";
		$fromhtml = false;
    	foreach ($parameter as $key => $value) {
    		${$key}=$value;
		}

		if(isset($html)){
			$fromhtml = true;
		}else{
			if(!isset($col)){
				echo "Definisi Kolom tidak ada!";
				die();
			}else{
				$jumlahKolom = count($col);
				if($jumlahKolom==0){
					echo "Definisi Kolom tidak ada!";
					die();
				}else{
					if($jumlahKolom>26){
						$alphas = $this->createColumnsArray("c");
					}
				}
			}
		}
		// Set document properties
		$objSpreadsheet->getProperties()->setCreator($pCreator)
			->setTitle($pTitle)
			->setSubject($pSubject)
			->setDescription($pDescription)
			->setKeywords($pKeywords)
			->setCategory($pCategory);

		$objSpreadsheet->setActiveSheetIndex(0)
			->setTitle($sheetname);

		if(!$fromhtml){
			if(isset($header_height)){
				$objSpreadsheet->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight($header_height);
			}
			$loop = 1;
			$arrKolom =0;
			foreach ($col as $colvalue) {
				foreach ($colvalue as $keycol => $valuecol) {
					${$keycol}=$valuecol;
				}
				// debug_array($col);
				if(isset($nilai)){
					if($nilai!=""){
						$arrHeader[] = $nilai;
						$objSpreadsheet->getActiveSheet()->setCellValue($alphas[$arrKolom]."1", $nilai);
					}
				}
				if($bentuk=="spreadsheet"){
					if(isset($fontsize)){
						if($fontsize!=0){
							$objSpreadsheet->getActiveSheet()->getStyle($alphas[$arrKolom]."1")->getFont()->setSize($fontsize);
						}
					}
					if(isset($bold)){	
						if($bold){
							$objSpreadsheet->getActiveSheet()->getStyle($alphas[$arrKolom]."1")->getFont()->setBold(true);
						}
					}
					if(isset($italic)){	
						if($italic){
							$objSpreadsheet->getActiveSheet()->getStyle($alphas[$arrKolom]."1")->getFont()->setItalic(true);
						}
					}
					if(isset($horizontal_align)){	
						$objSpreadsheet->getActiveSheet()->getStyle($alphas[$arrKolom]."1")->getAlignment()->setHorizontal($horizontal_align);
					}
	
					if(isset($wraptext)){	
						if($wraptext){
							$objSpreadsheet->getActiveSheet()->getStyle($alphas[$arrKolom]."1")->getAlignment()->setWrapText(true);
						}
					}
				}
				$loop++;
				$arrKolom++;			
			}
	
			if(isset($rsl)){
				$nomor = 1;
				$rowloc = 2;
				$arrKolom =0;
				foreach($arrHeader as $key=>$value){
					$objSpreadsheet->getActiveSheet()->setCellValue($alphas[$arrKolom]."1", $value);
					$arrKolom++;
				}
				// if(is_array($rsl)){
					foreach ($rsl->result() as $key => $value) {
						set_time_limit(20);						
						$col_loc=0;
						$arrKolom =0;
						foreach ($col as $key=>$valuecol) {
							foreach ($valuecol as $key_col=>$value_col) {
								${$key_col} = $value_col;
								if($bentuk=="spreadsheet"){
									if(isset($format)){
										switch($format){
											case "string":
												$formatnya = "string";
												$objSpreadsheet->getActiveSheet()->getStyle($alphas[$arrKolom].$rowloc)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
												$objSpreadsheet->getActiveSheet()->getStyle($alphas[$arrKolom].$rowloc)->getNumberFormat()->setFormatCode('#');
												break;
											case 'angkakoma':
												$objSpreadsheet->getActiveSheet()->getStyle($alphas[$arrKolom].$rowloc)->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');
												break;	
											case 'datetime':
												$objSpreadsheet->getActiveSheet()->getStyle($alphas[$arrKolom].$rowloc)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
												break;
										}
									}
								}
								if(isset($namanya)){
									if($namanya=="nomor"){
										$valueval = $nomor;	
									}else{
										if(isset($formatnya)){
											$valueval = $value->$namanya . "";	
										}else{
											$valueval = $value->$namanya;		
										}
									}
									$objSpreadsheet->getActiveSheet()->setCellValue($alphas[$arrKolom].$rowloc, $valueval);
									$objSpreadsheet->getActiveSheet()->getColumnDimension($alphas[$arrKolom])->setAutoSize(true);
								}
							}
							$arrKolom++;	
						}
						$rowloc++;
						$nomor++;
					}
				// }
				// die();
			}
		}else{
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
			$objSpreadsheet = $reader->loadFromString($html);
			$is_html = true;
		}

		if($bentuk=="spreadsheet"){
			$objSpreadsheet->setActiveSheetIndex(0);
			if($is_html){
				$sheet = $objSpreadsheet->getActiveSheet();
				foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
					$sheet->getColumnDimension($column)->setAutoSize(true);
				}
			}
			// Redirect output to a client’s web browser (Xlsx)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$filename. '_' . date('Y_m_d_His').'.xlsx"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');
			
			// If you're serving to IE over SSL, then the following may be needed
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
			header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header('Pragma: public'); // HTTP/1.0
			
			$writer = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
			$writer->save('php://output');
		}else{
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			// header("Content-Disposition: attachment;filename=".$filename."");
			header("Content-Transfer-Encoding: binary ");        
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y_m_d_His'). '.csv"');
			// header('Pragma: no-cache');
			// header('Expires: 0');
			// create a file pointer connected to the output stream
			$file = fopen('php://output', 'w');
				
			// send the column headers
			fputcsv($file, $arrHeader);
			unset($namanya);
			foreach ($rsl->result() as $key => $value) {
				set_time_limit(20);						
				$col_loc=0;
				$arrKolom =0;
				$row = array();
				foreach ($col as $keycol=>$valuecol) {
					foreach ($valuecol as $key_col=>$value_col) {
						${$key_col} = $value_col;
						if(isset($namanya)){
							$valueval = $value->$namanya;
						}
					}
					$row = array_merge($row, array($valueval));
				}
				fputcsv($file, $row);
			}
		}

		exit;
	}

    function createColumnsArray($end_column, $first_letters = ''){
		$columns = array();
		$length = strlen($end_column);
		$letters = range('A', 'Z');
		// Iterate over 26 letters.
		foreach ($letters as $letter) {
			// Paste the $first_letters before the next.
			$column = $first_letters . $letter;
			// Add the column to the final array.
			$columns[] = $column;
			// If it was the end column that was added, return the columns.
			if ($column == strtoupper($end_column))
				return $columns;
		}
		// Add the column children.
		foreach ($columns as $column) {
			// Don't itterate if the $end_column was already set in a previous itteration.
			// Stop iterating if you've reached the maximum character length.
			if (!in_array(strtoupper($end_column), $columns) && strlen($column) < $length) {
				$new_columns = $this->createColumnsArray(strtoupper($end_column), $column);
				// Merge the new columns which were created with the final columns array.
				$columns = array_merge($columns, $new_columns);
			}
		}

		return $columns;
	}
	public function xx(){

		$spreadsheet = new Spreadsheet();

		// Set document properties
		$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
			->setLastModifiedBy('Maarten Balliauw')
			->setTitle('Office 2007 XLSX Test Document')
			->setSubject('Office 2007 XLSX Test Document')
			->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
			->setKeywords('office 2007 openxml php')
			->setCategory('Test result file');
		
		// Add some data
		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A1', 'Hello')
			->setCellValue('B2', 'world!')
			->setCellValue('C1', 'Hello')
			->setCellValue('D2', 'world!');
		
		// Miscellaneous glyphs, UTF-8
		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A4', 'Miscellaneous glyphs')
			->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
		
		// Rename worksheet
		$spreadsheet->getActiveSheet()->setTitle('Simple');
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);
		
		// Redirect output to a client’s web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01simple.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0
		
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;		
	}
	public function example($parameter=null){
        $spreadsheet = new Spreadsheet(); // inisisasi spreadsheet
		$sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', 'Gipsy Danger'); 
        $sheet->setCellValue('B2', 'Gipsy Avenger');
        $sheet->setCellValue('B3', 'Striker Eureka');
        
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx
 
        $filename = $parameter; // set filename for excel file to be exported
 
        header('Content-Type: application/vnd.ms-excel'); // generate excel file
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');	// download file 		
	}
	public function readsheet($parameter){
		// debug_array($parameter);
		ini_set('memory_limit', '-1'); 
		foreach($parameter as $key=>$value){
			${$key} = $value;
		}
		
		// $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
		// $spreadsheet = $reader->load($file);
		if(!isset($inputFileType)){
			$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file);
		}
		
		// $this->CI->common->debug_array($inputFileType);
		// $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
		// $reader->setReadDataOnly(true);

		$spreadsheet = $reader->load($file);;
		// $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
		
		return $spreadsheet;
	}
}