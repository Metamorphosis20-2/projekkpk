<?
class Nosj extends MY_Controller{
    public function __construct() {
        parent::__construct();
        $this->load->model(array('m_master'));
        $this->load->library('gjson');
    }
    function getNosj_list($function, $type="list",$model="crud", $idents=null){
        if($model!="crud"){
        $this->load->model($model);
        }
        $jdata = $this->{$model}->{'get'.$function."_".$type}($idents);  
        $this->gjson->returnjson($jdata);
    }  
    function getNosjnull(){
        echo "[{\"TotalRows\":\"0\",\"Rows\":[]}]";
    }
    function getNosjnullarray(){
        $hasil = array();
    }
    function getExpensedetail_list($from, $anaKind, $anaGrant, $anaYear, $anaMonth1, $anaMonth2=0, $anaRefkey, $refkeydetail=0){
        $this->load->model("m_analisa");
        $jdata = $this->m_analisa->getExpensedetail_list($from, $anaKind, $anaGrant, $anaYear, $anaMonth1, $anaMonth2, $anaRefkey, $refkeydetail);
        $this->gjson->returnjson($jdata);
    }
    function getExpensesummary_list($from, $anaKind, $anaGrant, $anaYear, $anaMonth1, $anaMonth2, $anaRefkey, $refkeydetail=0){
        $this->load->model("m_analisa");
        $jdata = $this->m_analisa->getExpensesummary_list($from, $anaKind, $anaGrant, $anaYear, $anaMonth1, $anaMonth2, $anaRefkey, $refkeydetail);
        $this->gjson->returnjson($jdata);
    }
    function getBVAdetail_list($anaKind, $anaGrant, $anaYear, $anaMonth1){
        $this->load->model("m_analisa");
        $jdata = $this->m_analisa->getBVAdetail_list($anaKind, $anaGrant, $anaYear, $anaMonth1);
        $this->gjson->returnjson($jdata);
    }
    function getMismatch_list($anaGrant, $anaYear, $anaMonth1){
        $this->load->model("m_analisa");
        $jdata = $this->m_analisa->getMismatch_list($anaGrant, $anaYear, $anaMonth1);
        $this->gjson->returnjson($jdata);
    }
    function getBVAdetaildata_list($anaKind, $anaGrant, $anaYear, $anaMonth1){
        $this->load->model("m_analisa");
        $jdata = $this->m_analisa->getBVAdetail_list($anaKind, $anaGrant, $anaYear, $anaMonth1);
        $this->gjson->returnjson($jdata);
    }
    function getSalarydetailBenefitSalary($grant=null){
        $this->load->model("m_grafik");
        $jdata = $this->m_grafik->getSalarydetailBenefitSalary($grant);
        echo json_encode($jdata->result());
    }
    function getSalarygrant_list($from, $type, $grant, $source, $series=null){
        $this->load->model("m_analisa");
        $series = urldecode($series);
        // $this->common->debug_array($series);
        $jdata = $this->m_analisa->getSalarygrant_list($from, $type, $grant, $source, $series);
        $this->gjson->returnjson($jdata);
    }
    function getSalarygrantdetail_list($from, $type, $grant, $source, $series=null){
        $this->load->model("m_analisa");
        $series = urldecode($series);
        $jdata = $this->m_analisa->getSalarygrantdetail_list($from, $type, $grant, $source, $series);
        $this->gjson->returnjson($jdata);
    }
    function grfSalarylocation($anaGrant=null, $anaLocation=null){
        $this->load->model("m_grafik");
        $jdata = $this->m_grafik->grfSalarylocation($anaGrant, $anaLocation);
        echo json_encode($jdata->result());
    }
    function grfSalarylevel($anaGrant=0, $anaLevel=0){
        $this->load->model("m_grafik");
        $jdata = $this->m_grafik->grfSalarylevel($anaGrant, $anaLevel);
        echo json_encode($jdata->result());
    }
    function getSalarydetaillocation($anaGrant, $anaLocation){
        $this->load->model("m_grafik");
        $jdata = $this->m_grafik->getSalarydetaillocation($anaGrant, $anaLocation);
        echo json_encode($jdata->result());
    }
    function grfIncident($jenis, $inc_type_incident, $inc_category_incident, $anaYear, $anaMonth1, $anaMonth2){
        $this->load->model("m_incident");
        switch($jenis){
            case "location":
                $jdata = $this->m_incident->grfIncidentlocation($inc_type_incident, $inc_category_incident, $anaYear, $anaMonth1, $anaMonth2);
                $result = $jdata->result();
                break;
            case "category":
                $jdata = $this->m_incident->grfIncidentCategory($inc_type_incident, $inc_category_incident, $anaYear, $anaMonth1, $anaMonth2);
                $result = $jdata->result();
                break;
            case "yearly":
                $jdata = $this->m_incident->grfIncidentYearly($inc_type_incident, $inc_category_incident, $anaYear, $anaMonth1, $anaMonth2);
                $result = $jdata;
                break;
        }
        echo json_encode($result);
    }
}