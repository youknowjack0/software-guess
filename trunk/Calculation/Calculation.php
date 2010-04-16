<?php

class Calculation {
    
    var $Code;
    var $ID;
    var $PHP;
    var $Name;
    var $ReturnsArray;
    var $Order;
    var $GroupID;
    var $GroupName;
    var $result;
    var $error;
    
    public static $C = array();
        
    /* sets $C given an array of $calcs (probably from getAllCalculations)
     * & Q (from Question)
     * repeats the process 10 times to make sure all dependencies are accounted for.
     * 
     * note: using system_ prefix on vars to avoid conflicts with user code
     * 
     * note: will modify system_allcalcs with error codes if they exist
     * 
     */
    static function getC(&$Q, &$system_allcalcs) {
        $C =& Calculation::$C;
        for($system_i=0;$system_i<10;$system_i++) {
	        foreach($system_allcalcs as $system_c) {      
	            ob_start(); //capture errors
	        
	            $system_result = eval($system_c->PHP);
	            if(isset($system_result)) {
	                $C[$system_c->Code] = $system_result;
	                $system_c->result = $system_result;
	            } else {
	                $C[$system_c->Code] = null;
	            }
	            
	            $system_c->error = ob_get_clean(); //save errors to object            
	        }
        }
        
        return $C;
        
    }
    
    static function getAllCalculations() {
        $calculations = array();
        $sql = "SELECT Calculations.*, QuestionGroups.GroupName FROM Calculations LEFT JOIN QuestionGroups ON QuestionGroups.ID = Calculations.GroupID ORDER BY `Order` ASC";
        $result = mysql_query($sql);
        while($calc = mysql_fetch_assoc($result)) {                                     
            $cObj = new Calculation($calc["Code"], $calc["ID"], $calc["PHP"], $calc["Name"], $calc["ReturnsArray"], $calc["Order"], $calc["GroupID"], $calc["GroupName"]);            
            $calculations[$cObj->Code] = $cObj;            
        }                
        return $calculations;        
    }
    
    function __construct($Code, $ID, $PHP, $Name, $ReturnsArray, $Order, $GroupID, $GroupName) {
        $this->Code = $Code;
        $this->ID = $ID;
        $this->PHP = $PHP;
        $this->Name = $Name;
        $this->ReturnsArray = $ReturnsArray;
        $this->Order = $Order;
        $this->GroupID = $GroupID;
        $this->GroupName = $GroupName;
    }
    
    function getHTMLResult() {
        if ($this->ReturnsArray == 1) {
            ob_start();
            print_r($this->result);            
            return "<pre>". htmlspecialchars(ob_get_clean()) . "</pre>";
        } else {
            return htmlspecialchars($this->result);
        }
    }
}