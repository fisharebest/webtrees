<?php

define('WT_SCRIPT_NAME', 'user_message.php');
require '../../includes/session.php';

$controller=new WT_Controller_Base();
$controller->pageHeader();

$user_id 	= WT_Filter::getInteger('user_id');
$message_id = WT_Filter::getInteger('message_id');

$message =
WT_DB::prepare("SELECT message_id, sender, subject, body FROM `##message` WHERE message_id=?")
->execute(array($message_id))
->fetchOneRow();

$html = '';

// get message block
$html .= '<td colspan="4"><div id="message'.$message->message_id.'" style="padding:10px">';
$html .= expand_urls(WT_Filter::escapeHtml($message->body));

if (strpos($message->subject, /* I18N: When replying to an email, the subject becomes “RE: <subject>” */ WT_I18N::translate('RE: '))!==0) {
	$message->subject= WT_I18N::translate('RE: ').$message->subject;
}

if ($user_id) {
	$html .= '<div><a href="#" onclick="reply(\''.WT_Filter::escapeHtml($message->sender).'\', \''.WT_Filter::escapeHtml($message->subject).'\'); return false;">'.WT_I18N::translate('Reply').'</a> | ';
}
$html .= '<a href="index.php?action=deletemessage&amp;message_id[]='.$message->message_id.'" onclick="return confirm(\''.WT_I18N::translate('Are you sure you want to delete this message?  It cannot be retrieved later.').'\');">'.WT_I18N::translate('Delete').'</a></div>';

$html .= '</div></td>';

echo $html;
?>