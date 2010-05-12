<?php
class Report {
    
    var $fields = array();
    
    public static function newReport($type) {
        if($type == 1) {
            return new DetailedReport();
        }
    }
    
    function addf($key, $value) {
        $this->fields[$key] = $value;    
    }       
    
    function printReport($templatestring) { //children should override & call, should not be called directly
        foreach($this->fields as $k => $v) {
            $x = strtoupper($k);
            $templatestring = str_replace("%%$x%%", $v, $templatestring);
        }
        print($templatestring);
    }
}

class DetailedReport extends Report {
    
    var $template_file = 'templates/report1.html';
    
    function printReport() {       
        ob_start();
        require $this->template_file;
        $template_code = ob_get_clean();
        parent::printReport($template_code);        
    }    
    
}