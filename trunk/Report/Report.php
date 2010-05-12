<?php
class Report {
    
    var $fields = array();
    
    public static function newReport($type) {
        if($type == 1) {
            return new DetailedReport();
        }
    }
    
    function addf($key, $value) {
        $fields[$key] = $value;    
    }       
    
    function printReport($templatestring) { //children should override & call, should not be called directly
        foreach($fields as $k => $v) {
            $templatestring = str_replace("%%$k%%", $v, $templatestring);
        }
        print($templatestring);
    }
}

class DetailedReport extends Report {
    
    static $template_file = 'templates/report1.html';
    
    function printReport() {       
        ob_start();
        require $template_file;
        $template_code = ob_get_clean();
        parent::printReport($template_code);        
    }    
    
}