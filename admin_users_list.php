<?php
/**
 * Administrative User Interface.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Admin
 * @version $Id: admin_users_list 9870 2010-11-17 07:24:46Z nigel $
 */

define('WT_SCRIPT_NAME', 'admin_users_list.php');

require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

// Only admin users can access this page
if (!WT_USER_IS_ADMIN) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
	exit;
}

print_header(WT_I18N::translate('User administration'));

?>
<script type="text/javascript">
	jQuery(document).ready(function(){

		/* Insert a 'details' column to the table */
		var nCloneTh = document.createElement( 'th' );
		var nCloneTd = document.createElement( 'td' );
		nCloneTh.innerHTML = 'Details';
		nCloneTd.innerHTML = '<img class="open" src="./themes/_administration/images/open.png" width="11px">';
		nCloneTd.className = "";
		
		jQuery('#list thead tr').each( function () {
			this.insertBefore( nCloneTh, this.childNodes[0] );
		} );
		
		jQuery('#list tbody tr').each( function () {
			this.insertBefore(  nCloneTd.cloneNode( true ), this.childNodes[0] );
		} );

		var oTable = jQuery('#list').dataTable( {
			"oLanguage": {
				"sLengthMenu": 'Display <select><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option><option value="-1">All</option></select> records'
			},
			"bJQueryUI": true,
			"bAutoWidth":false,
			"aaSorting": [[ 1, "asc" ]],
			"iDisplayLength": 10,
			"sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "bSortable": false, "aTargets": [ 0 ] }
			]
		});
		
		/* Add event listener for opening and closing details
		 * Note that the indicator for showing which row is open is not controlled by DataTables,
		 * rather it is done here
		*/
		jQuery('#list tbody td img.open').live('click', function () {
			var nTr = this.parentNode.parentNode;
			if ( this.src.match('close') )
			{
				/* This row is already open - close it */
				this.src = "./themes/_administration/images/open.png";
				oTable.fnClose( nTr );
			}
			else
			{
				/* Open this row */
				this.src = "./themes/_administration/images/close.png";
				oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
			}
		} );
	
	});

	/* Formating function for row details */
	function fnFormatDetails ( oTable, nTr )
	{
		var aData = oTable.fnGetData( nTr );
		var sOut = '<table class="details">';
		sOut += '<tr><th>'+'<?php echo WT_I18N::translate('Role');?>'+': </th><td>'+aData[5]+'</td>';
		sOut += '<th>'+'<?php echo WT_I18N::translate('Language');?>'+': </th><td>'+aData[4]+'</td></tr>';
		sOut += '</table>';
		
		return sOut;
	}
</script>
<?php

//if ($ENABLE_AUTOCOMPLETE) require './js/autocomplete.js.htm';

// Valid values for form variables
$ALL_ACTIONS=array('cleanup', 'cleanup2', 'createform', 'createuser', 'deleteuser', 'edituser', 'edituser2', 'listusers');
$ALL_THEMES_DIRS=array();
foreach (get_theme_names() as $themename=>$themedir) {
	$ALL_THEME_DIRS[]=$themedir;
}
$ALL_EDIT_OPTIONS=array(
	'none'  => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Visitor'),
	'access'=> /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Member'),
	'edit'  => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Editor'),
	'accept'=> /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Moderator'),
	'admin' => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Manager')
);

// Extract form actions (GET overrides POST if both set)
$action                  =safe_POST('action',  $ALL_ACTIONS);
$usrlang                 =safe_POST('usrlang', array_keys(WT_I18N::installed_languages()));
$username                =safe_POST('username', WT_REGEX_USERNAME);
$filter                  =safe_POST('filter'   );
$sort                    =safe_POST('sort'     );
$ged                     =safe_POST('ged'      );

$action                  =safe_GET('action',   $ALL_ACTIONS,                            $action);
$usrlang                 =safe_GET('usrlang',  array_keys(WT_I18N::installed_languages()), $usrlang);
$username                =safe_GET('username', WT_REGEX_USERNAME,                      $username);
$filter                  =safe_GET('filter',   WT_REGEX_NOSCRIPT,                      $filter);
$sort                    =safe_GET('sort',     WT_REGEX_NOSCRIPT,                      $sort);
$ged                     =safe_GET('ged',      WT_REGEX_NOSCRIPT,                      $ged);

// Extract form variables
$oldusername             =safe_POST('oldusername',  WT_REGEX_USERNAME);
$realname                =safe_POST('realname'   );
$pass1                   =safe_POST('pass1',        WT_REGEX_PASSWORD);
$pass2                   =safe_POST('pass2',        WT_REGEX_PASSWORD);
$emailaddress            =safe_POST('emailaddress', WT_REGEX_EMAIL);
$user_theme              =safe_POST('user_theme',               $ALL_THEME_DIRS);
$user_language           =safe_POST('user_language',            array_keys(WT_I18N::installed_languages()), WT_LOCALE);
$new_contact_method      =safe_POST('new_contact_method');
$new_comment             =safe_POST('new_comment',              WT_REGEX_UNSAFE);
$new_comment_exp         =safe_POST('new_comment_exp'           );
$new_auto_accept         =safe_POST_bool('new_auto_accept');
$canadmin                =safe_POST_bool('canadmin');
$visibleonline           =safe_POST_bool('visibleonline');
$editaccount             =safe_POST_bool('editaccount');
$verified                =safe_POST_bool('verified');
$verified_by_admin       =safe_POST_bool('verified_by_admin');

if (empty($ged)) {
	$ged=$GEDCOM;
}

// Load all available gedcoms
$all_gedcoms = get_all_gedcoms();
//-- sorting by gedcom filename 
asort($all_gedcoms);

// Delete a user
if ($action=='deleteuser') {
	// don't delete ourselves
	$user_id=get_user_id($username);
	if ($user_id!=WT_USER_ID) {
		delete_user($user_id);
		AddToLog("deleted user ->{$username}<-", 'auth');
	}
	// User data is cached, so reload the page to ensure we're up to date
	header("Location: admin_users_list.php");
	exit;
}

//-- echo out a list of the current users
ob_start();
	$users = get_all_users();
	
	// First filter the users, otherwise the javascript to unfold priviledges gets disturbed
	foreach($users as $user_id=>$user_name) {
		if ($filter == "warnings") {
			if (get_user_setting($user_id, 'comment_exp')) {
				if ((strtotime(get_user_setting($user_id, 'comment_exp')) == "-1") || (strtotime(get_user_setting($user_id, 'comment_exp')) >= time("U"))) unset($users[$user_id]);
			}
			else if (((date("U") - (int)get_user_setting($user_id, 'reg_timestamp')) <= 604800) || get_user_setting($user_id, 'verified')) unset($users[$user_id]);
		}
		else if ($filter == "adminusers") {
			if (!get_user_setting($user_id, 'canadmin')) unset($users[$user_id]);
		}
		else if ($filter == "usunver") {
			if (get_user_setting($user_id, 'verified')) unset($users[$user_id]);
		}
		else if ($filter == "admunver") {
			if ((get_user_setting($user_id, 'verified_by_admin')) || (!get_user_setting($user_id, 'verified'))) {
				unset($users[$user_id]);
			}
		}
		else if ($filter == "language") {
			if (get_user_setting($user_id, 'language') != $usrlang) {
				unset($users[$user_id]);
			}
		}
		else if ($filter == "gedadmin") {
			if (get_user_gedcom_setting($user_id, $ged, 'canedit') != "admin") {
				unset($users[$user_id]);
			}
		}
	}

	echo
		'<table id="user-list" width="100%">',
		'<thead>',
		'<tr>',
		'<th>User ID</th>',
		'<th>', WT_I18N::translate('Real name'), '</th>',
		'<th>', WT_I18N::translate('User name'), '</th>',
		'<th>', WT_I18N::translate('Email'), '</th>',
		'<th>', WT_I18N::translate('Language'), '</th>',
		'<th>', WT_I18N::translate('Date registered'), '</th>',
		'<th>', WT_I18N::translate('Last logged in'), '</th>',
		'<th>', WT_I18N::translate('Verified'), '</th>',
		'<th>', WT_I18N::translate('Approved'), '</th>',
		'</tr>',
		'</thead>',
		'<tbody>',
		'</tbody>',
		'</table>',
		WT_JS_START,
		'jQuery(document).ready(function() {',
		' jQuery("#user-list").dataTable( {',
		'  "oLanguage": {',
		'   "sLengthMenu": "Display <select><option value=10>10</option><option value=20>20</option><option value=30>30</option><option value=40>40</option><option value=50>50</option><option value=-1>All</option></select> records"',
		'  },',
		'  "bAutoWidth":false,',
		'  "aaSorting": [[ 1, "asc" ]],',
		'  "bProcessing": true,',
		'  "bServerSide": true,',
		'  "sAjaxSource": "', WT_SERVER_NAME, WT_SCRIPT_PATH, 'load.php?src=user_list",',
		'  "aaSorting": [[ 1, "asc" ]],',
		'  "bJQueryUI": true,',
		'  "sPaginationType": "full_numbers"',
		' } );',
		'} );',		
		WT_JS_END;

	// Then show the users
	echo
		'<table id="list">',
			'<thead>',
				'<tr>',
					'<th>', WT_I18N::translate('Message'), '</th>',
					'<th>', WT_I18N::translate('Real name'), '</th>',
					'<th>', WT_I18N::translate('User name'), '</th>',
//					'<th>', WT_I18N::translate('Languages'), '</th>',
//					'<th>', WT_I18N::translate('Role'), '</th>',
					'<th>', WT_I18N::translate('Date registered'), '</th>',
					'<th>', WT_I18N::translate('Last logged in'), '</th>',
					'<th>', WT_I18N::translate('Verified'), '</th>',
					'<th>', WT_I18N::translate('Approved'), '</th>',
					'<th>', WT_I18N::translate('Delete'), '</th>',
				'</tr>',
			'</thead>',
			'<tbody>';
				foreach($users as $user_id=>$user_name) {
					echo "<tr><td>";
					if ($user_id!=WT_USER_ID && get_user_setting($user_id, 'contactmethod')!='none') {
						echo "<a href=\"javascript:;\" onclick=\"return message('", $user_name, "');\"><img src=\"".$WT_IMAGES['email']."\" \"alt=\"", WT_I18N::translate('Send Message'), "\" title=\"", WT_I18N::translate('Send Message'), "\" /></a>";
					} else {
						echo '&nbsp;';
					}
					echo '</td>';
					$userName = getUserFullName($user_id);
					echo "<td><a class=\"edit_link\" href=\"useradmin.php?action=edituser&amp;username={$user_name}&amp;filter={$filter}&amp;usrlang={$usrlang}&amp;ged={$ged}\" title=\"", WT_I18N::translate('Edit'), "\">", $userName, '</a>';
					if (get_user_setting($user_id, 'canadmin')) {
						echo '<div class="warning">', WT_I18N::translate('Administrator'), '</div>';
					}
					echo "</td>";
					if (get_user_setting($user_id, "comment_exp")) {
						if ((strtotime(get_user_setting($user_id, "comment_exp")) != "-1") && (strtotime(get_user_setting($user_id, "comment_exp")) < time("U")))
						echo '<td class="red">', $user_name;
						else echo '<td>', $user_name;
					}
					else echo '<td>', $user_name;
						if (get_user_setting($user_id, "comment")) {
							$tempTitle = PrintReady(get_user_setting($user_id, "comment"));
							echo '<img class="adminicon" align="top" alt="', $tempTitle, '" title="', $tempTitle, '" src="images/notes.png" />';
					}
					echo "</td>\n";
					echo '<td style="display:none;">', Zend_Locale::getTranslation(get_user_setting($user_id, 'language'), 'language', WT_LOCALE), '</td>';
					echo '<td style="display:none;">';
					//echo '<div id="role">';
						echo "<ul>";
						foreach ($all_gedcoms as $ged_id=>$ged_name) {
							$role=get_user_gedcom_setting($user_id, $ged_id, 'canedit');
							switch ($role) {
							case 'admin':
							case 'accept':
								echo '<li class="warning">', $ALL_EDIT_OPTIONS[$role];
								break;
							case 'edit':
							case 'access':
							case 'none':
								echo '<li>', $ALL_EDIT_OPTIONS[$role];
								break;
							default:
								echo '<li>', $ALL_EDIT_OPTIONS['none'];
								break;
							}
							$uged = get_user_gedcom_setting($user_id, $ged_id, 'gedcomid');
							if ($uged) {
								echo ' <a href="individual.php?pid=', $uged, '&amp;ged=', rawurlencode($ged_name), '">', $ged_name, '</a></li>';
							} else {
								echo ' ', $ged_name, '</li>';
							}
						}
						echo "</ul>";
					//	echo "</div>";
						echo '</td>';
						if (((date("U") - (int)get_user_setting($user_id, 'reg_timestamp')) > 604800) && !get_user_setting($user_id, 'verified'))
							echo '<td class="red">';
					else echo '<td>';
						echo format_timestamp((int)get_user_setting($user_id, 'reg_timestamp'));
					echo '</td>';
					echo '<td>';
						if ((int)get_user_setting($user_id, 'reg_timestamp') > (int)get_user_setting($user_id, 'sessiontime')) {
							echo WT_I18N::translate('Never'), '<br />', WT_I18N::time_ago(time() - (int)get_user_setting($user_id, 'reg_timestamp'));
						} else {
							echo format_timestamp((int)get_user_setting($user_id, 'sessiontime')), '<br />', WT_I18N::time_ago(time() - (int)get_user_setting($user_id, 'sessiontime'));
						}
					echo '</td>',
					'<td class="center">';
						if (get_user_setting($user_id, 'verified')) echo WT_I18N::translate('Yes');
						else echo WT_I18N::translate('No');
					echo '</td>',
					'<td class="center">';
						if (get_user_setting($user_id, 'verified_by_admin')) echo WT_I18N::translate('Yes');
						else echo WT_I18N::translate('No');
					echo '</td>',
					'<td>';
						if (WT_USER_ID!=$user_id)
							echo "<a href=\"useradmin.php?action=deleteuser&amp;username=", rawurlencode($user_name)."&amp;sort={$sort}&amp;filter={$filter}&amp;usrlang={$usrlang}&amp;ged=", rawurlencode($ged), "\" onclick=\"return confirm('", WT_I18N::translate('Are you sure you want to delete the user'), " $user_name');\"><img src=\"images/delete.png\" alt=\"", WT_I18N::translate('Delete'), "\" title=\"", WT_I18N::translate('Delete'), "\" /></a>";
					echo '</td>',
				'</tr>';
				}
			echo '</tbody>',
		'</table>';
	print_footer();
ob_flush();
