<?php
// Edit record -> built Edit-Form
$tpl=new MSDTemplate();
$tpl->set_filenames(array(
	'show' => './tpl/sqlbrowser/sql_record_update_inputmask.tpl'));

$target=($mode=="searchedit") ? '?mode=searchedit' : '?mode=update'; // jump back to search hit list after saving
$fields=getExtendedFieldInfo($db,$tablename);

$sqledit="SELECT * FROM `$tablename` WHERE ".$recordkey;
$res=MSD_query($sqledit);
$record=mysql_fetch_array($res,MYSQL_ASSOC); // get the record
$num=sizeof($record); // get the nr of fields of the record


// iterate fields
$x=0;
$fieldnames='';
foreach ($record as $field=>$fieldvalue)
{
	$fieldnames.=$field.'|';
	$tpl->assign_block_vars('ROW',array(
		'CLASS' => ($x%2) ? 1 : 2, 
		'FIELD_NAME' => $field, 
		'FIELD_VALUE' => my_quotes($fieldvalue), 
		'FIELD_ID' => correct_post_index($field)));
	
	if ('YES'==$fields[$field]['null'])
	{
		//field is nullable - precheck checkbox if value is null
		$tpl->assign_block_vars('ROW.IS_NULLABLE',array(
			'NULL_CHECKED' => is_null($fieldvalue) ? ' checked="checked"' : ''));
	}
	
	$type=strtoupper($fields[$field]['type']);
	if (in_array($type,array(
		'BLOB', 
		'TEXT'))) $tpl->assign_block_vars('ROW.IS_TEXTAREA',array());
	else
		$tpl->assign_block_vars('ROW.IS_TEXTINPUT',array());
	$x++;
}
$tpl->assign_vars(array(
	'HIDDEN_FIELDS' => FormHiddenParams(), 
	'FIELDNAMES' => substr($fieldnames,0,strlen($fieldnames)-1), 
	'SQL_STATEMENT' => my_quotes($sql['sql_statement']), 
	'RECORDKEY' => my_quotes($recordkey), 
	'TARGET' => $target));

$tpl->pparse('show');
