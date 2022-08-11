<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * CKEditor helper for highchart
 * 
 * @author detanto
 * @package CodeIgniter
 * 
 */

 /** initialize highchart
  * based on type call highchart type
  * */
function axpexchart_initialize($data = array()) {
    $return =  "
    <script src='". base_url(PLUGINS."apexcharts/dist/apexcharts.js") ."'></script>
    <link href='". base_url(PLUGINS."apexcharts/dist/apexcharts.css") . "' rel='stylesheet' type='text/css'/>
    ";
	return $return;
}
function apexchart_initialize($data = array()) {
    $return =  '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>';
	return $return;
}
function random_color(){  
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}
function apexchart_create_instance($data = array()){
	$CI =& get_instance();
    $return = null;
    $series = null;
    $labels = null;
    $script = "<script>";
    $color = null;
    $title = null;
    foreach($data as $key=>$value){
        ${$key} = $value;
    }
    foreach($fields as $keyf=>$valuef){
        ${$keyf} = $valuef;
    }
    if(isset($resultset)){
        $rc = false;
        foreach($resultset->result() as $keyrs=>$valuers){
            if ($rc) $series .= ",";
            if ($rc) $labels .= ",";
            $grf_descre = $valuers->{$descre};
            $grf_values = $valuers->{$values};
            if($grf_descre==""){
                $grf_descre = "Tidak Ada";
            }
            $color .= "'" . random_color() . "'";
            $series .= $grf_values;
            $labels .= '"' . $grf_descre . '"';
            $rc = true;
        }
    }
    switch($chart){
        case "pie":
            $script .= '
            new Chart(document.getElementById("'.$id.'"), {
                type: "pie",
                data: {
                  labels: ['.$labels.'],
                  datasets: [
                    {
                      label: "'.$labelling.'",
                      fill: true,
                      backgroundColor: ['.$color.'],
                      data: ['.$series.']
                    }
                  ]
                },
                options: {
                  title: {
                    display: true,
                    text: "Distribution in % of world population"
                  }
                }
            });            
            ';
            break;        
        case "spider":
            $script .= '
            new Chart(document.getElementById("'.$id.'"), {
                type: "radar",
                data: {
                  labels: ['.$labels.'],
                  datasets: [
                    {
                      label: "'.$labelling.'",
                      fill: true,
                      backgroundColor: "'.$warna.'",
                      borderColor: "'.$warna.'",
                      pointBorderColor: "#fff",
                      pointBackgroundColor: "'.$warna.'",
                      pointBorderColor: "#fff",
                      data: ['.$series.']
                    }
                  ]
                },
                options: {
                  title: {
                    display: true,
                    text: "Distribution in % of world population"
                  }
                }
            });            
            ';
            break;
        case "bar":
            $script .= '
            new Chart(document.getElementById("'.$id.'"), {
                type: "horizontalBar",
                data: {
                  labels: ['.$labels.'],
                  datasets: [
                    {
                      label: "'.$labelling.'",
                      backgroundColor: ['.$color.'],
                      data: ['.$series.']
                    }
                  ]
                },
                options: {
                  title: {
                    display: true,
                    text: "'.$title.'"
                  }
                }
            });            
            ';
            break;
            
    }
    $script .= "</script>";
    $return = '<canvas id="'.$id.'" width="800" height="600"></canvas>';
    $return .= $script;
    return $return;
}
function axpexchart_create_instance($data = array()){
	$CI =& get_instance();
    $return = null;
    $series = null;
    $labels = null;
    $script = "<script>";
    foreach($data as $key=>$value){
        ${$key} = $value;
    }
    switch($chart){
        case "pie":
            foreach($fields as $keyf=>$valuef){
                ${$keyf} = $valuef;
            }
            if(isset($resultset)){
                $rc = false;
                foreach($resultset->result() as $keyrs=>$valuers){
                    if ($rc) $series .= ",";
                    if ($rc) $labels .= ",";
                    $grf_descre = $valuers->{$descre};
                    $grf_values = $valuers->{$values};
                    if($grf_descre==""){
                        $grf_descre = "Tidak Ada";
                    }
                    $series .= $grf_values;
                    $labels .= "'" . $grf_descre . "'";
                    $rc = true;
                }
            }
            $script .= "
                    var options = {
                        series: [" . $series . "],
                        chart: {
                            width: '".$width."',
                            type: 'pie',
                        },
                        labels: [" . $labels . "],
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    };
            ";
            break;
        case "spider":
            foreach($fields as $keyf=>$valuef){
                ${$keyf} = $valuef;
            }
            if(isset($resultset)){
                $rc = false;
                foreach($resultset->result() as $keyrs=>$valuers){
                    if ($rc) $series .= ",";
                    if ($rc) $labels .= ",";
                    $grf_descre = $valuers->{$descre};
                    $grf_values = $valuers->{$values};
                    if($grf_descre==""){
                        $grf_descre = "Tidak Ada";
                    }
                    $series .= $grf_values;
                    $labels .= "'" . $grf_descre . "'";
                    $rc = true;
                }
            }            
            $script .= "
            var options = {
                series: [{
                    name: 'Series 1',
                    data: [".$series."],
                }],
                chart: {
                    width: '100%',
                    height: '100%',
                    type: 'radar',
                },
                title: {
                    text: 'Basic Radar Chart'
                },
                xaxis: {
                    categories: [".$labels."]
                }
            };
            ";
            break;
    }
    $script .= "var chart = new ApexCharts(document.querySelector('#".$id."'), options);chart.render();";
    $script .= "</script>";
    $return = "<div id='".$id."' style='width:100%;height:100%'></div>";
    $return .= $script;
	return	$return;
}
function display_chart($data = array(), $defined=false){
	// Initialization
	//
	$return = apexchart_initialize($data);
	$return .= apexchart_create_instance($data);
	return $return;
}
