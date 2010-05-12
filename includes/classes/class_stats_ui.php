<?php
/**
 * GEDCOM Statistics Feature Add-On Class
 *
 * This class provides access to additional non-stats features of PGV
 * for use in the Advanced HTML block.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 * @author Patrick Kellum
 * @package webtrees
 * @subpackage Lists
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_STATS_UI_PHP', '');

require_once WT_ROOT.'includes/classes/class_stats.php';
class stats_ui extends stats
{
///////////////////////////////////////////////////////////////////////////////
// Favorites                                                                 //
///////////////////////////////////////////////////////////////////////////////

	static function _getFavorites($isged=true) {
		global $GEDCOM, $WT_IMAGE_DIR, $WT_IMAGES, $GEDCOM, $ctype, $TEXT_DIRECTION, $INDEX_DIRECTORY;
		global $show_full, $PEDIGREE_FULL_DETAILS;

		// Override GEDCOM configuration temporarily
		if(isset($show_full)){$saveShowFull = $show_full;}
		$savePedigreeFullDetails = $PEDIGREE_FULL_DETAILS;
		$show_full = 1;
		$PEDIGREE_FULL_DETAILS = 1;

		if($isged) {
			$userfavs = getUserFavorites($GEDCOM);
		}
		else {
			$userfavs = getUserFavorites(WT_USER_NAME);
		}
		$content = '';
		if(!count($userfavs)) {
			if($isged) {
				if(WT_USER_GEDCOM_ADMIN) {
					$content .= i18n::translate('You have not selected any favorites.<br /><br />To add an individual, a family, or a source to your favorites, click on the <b>Add a new favorite</b> link to reveal some fields where you can enter or search for an ID number.  Instead of an ID number, you can enter a URL and a title.');
				} else {
					$content .= i18n::translate('At this moment there are no selected Favorites.	The admin can add Favorites to display at startup.');
				}
			}
			else {
				$content .= i18n::translate('You have not selected any favorites.<br /><br />To add an individual, a family, or a source to your favorites, click on the <b>Add a new favorite</b> link to reveal some fields where you can enter or search for an ID number.  Instead of an ID number, you can enter a URL and a title.');
			}
		}
		else {
			if(!$isged) {
				$mygedcom = $GEDCOM;
				$current_gedcom = $GEDCOM;
			}
			$content .= "<table width=\"99%\" style=\"border:none\" cellspacing=\"3px\" class=\"center {$TEXT_DIRECTION}\">";
			foreach($userfavs as $k=>$favorite) {
				if(isset($favorite['id'])){$k = $favorite['id'];}
				$removeFavourite = "<a class=\"font9\" href=\"".encode_url("index.php?ctype={$ctype}&action=deletefav&fv_id={$k}")."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to remove this item from your list of Favorites?')."');\">".i18n::translate('Remove')."</a><br />\n";
				if(!$isged) {
					$current_gedcom = $GEDCOM;
					$GEDCOM = $favorite['file'];
				}
				$content .= '<tr><td>';
				if($favorite['type'] == 'URL') {
					$content .= "<div id=\"boxurl{$k}.0\" class=\"person_box\">\n";
					if($ctype == 'user' || WT_USER_GEDCOM_ADMIN){$content .= $removeFavourite;}
					$content .= "<a href=\"{$favorite['url']}\"><b>".PrintReady($favorite['title']).'</b></a>';
					$content .= "<br />\n".PrintReady($favorite['note'], false, true);
					$content .= "</div>\n";
				}
				else {
					if(displayDetailsById($favorite['gid'], $favorite['type'])) {
						require $INDEX_DIRECTORY.$GEDCOM.'_conf.php';

						switch($favorite['type']) {
							case 'INDI':
							{
								$indirec = find_person_record($favorite['gid'], get_id_from_gedcom($GEDCOM));
								$content .= "<div id=\"box{$favorite['gid']}.0\" class=\"person_box";
								if(strpos($indirec, "\n1 SEX F")!==false){$content .= 'F';}
								elseif(strpos($indirec, "\n1 SEX M")!==false){$content .= '';}
								else{$content .= 'NN';}
								$content .= "\">\n";
								if($ctype == 'user' || WT_USER_GEDCOM_ADMIN){$content .= $removeFavourite;}
								ob_start();
								print_pedigree_person($favorite['gid'], 2, 1, $k);
								$content .= ob_get_clean();
								$content .= PrintReady($favorite['note'], false, true);
								$content .= "</div>\n";
								break;
							}
							default:
							{
								$record=GedcomRecord::getInstance($favorite['gid']);
								$content .= "<div id=\"box{$favorite['gid']}.0\" class=\"person_box\">\n";
								if($ctype == 'user' || WT_USER_GEDCOM_ADMIN){$content .= $removeFavourite;}
								$content .= $record->format_list('span');
								$content .= "<br />\n".PrintReady($favorite['note'], false, true);
								$content .= "</div>\n";
								break;
							}
						}
						if(!$isged) {
							$GEDCOM = $mygedcom;
							require $INDEX_DIRECTORY.$GEDCOM.'_conf.php';
						}
					}
				}
				$content .= "</div>\n"
					."</td></tr>\n"
				;
			}
			$content .= "</table>\n";
		}
		if(($isged && WT_USER_GEDCOM_ADMIN) || !$isged) {
			$content .= '
				<script language="JavaScript" type="text/javascript">
				var pastefield;
				function paste_id(value) {
					pastefield.value=value;
				}
				</script>
				<br />
				';
			$uniqueID = floor(microtime() * 1000000);
			if($isged) {
				$content .=
					"<b><a href=\"javascript://".i18n::translate('Add a new favorite')." \" onclick=\"expand_layer('add_ged_fav'); return false;\"><img id=\"add_ged_fav_img\" src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['plus']['other']}\" border=\"0\" alt=\"\" />&nbsp;".i18n::translate('Add a new favorite')."</a></b>"
				 	.help_link('index_add_favorites')
					."<br />\n<div id=\"add_ged_fav\" style=\"display: none;\">\n"
					."<form name=\"addgfavform\" method=\"post\" action=\"index.php\">\n"
					."<input type=\"hidden\" name=\"favtype\" value=\"gedcom\" />\n"
				;

			}
			else {
				$content .=
					"<b><a href=\"javascript://".i18n::translate('Add a new favorite')." \" onclick=\"expand_layer('add_user_fav'); return false;\"><img id=\"add_user_fav_img\" src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['plus']['other']}\" border=\"0\" alt=\"\" />&nbsp;".i18n::translate('Add a new favorite')."</a></b>"
					.help_link('index_add_favorites')
					."<br />\n<div id=\"add_user_fav\" style=\"display: none;\">\n"
					."<form name=\"addufavform\" method=\"post\" action=\"index.php\">\n"
					."<input type=\"hidden\" name=\"favtype\" value=\"user\" />\n"
				;
			}
			$content .= "<input type=\"hidden\" name=\"action\" value=\"addfav\" />\n"
				."<input type=\"hidden\" name=\"ctype\" value=\"{$ctype}\" />\n"
				."<input type=\"hidden\" name=\"ged\" value=\"{$GEDCOM}\" />\n"
				."<table width=\"99%\" style=\"border:none\" cellspacing=\"3px\" class=\"center {$TEXT_DIRECTION}\">"
				."<tr><td>".i18n::translate('Enter a Person, Family, or Source ID')."<br />\n"
				."<input class=\"pedigree_form\" type=\"text\" name=\"gid\" id=\"gid{$uniqueID}\" size=\"5\" value=\"\" />"
				.print_findindi_link("gid{$uniqueID}", '', true)
				.print_findfamily_link("gid{$uniqueID}", '', true)
				.print_findsource_link("gid{$uniqueID}", '', true)
				.print_findrepository_link("gid{$uniqueID}",'',true)
				.print_findnote_link("gid{$uniqueID}",'',true)
				.print_findmedia_link("gid{$uniqueID}",'1','',true)
				."<br />".i18n::translate('OR<br />Enter a URL and a title')
				."<table><tr><td>".translate_fact('URL')."</td><td><input type=\"text\" name=\"url\" size=\"40\" value=\"\" /></td></tr>"
				."<tr><td>".i18n::translate('Title:')."</td><td><input type=\"text\" name=\"favtitle\" size=\"40\" value=\"\" /></td></tr></table>"
				."</td><td>"
				.i18n::translate('Enter an optional note about this favorite')
				."<br />\n<textarea name=\"favnote\" rows=\"6\" cols=\"50\"></textarea>"
				."</td></tr></table>\n"
				."<br />\n<input type=\"submit\" value=\"".i18n::translate('Add')."\" style=\"font-size: 8pt; \" />"
				."\n</form></div>\n"
			;
		}

		// Restore GEDCOM configuration
		unset($show_full);
		if(isset($saveShowFull)){$show_full = $saveShowFull;}
		$PEDIGREE_FULL_DETAILS = $savePedigreeFullDetails;

		return $content;
	}

	static function gedcomFavorites(){return self::_getFavorites(true);}
	static function userFavorites(){return self::_getFavorites(false);}

	static function totalGedcomFavorites(){return count(getUserFavorites($GLOBALS['GEDCOM']));}
	static function totalUserFavorites(){return count(getUserFavorites(WT_USER_NAME));}

///////////////////////////////////////////////////////////////////////////////
// Messages                                                                  //
///////////////////////////////////////////////////////////////////////////////

	static function userMessages() {
		global $WT_IMAGE_DIR, $TEXT_DIRECTION, $WT_STORE_MESSAGES, $WT_IMAGES;

		$usermessages = getUserMessages(WT_USER_NAME);

		$content = "<form name=\"messageform\" action=\"\" onsubmit=\"return confirm('".i18n::translate('Are you sure you want to delete this message?  It cannot be retrieved later.')."');\">";
		if(count($usermessages) == 0) {
			$content .= i18n::translate('You have no pending messages.')."<br />";
		}
		else {
			$content .= '
				<script language="JavaScript" type="text/javascript">
				<!--
					function select_all() {
			';
			foreach($usermessages as $k=>$message) {
				if(isset($message['id'])){$k = $message['id'];}
				$content .= '
					var cb = document.getElementById("cb_message'.$k.'");
					if (cb) {
						if (!cb.checked) cb.checked = true;
						else cb.checked = false;
					}
				';
			}
			$content .= '
					return false;
				}
				//-->
				</script>
			';
			$content .= '<input type="hidden" name="action" value="deletemessage" />'
				.'<table class="list_table"><tr>'
				."<td class=\"list_label\">".i18n::translate('Delete')."<br />\n<a href=\"javascript:;\" onclick=\"return select_all();\">".i18n::translate('All')."</a></td>\n"
				."<td class=\"list_label\">".i18n::translate('Subject:')."</td>\n"
				."<td class=\"list_label\">".i18n::translate('Date Sent:')."</td>\n"
				."<td class=\"list_label\">".i18n::translate('Email Address:')."</td>\n"
				."</tr>\n";
			foreach($usermessages as $k=>$message) {
				if(isset($message['id'])){$k = $message['id'];}
				$content .= "<tr>\n<td class=\"list_value_wrap\"><input type=\"checkbox\" id=\"cb_message{$k}\" name=\"message_id[]\" value=\"{$k}\" /></td>\n";
				$showmsg = preg_replace('/(\w)\/(\w)/',"\$1/<span style=\"font-size:1px;\"> </span>\$2", PrintReady($message['subject']));
				$showmsg = str_replace("@","@<span style=\"font-size:1px;\"> </span>", $showmsg);
				$content .= "<td class=\"list_value_wrap\"><a href=\"javascript:;\" onclick=\"expand_layer('message{$k}'); return false;\"><b>{$showmsg}</b> <img id=\"message{$k}_img\" src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['plus']['other']}\" border=\"0\" alt=\"\" title=\"\" /></a></td>\n";
				if(!empty($message['created'])){$t = strtotime($message['created']);}else{$t = time();}
				$content .= '<td class="list_value_wrap">'.format_timestamp($t)."</td>\n".'<td class="list_value_wrap">';
				$user_id = get_user_id($message['from']);
				if($user_id) {
					$content .= PrintReady(getUserFullName($user_id));
					if($TEXT_DIRECTION == 'ltr') {
						$content .= ' '.getLRM().' - '.htmlspecialchars($user_id,ENT_COMPAT,'UTF-8').getLRM();
					}
					else {
						$content .= ' '.getRLM().' - '.htmlspecialchars($user_id,ENT_COMPAT,'UTF-8').getRLM();
					}
				}
				else {
					$content .= "<a href=\"mailto:{$user_id}\">".str_replace("@","@<span style=\"font-size:1px;\"> </span>", $user_id).'</a>';
				}
				$content .= "</td>\n"
					."</tr>\n"
					."<tr>\n<td class=\"list_value_wrap\" colspan=\"5\"><div id=\"message{$k}\" style=\"display: none;\">"
				;
				$message['body'] = expand_urls(nl2br(htmlspecialchars($message['body'],ENT_COMPAT,'UTF-8')));
				$content .= PrintReady($message['body'])."<br />\n<br />\n";
				if(strpos($message["subject"], "RE:")===false) {
					$message['subject'] = "RE:{$message['subject']}";
				}
				if($user_id) {
					$content .= "<a href=\"javascript:;\" onclick=\"reply('{$user_id}', '{$message['subject']}'); return false;\">".i18n::translate('Reply')."</a> | ";
				}
				$content .= "<a href=\"".encode_url("index.php?action=deletemessage&message_id={$k}")."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to delete this message?  It cannot be retrieved later.')."');\">".i18n::translate('Delete')."</a></div></td>\n</tr>\n";
			}
			$content .= "</table>\n"
				."<input type=\"submit\" value=\"".i18n::translate('Delete Selected Messages')."\" /><br />\n<br />\n"
			;
		}
		if(get_user_count() > 1) {
			$content .= i18n::translate('Send Message')." <select name=\"touser\">";
			if(WT_USER_IS_ADMIN) {
				$content .= "<option value=\"all\">".i18n::translate('Broadcast to all users')."</option>\n"
					."<option value=\"never_logged\">".i18n::translate('Send message to users who have never logged in')."</option>\n"
					."<option value=\"last_6mo\">".i18n::translate('Send message to users who have not logged in for 6 months')."</option>\n"
				;
			}
			foreach(get_all_users() as $user_id=>$user_name) {
				if($user_id != WT_USER_ID && get_user_setting($user_id, 'verified_by_admin') == 'yes') {
					$content .= "<option value=\"{$user_id}\">".PrintReady(getUserFullName($user_id)).' ';
					if($TEXT_DIRECTION == 'ltr') {
						$content .= getLRM()." - {$user_id}".getLRM();
					}
					else {
						$content .= getRLM()." - {$user_id}".getRLM();
					}
					$content .= "</option>\n";
				}
			}
			$content .= "</select>\n<input type=\"button\" value=\"".i18n::translate('Send')."\" onclick=\"message(document.messageform.touser.options[document.messageform.touser.selectedIndex].value, 'messaging2', ''); return false;\" />\n";
		}
		$content .= "</form>\n";
		return $content;
	}

	function totalUserMessages(){return count(getUserMessages(WT_USER_NAME));}

///////////////////////////////////////////////////////////////////////////////
// Journal                                                                //
///////////////////////////////////////////////////////////////////////////////

	static function userJournal() {
		global $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $ctype;

		$usernews = getUserNews(WT_USER_ID);
		$content = '';
		if(count($usernews) == 0) {
			$content .= i18n::translate('You have not created any Journal items.')." ";
		}
		foreach($usernews as $k=>$news) {
			$day = date('j', $news['date']);
			$mon = date('M', $news['date']);
			$year = date('Y', $news['date']);
			$content .= "<div class=\"person_box\">";
			$news['title']=embed_globals($news['title']);
			$news['text' ]=embed_globals($news['text' ]);
			$trans = array_flip(get_html_translation_table(HTML_SPECIALCHARS));
			$news['text'] = strtr($news['text'], $trans);
			$content .= PrintReady($news['text'])."<br />\n<br />\n"
				."<a href=\"javascript:;\" onclick=\"editnews('{$k}'); return false;\">".i18n::translate('Edit')."</a> | "
				."<a href=\"".encode_url("index.php?action=deletenews&news_id={$k}&ctype={$ctype}")."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to delete this Journal entry?')."');\">".i18n::translate('Delete')."</a><br />\n"
				."</div><br />\n"
			;
		}
		if(WT_USER_ID) {
			$content .= "<br />\n<a href=\"javascript:;\" onclick=\"addnews('".WT_USER_ID."'); return false;\">".i18n::translate('Add a new Journal entry')."</a>";
		}
		return $content;
	}

	function totalUserJournal(){ return count(getUserNews(WT_USER_ID));}

///////////////////////////////////////////////////////////////////////////////
// News                                                                      //
///////////////////////////////////////////////////////////////////////////////

	static function gedcomNews($params=null) {
		global $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $GEDCOM, $ctype;

		if($params === null){$params = array();}
		if(isset($params[0]) && $params[0] != ''){$limit = strtolower($params[0]);}else{$limit = 'count';}
		if(isset($params[1]) && $params[1] != ''){$flag = strtolower($params[0]);}else{$flag = 5;} // News postings

		if($flag == 0){$limit = 'nolimit';}
		if(isset($_REQUEST['gedcom_news_archive'])) {
			$limit = 'nolimit';
			$flag = 0;
		}

		$usernews = getUserNews($GEDCOM);

		$content = '';
		if(count($usernews) == 0) {
			$content .= i18n::translate('No News articles have been submitted.')."<br />\n";
		}
		$c = 0;
		$td = time();
		foreach($usernews as $k=>$news) {
			if($limit == 'count') {
				if($c >= $flag){break;}
				$c++;
			}
			if($limit == 'date') {
				if(floor(($td - $news['date']) / 86400) > $flag){break;}
			}
			$content .= "<div class=\"news_box\" id=\"{$news['anchor']}\">\n";

			// Look for $GLOBALS substitutions in the News title
			$newsTitle = embed_globals($news['title']);
			$content .= "<span class=\"news_title\">".PrintReady($newsTitle)."</span><br />\n";
			$content .= "<span class=\"news_date\">".format_timestamp($news['date'])."</span><br /><br />\n";

			// Look for $GLOBALS substitutions in the News text
			$newsText = embed_globals($news['text']);
			$trans = get_html_translation_table(HTML_SPECIALCHARS);
			$trans = array_flip($trans);
			$newsText = strtr($newsText, $trans);
			//$newsText = nl2br($newsText);
			$content .= PrintReady($newsText)."<br />\n";

			// Print Admin options for this News item
			if(WT_USER_GEDCOM_ADMIN) {
				$content .= "<hr size=\"1\" />"
					."<a href=\"javascript:;\" onclick=\"editnews('{$k}'); return false;\">".i18n::translate('Edit')."</a> | "
					."<a href=\"".encode_url("index.php?action=deletenews&news_id={$k}&ctype={$ctype}")."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to delete this News entry?')."');\">".i18n::translate('Delete')."</a><br />"
				;
			}
			$content .= "</div>\n";
		}
		$printedAddLink = false;
		if(WT_USER_GEDCOM_ADMIN) {
			$content .= "<a href=\"javascript:;\" onclick=\"addnews('".str_replace("'", "\'", $GEDCOM)."'); return false;\">".i18n::translate('Add a News article')."</a>";
			$printedAddLink = true;
		}
		if($limit == 'date' || $limit == 'count') {
			if($printedAddLink){$content .= '&nbsp;&nbsp;|&nbsp;&nbsp;';}
			$content .= "<a href=\"".encode_url("index.php?gedcom_news_archive=yes&ctype={$ctype}")."\">".i18n::translate('View archive')."</a>";
			$content .= help_link('gedcom_news_archive');
			$content .= '<br />';
		}
		return $content;
	}

	function totalGedcomNews(){return count(getUserNews($GLOBALS['GEDCOM']));}

///////////////////////////////////////////////////////////////////////////////
// Block                                                                     //
///////////////////////////////////////////////////////////////////////////////

	static function callBlock($params=null) {
		if($params === null){return '';}
		if(isset($params[0]) && $params[0] != ''){$block = strtolower($params[0]);}else{return '';}

		$func = "print_{$block}";
		if(!function_exists("print_{$block}")){return '';}

		// Build the config array
		array_shift($params);
		$cfg = array();
		foreach($params as $config) {
			$bits = explode('=', $config);
			if(count($bits) < 2){continue;}
			$v = array_shift($bits);
			$cfg[$v] = join('=', $bits);
		}

		// Run the block and retrive its contents
		ob_start();
		$func(false, $cfg, 'main', 9999);
		$out = ob_get_clean();
		// Rip out the content of the block
		return trim(substr(trim(stristr($out, 'blockcontent')), 14, -12));
	}

///////////////////////////////////////////////////////////////////////////////
// System                                                                    //
// Only allowed in GEDCOM Home Page, not user portals for security.       //
///////////////////////////////////////////////////////////////////////////////

	static function includeFile($params=null) {
		if(!isset($_GET['ctype']) || $_GET['ctype'] != 'gedcom'){return '';}
		if($params === null){$params = array();}
		if(isset($params[0]) && $params[0] != ''){$fn = $params[0];}else{return '';}

		if(!file_exists($fn) || stristr($fn, 'config.php')){return '';}
		ob_start();
		include filename_decode(real_path($fn));
		return trim(ob_get_clean());
	}
}
