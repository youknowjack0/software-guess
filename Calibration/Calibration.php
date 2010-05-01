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

FILE INFO: Calibration/calibration.php
$LastChangedDate: 2010-04-21 11:25:36 +0800 (Wed, 21 Apr 2010) $
$Revision: 40 $
$Author: youknowjack@gmail.com $

*/

ob_start();
require_once 'chart/pChart/pData.class';
require_once 'chart/pChart/pChart.class';
ob_end_clean();


/* Description:
 * 
 * The Calibration class supports the creation of calibration-pair data from
 * calculation inputs. 
 */

class Calibration {
    
    var $hasData = false; 
    var $displayText = "";
    var $description = "";
    
    //mathy stuff
    var $stdev = "";
    var $linearfit_a = 0;
    var $linearfit_b = 0;
    var $correlation = 0;
        	   
    //data series for the chart output
    var $data;
    var $data2;
    var $data3;
    var $data4;
    
    //chart 
    var $chart;
    
    //calc names
    var $calc1name;
    var $calc2name;
        
    
    /* Construct a calibration object given two calculations as parameters
     * 
     * $calc1 First Calculation object code
     * $calc2 Second Calculation object code
     * 
     * note: although passing by code isn't OO, it allows calls in DB code
     */
    function __construct($calc1code, $calc2code, $createChart = false) {
        
        $allcalcs = Calculation::getAllCalculations();
        $calc1 = $allcalcs[$calc1code];
        $calc2 = $allcalcs[$calc2code];          
        
        ob_start();
        
        // fetch array of all calculation results for use in calculations
        // note this function is defined only to return results from estimates
        // where the project is released
        $arr1 = $calc1->getAllResults();
        $arr2 = $calc2->getAllResults(); 
        
        //save calc names
        $this->calc1name = $calc1->Name;
        $this->calc2name = $calc2->Name;

        // chart data        
        $this->data = new pData;
        $this->data2 = new pData;
        
        // some mathy stuff
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
    
	    
	    // iterate all calculation results
	    foreach($arr1 as $k => $x) {      
	        //only use results if there is a pair from a single estimate  
	        if(isset($arr2[$k])) {
	            $N++;
	            $y = $arr2[$k];
	
	            $sumx += $x;
	            $sumy += $y;
	            $sumxy += $x*$y;
	            $sumx2 += $x*$x;
	            $sumy2 += $y*$y;            
	            
	            print($x . " - " . $y . "<br />");
	            $this->data->AddPoint($x,"Serie1");
	            $this->data->AddPoint($y,"Serie2");
	            
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
	    
	    if (!isset($xmax)) {
	        $this->hasData = false;
	        return;
	    }
        
	    
        $xmin = floor($xmin/5)*5;
	    $xmax = ceil($xmax/5)*5;
	    $ymin = floor($ymin/5)*5;
	    $ymax = ceil($ymax/5)*5;
	    
        $stddev = 0;
    
	    if($N > 1) {
	        $correlation = ($N * $sumxy - $sumx * $sumy) / sqrt(($N*$sumx2 - $sumx2)*($N*$sumy2 - $sumy2));
	        $a = ($sumy * $sumx2 - $sumx * $sumxy) / ($N*$sumx2 - $sumx * $sumx);
	        $b = ($N*$sumxy - $sumx*$sumy) / ($N*$sumx2 - $sumx*$sumx);
	        
	        $sumdev=0;
	        
	        //std deviation (probably an elegant mathematical way to do this (TODO)
	        foreach($arr1 as $k => $x) {        
	            if(isset($arr2[$k])) {             
	                $y = $arr2[$k];
	                $yt = $a + $b * $x;
	                $dev = $y - $yt;
	                $sumdev += $dev*$dev;
	                
	            }
	        }
	        
	        $stddev = sqrt($sumdev / ($N-1));
	        

	    }

	    $this->stdev = $stddev;
	    $this->linearfit_a = $a;
	    $this->linearfit_b = $b;
	    $this->correlation = $correlation;
	    
	    if($createChart) {
	    
		    // CHART SETUP	    
	        $this->data->AddSerie("Serie1");
			$this->data->AddSerie("Serie2");
			$this->data->SetXAxisName($calc1->Name);
			$this->data->SetYAxisName($calc2->Name);
			
			// linear fit line
			$this->data2->AddPoint($xmin,"Serie3");
			$this->data2->AddPoint($a+$b*$xmin, "Serie4");
			$this->data2->AddPoint($xmax,"Serie3");
			$this->data2->AddPoint($a+$b*$xmax, "Serie4");
			$this->data2->AddSerie("Serie3");
			$this->data2->AddSerie("Serie4");
			
			//more chart setup
			$this->chart = new pChart(300,300);
	        $chart =& $this->chart;
	        
			$chart->setFixedScale($ymin, $ymax, 5, $xmin, $xmax, 5);
			
			// Prepare the graph area
			$chart->setFontProperties("chart/Fonts/tahoma.ttf",8);
			$chart->setGraphArea(55,30,270,230);
			$chart->drawXYScale($this->data->GetData(),$this->data->GetDataDescription(),"Serie2","Serie1",0,0,0,TRUE,45);
			$chart->drawGraphArea(255,249,234,FALSE);	
			$chart->setColorPalette(0, 51,102,153);
			$chart->setColorPalette(1, 51,102,153);
			$chart->drawGrid(4,TRUE,150,150,150,120);
			$chart->drawXYPlotGraph($this->data->GetData(),$this->data->GetDataDescription(), "Serie2", "Serie1");
		    $chart->drawXYGraph($this->data2->GetData(), $this->data2->GetDataDescription(), "Serie4", "Serie3");
	        
			//draw a vertical origin 
		    $this->data3 = new pData;
		    $this->data3->AddPoint(0, "Serie5");
		    $this->data3->AddPoint($ymin, "Serie6");
		    $this->data3->AddPoint(0, "Serie5");
		    $this->data3->AddPoint($ymax, "Serie6");
		    $this->data3->AddSerie("Serie5");
		    $this->data3->AddSerie("Serie6");
		    
		    
		    //more chart setup
		    $chart->setColorPalette(0, 0,0,0);
	        $chart->setLineStyle(2);
	        $chart->drawTreshold(0,0,0,0,FALSE,FALSE,0);  
		    $chart->drawXYGraph($this->data3->GetData(), $this->data2->GetDataDescription(), "Serie6", "Serie5");	    
		    
		    //output file name
		    $this->filename = $calc1->Code . "~~" . $calc2->Code     . ".png";
		    
		    //save file
		    $chart->Render($this->filename);
	    }
	    
	    //clear buffer
	    ob_end_clean();	    	    
        
    }
    
    /* Set the display text (title) for this calibration pair
     * 
     */
    function setDisplayText($str) {
        $this->displayText = $str;
    }
    
    /* Set the description for this calibration pair
     * 
     */
    function setDescription($str) {
        $this->description = $str;
    }
    
    function getDisplayText() {
        return $this->displayText;        
    }
    
    function getDescription() {
        return $this->description;
    }
    
    // file name of the chart created when __construct is called
    function getChartFilename() {
        return $this->filename;
    }
    
    function getA() {
        return $this->linearfit_a;
    }
    
    function getB() {
        return $this->linearfit_b;
    }
    
    function getCorrelation() {
        return $this->correlation;
    }
    
    function getStDev() {
        return $this->stdev;
    }
    
    function getCalc1Name() {
        return $this->calc1name;
    }
    
    function getCalc2Name() {
        return $this->calc2name;
    }
    
    function predictMean($x) {
        return $this->linearfit_a + $this->linearfit_b*$x;
    }
    
    //alias for getStDev
    function predictStDev() {
        return $this->getStDev();
    }
    
}