<?php
// insert a new record
$tpl=new MSDTemplate();
$tpl->set_filenames(array(
	'show' => './tpl/sqlbrowser/sql_record_insert_inputmask.tpl'));

$sqledit="SHOW FIELDS FROM `$tablename`";
$res=MSD_query($sqledit);
$num=mysql_numrows($res);

$feldnamen="";
for ($x=0; $x<$num; $x++)
{
	$row=mysql_fetch_object($res);
	$feldnamen.=$row->Field.'|';
	$tpl->assign_block_vars('ROW',array(
		'CLASS' => ($x%2) ? 1 : 2, 
		'FIELD_NAME' => $row->Field, 
		'FIELD_ID' => correct_post_index($row->Field)));
	
	$type=strtoupper($row->Type);
	
	if (strtoupper($row->Null)=='YES')
	{
		//field is nullable
		$tpl->assign_block_vars('ROW.IS_NULLABLE',array());
	}
	
	if (in_array($type,array(
		'BLOB', 
		'TEXT'))) $tpl->assign_block_vars('ROW.IS_TEXTAREA',array());
	else
		$tpl->assign_block_vars('ROW.IS_TEXTINPUT',array());
}

$tpl->assign_vars(array(
	'HIDDEN_FIELDS' => FormHiddenParams(), 
	'FIELDNAMES' => substr($feldnamen,0,strlen($feldnamen)-1), 
	'SQL_STATEMENT' => my_quotes($sql['sql_statement'])));

$tpl->pparse('show');