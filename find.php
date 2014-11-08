<?php
// Popup window that will allow a user to search for a family id, person id
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

define('WT_SCRIPT_NAME', 'find.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller = new WT_Controller_Simple();

$type      = WT_Filter::get('type');
$filter    = WT_Filter::get('filter');
$action    = WT_Filter::get('action');
$callback  = WT_Filter::get('callback', '[a-zA-Z0-9_]+', 'paste_id');
$media     = WT_Filter::get('media');
$all       = WT_Filter::getBool('all');
$subclick  = WT_Filter::get('subclick');
$choose    = WT_Filter::get('choose', '[a-zA-Z0-9_]+', '0all');
$qs        = WT_Filter::get('tags');

// Retrives the currently selected tags in the opener window (reading curTags value of the query string)
// $preselDefault will be set to the array of DEFAULT preselected tags
// $preselCustom will be set to the array of CUSTOM preselected tags
function getPreselectedTags(&$preselDefault, &$preselCustom) {
	global $qs;
	$all = strlen($qs) ? explode(',', strtoupper($qs)) : array();
	$preselDefault = array();
	$preselCustom = array();
	foreach ($all as $one) {
		if (WT_Gedcom_Tag::isTag($one)) {
			$preselDefault[] = $one;
		} else {
			$preselCustom[] = $one;
		}
	}
}

if ($subclick=='all') {
	$all=true;
}

$embed = substr($choose, 0, 1)=="1";
$chooseType = substr($choose, 1);
if ($chooseType!="media" && $chooseType!="0file") {
	$chooseType = "all";
}

// End variables for find media

switch ($type) {
case "indi":
	$controller->setPageTitle(WT_I18N::translate('Find an individual'));
	break;
case "fam":
	$controller->setPageTitle(WT_I18N::translate('Find a family'));
	break;
case "media":
	$controller->setPageTitle(WT_I18N::translate('Find a media object'));
	break;
case "place":
	$controller->setPageTitle(WT_I18N::translate('Find a place'));
	break;
case "repo":
	$controller->setPageTitle(WT_I18N::translate('Find a repository'));
	break;
case "note":
	$controller->setPageTitle(WT_I18N::translate('Find a shared note'));
	break;
case "source":
	$controller->setPageTitle(WT_I18N::translate('Find a source'));
	break;
case "specialchar":
	$controller->setPageTitle(WT_I18N::translate('Find a special character'));
	$language_filter = WT_Filter::get('language_filter');
	// Users will probably always want the same language, so remember their setting
	if (!$language_filter) {
		$language_filter = Auth::user()->getPreference('default_language_filter');
	} else {
		Auth::user()->setPreference('default_language_filter', $language_filter);
	}
	require WT_ROOT.'includes/specialchars.php';
	$action="filter";
	break;
case "facts":
	$controller
		->setPageTitle(WT_I18N::translate('Find a fact or event'))
		->addInlineJavascript('initPickFact();');
	break;
}
$controller->pageHeader();

echo '<script>';
?>
	function pasteid(id, name, thumb) {
		if (thumb) {
			window.opener.<?php echo $callback; ?>(id, name, thumb);
			<?php echo "window.close();"; ?>
		} else {
			// GEDFact_assistant ========================
			if (window.opener.document.getElementById('addlinkQueue')) {
				window.opener.insertRowToTable(id, name);
				// Check if Indi, Fam or source ===================
				/*
				if (id.match("I")=="I") {
					var win01 = window.opener.window.open('edit_interface.php?action=addmedia_links&noteid=newnote&pid='+id, 'win01', edit_window_specs);
					if (window.focus) {win01.focus();}
				} else if (id.match("F")=="F") {
					// TODO --- alert('Opening Navigator with family id entered will come later');
				}
				*/
			}
			window.opener.<?php echo $callback; ?>(id);
			if (window.opener.pastename) window.opener.pastename(name);
			<?php echo "window.close();"; ?>
		}
	}
	function checknames(frm) {
		if (document.forms[0].subclick) button = document.forms[0].subclick.value;
		else button = "";
		if (frm.filter.value.length<2&button!="all") {
			alert("<?php echo WT_I18N::translate('Please enter more than one character'); ?>");
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

$options = array();
$options["option"][]= "findindi";
$options["option"][]= "findfam";
$options["option"][]= "findmedia";
$options["option"][]= "findplace";
$options["option"][]= "findrepo";
$options["option"][]= "findnote";
$options["option"][]= "findsource";
$options["option"][]= "findspecialchar";
$options["option"][]= "findfact";
$options["form"][]= "formindi";
$options["form"][]= "formfam";
$options["form"][]= "formmedia";
$options["form"][]= "formplace";
$options["form"][]= "formrepo";
$options["form"][]= "formnote";
$options["form"][]= "formsource";
$options["form"][]= "formspecialchar";

echo '<div id="find-page"><h3>', $controller->getPageTitle(), '</h3>';

// Show indi and hide the rest
if ($type == "indi") {
	echo '<div id="find-header">
	<form name="filterindi" method="get" onsubmit="return checknames(this);" action="find.php">
	<input type="hidden" name="callback" value="'.$callback.'">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="type" value="indi">
	<span>', WT_I18N::translate('Name contains:'), '&nbsp;</span>
	<input type="text" name="filter" value="';
	if ($filter) echo $filter;
	echo '" autofocus>
	<input type="submit" value="', WT_I18N::translate('Filter'), '">
	</form></div>';
}

// Show fam and hide the rest
if ($type == "fam") {
	echo '<div id="find-header">
	<form name="filterfam" method="get" onsubmit="return checknames(this);" action="find.php">
	<input type="hidden" name="callback" value="'.$callback.'">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="type" value="fam">
	<span>', WT_I18N::translate('Name contains:'), '&nbsp;</span>
	<input type="text" name="filter" value="';
	if ($filter) echo $filter;
	echo '" autofocus>
	<input type="submit" value="', WT_I18N::translate('Filter'), '">
	</form></div>';
}

// Show media and hide the rest
if ($type == 'media') {
	echo '<div id="find-header">
	<form name="filtermedia" method="get" onsubmit="return checknames(this);" action="find.php">
	<input type="hidden" name="choose" value="', $choose, '">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="type" value="media">
	<input type="hidden" name="callback" value="', $callback, '">
	<input type="hidden" name="subclick">
	<span>', WT_I18N::translate('Media contains:'), '</span>
	<input type="text" name="filter" value="';
	if ($filter) echo $filter;
	echo '" autofocus>',
	help_link('simple_filter'),
	'<p><input type="submit" name="search" value="', WT_I18N::translate('Filter'), '" onclick="this.form.subclick.value=this.name">&nbsp;
	<input type="submit" name="all" value="', WT_I18N::translate('Display all'), '" onclick="this.form.subclick.value=this.name">
	</p></form></div>';
}

// Show place and hide the rest
if ($type == "place") {
	echo '<div id="find-header">
	<form name="filterplace" method="get"  onsubmit="return checknames(this);" action="find.php">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="type" value="place">
	<input type="hidden" name="callback" value="', $callback, '">
	<input type="hidden" name="subclick">
	<span>', WT_I18N::translate('Place contains:'), '</span>
	<input type="text" name="filter" value="';
	if ($filter) echo $filter;
	echo '" autofocus>
	<p><input type="submit" name="search" value="', WT_I18N::translate('Filter'), '" onclick="this.form.subclick.value=this.name">&nbsp;
	<input type="submit" name="all" value="', WT_I18N::translate('Display all'), '" onclick="this.form.subclick.value=this.name">
	</p></form></div>';
}

// Show repo and hide the rest
if ($type == "repo") {
	echo '<div id="find-header">
	<form name="filterrepo" method="get" onsubmit="return checknames(this);" action="find.php">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="type" value="repo">
	<input type="hidden" name="callback" value="', $callback, '">
	<input type="hidden" name="subclick">
	<span>', WT_I18N::translate('Repository contains:'), '</span>
	<input type="text" name="filter" value="';
	if ($filter) echo $filter;
	echo '" autofocus>
	<p><input type="submit" name="search" value="', WT_I18N::translate('Filter'), '" onclick="this.form.subclick.value=this.name">&nbsp;
	<input type="submit" name="all" value="', WT_I18N::translate('Display all'), '" onclick="this.form.subclick.value=this.name">
	</td></tr></table>
	</p></form></div>';
}

// Show Shared Notes and hide the rest
if ($type == "note") {
	echo '<div id="find-header">
	<form name="filternote" method="get" onsubmit="return checknames(this);" action="find.php">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="type" value="note">
	<input type="hidden" name="callback" value="', $callback, '">
	<input type="hidden" name="subclick">
	<span>', WT_I18N::translate('Shared note contains:'), '</span>
	<input type="text" name="filter" value="';
	if ($filter) echo $filter;
	echo '" autofocus>
	<p><input type="submit" name="search" value="', WT_I18N::translate('Filter'), '" onclick="this.form.subclick.value=this.name">&nbsp;
	<input type="submit" name="all" value="', WT_I18N::translate('Display all'), '" onclick="this.form.subclick.value=this.name">
	</p></form></div>';
}

// Show source and hide the rest
if ($type == "source") {
	echo '<div id="find-header">
	<form name="filtersource" method="get" onsubmit="return checknames(this);" action="find.php">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="type" value="source">
	<input type="hidden" name="callback" value="', $callback, '">
	<input type="hidden" name="subclick">
	<span>', WT_I18N::translate('Source contains:'), '</span>
	<input type="text" name="filter" value="';
	if ($filter) echo $filter;
	echo '" autofocus>
	<p><input type="submit" name="search" value="', WT_I18N::translate('Filter'), '" onclick="this.form.subclick.value=this.name">&nbsp;
	<input type="submit" name="all" value="', WT_I18N::translate('Display all'), '" onclick="this.form.subclick.value=this.name">
	</p></form></div>';
}

// Show specialchar and hide the rest
if ($type == 'specialchar') {
	echo '<div id="find-header">
	<form name="filterspecialchar" method="get" action="find.php">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="type" value="specialchar">
	<input type="hidden" name="callback" value="'.$callback.'">
	<p><select id="language_filter" name="language_filter" onchange="submit();">
	<option value="">', WT_I18N::translate('Change language'), '</option>';
	$language_options = '';
	foreach ($specialchar_languages as $key=>$special_character) {
		$language_options.= '<option value="'.$key.'"';
		if ($key==$language_filter) {
			$language_options.=' selected="selected"';
		}
		$language_options.='>'.$special_character.'</option>';
	}
	echo $language_options,
	'</select>
	</p></form></div>';
}

// Show facts
if ($type == "facts") {
	echo '<div id="find-facts-header">
	<form name="filterfacts" method="get" action="find.php"
	input type="hidden" name="type" value="facts">
	<input type="hidden" name="tags" value="', $qs, '">
	<input type="hidden" name="callback" value="', $callback, '">
	<table class="list_table width100" border="0">
	<tr><td class="list_label" style="padding: 5px; font-weight: normal; white-space: normal;">' ;
	getPreselectedTags($preselDefault, $preselCustom);
	echo '<script>'; ?>
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

	function initPickFact() {
		var n,i,j,tmp,preselectedDefaultTags="\x01<?php foreach ($preselDefault as $p) echo addslashes($p), '\\x01'; ?>";

		DefaultTags=[<?php
		$firstFact=TRUE;
		foreach (WT_Gedcom_Tag::getPicklistFacts() as $factId => $factName) {
			if ($firstFact) $firstFact=FALSE;
			else echo ',';
			echo 'new DefaultTag("'.addslashes($factId).'","'.addslashes($factName).'",preselectedDefaultTags.indexOf("\\x01'.addslashes($factId).'\\x01")>=0)';
		}
		?>];
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
	<?php echo '</script>';
	echo '<div id="layDefinedTags"><table id="tabDefinedTags">
		<thead><tr>
			<th>&nbsp;</th>
			<th>', WT_I18N::translate('Tag'), '</th>
			<th>', WT_I18N::translate('Description'), '</th>
		</tr></thead>
		<tbody id="tbDefinedTags">
		</tbody>
	</table></div>

	<table id="tabDefinedTagsShow"><tbody><tr>
		<td><a href="#" onclick="Lister.showSelected();return false">', WT_I18N::translate('Show only the selected tags'), ' (<span id="layCurSelectedCount"></span>)</a></td>
		<td><a href="#" onclick="Lister.refreshNow(true);return false">', WT_I18N::translate('Show all tags'), '</a></td>
	</tr></tbody></table>

	<table id="tabFilterAndCustom"><tbody>
		<tr><td>', WT_I18N::translate('Filter'), ':</td><td><input type="text" id="tbxFilter"></td></tr>
		<tr><td>', WT_I18N::translate('Custom tags'), ':</td><td><input type="text" id="tbxCustom" value="', addslashes(implode(",", $preselCustom)), '"></td></tr>
	<td><td></tbody></table>

	<table id="tabAction"><tbody><tr>
		<td colspan="2"><button id="btnOk" disabled="disabled" onclick="if (!this.disabled) { DoOK(); }">', WT_I18N::translate('save'), '</button></td>
	<tr></tbody></table>
	</td></tr></table>
	</form></div>';
}

if ($action=="filter") {
	$filter = trim($filter);
	$filter_array=explode(' ', preg_replace('/ {2,}/', ' ', $filter));

	// Output Individual
	if ($type == "indi") {
		echo '<div id="find-output">';
		$myindilist=search_indis_names($filter_array, array(WT_GED_ID), 'AND');
		if ($myindilist) {
			echo '<ul>';
			usort($myindilist, array('WT_GedcomRecord', 'compare'));
			foreach ($myindilist as $indi) {
				echo $indi->format_list('li', true);
			}
			echo '</ul>
			<p>', WT_I18N::translate('Total individuals: %s', count($myindilist)), '</p>';
		} else {
			echo '<p>', WT_I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Family
	if ($type == "fam") {
		echo '<div id="find-output">';
		// Get the famrecs with hits on names from the family table
		// Get the famrecs with hits in the gedcom record from the family table
		$myfamlist = array_unique(array_merge(
			search_fams_names($filter_array, array(WT_GED_ID), 'AND'),
			search_fams($filter_array, array(WT_GED_ID), 'AND')
		));

		if ($myfamlist) {
			$curged = $GEDCOM;
			echo '<ul>';
			usort($myfamlist, array('WT_GedcomRecord', 'compare'));
			foreach ($myfamlist as $family) {
				echo $family->format_list('li', true);
			}
			echo '</ul>
			<p>', WT_I18N::translate('Total families: %s', count($myfamlist)), '</p>';
		} else {
			echo '<p>', WT_I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Media
	if ($type == "media") {
		global $dirs;

		$medialist = WT_Query_Media::mediaList('', 'include', 'title', $filter);

		echo '<div id="find-output">';

		if ($medialist) {
			foreach ($medialist as $media) {
				echo '<div class="find-media-media">';
				echo '<div class="find-media-thumb">', $media->displayImage(), '</div>';
				echo '<div class="find-media-details">', $media->getFullName(), '</div>';
				if (!$embed) {
					echo '<p><a href="#" dir="auto" onclick="pasteid(\'', $media->getXref(), '\');">', $media->getFilename(), '</a></p>';
				} else {
					echo '<p><a href="#" dir="auto" onclick="pasteid(\'', $media->getXref(), '\', \'', '\', \'', WT_Filter::escapeJs($media->getFilename()), '\');">', WT_Filter::escapeHtml($media->getFilename()), '</a></p> ';
				}
				if ($media->fileExists()) {
					$imgsize = $media->getImageAttributes();
					echo WT_Gedcom_Tag::getLabelValue('__IMAGE_SIZE__', $imgsize['WxH']);
				}
				echo '<ul>';
				$found=false;
				foreach ($media->linkedIndividuals('OBJE') as $indindividual) {
					echo '<li>', $indindividual->getFullName(), '</li>';
					$found=true;
				}
				foreach ($media->linkedFamilies('OBJE') as $family) {
					echo '<li>', $family->getFullName(), '</li>';
					$found=true;
				}
				foreach ($media->linkedSources('OBJE') as $source) {
					echo '<li>', $source->getFullName(), '</li>';
					$found=true;
				}
				foreach ($media->linkedNotes('OBJE') as $note) { // Invalid GEDCOM - you cannot link a NOTE to an OBJE
					echo '<li>', $note->getFullName(), '</li>';
					$found=true;
				}
				foreach ($media->linkedRepositories('OBJE') as $repository) { // Invalid GEDCOM - you cannot link a REPO to an OBJE
					echo '<li>', $repository->getFullName(), '</li>';
					$found=true;
				}
				if (!$found) {
					echo '<li>', WT_I18N::translate('This media object is not linked to any other record.'), '</li>';
				}
				echo '</ul>';
				echo '</div>'; // close div="find-media-media"
			}
		} else {
			echo '<p>', WT_I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Places
	if ($type == "place") {
		echo '<div id="find-output">';
		if (!$filter || $all) {
			$places=WT_Place::allPlaces(WT_GED_ID);
		} else {
			$places=WT_Place::findPlaces($filter, WT_GED_ID);
		}
		if ($places) {
			echo '<ul>';
			foreach ($places as $place) {
				echo '<li><a href="#" onclick="pasteid(\'', WT_Filter::escapeJs($place->getGedcomName()), '\');">';
				if (!$filter || $all) {
					echo $place->getReverseName(); // When displaying all names, sort/display by the country, then region, etc.
				} else {
					echo $place->getFullName(); // When we’ve searched for a place, sort by this place
				}
				echo '</a></li>';
			}
			echo '</ul>
			<p>', WT_I18N::translate('Places found'), '&nbsp;', count($places), '</p>';
		}
		else {
			echo '<p>', WT_I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Repositories
	if ($type == "repo") {
		echo '<div id="find-output">';
		if ($filter) {
			$repo_list = search_repos($filter_array, array(WT_GED_ID), 'AND');
		} else {
			$repo_list = get_repo_list(WT_GED_ID);
		}
		if ($repo_list) {
			usort($repo_list, array('WT_GedcomRecord', 'compare'));
			echo '<ul>';
			foreach ($repo_list as $repo) {
				echo '<li><a href="', $repo->getHtmlUrl(), '" onclick="pasteid(\'', $repo->getXref(), '\');"><span class="list_item">', $repo->getFullName(),'</span></a></li>';
			}
			echo '</ul>
			<p>', WT_I18N::translate('Repositories found'), " ", count($repo_list), '</p>';
		}
		else {
			echo '<p>', WT_I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Shared Notes
	if ($type=="note") {
		echo '<div id="find-output">';
		if ($filter) {
			$mynotelist = search_notes($filter_array, array(WT_GED_ID), 'AND');
		} else {
			$mynotelist = get_note_list(WT_GED_ID);
		}
		if ($mynotelist) {
			usort($mynotelist, array('WT_GedcomRecord', 'compare'));
			echo '<ul>';
			foreach ($mynotelist as $note) {
				echo '<li><a href="', $note->getHtmlUrl(), '" onclick="pasteid(\'', $note->getXref(), '\');"><span class="list_item">', $note->getFullName(),'</span></a></li>';
			}
			echo '</ul>
			<p>', WT_I18N::translate('Shared notes found'), ' ', count($mynotelist), '</p>';
		}
		else {
			echo '<p>', WT_I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Sources
	if ($type=="source") {
		echo '<div id="find-output">';
		if ($filter) {
			$mysourcelist = search_sources($filter_array, array(WT_GED_ID), 'AND');
		} else {
			$mysourcelist = get_source_list(WT_GED_ID);
		}
		if ($mysourcelist) {
			usort($mysourcelist, array('WT_GedcomRecord', 'compare'));
			echo '<ul>';
			foreach ($mysourcelist as $source) {
				echo '<li><a href="', $source->getHtmlUrl(), '" onclick="pasteid(\'', $source->getXref(), '\', \'',
					WT_Filter::escapeJs($source->getFullName()), '\');"><span class="list_item">',
					$source->getFullName(),'</span></a></li>';
			}
			echo '</ul>
			<p>', WT_I18N::translate('Total sources: %s', count($mysourcelist)), '</p>';
		}
		else {
			echo '<p>', WT_I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Special Characters
	if ($type == "specialchar") {
		echo '<div id="find-output-special"><p>';
		// lower case special characters
		foreach ($lcspecialchars as $special_character) {
			echo '<a class="largechars" href="#" onclick="return window.opener.paste_char(\'', $special_character, '\');">', $special_character, '</a> ';
		}
		echo '</p><p>';
		//upper case special characters
		foreach ($ucspecialchars as $special_character) {
			echo '<a class="largechars" href="#" onclick="return window.opener.paste_char(\'', $special_character, '\');">', $special_character, '</a> ';
		}
		echo '</p><p>';
		// other special characters (not letters)
		foreach ($otherspecialchars as $special_character) {
			echo '<a class="largechars" href="#" onclick="return window.opener.paste_char(\'', $special_character, '\');">', $special_character, '</a> ';
		}
		echo '</p></div>';
	}
}
echo '<button onclick="window.close();">', WT_I18N::translate('close'), '</button>';
echo "</div>";
