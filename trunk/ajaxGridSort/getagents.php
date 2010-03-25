<?
//
// +------------------------------------------------------------------------+
// | PHP version 5.0 					                                  	|
// +------------------------------------------------------------------------+
// | Description:													      	|
// | class to manage data in grid using AJAX		  						|
// | 																		|
// +------------------------------------------------------------------------+
// | Author				: Neeraj Thakur <neeraj_th@yahoo.com>   	|
// | Created Date     	: 28-08-2006                  						|
// | Last Modified    	: 28-08-2006                  						|
// | Last Modified By 	: Neeraj Thakur                  					|
// +------------------------------------------------------------------------+

class clsAJAX
{
    public $dbhost;
    public $dbuname;
    public $dbpass;
    public $dbname;

    private $numFields = 6;
    private $arrTable = "projecthistory";
    public $arrFields = array('ID', 'Project Name','Domain Experience','Field Experience','Estimated Effort','Actual Effort');
    public $arrValidate = array('', '[0-9a-zA-Z\\-]+', '\\d+', '\\d+', '\\d+', '\\d+');
    public $arrRange = array(0, 0, array(1, 10), array(1, 10), 0, 0);
    
    public function __construct()
    {
        $dbhost = "localhost";
        $dbuname = "root";
        $dbpass = "getout";
        $dbname = "GUESS";

        $db = mysql_connect($dbhost, $dbuname, $dbpass);
        if (!$db) {  die('There was a problem with the database, please try back later'); }
        mysql_select_db($dbname, $db);
    }

    public function showList($id='')
    {
        $textout = "";
        $param = addslashes($_GET['param']);
        $dir = addslashes($_GET['dir']);
        if(strlen($param)>0){

            $sortupimg = '^';
            $sortdownimg = 'v';
            	
            $param = addslashes($_REQUEST['param']);
            $dir = addslashes($_REQUEST['dir']);
            	
            //start fields generation
            if ( $_GET['dir'] == 'desc' )
            {
                $textout .= '<tr class="txtheading">';
                for ( $i = 0 ; $i < count($this->arrFields) ; $i ++ )
                {
                    if ($_GET['param'] == $this->arrFields[$i]) {
                        $textout .= '<td><a href="#" onClick=\'getagents("'.$this->arrFields[$i].'","")\'>'.$this->arrFields[$i].'</a> '.$sortdownimg.'</td>';
                    } else {
                        $textout .= '<td><a href="#" onClick=\'getagents("'.$this->arrFields[$i].'","")\'>'.$this->arrFields[$i].'</a></td>';
                    }
                }
                $textout .= '<td></td><td></td>';
                $textout .= '</tr>';
            }
            else
            {
                $textout .= '<tr class="txtheading">';
                for ( $i = 0 ; $i < count($this->arrFields) ; $i ++ )
                {
                    if ($_GET['param'] == $this->arrFields[$i]) {
                        $textout .= '<td><a href="#" onClick=\'getagents("'.$this->arrFields[$i].'","desc")\'>'.$this->arrFields[$i].'</a> '.$sortupimg.'</td>';
                    } else {
                        $textout .= '<td><a href="#" onClick=\'getagents("'.$this->arrFields[$i].'","desc")\'>'.$this->arrFields[$i].'</a></td>';
                    }
                }
                $textout .= '<td></td><td></td>';
                $textout .= '</tr>';
            }

            //end fields generation

            $arrf='';
            for ( $i = 0 ; $i < count($this->arrFields) ; $i ++ )
            {
                $arrf .= ',`'.$this->arrFields[$i].'`';
            }

            $q = 'SELECT '.substr($arrf,1,strlen($arrf)).' FROM '.$this->arrTable.' ORDER BY `'.$param.'` '.$dir;

            $result = mysql_query($q);
            while( $myrow = mysql_fetch_array($result) ){ 
                if ( $id == $myrow[$this->arrFields[0]] )
                {                    
                    //edit as per fields controls
                    $textout .= '<tr class="txtcontents">';

                    $textout .= '<td><input type="text" size="15" class="textbox" readonly name="f0" id="f0" value="'.$myrow[$this->arrFields[0]].'"></td>';
                    for($i = 1; $i < $this->numFields; $i++) {
                        $textout .= '<td><input type="text" size="15" class="textbox" name="f'.$i.'" id="f'.$i.'" value="'.$myrow[$this->arrFields[$i]].'"></td>';
                    }
                    	
                    $textout .= '<td><a href="#" onClick=\'saveRecord("save",'.$myrow[$this->arrFields[0]].',"'.$param.'","'.$dir.'")\'>Save</a> </td>
						<td nowrap>| <a href="#" onClick=\'getagents("'.$param.'","'.$dir.'")\'>Cancel</a></td>
								</tr>
							';
                }
                else
                {
                    $textout .= '<tr class="txtcontents">';
                    for ( $i = 0 ; $i < count($this->arrFields) ; $i ++ )
                    {
                        $textout .= '<td width="18%">'.$myrow[$this->arrFields[$i]].'</td>';
                    }
                    $textout .= '<td><a href="#" onClick=\'manipulateRecord("update",'.$myrow[$this->arrFields[0]].',"'.$param.'","'.$dir.'")\'>Update</a> </td>
									<td nowrap>| <a href="#" onClick=\'manipulateRecord("delete",'.$myrow[$this->arrFields[0]].',"'.$param.'","'.$dir.'")\'>Delete</a></td>
								</tr>';
                }
            }
        } else {
            $textout='<tr><td colspan="6">No record available..</td></tr>';
        }
        if ( $_REQUEST['mode'] != "new" )
        {
            $textout .= '<tr><td height="20" valign="bottom" class="txtcontents" colspan="6">
			<a href="#" onClick=\'newRecord("new","'.$param.'","'.$dir.'")\'>New</a>
			</td></tr>';
        }
        else if ( $_REQUEST['mode'] == "new" )
        {
            $textout .= '<tr class="txtcontents"><td></td>';
            for($i = 1; $i < $this->numFields; $i++) {
                $textout .= '<td><input type="text" size="15" class="textbox" name="f'.$i.'" id="f'.$i.'" value="'.$myrow[$this->arrFields[$i]].'"></td>';
            }
            	
            $textout .= '<td><a href="#" onClick=\'saveNewRecord("newsave","'.$param.'","'.$dir.'")\'>Save</a> </td>
					   <td nowrap>| <a href="#" onClick=\'getagents("'.$param.'","'.$dir.'")\'>Cancel</a></td>
					   </tr>';
        }

        echo "<table cellspacing=\"0\" border=\"0\" cellpadding=\"2\" align=\"center\" width=\"70%\">".$textout."</table>";
    }

    public function deleteRecord($id='')
    {
        $result = mysql_query('delete from '.$this->arrTable.' where ID = '.$id);
    }

    public function saveEditedRecord()
    {
        $q = 'update '.$this->arrTable.' set ';
        for ($i=1; $i < $this->numFields;  $i++) {
            $q .= "`".$this->arrFields[$i]."` = '".addslashes($_REQUEST['f'.$i])."'";
            if($i < $this->numFields - 1) $q .= ", ";
        }
        $q  .= ' where ID =\''.addslashes($_REQUEST['f0']).'\'';
        $result = mysql_query($q);
    }

    public function saveNewRecord()
    {
        $exFieldNames = array();
        for ($i=1;$i<$this->numFields;$i++) {
            $exFieldNames[$i] = $this->arrFields[$i];
        }        
        $fieldNames = implode("`,`", $exFieldNames);
        $requestsArr = array();
        for ($i=1;$i<$this->numFields;$i++) {
            $requestsArr[$i] = addslashes($_REQUEST['f'.$i]);
        }
        $requests = implode("','", $requestsArr);
        $q = 'insert into '.$this->arrTable.' (`'.$fieldNames.'`) values (\''.$requests.'\')';
        $result = mysql_query($q);        
    }
}

$obj = new clsAJAX();

if ( $_REQUEST['mode'] == "delete" )
{
    $obj->deleteRecord(addslashes($_REQUEST['ID']));
    echo $obj->showList();
}

if ( $_REQUEST['mode'] == "update" )
{
    $obj->showList(addslashes($_REQUEST['ID']));
}

if ( $_REQUEST['mode'] == "save" )
{
    $obj->saveEditedRecord();
    $obj->showList();
}

if ( $_REQUEST['mode'] == "newsave" )
{
    $obj->saveNewRecord();
    $obj->showList();
}

if ( $_REQUEST['mode'] == "new" )
{
    $obj->showList();
}

if ( $_REQUEST['mode'] == "list" )
{
    $obj->showList();
}
?>