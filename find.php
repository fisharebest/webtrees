<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\SimpleController;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Query\QueryMedia;

define('WT_SCRIPT_NAME', 'find.php');
require './includes/session.php';

$controller = new SimpleController;

$type      = Filter::get('type');
$filter    = Filter::get('filter');
$action    = Filter::get('action');
$callback  = Filter::get('callback', '[a-zA-Z0-9_]+', 'paste_id');
$all       = Filter::getBool('all');
$subclick  = Filter::get('subclick');
$choose    = Filter::get('choose', '[a-zA-Z0-9_]+', '0all');
$qs        = Filter::get('tags');

if ($subclick === 'all') {
	$all = true;
}

$embed = substr($choose, 0, 1) === '1';

switch ($type) {
case 'indi':
	$controller->setPageTitle(I18N::translate('Find an individual'));
	break;
case 'fam':
	$controller->setPageTitle(I18N::translate('Find a family'));
	break;
case 'media':
	$controller->setPageTitle(I18N::translate('Find a media object'));
	break;
case 'place':
	$controller->setPageTitle(I18N::translate('Find a place'));
	break;
case 'repo':
	$controller->setPageTitle(I18N::translate('Find a repository'));
	break;
case 'note':
	$controller->setPageTitle(I18N::translate('Find a shared note'));
	break;
case 'source':
	$controller->setPageTitle(I18N::translate('Find a source'));
	break;
case 'specialchar':
	$controller->setPageTitle(I18N::translate('Find a special character'));
	break;
case 'facts':
	$controller
		->setPageTitle(I18N::translate('Find a fact or event'))
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
			alert("<?php echo I18N::translate('Please enter more than one character.'); ?>");
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

echo '<div id="find-page"><h3>', $controller->getPageTitle(), '</h3>';

// Show indi and hide the rest
if ($type == "indi") {
	echo '<div id="find-header">
	<form name="filterindi" method="get" onsubmit="return checknames(this);" action="find.php">
	<input type="hidden" name="callback" value="' . $callback . '">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="type" value="indi">
	<span>', /* I18N: Label for search field */ I18N::translate('Name contains'), '&nbsp;</span>
	<input type="text" name="filter" value="';
	if ($filter) {
		echo $filter;
	}
	echo '" autofocus>
	<input type="submit" value="', I18N::translate('Filter'), '">
	</form></div>';
}

// Show fam and hide the rest
if ($type == "fam") {
	echo '<div id="find-header">
	<form name="filterfam" method="get" onsubmit="return checknames(this);" action="find.php">
	<input type="hidden" name="callback" value="' . $callback . '">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="type" value="fam">
	<span>', I18N::translate('Name contains'), '&nbsp;</span>
	<input type="text" name="filter" value="';
	if ($filter) {
		echo $filter;
	}
	echo '" autofocus>
	<input type="submit" value="', I18N::translate('Filter'), '">
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
	<span>', /* I18N: Label for search field */ I18N::translate('Media contains'), '</span>
	<input type="text" name="filter" value="';
	if ($filter) {
		echo $filter;
	}
	echo '" autofocus>',
	'<p class="small text-muted">', I18N::translate('Simple search filter based on the characters entered, no wildcards are accepted.'), '</p>',
	'<p><input type="submit" name="search" value="', I18N::translate('Filter'), '" onclick="this.form.subclick.value=this.name">&nbsp;
	<input type="submit" name="all" value="', I18N::translate('Display all'), '" onclick="this.form.subclick.value=this.name">
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
	<span>', /* I18N: Label for search field */ I18N::translate('Place contains'), '</span>
	<input type="text" name="filter" value="';
	if ($filter) {
		echo $filter;
	}
	echo '" autofocus>
	<p><input type="submit" name="search" value="', I18N::translate('Filter'), '" onclick="this.form.subclick.value=this.name">&nbsp;
	<input type="submit" name="all" value="', I18N::translate('Display all'), '" onclick="this.form.subclick.value=this.name">
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
	<span>', /* I18N: Label for search field */ I18N::translate('Repository contains'), '</span>
	<input type="text" name="filter" value="';
	if ($filter) {
		echo $filter;
	}
	echo '" autofocus>
	<p><input type="submit" name="search" value="', I18N::translate('Filter'), '" onclick="this.form.subclick.value=this.name">&nbsp;
	<input type="submit" name="all" value="', I18N::translate('Display all'), '" onclick="this.form.subclick.value=this.name">
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
	<span>', /* I18N: Label for search field */ I18N::translate('Shared note contains'), '</span>
	<input type="text" name="filter" value="';
	if ($filter) {
		echo $filter;
	}
	echo '" autofocus>
	<p><input type="submit" name="search" value="', I18N::translate('Filter'), '" onclick="this.form.subclick.value=this.name">&nbsp;
	<input type="submit" name="all" value="', I18N::translate('Display all'), '" onclick="this.form.subclick.value=this.name">
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
	<span>', /* I18N: Label for search field */ I18N::translate('Source contains'), '</span>
	<input type="text" name="filter" value="';
	if ($filter) {
		echo $filter;
	}
	echo '" autofocus>
	<p><input type="submit" name="search" value="', I18N::translate('Filter'), '" onclick="this.form.subclick.value=this.name">&nbsp;
	<input type="submit" name="all" value="', I18N::translate('Display all'), '" onclick="this.form.subclick.value=this.name">
	</p></form></div>';
}

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
	<form name="filterspecialchar" method="get" action="find.php">
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
if ($type == "facts") {
	echo '<div id="find-facts-header">
	<form name="filterfacts" method="get" action="find.php"
	input type="hidden" name="type" value="facts">
	<input type="hidden" name="tags" value="', $qs, '">
	<input type="hidden" name="callback" value="', $callback, '">
	<table class="list_table width100" border="0">
	<tr><td class="list_label" style="padding: 5px; font-weight: normal; white-space: normal;">';

	$all           = strlen($qs) ? explode(',', strtoupper($qs)) : array();
	$preselDefault = array();
	$preselCustom  = array();
	foreach ($all as $one) {
		if (GedcomTag::isTag($one)) {
			$preselDefault[] = $one;
		} else {
			$preselCustom[] = $one;
		}
	}

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
		var n,i,j,tmp,preselectedDefaultTags="\x01<?php foreach ($preselDefault as $p) { echo addslashes($p), '\\x01'; } ?>";

		DefaultTags=[<?php
		$firstFact = true;
		foreach (GedcomTag::getPicklistFacts() as $factId => $factName) {
			if ($firstFact) {
				$firstFact = false;
			} else {
				echo ',';
			}
			echo 'new DefaultTag("' . addslashes($factId) . '","' . addslashes($factName) . '",preselectedDefaultTags.indexOf("\\x01' . addslashes($factId) . '\\x01")>=0)';
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
		<tr><td>', I18N::translate('Custom tags'), ':</td><td><input type="text" id="tbxCustom" value="', addslashes(implode(",", $preselCustom)), '"></td></tr>
	<td><td></tbody></table>

	<table id="tabAction"><tbody><tr>
		<td colspan="2"><button id="btnOk" disabled onclick="if (!this.disabled) { DoOK(); }">', I18N::translate('save'), '</button></td>
	<tr></tbody></table>
	</td></tr></table>
	</form></div>';
}

if ($action == "filter") {
	$filter       = trim($filter);
	$filter_array = explode(' ', preg_replace('/ {2,}/', ' ', $filter));

	// Output Individual
	if ($type == "indi") {
		echo '<div id="find-output">';
		$myindilist = FunctionsDb::searchIndividualNames($filter_array, array($WT_TREE));
		if ($myindilist) {
			echo '<ul>';
			usort($myindilist, '\Fisharebest\Webtrees\GedcomRecord::compare');
			foreach ($myindilist as $indi) {
				echo $indi->formatList('li', true);
			}
			echo '</ul>
			<p>', I18N::translate('Total individuals: %s', count($myindilist)), '</p>';
		} else {
			echo '<p>', I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Family
	if ($type == "fam") {
		echo '<div id="find-output">';
		// Get the famrecs with hits on names from the family table
		// Get the famrecs with hits in the gedcom record from the family table
		$myfamlist = array_unique(array_merge(
			FunctionsDb::searchFamilyNames($filter_array, array($WT_TREE)),
			FunctionsDb::searchFamilies($filter_array, array($WT_TREE))
		));

		if ($myfamlist) {
			echo '<ul>';
			usort($myfamlist, '\Fisharebest\Webtrees\GedcomRecord::compare');
			foreach ($myfamlist as $family) {
				echo $family->formatList('li', true);
			}
			echo '</ul>
			<p>', I18N::translate('Total families: %s', count($myfamlist)), '</p>';
		} else {
			echo '<p>', I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Media
	if ($type === 'media') {
		$medialist = QueryMedia::mediaList('', 'include', 'title', $filter, '');

		echo '<div id="find-output">';

		if ($medialist) {
			foreach ($medialist as $media) {
				echo '<div class="find-media-media">';
				echo '<div class="find-media-thumb">', $media->displayImage(), '</div>';
				echo '<div class="find-media-details">', $media->getFullName(), '</div>';
				if (!$embed) {
					echo '<p><a href="#" dir="auto" onclick="pasteid(\'', $media->getXref(), '\');">', $media->getFilename(), '</a></p>';
				} else {
					echo '<p><a href="#" dir="auto" onclick="pasteid(\'', $media->getXref(), '\', \'', '\', \'', Filter::escapeJs($media->getFilename()), '\');">', Filter::escapeHtml($media->getFilename()), '</a></p> ';
				}
				if ($media->fileExists()) {
					$imgsize = $media->getImageAttributes();
					echo GedcomTag::getLabelValue('__IMAGE_SIZE__', $imgsize['WxH']);
				}
				echo '<ul>';
				$found = false;
				foreach ($media->linkedIndividuals('OBJE') as $indindividual) {
					echo '<li>', $indindividual->getFullName(), '</li>';
					$found = true;
				}
				foreach ($media->linkedFamilies('OBJE') as $family) {
					echo '<li>', $family->getFullName(), '</li>';
					$found = true;
				}
				foreach ($media->linkedSources('OBJE') as $source) {
					echo '<li>', $source->getFullName(), '</li>';
					$found = true;
				}
				foreach ($media->linkedNotes('OBJE') as $note) {
					// Invalid GEDCOM - you cannot link a NOTE to an OBJE
					echo '<li>', $note->getFullName(), '</li>';
					$found = true;
				}
				foreach ($media->linkedRepositories('OBJE') as $repository) {
					// Invalid GEDCOM - you cannot link a REPO to an OBJE
					echo '<li>', $repository->getFullName(), '</li>';
					$found = true;
				}
				if (!$found) {
					echo '<li>', I18N::translate('This media object is not linked to any other record.'), '</li>';
				}
				echo '</ul>';
				echo '</div>'; // close div="find-media-media"
			}
		} else {
			echo '<p>', I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Places
	if ($type == "place") {
		echo '<div id="find-output">';
		if (!$filter || $all) {
			$places = Place::allPlaces($WT_TREE);
		} else {
			$places = Place::findPlaces($filter, $WT_TREE);
		}
		if ($places) {
			echo '<ul>';
			foreach ($places as $place) {
				echo '<li><a href="#" onclick="pasteid(\'', Filter::escapeJs($place->getGedcomName()), '\');">';
				if (!$filter || $all) {
					echo $place->getReverseName(); // When displaying all names, sort/display by the country, then region, etc.
				} else {
					echo $place->getFullName(); // When we’ve searched for a place, sort by this place
				}
				echo '</a></li>';
			}
			echo '</ul>
			<p>', I18N::translate('Places found'), '&nbsp;', count($places), '</p>';
		} else {
			echo '<p>', I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Repositories
	if ($type == "repo") {
		echo '<div id="find-output">';
		if ($filter) {
			$repo_list = FunctionsDb::searchRepositories($filter_array, array($WT_TREE));
		} else {
			$repo_list = FunctionsDb::getRepositoryList($WT_TREE);
		}
		if ($repo_list) {
			usort($repo_list, '\Fisharebest\Webtrees\GedcomRecord::compare');
			echo '<ul>';
			foreach ($repo_list as $repo) {
				echo '<li><a href="', $repo->getHtmlUrl(), '" onclick="pasteid(\'', $repo->getXref(), '\');"><span class="list_item">', $repo->getFullName(), '</span></a></li>';
			}
			echo '</ul>
			<p>', I18N::translate('Repositories found'), " ", count($repo_list), '</p>';
		} else {
			echo '<p>', I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Shared Notes
	if ($type == "note") {
		echo '<div id="find-output">';
		if ($filter) {
			$mynotelist = FunctionsDb::searchNotes($filter_array, array($WT_TREE));
		} else {
			$mynotelist = FunctionsDb::getNoteList($WT_TREE);
		}
		if ($mynotelist) {
			usort($mynotelist, '\Fisharebest\Webtrees\GedcomRecord::compare');
			echo '<ul>';
			foreach ($mynotelist as $note) {
				echo '<li><a href="', $note->getHtmlUrl(), '" onclick="pasteid(\'', $note->getXref(), '\');"><span class="list_item">', $note->getFullName(), '</span></a></li>';
			}
			echo '</ul>
			<p>', I18N::translate('Shared notes found'), ' ', count($mynotelist), '</p>';
		} else {
			echo '<p>', I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Sources
	if ($type == "source") {
		echo '<div id="find-output">';
		if ($filter) {
			$mysourcelist = FunctionsDb::searchSources($filter_array, array($WT_TREE));
		} else {
			$mysourcelist = FunctionsDb::getSourceList($WT_TREE);
		}
		if ($mysourcelist) {
			usort($mysourcelist, '\Fisharebest\Webtrees\GedcomRecord::compare');
			echo '<ul>';
			foreach ($mysourcelist as $source) {
				echo '<li><a href="', $source->getHtmlUrl(), '" onclick="pasteid(\'', $source->getXref(), '\', \'',
					Filter::escapeJs($source->getFullName()), '\');"><span class="list_item">',
					$source->getFullName(), '</span></a></li>';
			}
			echo '</ul>
			<p>', I18N::translate('Total sources: %s', count($mysourcelist)), '</p>';
		} else {
			echo '<p>', I18N::translate('No results found.'), '</p>';
		}
		echo '</div>';
	}

	// Output Special Characters
	if ($type == "specialchar") {
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
echo "</div>";
