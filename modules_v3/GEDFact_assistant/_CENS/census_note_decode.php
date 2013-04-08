<?php
// Census Assistant Control module for webtrees
//
// Census Shared Note Decode for a formatted file
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team. All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
//
// $Id$

	$text = "xCxAx<table><tr><td>" . $text;
	$text = str_replace("<br>.start_formatted_area.<br>", "</td></tr></table><table><tr><td class=\"notecell\">", $text);

		// -- Create View Header Tooltip explanations (Use embolden) -----------
		$text = str_replace(".b.".'Name', "<span class=\"note2\" title=\"".'Full Name or Married name if married'."\"> <b>".'Name'."</b></span>", $text);
		$text = str_replace(".b.".'Relation', "<span class=\"note1\" title=\"".'Relationship to Head of Household'."\"> <b>".'Relation'."</b></span>", $text);
		$text = str_replace(".b.".'Assets', "<span class=\"note1\" title=\"".'Assets = Owned,Rented - Value,Rent - Radio - Farm'."\"> <b>".'Assets'."</b></span>", $text);
		$text = str_replace(".b.".'Sex', "<span class=\"note1\" title=\"".'Male or Female'."\"> <b>".'Sex'."</b></span>", $text);
		$text = str_replace(".b.".'Race', "<span class=\"note1\" title=\"".'Race or Color - Black, White, Mulatto, Asian, Indian, Chinese etc'."\"> <b>".'Race'."</b></span>", $text);
		$text = str_replace(".b.".'Age', "<span class=\"note1\" title=\"".'Age at last birthday'."\"> <b>".'Age'."</b></span>", $text);
		$text = str_replace(".b.".'MC', "<span class=\"note1\" title=\"".'Marital Condition - Married, Single, Unmarried, Widowed or Divorced'."\"> <b>".'MC'."</b></span>", $text);
		$text = str_replace(".b.".'DOB', "<span class=\"note1\" title=\"".'Date of birth'."\"> <b>".'DOB'."</b></span>", $text);
		$text = str_replace(".b.".'Bmth', "<span class=\"note1\" title=\"".'Month of birth - If born within Census year'."\"> <b>".'Bmth'."</b></span>", $text);
		$text = str_replace(".b.".'YrsM', "<span class=\"note1\" title=\"".'Years Married, or Y if married in Census Year'."\"> <b>".'YrsM'."</b></span>", $text);
		$text = str_replace(".b.".'ChB', "<span class=\"note1\" title=\"".'Children born alive'."\"> <b>".'ChB'."</b></span>", $text);
		$text = str_replace(".b.".'ChL', "<span class=\"note1\" title=\"".'Children still living'."\"> <b>".'ChL'."</b></span>", $text);
		$text = str_replace(".b.".'ChD', "<span class=\"note1\" title=\"".'Children who have died'."\"> <b>".'ChD'."</b></span>", $text);
		$text = str_replace(".b.".'AgM', "<span class=\"note1\" title=\"".'Age at first marriage'."\"> <b>".'AgM'."</b></span>", $text);
		$text = str_replace(".b.".'Occupation', "<span class=\"note1\" title=\"".'Occupation'."\"> <b>".'Occupation'."</b></span>", $text);
		$text = str_replace(".b.".'Birthplace', "<span class=\"note1\" title=\"".'Birthplace (Full format)'."\"><b>".'Birthplace'."</b></span>", $text);
		$text = str_replace(".b.".'BP', "<span class=\"note1\" title=\"".'Birthplace - (Chapman format)'."\"> <b>".'BP'."</b></span>", $text);
		$text = str_replace(".b.".'FBP', "<span class=\"note1\" title=\"".WT_I18N::translate('Father\'s Birthplace - (Chapman format)')."\"> <b>".'FBP'."</b></span>", $text);
		$text = str_replace(".b.".'MBP', "<span class=\"note1\" title=\"".WT_I18N::translate('Mother\'s Birthplace - (Chapman format)')."\"> <b>".'MBP'."</b></span>", $text);
		$text = str_replace(".b.".'NL', "<span class=\"note1\" title=\"".'If Foreign Born - Native Language'."\"> <b>".'NL'."</b></span>", $text);
		$text = str_replace(".b.".'YUS', "<span class=\"note1\" title=\"".'If Foreign Born - Years in the USA'."\"> <b>".'YUS'."</b></span>", $text);
		$text = str_replace(".b.".'YOI', "<span class=\"note1\" title=\"".'If Foreign Born - Year of Immigration'."\"> <b>".'YOI'."</b></span>", $text);
		$text = str_replace(".b.".'N/A', "<span class=\"note1\" title=\"".'If Foreign Born - Naturalized, Alien'."\"> <b>".'N/A'."</b></span>", $text);
		$text = str_replace(".b.".'YON', "<span class=\"note1\" title=\"".'If Foreign Born - Year of Naturalization'."\"> <b>".'YON'."</b></span>", $text);
		$text = str_replace(".b.".'EngL', "<span class=\"note1\" title=\"".'English spoken?, if not, Native Language'."\"> <b>".'EngL'."</b></span>", $text);
		$text = str_replace(".b.".'Health', "<span class=\"note1\" title=\"".'Health - 1.Blind, 2.Deaf&amp;Dumb, 3.Idiotic, 4.Insane, 5.Disabled etc'."\"><b>".'Health'."</b></span>", $text);
		$text = str_replace(".b.".'Industry', "<span class=\"note1\" title=\"".'Industry'."\"> <b>".'Industry'."</b></span>", $text);
		$text = str_replace(".b.".'Employ', "<span class=\"note1\" title=\"".'Employment'."\"> <b>".'Employ'."</b></span>", $text);
		$text = str_replace(".b.".'EmR', "<span class=\"note1\" title=\"".'Employer?'."\"> <b>".'EmR'."</b></span>", $text);
		$text = str_replace(".b.".'EmD', "<span class=\"note1\" title=\"".'Employed?'."\"> <b>".'EmD'."</b></span>", $text);
		$text = str_replace(".b.".'WH', "<span class=\"note1\" title=\"".'Working at Home?'."\"> <b>".'WH'."</b></span>", $text);
		$text = str_replace(".b.".'EmN', "<span class=\"note1\" title=\"".'Unemployed?'."\"> <b>".'EmN'."</b></span>", $text);
		$text = str_replace(".b.".'Edu', "<span class=\"note1\" title=\"".'Education - At School, Can Read, Can Write'."\"> <b>".'Edu'."</b></span>", $text);
		$text = str_replace(".b.".'Eng?', "<span class=\"note1\" title=\"".'English spoken?'."\"> <b>".'Eng?'."</b></span>", $text);
		$text = str_replace(".b.".'BIC', "<span class=\"note1\" title=\"".'Born in County'."\"> <b>".'BIC'."</b></span>", $text);
		$text = str_replace(".b.".'BOE', "<span class=\"note1\" title=\"".'Born outside England'."\"> <b>".'BOE'."</b></span>", $text);
		$text = str_replace(".b.".'Lang', "<span class=\"note1\" title=\"".'If Foreign Born - Native Language'."\"> <b>".'Lang'."</b></span>", $text);
		$text = str_replace(".b.".'Infirm', "<span class=\"note1\" title=\"".'Infirmaties - 1.Deaf&amp;Dumb, 2.Blind, 3.Lunatic, 4.Imbecile/feeble-minded'."\"><b>".'Infirm'."</b></span>", $text);
		$text = str_replace(".b.".'Vet', "<span class=\"note1\" title=\"".'War Veteran?'."\"> <b>".'Vet'."</b></span>", $text);
		$text = str_replace(".b.".'Ten', "<span class=\"note1\" title=\"".'Tenure - Owned/Rented, (if owned)Free/Morgaged'."\"> <b>".'Ten'."</b></span>", $text);
		$text = str_replace(".b.".'Par', "<span class=\"note1\" title=\"".'Parentage - Father if foreign born, Mother if foreign born'."\"> <b>".'Par'."</b></span>", $text);
		$text = str_replace(".b.".'Mmth', "<span class=\"note1\" title=\"".'Month of marriage - If married during Census Year'."\"> <b>".'Mmth'."</b></span>", $text);
		$text = str_replace(".b.".'MnsE', "<span class=\"note1\" title=\"".'Months employed during Census Year'."\"> <b>".'MnsE'."</b></span>", $text);
		$text = str_replace(".b.".'WksU', "<span class=\"note1\" title=\"".'Weeks unemployed during Census Year'."\"> <b>".'WksU'."</b></span>", $text);
		$text = str_replace(".b.".'MnsU', "<span class=\"note1\" title=\"".'Months unemployed during Census Year'."\"> <b>".'MnsU'."</b></span>", $text);
		$text = str_replace(".b.".'Edu', "<span class=\"note1\" title=\"".'Education - xxx - At School, Cannot Read, Cannot Write'."\"><b>".'Edu'."</b></span>", $text);
		$text = str_replace(".b.".'Home', "<span class=\"note1\" title=\"".'Home Ownership - Owned/Rented-Free/Mortgaged-Farm/House-Farm Schedule number'."\"> <b>".'Home'."</b></span>", $text);
		$text = str_replace(".b.".'Situ', "<span class=\"note1\" title=\"".'Situation - Disease, Infirmaty, Convict, Pauper etc'."\"> <b>".'Situ'."</b></span>", $text);
		$text = str_replace(".b.".'War', "<span class=\"note1\" title=\"".'War or Expedition'."\"> <b>".'War'."</b></span>", $text);
		$text = str_replace(".b.".'Infirm', "<span class=\"note1\" title=\"".'Infirmaties - Whether blind, Whether Deaf and Dumb'."\"> <b>".'Infirm'."</b></span>", $text);


		// Regular Field Highlighting (Use embolden) ------------
		$text = str_replace(".b.", "<b>", $text);

		// Replace "pipe" with </td><td> ------------------------
		$text = str_replace("|", "&nbsp;&nbsp;</td><td class=\"notecell\">", $text);

	$text = str_replace(".end_formatted_area.<br>", "</td></tr></table><table><tr><td>", $text);
	$text = str_replace("<br>", "</td></tr><tr><td class=\"notecell\">", $text);
	$text = $text . "</td></tr></table>";
	$text = str_replace("xCxAx", $centitl."<br>", $text);
	$text = str_replace("Notes:", "<b>Notes:</b>", $text);
