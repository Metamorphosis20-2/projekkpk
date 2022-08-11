<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
    function berkas($path){
		// $this->common->debug_array($_FILES);
        $filename = null;
        $pathfile = "/assets/" . $path;
        // $this->common->debug_array($pathfile);
        // $this->common->debug_array($_FILES['file']['tmp_name']);
        if (!empty($_FILES)) {
            $pathupload = $_SERVER["DOCUMENT_ROOT"] . $pathfile;
            // foreach($_FILES['file']['tmp_name'] as $key => $value) {
                $tempFile = $_FILES['file']['tmp_name'];
                $filename = time().'-'. $_FILES['file']['name'];
                $targetFile = $pathupload.'/'. $filename;
                move_uploaded_file($tempFile,$targetFile);
            // }
            echo $filename;
        }
    }
	function multipleberkas($path){
        $filename = null;
        $pathfile = "/assets/" . $path;
        if (!empty($_FILES)) {
            $pathupload = $_SERVER["DOCUMENT_ROOT"] . $pathfile;
            foreach($_FILES['file']['tmp_name'] as $key => $value) {
                $tempFile = $_FILES['file']['tmp_name'][$key];
                $filename = time().'-'. $_FILES['file']['name'][$key];
                $targetFile = $pathupload.'/'. $filename;
                $file[] = $filename;
                move_uploaded_file($tempFile,$targetFile);
            }

            echo json_encode($file);
        }        
	}
}
