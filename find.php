<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\SimpleController;

require 'includes/session.php';

$controller = new SimpleController;

$type      = Filter::get('type');
$filter    = Filter::get('filter');
$action    = Filter::get('action');
$callback  = Filter::get('callback', '[a-zA-Z0-9_]+', 'paste_id');
$subclick  = Filter::get('subclick');
$choose    = Filter::get('choose', '[a-zA-Z0-9_]+', '0all');
$qs        = Filter::get('tags');

switch ($type) {
case 'specialchar':
	$controller->setPageTitle(I18N::translate('Find a special character'));
	break;
case 'factINDI':
	$controller
		->setPageTitle(I18N::translate('Find a fact or event'))
		->addInlineJavascript('initPickFact("INDI");');
	break;
case 'factFAM':
	$controller
		->setPageTitle(I18N::translate('Find a fact or event'))
		->addInlineJavascript('initPickFact("FAM");');
	break;
case 'factSOUR':
	$controller
		->setPageTitle(I18N::translate('Find a fact or event'))
		->addInlineJavascript('initPickFact("SOUR");');
	break;
case 'factREPO':
	$controller
		->setPageTitle(I18N::translate('Find a fact or event'))
		->addInlineJavascript('initPickFact("REPO");');
	break;
case 'factNAME':
	$controller
		->setPageTitle(I18N::translate('Find a fact or event'))
		->addInlineJavascript('initPickFact("NAME");');
	break;
case 'factPLAC':
	$controller
		->setPageTitle(I18N::translate('Find a fact or event'))
		->addInlineJavascript('initPickFact("PLAC");');
	break;
}
$controller->pageHeader();

echo '<script>';
?>
	function pasteid(id, name, thumb) {
		if (thumb) {
			window.opener.<?= $callback ?>(id, name, thumb);
			<?= 'window.close();' ?>
		} else {
			// GEDFact_assistant ========================
			if (window.opener.document.getElementById('addlinkQueue')) {
				window.opener.insertRowToTable(id, name);
			}
			window.opener.<?= $callback ?>(id);
			if (window.opener.pastename) window.opener.pastename(name);
			<?= 'window.close();' ?>
		}
	}
	function checknames(frm) {
		if (document.forms[0].subclick) button = document.forms[0].subclick.value;
		else button = "";
		if (frm.filter.value.length<2&button!="all") {
			alert("<?= I18N::translate('Please enter more than one character.') ?>");
			frm.filter.focus();
			return false;
		}
		if (button=="all") {
			frm.filter.value = "";
		}
		return true;
	}
<?php
echo '</script>';

echo '<div id="find-page"><h2>', $controller->getPageTitle(), '</h2>';

// Show specialchar and hide the rest
if ($type == 'specialchar') {
	$language_filter       = Filter::get('language_filter', null, Auth::user()->getPreference('default_language_filter'));
	$specialchar_languages = SpecialChars::allLanguages();
	if (!array_key_exists($language_filter, $specialchar_languages)) {
		$language_filter = 'en';
	}
	Auth::user()->setPreference('default_language_filter', $language_filter);
	$action = 'filter';
	echo '<div id="find-header">
	<form name="filterspecialchar" action="find.php">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="type" value="specialchar">
	<input type="hidden" name="callback" value="' . $callback . '">
	<p><select id="language_filter" name="language_filter" onchange="submit();">';
	foreach (SpecialChars::allLanguages() as $lanuguage_tag => $language_name) {
		echo '<option value="' . $lanuguage_tag . '" ';
		if ($lanuguage_tag === $language_filter) {
			echo 'selected';
		}
		echo '>', $language_name, '</option>';
	}
	echo '</select>
	</p></form></div>';
}

// Show facts
if ($type == 'factINDI' || $type == 'factFAM' || $type == 'factSOUR' || $type == 'factREPO' || $type == 'factNAME' || $type == 'factPLAC') {
	echo '<div id="find-facts-header">
	<form name="filterfacts" action="find.php"
	input type="hidden" name="type" value="facts">
	<input type="hidden" name="tags" value="', $qs, '">
	<input type="hidden" name="callback" value="', $callback, '">
	<table class="list_table width100" border="0">
	<tr><td class="list_label" style="padding: 5px; font-weight: normal; white-space: normal;">';

	$all           = strlen($qs) ? explode(',', strtoupper($qs)) : [];
	$preselDefault = [];
	$preselCustom  = [];
	foreach ($all as $one) {
		if (GedcomTag::isTag($one)) {
			$preselDefault[] = $one;
		} else {
			$preselCustom[] = $one;
		}
	}

	echo '<script>' ?>
	// A class representing a default tag
	function DefaultTag(id, name, selected) {
		this.Id=id;
		this.Name=name;
		this.LowerName=name.toLowerCase();
		this._counter=DefaultTag.prototype._newCounter++;
		this.selected=!!selected;
	}
	DefaultTag.prototype= {
		_newCounter:0
		,view:function() {
			var row=document.createElement("tr"),cell;
			row.appendChild(cell=document.createElement("td"));
			var o = document.createElement("input");
			o.id = "tag"+this._counter;
			o.type = "checkbox";
			o.checked = this.selected;
			o.DefaultTag=this;
			o.ParentRow=row;
			o.onclick=function() {
				this.DefaultTag.selected=!!this.checked;
				this.ParentRow.className=this.DefaultTag.selected?"sel":"unsel";
				Lister.recount();
			};
			cell.appendChild(o);
			row.appendChild(cell=document.createElement("th"));
			cell.appendChild(o=document.createElement("label"));
			o.htmlFor="tag"+this._counter;
			o.appendChild(document.createTextNode(this.Id));
			row.appendChild(cell=document.createElement("td"));
			cell.appendChild(document.createTextNode(this.Name));
			TheList.appendChild(row);
			row.className=this.selected?"sel":"unsel";
		}
	};
	// Some global variable
	var DefaultTags=null /*The list of the default tag*/, TheList=null /* The body of the table that will show the default tabs */;

	// A single-instance class that manage the populating of the table
	var Lister= {
		_curFilter:null
		,_timer:null
		,clear:function() {
			var n=TheList.childNodes.length;
			while (n) TheList.removeChild(TheList.childNodes[--n]);
		}
		,_clearTimer:function() {
			if (this._timer!=null) {
				clearTimeout(this._timer);
				this._timer=null;
			}
		}
		,askRefresh:function() {
			this._clearTimer();
			this._timer=setTimeout("Lister.refreshNow()",200);
		}
		,refreshNow:function(force) {
			this._clearTimer();
			var s=document.getElementById("tbxFilter").value.toLowerCase().replace(/\s+/g," ").replace(/^ | $/g,""),k;
			if (force||(typeof(this._curFilter)!="string")||(this._curFilter!=s)) {
				this._curFilter=s;
				this.clear();
				for (k=0;k<DefaultTags.length;k++) {
					if (DefaultTags[k].LowerName.indexOf(this._curFilter)>=0) DefaultTags[k].view();
				}
			}
		}
		,recount:function() {
			var k,n=0;
			for (k=0;k<DefaultTags.length;k++)
				if (DefaultTags[k].selected)
					n++;
			document.getElementById("layCurSelectedCount").innerHTML=n.toString();
		}
		,showSelected:function() {
			this._clearTimer();
			this.clear();
			for (var k=0;k<DefaultTags.length;k++) {
				if (DefaultTags[k].selected)
					DefaultTags[k].view();
			}
		}
	};

	function initPickFact(factType) {
		var n,i,j,tmp,preselectedDefaultTags="\x01<?php foreach ($preselDefault as $p) { echo addslashes($p), '\\x01'; } ?>";

		switch (factType) {
			case "INDI":
				DefaultTags=[<?php
				$firstFact = true;
				foreach (GedcomTag::getPicklistFacts('INDI') as $factId => $factName) {
					if ($firstFact) {
						$firstFact = false;
					} else {
						echo ',';
					}
					echo 'new DefaultTag("' . addslashes($factId) . '","' . addslashes($factName) . '",preselectedDefaultTags.indexOf("\\x01' . addslashes($factId) . '\\x01")>=0)';
				}
				?>];
				break;
			case "FAM":
				DefaultTags=[<?php
				$firstFact = true;
				foreach (GedcomTag::getPicklistFacts('FAM') as $factId => $factName) {
					if ($firstFact) {
						$firstFact = false;
					} else {
						echo ',';
					}
					echo 'new DefaultTag("' . addslashes($factId) . '","' . addslashes($factName) . '",preselectedDefaultTags.indexOf("\\x01' . addslashes($factId) . '\\x01")>=0)';
				}
				?>];
				break;
			case "SOUR":
				DefaultTags=[<?php
				$firstFact = true;
				foreach (GedcomTag::getPicklistFacts('SOUR') as $factId => $factName) {
					if ($firstFact) {
						$firstFact = false;
					} else {
						echo ',';
					}
					echo 'new DefaultTag("' . addslashes($factId) . '","' . addslashes($factName) . '",preselectedDefaultTags.indexOf("\\x01' . addslashes($factId) . '\\x01")>=0)';
				}
				?>];
				break;
			case "REPO":
				DefaultTags=[<?php
				$firstFact = true;
				foreach (GedcomTag::getPicklistFacts('REPO') as $factId => $factName) {
					if ($firstFact) {
						$firstFact = false;
					} else {
						echo ',';
					}
					echo 'new DefaultTag("' . addslashes($factId) . '","' . addslashes($factName) . '",preselectedDefaultTags.indexOf("\\x01' . addslashes($factId) . '\\x01")>=0)';
				}
				?>];
				break;
			case "PLAC":
				DefaultTags=[<?php
				$firstFact = true;
				foreach (GedcomTag::getPicklistFacts('PLAC') as $factId => $factName) {
					if ($firstFact) {
						$firstFact = false;
					} else {
						echo ',';
					}
					echo 'new DefaultTag("' . addslashes($factId) . '","' . addslashes($factName) . '",preselectedDefaultTags.indexOf("\\x01' . addslashes($factId) . '\\x01")>=0)';
				}
				?>];
				break;
			case "NAME":
				DefaultTags=[<?php
				$firstFact = true;
				foreach (GedcomTag::getPicklistFacts('NAME') as $factId => $factName) {
					if ($firstFact) {
						$firstFact = false;
					} else {
						echo ',';
					}
					echo 'new DefaultTag("' . addslashes($factId) . '","' . addslashes($factName) . '",preselectedDefaultTags.indexOf("\\x01' . addslashes($factId) . '\\x01")>=0)';
				}
				?>];
				break;
			default:
				DefaultTags=[];
				break;
		}
		TheList=document.getElementById("tbDefinedTags");
		i=document.getElementById("tbxFilter");
		i.onkeypress=i.onchange=i.onkeyup=function() {
			Lister.askRefresh();
		};
		Lister.recount();
		Lister.refreshNow();
		document.getElementById("btnOk").disabled=false;
	}
	function DoOK() {
		var result=[],k,linearResult,custom;
		for (k=0;k<DefaultTags.length;k++) {
			if (DefaultTags[k].selected) result.push(DefaultTags[k].Id);
		}
		linearResult="\x01"+result.join("\x01")+"\x01";
		custom=document.getElementById("tbxCustom").value.toUpperCase().replace(/\s/g,"").split(",");
		for (k=0;k<custom.length;k++) {
			if (linearResult.indexOf("\x01"+custom[k]+"\x01")<0) {
				linearResult+=custom[k]+"\x01";
				result.push(custom[k]);
			}
		}
		result = result.join(",")
		if (result.substring(result.length-1, result.length)==',') {
			result = result.substring(0, result.length-1);
		}
		pasteid(result);
		window.close();
		return false;
	}
	<?= '</script>';
	echo '<div id="layDefinedTags"><table id="tabDefinedTags">
		<thead><tr>
			<th></th>
			<th>', I18N::translate('Tag'), '</th>
			<th>', I18N::translate('Description'), '</th>
		</tr></thead>
		<tbody id="tbDefinedTags">
		</tbody>
	</table></div>

	<table id="tabDefinedTagsShow"><tbody><tr>
		<td><a href="#" onclick="Lister.showSelected();return false">', I18N::translate('Show only the selected tags'), ' (<span id="layCurSelectedCount"></span>)</a></td>
		<td><a href="#" onclick="Lister.refreshNow(true);return false">', I18N::translate('Show all tags'), '</a></td>
	</tr></tbody></table>

	<table id="tabFilterAndCustom"><tbody>
		<tr><td>', I18N::translate('Filter'), ':</td><td><input type="text" id="tbxFilter"></td></tr>
		<tr><td>', I18N::translate('Custom tags'), ':</td><td><input type="text" id="tbxCustom" value="', addslashes(implode(',', $preselCustom)), '"></td></tr>
	<td><td></tbody></table>

	<table id="tabAction"><tbody><tr>
		<td colspan="2"><button id="btnOk" disabled onclick="if (!this.disabled) { DoOK(); }">', I18N::translate('save'), '</button></td>
	<tr></tbody></table>
	</td></tr></table>
	</form></div>';
}

if ($action === 'filter') {
	$filter       = trim($filter);
	$filter_array = explode(' ', preg_replace('/ {2,}/', ' ', $filter));

	// Output Special Characters
	if ($type == 'specialchar') {
		echo '<div id="find-output-special"><p>';
		// lower case special characters
		foreach (SpecialChars::create($language_filter)->upper() as $special_character) {
			echo '<a class="largechars" href="#" onclick="return window.opener.paste_char(\'', $special_character, '\');">', $special_character, '</a> ';
		}
		echo '</p><p>';
		//upper case special characters
		foreach (SpecialChars::create($language_filter)->lower() as $special_character) {
			echo '<a class="largechars" href="#" onclick="return window.opener.paste_char(\'', $special_character, '\');">', $special_character, '</a> ';
		}
		echo '</p><p>';
		// other special characters (not letters)
		foreach (SpecialChars::create($language_filter)->other() as $special_character) {
			echo '<a class="largechars" href="#" onclick="return window.opener.paste_char(\'', $special_character, '\');">', $special_character, '</a> ';
		}
		echo '</p></div>';
	}
}
echo '<button onclick="window.close();">', I18N::translate('close'), '</button>';
echo '</div>';
