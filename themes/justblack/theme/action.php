<?php

define('WT_SCRIPT_NAME', 'json.php');
require './../../../includes/session.php';

Zend_Session::writeClose();

$action = WT_Filter::get('action');
switch($action) {
	case 'imagetype':
		$xrefs = WT_Filter::postArray('xrefs');
		
		foreach($xrefs as $xref) {
			$row=
				WT_DB::prepare("SELECT m_type as imagetype FROM `##media` WHERE m_id=?")
				->execute(array($xref))
				->fetchOneRow(PDO::FETCH_ASSOC);		
			
			$data[$xref] = $row['imagetype'];
		};
		
		header("Content-Type: application/json; charset=UTF-8");
		echo json_encode((object)$data);
		break;
	default:
		header('HTTP/1.0 404 Not Found');
		break;
}