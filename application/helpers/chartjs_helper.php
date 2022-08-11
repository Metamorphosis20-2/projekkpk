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
function chartjs_initialize($data = array()) {
  // $return =  '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>';
  $return =  '<script src="' . base_url(PLUGINS."chartjs/Chart.min.js") .'"></script>';
	return $return;
}
function xrandom_color(){  
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}
function random_color() {
    // $str = '#';
    // for($i = 0 ; $i < 3 ; $i++) {
    //     $str .= dechex( rand(170 , 255) );
    // }
    $str = '#';
    for ($i = 0; $i < 6; $i++) {
        $randNum = rand(0, 15);
        switch ($randNum) {
            case 10: $randNum = 'A';
                break;
            case 11: $randNum = 'B';
                break;
            case 12: $randNum = 'C';
                break;
            case 13: $randNum = 'D';
                break;
            case 14: $randNum = 'E';
                break;
            case 15: $randNum = 'F';
                break;
        }
        $str .= $randNum;
    }
    return $str;
}
function chartjs_create_instance($data = array()){
	$CI =& get_instance();
    $return = null;
    $series = null;
    $labels = null;
    $script = "<script>";
    $color = null;
    $title = null;
    $limit = 0;
    $show = true;
    $legend_display = "'false'";
    foreach($data as $key=>$value){
        ${$key} = $value;
    }
    foreach($fields as $keyf=>$valuef){
        ${$keyf} = $valuef;
    }

    if(isset($resultset)){
        $rc = false;
        if(is_array($values)){
          $arrColor = array("#5bc0de", "#0275d8", "#d9534f", "#5cb85c", "#f0ad4e");
          if($resultset->num_rows()>0){
 
            foreach($resultset->result() as $keyrs=>$valuers){
              if ($rc) $labels .= ",";
              $grf_descre = $valuers->{$descre};
              if($grf_descre==""){
                  $grf_descre = "Tidak Ada";
              }
              if($limit>0){
                $len_descre = strlen($grf_descre);
                if($len_descre>$limit){
                  $grf_descre = substr($grf_descre, 0, $limit + 1) . "...";
                }
              }
              $labels .= '"' . $grf_descre . '"';
              $rc=true;
              foreach($values as $keyvalues){
                $datanya[$keyvalues][] = $valuers->$keyvalues;
              }
            }
            $datasets = null;
            $rc = false;
            $loopcolor = 0;
            $total_data = 0;
            foreach($datanya as $key_data=>$value_data){
              if($rc) $datasets.= ",";
              $datasets .= "{";
              $datasets .= "  label:'" . $key_data ."',";
              $datasets .= "  backgroundColor:'".$arrColor[$loopcolor]."',";
              $datasets .= "  data:[";
              $rc_value_data = false;
              foreach($value_data as $key_value_data){
                if($rc_value_data) $datasets.= ",";
                $datasets .= $key_value_data;
                $total_data = $total_data + $key_value_data;
                $rc_value_data = true;
              }
              $datasets .= "]";
              $datasets .= "}";
              $rc = true;
              $loopcolor++;
            }
            // debug_array($datasets);
            if($total_data==0){
              $show = false;
            }
          }else{
            $show = false;
          }
        }else{

          foreach($resultset->result() as $keyrs=>$valuers){
            if ($rc) $series .= ",";
            if ($rc) $labels .= ",";
            if ($rc) $color .= ",";
            $grf_descre = $valuers->{$descre};
            $grf_values = $valuers->{$values};
            if($grf_descre==""){
                $grf_descre = "Tidak Ada";
            }
            if($limit>0){
              $len_descre = strlen($grf_descre);
              if($len_descre>$limit){
                $grf_descre = substr($grf_descre, 0, $limit + 1) . "...";
              }
            }
            if(!isset($warna)){
              $warna = random_color();
            }
            if($chart!="spider"){
              $warna =   "'" . str_replace("'","", $warna) . "'";
            }
            $series .= $grf_values;
            $labels .= '"' . $grf_descre . '"';
            if($chart=="line"){
              $color_parameter = "borderColor: " .$warna . ", ";
              $represent = "label: '" . $yAxistitle . "',";
              $fill = ",fill:false";
            }else{
              $color_parameter = null;
              $represent = null;
              $fill = "";
            }           
            $datasets = '{ backgroundColor: ['.$warna.'], ' . $color_parameter  . ' ' . $represent . ' data: ['.$series.'] '.$fill.'}';
            $color .= "'" . random_color() . "'";
            $rc = true;
            // debug_array($datasets, false);
          }
        }
    }

    // debug_array($datasets);
    // {
    //   borderColor: ['.$color.'],
    //   label:"'.$labelnya.'",
    //   data: ['.$series.']
    // }    
    // debug_array($labels);
    if($show){
      switch($chart){
          case "pie":
              $script .= '
              var dChart'.$id.' =  new Chart(document.getElementById("'.$id.'"), {
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
              var dChart'.$id.' =  new Chart(document.getElementById("'.$id.'"), {
                  type: "radar",
                  data: {
                    labels: ['.$labels.'],
                    datasets: [
                      {
                        label: "'.$labelling.'",
                        fill: true,
                        backgroundColor: "rgba(255, 99, 132, 0.2)",
                        borderColor: "rgb(255, 99, 132)",
                        pointBackgroundColor: "rgb(255, 99, 132)",
                        pointBorderColor: "#fff",
                        pointHoverBackgroundColor: "#fff",
                        pointHoverBorderColor: "rgb(255, 99, 132)",                    
                        data: ['.$series.']
                      }
                    ]
                  },
                  options: {
                    responsive: true,
                    maintainAspectRatio: false, 
                    aspectRatio: 1, 
                    title: {
                      display: true,
                      text: "'.$title.'"
                    },
                    scale: {
                      display: true,
                      ticks: {
                        showLabelBackdrop: false,
                        stepSize: 1
                      },
                    },
                    elements: {
                      line: {
                        borderWidth: 3
                      }
                    }
                  }
              });            
              ';
              // backgroundColor: "'.$warna.'",
              // borderColor: "'.$warna.'",
              // pointBorderColor: "#fff",
              // pointBackgroundColor: "'.$warna.'",
              // pointBorderColor: "#fff",

              // debug_array($script);
              break;
          case "bar":
          case "column":
            $rotation_txt = null;
            if(isset($rotation)){
              $rotation_txt = ", scales: { xAxes: [{ ticks: { autoSkip: false, maxRotation: 90, minRotation: 90} }] }";
            }
              
              if($chart=="bar"){
                  $charttype = "horizontalBar";
              }
              if($chart=="column"){
                  $charttype = "bar";
              }
              $script .= '
              var dChart'.$id.' =  new Chart(document.getElementById("'.$id.'"), {
                  type: "'.$charttype.'",
                  data: {
                    labels: ['.$labels.'],
                    datasets: [' . $datasets . ']
                  },
                  options: {
                      responsive: true,
                      maintainAspectRatio: true,
                      legend: { display: '.$legend_display.' },
                      title: {
                          display: true,
                          text: "'.$title.'"
                      }
                      '.$rotation_txt.'
                  }
              });            
              ';
              // debug_array($script);
              break;
          case "line":
            if(!isset($datasets)){
              $datasets = null;
            }
            $labelnya = "USer akses";

            $script .= '
            var dChart'.$id.' =  new Chart(document.getElementById("'.$id.'"), {
                type: "line",
                data: {
                  labels: ['.$labels.'],
                  datasets: ['.$datasets.']
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                          position: "top",
                        },
                        title: {
                          display: true,
                          text: "'.$title.'"
                        }
                    },
                    scales: {
                      y: {
                        title: {
                          display: true,
                        },
                        min: 0,
                        max: 100,
                        ticks: {
                          // forces step size to be 50 units
                          stepSize: 10
                        }
                      }
                    }
                }
            });            
            ';
            break;
              
      }
      $script .= "
      </script>";
      $return = '<style>
      </style>';
      $return = null;
      $return .= '<div id="chart"><canvas id="'.$id.'" style="display: block; box-sizing: border-box; "></canvas></div>';
      $return .= $script;
    }else{
      $return = '
      <div class="card-body d-flex flex-column">
        <div class="pt-5">
          <a href="#" style="position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);" class="btn btn-danger btn-shadow-hover font-weight-bolder w-100 py-3">Data Tidak Ditemukan</a>
        </div>
      </div>';
    }
    return $return;
}
function display_chart($data = array(), $defined=false){
	$return = chartjs_initialize($data);
	$return .= chartjs_create_instance($data);
  // debug_array($return);
	return $return;
}
