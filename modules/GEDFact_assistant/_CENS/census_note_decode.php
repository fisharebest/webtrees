<?php
/**
 * Census Assistant Control module for phpGedView
 *
 * Census Shared Note Decode for a formatted file
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
 * @subpackage Census Assistant
 * @version $Id$
 */
 
	$text = "xCxAx<table cellpadding=\"0\"><tr><td>" . $text;
	$text = str_replace("<br />.start_formatted_area.<br />", "</td></tr></table><table cellpadding=\"0\"><tr><td class=\"notecell\">&nbsp;", $text);
	
		// -- Create View Header Tooltip explanations (Use embolden) -----------
		$text = str_replace(".b.".'Name',   "<span class=\"note2\" alt=\"".'Full Name or Married name if married'."\"   title=\"".'Full Name or Married name if married'."\">  <b>".'Name'."</span>",   $text);
		$text = str_replace(".b.".'Relation',   "<span class=\"note1\" alt=\"".'Relationship to Head of Household'."\"   title=\"".'Relationship to Head of Household'."\">  <b>".'Relation'."</span>",   $text);
		$text = str_replace(".b.".'Assets',  "<span class=\"note1\" alt=\"".'Assets = Owned,Rented - Value,Rent - Radio - Farm'."\"  title=\"".'Assets = Owned,Rented - Value,Rent - Radio - Farm'."\"> <b>".'Assets'."</span>",  $text);
		$text = str_replace(".b.".'Sex',    "<span class=\"note1\" alt=\"".'Male or Female'."\"    title=\"".'Male or Female'."\">   <b>".'Sex'."</span>",    $text);
		$text = str_replace(".b.".'Race',   "<span class=\"note1\" alt=\"".'Race or Color - Black, White, Mulatto, Asian, Indian, Chinese etc'."\"   title=\"".'Race or Color - Black, White, Mulatto, Asian, Indian, Chinese etc'."\">  <b>".'Race'."</span>",   $text);
		$text = str_replace(".b.".'Age',    "<span class=\"note1\" alt=\"".'Age at last birthday'."\"    title=\"".'Age at last birthday'."\">   <b>".'Age'."</span>",    $text);
		$text = str_replace(".b.".'MC',  "<span class=\"note1\" alt=\"".'Marital Condition - Married, Single, Unmarried, Widowed or Divorced'."\"  title=\"".'Marital Condition - Married, Single, Unmarried, Widowed or Divorced'."\"> <b>".'MC'."</span>",  $text);
		$text = str_replace(".b.".'DOB',    "<span class=\"note1\" alt=\"".'Date of Birth'."\"    title=\"".'Date of Birth'."\">   <b>".'DOB'."</span>",    $text);
		$text = str_replace(".b.".'Bmth',   "<span class=\"note1\" alt=\"".'Month of birth - If born within Census year'."\"   title=\"".'Month of birth - If born within Census year'."\">  <b>".'Bmth'."</span>",   $text);
		$text = str_replace(".b.".'YrsM',   "<span class=\"note1\" alt=\"".'Years Married, or Y if married in Census Year'."\"   title=\"".'Years Married, or Y if married in Census Year'."\">  <b>".'YrsM'."</span>",   $text);
		$text = str_replace(".b.".'ChB',  "<span class=\"note1\" alt=\"".'Children born alive'."\"  title=\"".'Children born alive'."\"> <b>".'ChB'."</span>",  $text);
		$text = str_replace(".b.".'ChL',  "<span class=\"note1\" alt=\"".'Children still living'."\"  title=\"".'Children still living'."\"> <b>".'ChL'."</span>",  $text);
		$text = str_replace(".b.".'ChD',  "<span class=\"note1\" alt=\"".'Children who have died'."\"  title=\"".'Children who have died'."\"> <b>".'ChD'."</span>",  $text);
		$text = str_replace(".b.".'AgM',    "<span class=\"note1\" alt=\"".'Age at first marriage'."\"    title=\"".'Age at first marriage'."\">   <b>".'AgM'."</span>",    $text);
		$text = str_replace(".b.".'Occupation',   "<span class=\"note1\" alt=\"".'Occupation'."\"   title=\"".'Occupation'."\">  <b>".'Occupation'."</span>",   $text);
		$text = str_replace(".b.".'Birthplace', "<span class=\"note1\" alt=\"".'Birthplace (Full format)'."\" title=\"".'Birthplace (Full format)'."\"><b>".'Birthplace'."</span>", $text);
		$text = str_replace(".b.".'BP',     "<span class=\"note1\" alt=\"".'Birthplace - (Chapman format)'."\"     title=\"".'Birthplace - (Chapman format)'."\">    <b>".'BP'."</span>",     $text);
		$text = str_replace(".b.".'FBP',    "<span class=\"note1\" alt=\"".i18n::translate('Father\'s Birthplace - (Chapman format)')."\"    title=\"".i18n::translate('Father\'s Birthplace - (Chapman format)')."\">   <b>".'FBP'."</span>",    $text);
		$text = str_replace(".b.".'MBP',    "<span class=\"note1\" alt=\"".i18n::translate('Mother\'s Birthplace - (Chapman format)')."\"    title=\"".i18n::translate('Mother\'s Birthplace - (Chapman format)')."\">   <b>".'MBP'."</span>",    $text);
		$text = str_replace(".b.".'NL',     "<span class=\"note1\" alt=\"".'If Foreign Born - Native Language'."\"     title=\"".'If Foreign Born - Native Language'."\">    <b>".'NL'."</span>",     $text);
		$text = str_replace(".b.".'YUS',  "<span class=\"note1\" alt=\"".'If Foreign Born - Years in the USA'."\"  title=\"".'If Foreign Born - Years in the USA'."\"> <b>".'YUS'."</span>",  $text);
		$text = str_replace(".b.".'YOI',    "<span class=\"note1\" alt=\"".'If Foreign Born - Year of Immigration'."\"    title=\"".'If Foreign Born - Year of Immigration'."\">   <b>".'YOI'."</span>",    $text);
		$text = str_replace(".b.".'N/A',     "<span class=\"note1\" alt=\"".'If Foreign Born - Naturalized, Alien'."\"     title=\"".'If Foreign Born - Naturalized, Alien'."\">    <b>".'N/A'."</span>",     $text);
		$text = str_replace(".b.".'YON',    "<span class=\"note1\" alt=\"".'If Foreign Born - Year of Naturalization'."\"    title=\"".'If Foreign Born - Year of Naturalization'."\">   <b>".'YON'."</span>",    $text);
		$text = str_replace(".b.".'EngL',   "<span class=\"note1\" alt=\"".'English spoken?, if not, Native Language'."\"   title=\"".'English spoken?, if not, Native Language'."\">  <b>".'EngL'."</span>",   $text);
		$text = str_replace(".b.".'Health', "<span class=\"note1\" alt=\"".'Health - 1.Blind, 2.Deaf&amp;Dumb, 3.Idiotic, 4.Insane, 5.Disabled etc'."\" title=\"".'Health - 1.Blind, 2.Deaf&amp;Dumb, 3.Idiotic, 4.Insane, 5.Disabled etc'."\"><b>".'Health'."</span>", $text);
		$text = str_replace(".b.".'Industry',    "<span class=\"note1\" alt=\"".'Industry'."\"    title=\"".'Industry'."\">   <b>".'Industry'."</span>",    $text);
		$text = str_replace(".b.".'Employ',    "<span class=\"note1\" alt=\"".'Employment'."\"    title=\"".'Employment'."\">   <b>".'Employ'."</span>",    $text);
		$text = str_replace(".b.".'EmR',    "<span class=\"note1\" alt=\"".'Employer?'."\"    title=\"".'Employer?'."\">   <b>".'EmR'."</span>",    $text);
		$text = str_replace(".b.".'EmD',    "<span class=\"note1\" alt=\"".'Employed?'."\"    title=\"".'Employed?'."\">   <b>".'EmD'."</span>",    $text);
		$text = str_replace(".b.".'WH',    "<span class=\"note1\" alt=\"".'Working at Home?'."\"    title=\"".'Working at Home?'."\">   <b>".'WH'."</span>",    $text);
		$text = str_replace(".b.".'EmN',    "<span class=\"note1\" alt=\"".'Unemployed?'."\"    title=\"".'Unemployed?'."\">   <b>".'EmN'."</span>",    $text);
		$text = str_replace(".b.".'Edu',   "<span class=\"note1\" alt=\"".'Education - At School, Can Read, Can Write'."\"   title=\"".'Education - At School, Can Read, Can Write'."\">  <b>".'Edu'."</span>",   $text);
		$text = str_replace(".b.".'Eng?',    "<span class=\"note1\" alt=\"".'English spoken?'."\"    title=\"".'English spoken?'."\">   <b>".'Eng?'."</span>",    $text);
		$text = str_replace(".b.".'BIC',    "<span class=\"note1\" alt=\"".'Born in County'."\"    title=\"".'Born in County'."\">   <b>".'BIC'."</span>",    $text);
		$text = str_replace(".b.".'BOE',    "<span class=\"note1\" alt=\"".'Born outside England'."\"    title=\"".'Born outside England'."\">   <b>".'BOE'."</span>",    $text);
		$text = str_replace(".b.".'Lang',   "<span class=\"note1\" alt=\"".'If Foreign Born - Native Language'."\"   title=\"".'If Foreign Born - Native Language'."\">  <b>".'Lang'."</span>",   $text);
		$text = str_replace(".b.".'Infirm', "<span class=\"note1\" alt=\"".'Infirmaties - 1.Deaf&amp;Dumb, 2.Blind, 3.Lunatic, 4.Imbecile/feeble-minded'."\" title=\"".'Infirmaties - 1.Deaf&amp;Dumb, 2.Blind, 3.Lunatic, 4.Imbecile/feeble-minded'."\"><b>".'Infirm'."</span>", $text);
		$text = str_replace(".b.".'Vet',    "<span class=\"note1\" alt=\"".'War Veteran?'."\"    title=\"".'War Veteran?'."\">   <b>".'Vet'."</span>",    $text);

		$text = str_replace(".b.".'Ten',      "<span class=\"note1\" alt=\"".'Tenure - Owned/Rented, (if owned)Free/Morgaged'."\"       title=\"".'Tenure - Owned/Rented, (if owned)Free/Morgaged'."\">     <b>".'Ten'."</span>",      $text);
		$text = str_replace(".b.".'Par',      "<span class=\"note1\" alt=\"".'Parentage - Father if foreign born, Mother if foreign born'."\"       title=\"".'Parentage - Father if foreign born, Mother if foreign born'."\">     <b>".'Par'."</span>",      $text);
		$text = str_replace(".b.".'Mmth',        "<span class=\"note1\" alt=\"".'Month of marriage - If married during Census Year'."\"         title=\"".'Month of marriage - If married during Census Year'."\">       <b>".'Mmth'."</span>",        $text);
		$text = str_replace(".b.".'MnsE',        "<span class=\"note1\" alt=\"".'Months employed during Census Year'."\"         title=\"".'Months employed during Census Year'."\">       <b>".'MnsE'."</span>",        $text);
		$text = str_replace(".b.".'WksU',        "<span class=\"note1\" alt=\"".'Weeks unemployed during Census Year'."\"         title=\"".'Weeks unemployed during Census Year'."\">       <b>".'WksU'."</span>",        $text);
		$text = str_replace(".b.".'MnsU',        "<span class=\"note1\" alt=\"".'Months unemployed during Census Year'."\"         title=\"".'English spoken?'."\">        <b>".'Eng?'."</span>",         $text);
		$text = str_replace(".b.".'Edu', "<span class=\"note1\" alt=\"".'Education - xxx - At School, Cannot Read, Cannot Write'."\"  title=\"".'Education - xxx - At School, Cannot Read, Cannot Write'."\"><b>".'Edu'."</span>", $text);
		$text = str_replace(".b.".'Home',        "<span class=\"note1\" alt=\"".'Home Ownership - Owned/Rented-Free/Mortgaged-Farm/House-Farm Schedule number'."\"         title=\"".'Home Ownership - Owned/Rented-Free/Mortgaged-Farm/House-Farm Schedule number'."\">       <b>".'Home'."</span>",        $text);
		$text = str_replace(".b.".'Situ',        "<span class=\"note1\" alt=\"".'Situation - Disease, Infirmaty, Convict, Pauper etc'."\"         title=\"".'Situation - Disease, Infirmaty, Convict, Pauper etc'."\">       <b>".'Situ'."</span>",        $text);
		$text = str_replace(".b.".'War',         "<span class=\"note1\" alt=\"".'War or Expedition'."\"          title=\"".'War or Expedition'."\">        <b>".'War'."</span>",         $text);
		$text = str_replace(".b.".'Infirm',  "<span class=\"note1\" alt=\"".'Infirmaties - Whether blind, Whether Deaf and Dumb'."\"   title=\"".'Infirmaties - Whether blind, Whether Deaf and Dumb'."\"> <b>".'Infirm'."</span>",  $text);


		// Regular Field Highlighting (Use embolden) ------------
		$text = str_replace(".b.", "<b>", $text); 
		
		// Replace "pipe" with </td><td> ------------------------
		$text = str_replace("|", "&nbsp;&nbsp;</td><td class=\"notecell\">", $text);
		
	$text = str_replace(".end_formatted_area.<br />", "</td></tr></table><table cellpadding=\"0\"><tr><td>", $text);
	$text = str_replace("<br />", "</td></tr><tr><td class=\"notecell\">&nbsp;", $text);
	$text = $text . "</td></tr></table>";
	$text = str_replace("xCxAx", $centitl."<br />", $text);
	$text = str_replace("Notes:", "<b>Notes:</b>", $text);

?>
