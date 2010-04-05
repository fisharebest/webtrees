<?php
/**
 * Gedcom Statistics Block
 *
 * This block prints statistical information for the currently active gedcom
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
 * -- Slightly modified (rtl in table values) 2006/06/09 18:00:00 pfblair
 * @package webtrees
 * @subpackage Blocks
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_GEDCOM_STATS_PHP', '');

require_once WT_ROOT.'includes/functions/functions_print_lists.php';
require_once WT_ROOT.'includes/classes/class_stats.php';

$WT_BLOCKS['print_gedcom_stats']['name']     =i18n::translate('GEDCOM Statistics');
$WT_BLOCKS['print_gedcom_stats']['descr']    =i18n::translate('The GEDCOM Statistics block shows the visitor some basic information about the database, such as when it was created and how many people are in it.<br /><br />It also has a list of the most frequent surnames.  You can configure this block to not show the Frequent Surnames list, and you can also configure the GEDCOM to remove or add names to this list.  You can set the occurrence threshold for this list in the GEDCOM configuration.');
$WT_BLOCKS['print_gedcom_stats']['canconfig']=true;
$WT_BLOCKS['print_gedcom_stats']['config']   =array(
	'cache'               =>1,
	'show_common_surnames'=>'yes',
	'stat_indi'           =>'yes',
	'stat_fam'            =>'yes',
	'stat_sour'           =>'yes',
	'stat_other'          =>'yes',
	'stat_media'          =>'yes',
	'stat_surname'        =>'yes',
	'stat_events'         =>'yes',
	'stat_users'          =>'yes',
	'stat_first_birth'    =>'yes',
	'stat_last_birth'     =>'yes',
	'stat_first_death'    =>'yes',
	'stat_last_death'     =>'yes',
	'stat_long_life'      =>'yes',
	'stat_avg_life'       =>'yes',
	'stat_most_chil'      =>'yes',
	'stat_avg_chil'       =>'yes',
	'stat_link'           =>'yes'
);

//-- function to print the gedcom statistics block

function print_gedcom_stats($block=true, $config='', $side, $index) {
	global $WT_BLOCKS, $ALLOW_CHANGE_GEDCOM, $ctype, $COMMON_NAMES_THRESHOLD, $WT_IMAGE_DIR, $WT_IMAGES, $MULTI_MEDIA;
	global $top10_block_present;

	if (empty($config)) $config = $WT_BLOCKS['print_gedcom_stats']['config'];
	if (!isset($config['stat_indi'])) $config = $WT_BLOCKS['print_gedcom_stats']['config'];
	if (!isset($config['stat_first_death'])) $config['stat_first_death'] = $WT_BLOCKS['print_gedcom_stats']['config']['stat_first_death'];
	if (!isset($config['stat_last_death'])) $config['stat_last_death'] = $WT_BLOCKS['print_gedcom_stats']['config']['stat_last_death'];
	if (!isset($config['stat_media'])) $config['stat_media'] = $WT_BLOCKS['print_gedcom_stats']['config']['stat_media'];
	if (!isset($config['stat_link'])) $config['stat_link'] = $WT_BLOCKS['print_gedcom_stats']['config']['stat_link'];

	$id = 'gedcom_stats';
	$title='';
	if ($WT_BLOCKS['print_gedcom_stats']['canconfig']) {
		if ($ctype=='gedcom' && WT_USER_GEDCOM_ADMIN || $ctype=='user' && WT_USER_ID) {
			if ($ctype=='gedcom') {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('".encode_url("index_edit.php?name={$name}&ctype={$ctype}&action=configure&side={$side}&index={$index}")."', '_blank', 'top=50,left=50,width=700,height=400,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES['admin']['small']."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
	}
	$title.=i18n::translate('GEDCOM Statistics').help_link('index_stats');

	$stats=new stats(WT_GEDCOM);

	$content = "<b><a href=\"index.php?ctype=gedcom\">".PrintReady(strip_tags(get_gedcom_setting(WT_GED_ID, 'title')))."</a></b><br />";
	$head = find_other_record('HEAD', WT_GED_ID);
	$ct=preg_match('/1 SOUR (.*)/', $head, $match);
	if ($ct>0) {
		$softrec = get_sub_record(1, '1 SOUR', $head);
		$tt= preg_match('/2 NAME (.*)/', $softrec, $tmatch);
		if ($tt>0) $software = printReady(trim($tmatch[1]));
		else $software = trim($match[1]);
		if (!empty($software)) {
			$tt = preg_match('/2 VERS (.*)/', $softrec, $tmatch);
			if ($tt>0) $version = printReady(trim($tmatch[1]));
			else $version='';
			$content .= i18n::translate('This GEDCOM was created using <b>%s %s</b>', $software, $version);
		}
	}
	if (preg_match('/1 DATE (.+)/', $head, $match)) {
		if (empty($software)) {
			$content.=i18n::translate('This GEDCOM was created on <b>%s</b>', $stats->gedcomDate());
		} else {
			$content.=i18n::translate(' on <b>%s</b>', $stats->gedcomDate());
		}
	}

	$content .= '<br /><table><tr><td valign="top" class="width20"><table cellspacing="1" cellpadding="0">';
	if ($config['stat_indi']=='yes') {
		$content.='<tr><td class="facts_label">'.i18n::translate('Individuals').'</td><td class="facts_value"><div dir="rtl"><a href="'.encode_url("indilist.php?surname_sublist=no&ged=".WT_GEDCOM).'">'.$stats->totalIndividuals().'</a></div></td></tr>';
		$content.='<tr><td class="facts_label">'.i18n::translate('Males').'</td><td class="facts_value"><div dir="rtl">'.$stats->totalSexMales().'<br />'.$stats->totalSexMalesPercentage().'%</div></td></tr>';
		$content.='<tr><td class="facts_label">'.i18n::translate('Females').'</td><td class="facts_value"><div dir="rtl">'.$stats->totalSexFemales().'<br />'.$stats->totalSexFemalesPercentage().'%</div></td></tr>';
	}
	if ($config['stat_surname']=='yes') {
		$content .= '<tr><td class="facts_label">'.i18n::translate('Total surnames').'</td><td class="facts_value"><div dir="rtl"><a href="'.encode_url("indilist.php?show_all=yes&surname_sublist=yes&ged=".WT_GEDCOM).'">'.$stats->totalSurnames().'</a></div></td></tr>';
	}
	if ($config['stat_fam']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Families').'</td><td class="facts_value"><div dir="rtl"><a href="famlist.php">'.$stats->totalFamilies().'</a></div></td></tr>';
	}
	if ($config['stat_sour']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Sources').'</td><td class="facts_value"><div dir="rtl"><a href="sourcelist.php">'.$stats->totalSources().'</a></div></td></tr>';
	}
	if ($config['stat_media']=='yes' && $MULTI_MEDIA==true) {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Media objects').'</td><td class="facts_value"><div dir="rtl"><a href="medialist.php">'.$stats->totalMedia().'</a></div></td></tr>';
	}
	if ($config['stat_other']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Other records').'</td><td class="facts_value"><div dir="rtl">'.$stats->totalOtherRecords().'</div></td></tr>';
	}
	if ($config['stat_events']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Total events').'</td><td class="facts_value"><div dir="rtl">'.$stats->totalEvents().'</div></td></tr>';
	}
	if ($config['stat_users']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Total users').'</td><td class="facts_value"><div dir="rtl">';
			if (WT_USER_GEDCOM_ADMIN){
			$content .= '<a href="useradmin.php">'.$stats->totalUsers().'</a>';
		} else {
			$content .= $stats->totalUsers();
		}
		$content .= '</div>
</td>
</tr>';
	}
	if (!$block) {
		$content .= '</table></td><td><br /></td><td valign="top"><table cellspacing="1" cellpadding="1" border="0">';
	}
	if ($config['stat_first_birth']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Earliest birth year').'</td><td class="facts_value"><div dir="rtl">'.$stats->firstBirthYear().'</div></td>';
		if (!$block) {
			$content .= '<td class="facts_value">'.$stats->firstBirth().'</td>';
		}
		$content .= '</tr>';
	}
	if ($config['stat_last_birth']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Latest birth year').'</td><td class="facts_value"><div dir="rtl">'.$stats->lastBirthYear().'</div></td>';
		if (!$block){
			$content .= '<td class="facts_value">'.$stats->lastBirth().'</td>';
		}
		$content .= '</tr>';
	}
	if ($config['stat_first_death']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Earliest death year').'</td><td class="facts_value"><div dir="rtl">'.$stats->firstDeathYear().'</div></td>';
		if (!$block){
			$content .= '<td class="facts_value">'.$stats->firstDeath().'</td>';
		}
		$content .= '</tr>';
	}
	if ($config['stat_last_death']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Latest death year') .'</td><td class="facts_value"><div dir="rtl">'.$stats->lastDeathYear().'</div>
</td>';
		if (!$block){
			$content .= '<td class="facts_value">'.$stats->lastDeath().'</td>';
		}
		$content .='</tr>';
	}
	if ($config['stat_long_life']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Person who lived the longest').'</td><td class="facts_value"><div dir="rtl">'.$stats->LongestLifeAge().'</div></td>';
		if (!$block){
			$content .= '<td class="facts_value">'.$stats->LongestLife().'</td>';
		}
		$content .= '</tr>';
	}
	if ($config['stat_avg_life']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Average age at death').'</td><td class="facts_value"><div dir="rtl">'.$stats->averageLifespan().'</div></td>';
		if (!$block) {
			$content .= '<td class="facts_value">'.i18n::translate('Males').':&nbsp;'.$stats->averageLifespanMale();
			$content .= '&nbsp;&nbsp;&nbsp;'.i18n::translate('Females').':&nbsp;'.$stats->averageLifespanFemale().'</td>';
		}
		$content .= '</tr>';
	}

	if ($config['stat_most_chil']=='yes' && !$block) {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Family with the most children').'</td><td class="facts_value"><div dir="rtl">'.$stats->largestFamilySize().'</div></td>';
		if (!$block) {
			$content .= '<td class="facts_value">'.$stats->largestFamily().'</td>';
		}
		$content .= '</tr>';
	}
	if ($config['stat_avg_chil']=='yes') {
		$content .= '<tr><td class="facts_label">'. i18n::translate('Average number of children per family').'</td><td class="facts_value"><div dir="rtl">'.$stats->averageChildren().'</div></td>';
		if (!$block) {
			$content .= '<td class="facts_value">&nbsp;</td>';
		}
		$content .= '</tr>';
	}
	$content .= '</table></td></tr></table>';
	if ($config['stat_link']=='yes') {
		$content .= '<a href="statistics.php"><b>'.i18n::translate('View statistics as graphs').'</b></a><br />';
	}
	// NOTE: Print the most common surnames
	if ($config['show_common_surnames']=='yes') {
		$surnames = get_common_surnames($COMMON_NAMES_THRESHOLD);
		if (count($surnames)>0) {
			$content .= '<br /><b>'.i18n::translate('Most Common Surnames').'</b>';
			$content .= help_link('index_common_names');
			$content .= '<br />';
			$i=0;
			foreach($surnames as $indexval => $surname) {
				if (stristr($surname['name'], '@N.N')===false) {
					if ($i>0) {
						$content .= ', ';
					}
					$content .= '<a href="'.encode_url("indilist.php?ged=".WT_GEDCOM."&surname=".$surname['name']).'">'.PrintReady($surname['name']).'</a>';
					$i++;
				}
			}
		}
	}

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}

function print_gedcom_stats_config($config) {
	global $ctype, $WT_BLOCKS, $TEXT_DIRECTION;
	if (empty($config)) $config = $WT_BLOCKS['print_gedcom_stats']['config'];
	if (!isset($config['stat_indi'])) $config = $WT_BLOCKS['print_gedcom_stats']['config'];
	if (!isset($config['stat_first_death'])) $config['stat_first_death'] = $WT_BLOCKS['print_gedcom_stats']['config']['stat_first_death'];
	if (!isset($config['stat_last_death'])) $config['stat_last_death'] = $WT_BLOCKS['print_gedcom_stats']['config']['stat_last_death'];
	if (!isset($config['stat_media'])) $config['stat_media'] = $WT_BLOCKS['print_gedcom_stats']['config']['stat_media'];
	if (!isset($config['stat_link'])) $config['stat_link'] = $WT_BLOCKS['print_gedcom_stats']['config']['stat_link'];
	if (!isset($config['cache'])) $config['cache'] = $WT_BLOCKS['print_gedcom_stats']['config']['cache'];

	?><tr><td class="descriptionbox wrap width33"> <?php echo i18n::translate('Show common surnames?'); ?></td>
<td class="optionbox"><select name="show_common_surnames">
<option value="yes"
<?php if ($config['show_common_surnames']=='yes') echo ' selected="selected"'; ?>><?php echo i18n::translate('Yes'); ?></option>
<option value="no"
<?php if ($config['show_common_surnames']=='no') echo ' selected="selected"'; ?>><?php echo i18n::translate('No'); ?></option>
</select></td>
</tr>
<tr>
<td class="descriptionbox wrap width33"><?php echo i18n::translate('Select the stats to show in this block'); ?></td>
<td class="optionbox">
<table>
	<tr>
		<td><input type="checkbox" value="yes" name="stat_indi"
		<?php if ($config['stat_indi']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Individuals'); ?></td>
		<td><input type="checkbox" value="yes" name="stat_first_birth"
		<?php if ($config['stat_first_birth']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Earliest birth year'); ?></td>
	</tr>
	<tr>
		<td><input type="checkbox" value="yes" name="stat_surname"
		<?php if ($config['stat_surname']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Total surnames'); ?></td>
		<td><input type="checkbox" value="yes" name="stat_last_birth"
		<?php if ($config['stat_last_birth']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Latest birth year'); ?></td>
	</tr>
	<tr>
		<td><input type="checkbox" value="yes" name="stat_fam"
		<?php if ($config['stat_fam']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Families'); ?></td>
		<td><input type="checkbox" value="yes" name="stat_first_death"
		<?php if ($config['stat_first_death']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Earliest death year'); ?></td>
	</tr>
	<tr>
		<td><input type="checkbox" value="yes" name="stat_sour"
		<?php if ($config['stat_sour']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Sources'); ?></td>
		<td><input type="checkbox" value="yes" name="stat_last_death"
		<?php if ($config['stat_last_death']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Latest death year'); ?></td>
	</tr>
	<tr>
		<td><input type="checkbox" value="yes" name="stat_media"
		<?php if ($config['stat_media']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Media objects'); ?></td>
		<td><input type="checkbox" value="yes" name="stat_long_life"
		<?php if ($config['stat_long_life']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Person who lived the longest'); ?></td>
	</tr>
	<tr>
		<td><input type="checkbox" value="yes" name="stat_other"
		<?php if ($config['stat_other']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Other records'); ?></td>
		<td><input type="checkbox" value="yes" name="stat_avg_life"
		<?php if ($config['stat_avg_life']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Average age at death'); ?></td>
	</tr>
	<tr>
		<td><input type="checkbox" value="yes" name="stat_events"
		<?php if ($config['stat_events']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Total events'); ?></td>
		<td><input type="checkbox" value="yes" name="stat_most_chil"
		<?php if ($config['stat_most_chil']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Family with the most children'); ?></td>
	</tr>
	<tr>
		<td><input type="checkbox" value="yes" name="stat_users"
		<?php if ($config['stat_users']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Total users'); ?></td>
		<td><input type="checkbox" value="yes" name="stat_avg_chil"
		<?php if ($config['stat_avg_chil']=='yes') echo ' checked="checked"'; ?> />
		<?php echo i18n::translate('Average number of children per family'); ?></td>
	</tr>
</table>
</td>
</tr>
<tr>
	<td class="descriptionbox wrap width33"> <?php echo i18n::translate('Show link to Statistics charts?'); ?></td>
	<td class="optionbox">
		<select name="stat_link">
			<option value="yes" <?php if ($config['stat_link']=='yes') echo ' selected="selected"'; ?>><?php echo i18n::translate('Yes'); ?></option>
			<option value="no" <?php if ($config['stat_link']=='no') echo ' selected="selected"'; ?>><?php echo i18n::translate('No'); ?></option>
		</select>
	</td>
</tr>
<?php

	// Cache file life
	if ($ctype=='gedcom') {
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Cache file life'), help_link('cache_life');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="cache" size="2" value="', $config['cache'], '" />';
		echo '</td></tr>';
	}
}
?>
