<pre>
<?php

define('WT_SCRIPT_NAME', 'serviceClientTest.php');
require './includes/session.php';

ob_start();
require_once './library/Zend/Soap/Client.php';

//-- put your URL here
$url = 'http://localhost/phpgedview/gen2service.php?wsdl';
echo "Getting WSDL<br />";

if (!class_exists('SoapClient')) {
	echo "Using Zend SOAP<br />";
	$soap = new Zend_Soap_Client($url,array('soap_version' => SOAP_1_1 ));
}
else {
	echo "Using SOAP Extension<br />";
	$soap = new SoapClient($url);
}

echo "Getting ServiceInfo<br />";
$s = $soap->ServiceInfo();
var_dump($s);
echo "After ServiceInfo()<br />";

$result = $soap->Authenticate('', '', '', '');
var_dump($result);

echo "After Authenticate<br />";

$res = $soap->getPersonById($result->SID, "I2");
var_dump($res);
echo "After getPersonById<br />";

$res = $soap->getGedcomRecord($result->SID, "I2");
var_dump($res);
echo "After getGedcomRecord<br />";

//$family = $soap->getFamilyByID($result->SID, "F1");
//print_r($family);

//$ids = $soap->checkUpdates($result->SID, "01 JAN 2006");
//print_r($ids);
//
//$s = $soap->search($result->item->SID, 'week', '0','100');
//echo print_r($s,true);
//
//$res = $soap->getPersonById($result->SID, "I1");
//print_r($res);
//
/*************************************** getVar TESTS *********************************************/
/*$s = $soap->getVar($result->SID, 'GEDCOM');
print_r($s);

$s = $soap->getXref($result->SID, 'new', 'INDI');
print_r($s);

$s = $soap->checkUpdates($result->SID, '10 JAN 2005');
print_r($s);
*/
//
//$s = $soap->getVar($result->SID, 'CHARACTER_SET');
//print_r($s);
//
//$s = $soap->getVar($result->SID, 'PEDIGREE_ROOT_ID');
//print_r($s);
//
// The rest of these are examples that only work if you are
// actually authenticated as a user first not anonymously
//$s = $soap->getVar($result->SID, 'CALENDAR_FORMAT');
//print_r($s);
//
//$s = $soap->getVar($result->SID, 'LANGUAGE');
//print_r($s);
//
/************* THE REST OF THESE SCHOULD RETURN SOAP FAULTS SINCE THEY'RE NOT ALLOWED   **********/
//$s = $soap->getVar($result->SID, 'DBTYPE');
//print_r($s);
//
//$s = $soap->getVar($result->SID, 'SERVER_URL');
//print_r($s);
/**************************************** END OF getVar TEST *************************************/
//
//$s = $soap->appendRecord($result->SID, 'RoyalBaseGarrett05.ged', $gedrec);
//print_r($s);
//
//$s = $soap->deleteRecord($result->SID, 'RoyalBaseGarrett05.ged');
//print_r($s);

ob_end_flush();
?>
</pre>
