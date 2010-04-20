<?php /*
Copyright (c) 2010 Jack Langman, Daniel Fozdar, Nelson Yiap, Zhihua Guo,
Vivek Koul & Aaron Taylor
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice,
this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice,
this list of conditions and the following disclaimer in the documentation
and/or other materials provided with the distribution.
* Neither the name(s) of the authors nor the names of its contributors
may be used to endorse or promote products derived from this software
without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

FILE INFO: calibrate.php
$LastChangedDate$
$Revision$
$Author$

*/

// TODO: set up user level calibration, multiple org calibration, etc...
// probably wont happen....

// buffer output
ob_start();

require 'components/utility.php';
require 'components/db.php';
require 'Calculation/Calculation.php';
require 'question/Question.php';

ob_start();
require 'chart/pChart/pData.class';
require 'chart/pChart/pChart.class';
ob_end_clean();

$header_title = "Calibration";
$template_breadcrumbs = getBreadcrumbs('calibration.php', array());



// loop all calibration pairs

$sql = "SELECT * FROM CalibrationPairs";
$rs_pairs = mysql_query($sql);

while($rowp = mysql_fetch_assoc($rs_pairs)) { // iterate over pairs
    ob_start();
    $data = new pData;
    $c1code = $rowp["Calc1"];
    $c2code = $rowp["Calc2"];
    
    $allcalcs = Calculation::getAllCalculations();    
    
    $arr1 = $allcalcs[$c1code]->getAllResults();
    $arr2 = $allcalcs[$c2code]->getAllResults();
    
    $result = array();
    
    $correlation;
    $sumx=0;
    $sumy=0;
    $sumxy=0;
    $sumx2=0;
    $sumy2=0;
    $N=0;    
    $xmin=null;
    $xmax=null;
    $ymin=null;
    $ymax=null;
        
    foreach($arr1 as $k => $x) {        
        if(isset($arr2[$k])) {
            $N++;
            $y = $arr2[$k];

            $sumx += $x;
            $sumy += $y;
            $sumxy += $x*$y;
            $sumx2 += $x*$x;
            $sumy2 += $y*$y;            
            
            print($x . " - " . $y . "<br />");
            $data->AddPoint($x,"Serie1");
            $data->AddPoint($y,"Serie2");
            
            if(!isset($xmin) || $x < $xmin) {
                $xmin = $x;
            }
            if(!isset($ymin) || $y < $ymin) {
                $ymin = $y;
            }
            if(!isset($xmax) || $x > $xmax) {
                $xmax = $x;
            }
            if(!isset($ymax) || $y > $ymax) {
                $ymax = $y;
            }

        }
    }
    
    if(!isset($xmax)) {
        continue;
    }
    
    $xmin = floor($xmin/5)*5;
    $xmax = ceil($xmax/5)*5;
    $ymin = floor($ymin/5)*5;
    $ymax = ceil($ymax/5)*5;
    
    printf("%d.%d.%d.%d",$xmin,$xmax,$ymin,$ymax);
    
    if($N > 1) {
        $correlation = ($N * $sumxy - $sumx * $sumy) / sqrt(($N*$sumx2 - $sumx2)*($N*$sumy2 - $sumy2));
        print("correlation: " . $correlation);
    }
    
    //$DataSet->SetSerieName("Trigonometric function","Serie1");
	$data->AddSerie("Serie1");
	$data->AddSerie("Serie2");
	$data->SetXAxisName($allcalcs[$c1code]->Name);
	$data->SetYAxisName($allcalcs[$c2code]->Name);
	$chart = new pChart(300,300);
	/*$chart->drawGraphAreaGradient(255,255,255,-100,TARGET_BACKGROUND);*/
	$chart->setFixedScale($ymin, $ymax, 5, $xmin, $xmax, 5);
	
	 // Prepare the graph area
	 $chart->setFontProperties("chart/Fonts/tahoma.ttf",8);
	 $chart->setGraphArea(55,30,270,230);
	 $chart->drawXYScale($data->GetData(),$data->GetDataDescription(),"Serie2","Serie1",0,0,0,TRUE,45);
	 $chart->drawGraphArea(255,249,234,FALSE);
	 //$chart->drawGraphAreaGradient(230,230,250,-50);
	 $chart->setColorPalette(0, 51,102,153);
	 $chart->setColorPalette(1, 51,102,153);
	 $chart->drawGrid(4,TRUE,50,50,50,120);

	 $chart->drawXYPlotGraph($data->GetData(),$data->GetDataDescription(), "Serie2", "Serie1");
	 
	 $fname = $c1code . "~" . $c2code . ".png";
	 
	 $chart->Render($fname);
    
	 ob_end_clean(); //print this for debug info
	 
	 printf("<h3>%s</h3>", $rowp["DisplayText"]);
     printf("%s<br />",$rowp["Description"]);
	 print("<img src=\"$fname\" /><br />");
     printf("<strong>Correlation number: %.3f </strong>(1 indicates a strong positive relationship, -1 a strong negative relationship)<br />", $correlation);
     print("<hr />");
     
}

// Footer file to show some copyright info etc...
$template_body = ob_get_clean();
require 'templates/main.php';
?>