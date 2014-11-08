<?php
// Function for printing
//
// Various printing functions used by all scripts and included by the functions.php file.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
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

use Rhumsaa\Uuid\Uuid;
use WT\Auth;
use WT\User;

/**
 * print the information for an individual chart box
 *
 * find and print a given individuals information for a pedigree chart
 *
 * @param WT_Individual $person The person to print
 * @param integer       $style  the style to print the box in, 1 for smaller boxes, 2 for larger boxes
 */
function print_pedigree_person(WT_Individual $person = null, $style = 1) {
	global $GEDCOM;
	global $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS;
	global $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT;
	global $chart_style, $box_width, $generations, $show_spouse, $show_full;
	global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE, $PEDIGREE_SHOW_GENDER;
	global $SEARCH_SPIDER;

	if (empty($show_full)) {
		$show_full = 0;
	}

	if (empty($PEDIGREE_FULL_DETAILS)) {
		$PEDIGREE_FULL_DETAILS = 0;
	}

	if (!isset($OLD_PGENS)) {
		$OLD_PGENS = $DEFAULT_PEDIGREE_GENERATIONS;
	}
	if (!isset($talloffset)) {
		$talloffset = $PEDIGREE_LAYOUT;
	}

	// extend $style to incorporate compact view
	$style = $show_full ? $style == 2 ? 2 : 1 : 0;
	/* $style in conjunction with "box-style" and "detail" is used to create css classes
	 * e.g box-style1 and detail1
	 * 0: compact box   - used in charts (birth & death events hidden)
	 * 1: small box     - used in charts (birth & death events shown)
	 * 2: large box     - used elsewhere eg favourites block
	 */
	$dims = "style='width:{$bwidth}px; min-height:{$bheight}px'";

	// NOTE: Start div out-rand()
	if (!$person) {
		echo "<div $dims class=\"person_boxNN box-style$style\"></div>";
		return;
	}
	$pid = $person->getXref();
	$isF = array_search($person->getSex(), array('' => 'M', 'F' => 'F', 'NN' => 'U'));

	$personlinks   = '';
	$icons         = '';
	$genderImage   = '';
	$BirthDeath    = '';
	$LDSord        = '';
	$outBoxAdd     = "class='person_box_template person_box$isF box-style$style";
	if (!$show_full) {
		$outBoxAdd .= " iconz";
	}
	$outBoxAdd .= $style < 2 ? ("' " . $dims) : "'";

	if ($person->canShowName()) {
		if (empty($SEARCH_SPIDER)) {
			//-- draw a box for the family popup

			$personlinks .= '<ul class="person_box' . $isF . '">';
			$personlinks .= '<li><a href="pedigree.php?rootid=' . $pid . '&amp;show_full=' . $PEDIGREE_FULL_DETAILS . '&amp;PEDIGREE_GENERATIONS=' . $OLD_PGENS . '&amp;talloffset=' . $talloffset . '&amp;ged=' . rawurlencode($GEDCOM) . '"><strong>' . WT_I18N::translate('Pedigree') . '</strong></a></li>';
			if (array_key_exists('googlemap', WT_Module::getActiveModules())) {
				$personlinks .= '<li><a href="module.php?mod=googlemap&amp;mod_action=pedigree_map&amp;rootid=' . $pid . '&amp;ged=' . WT_GEDURL . '"><strong>' . WT_I18N::translate('Pedigree map') . '</strong></a></li>';
			}
			if (WT_USER_GEDCOM_ID && WT_USER_GEDCOM_ID != $pid) {
				$personlinks .= '<li><a href="relationship.php?show_full=' . $PEDIGREE_FULL_DETAILS . '&amp;pid1=' . WT_USER_GEDCOM_ID . '&amp;pid2=' . $pid . '&amp;show_full=' . $PEDIGREE_FULL_DETAILS . '&amp;pretty=2&amp;followspouse=1&amp;ged=' . WT_GEDURL . '"><strong>' . WT_I18N::translate('Relationship to me') . '</strong></a></li>';
			}
			$personlinks .= '<li><a href="descendancy.php?rootid=' . $pid . '&amp;show_full=' . $PEDIGREE_FULL_DETAILS . '&amp;generations=' . $generations . '&amp;box_width=' . $box_width . '&amp;ged=' . rawurlencode($GEDCOM) . '"><strong>' . WT_I18N::translate('Descendants') . '</strong></a></li>';
			$personlinks .= '<li><a href="ancestry.php?rootid=' . $pid . '&amp;show_full=' . $PEDIGREE_FULL_DETAILS . '&amp;chart_style=' . $chart_style . '&amp;PEDIGREE_GENERATIONS=' . $OLD_PGENS . '&amp;box_width=' . $box_width . '&amp;ged=' . rawurlencode($GEDCOM) . '"><strong>' . WT_I18N::translate('Ancestors') . '</strong></a></li>';
			$personlinks .= '<li><a href="compact.php?rootid=' . $pid . '&amp;ged=' . rawurlencode($GEDCOM) . '"><strong>' . WT_I18N::translate('Compact tree') . '</strong></a></li>';
			if (function_exists("imagettftext")) {
				$personlinks .= '<li><a href="fanchart.php?rootid=' . $pid . '&amp;PEDIGREE_GENERATIONS=' . $OLD_PGENS . '&amp;ged=' . rawurlencode($GEDCOM) . '"><strong>' . WT_I18N::translate('Fan chart') . '</strong></a></li>';
			}
			$personlinks .= '<li><a href="hourglass.php?rootid=' . $pid . '&amp;show_full=' . $PEDIGREE_FULL_DETAILS . '&amp;chart_style=' . $chart_style . '&amp;PEDIGREE_GENERATIONS=' . $OLD_PGENS . '&amp;box_width=' . $box_width . '&amp;ged=' . rawurlencode($GEDCOM) . '&amp;show_spouse=' . $show_spouse . '"><strong>' . WT_I18N::translate('Hourglass chart') . '</strong></a></li>';
			if (array_key_exists('tree', WT_Module::getActiveModules())) {
				$personlinks .= '<li><a href="module.php?mod=tree&amp;mod_action=treeview&amp;ged=' . WT_GEDURL . '&amp;rootid=' . $pid . '"><strong>' . WT_I18N::translate('Interactive tree') . '</strong></a></li>';
			}
			foreach ($person->getSpouseFamilies() as $family) {
				$spouse = $family->getSpouse($person);
				if ($spouse) {
					$personlinks .= '<li>';
					$personlinks .= '<a href="' . $family->getHtmlUrl() . '"><strong>' . WT_I18N::translate('Family with spouse') . '</strong></a><br>';
					$personlinks .= '<a href="' . $spouse->getHtmlUrl() . '">' . $spouse->getFullName() . '</a>';
					$personlinks .= '</li>';
					$personlinks .= '<li><ul>';
				}
				foreach ($family->getChildren() as $child) {
					$personlinks .= '<li><a href="' . $child->getHtmlUrl() . '">';
					$personlinks .= $child->getFullName();
					$personlinks .= '</a></li>';
				}
				$personlinks .= '</ul></li>';
			}
			$personlinks .= '</ul>';

			// NOTE: Zoom
			if ($show_full) {
				$icons .= '<span class="iconz icon-zoomin" title="' . WT_I18N::translate('Zoom in/out on this box.') . '"></span>';
				$icons .= '<div class="itr"><a href="#" class="icon-pedigree"></a><div class="popup">' . $personlinks . '</div></div>';
			}
		}
	}
	//-- find the name
	$name      = $person->getFullName();
	$shortname = $person->getShortName();
	$thumbnail = $SHOW_HIGHLIGHT_IMAGES ? $person->displayImage() : '';

	if ($PEDIGREE_SHOW_GENDER && $show_full) {
		$genderImage = $person->getSexImage('large');
	}

	//-- find additional name, e.g. Hebrew
	$addname = $person->getAddName();

	if ($SHOW_LDS_AT_GLANCE && $show_full) {
		$LDSord = get_lds_glance($person);
	}

	if ($show_full && $person->canShow()) {
		$opt_tags = preg_split('/\W/', $CHART_BOX_TAGS, 0, PREG_SPLIT_NO_EMPTY);
		// Show BIRT or equivalent event
		foreach (explode('|', WT_EVENTS_BIRT) as $birttag) {
			if (!in_array($birttag, $opt_tags)) {
				$event = $person->getFirstFact($birttag);
				if ($event) {
					$BirthDeath .= $event->summary();
					break;
				}
			}
		}
		// Show optional events (before death)
		foreach ($opt_tags as $key => $tag) {
			if (!preg_match('/^(' . WT_EVENTS_DEAT . ')$/', $tag)) {
				$event = $person->getFirstFact($tag);
				if (!is_null($event)) {
					$BirthDeath .= $event->summary();
					unset ($opt_tags[$key]);
				}
			}
		}
		// Show DEAT or equivalent event
		foreach (explode('|', WT_EVENTS_DEAT) as $deattag) {
			$event = $person->getFirstFact($deattag);
			if ($event) {
				$BirthDeath .= $event->summary();
				if (in_array($deattag, $opt_tags)) {
					unset ($opt_tags[array_search($deattag, $opt_tags)]);
				}
				break;
			}
		}
		// Show remaining optional events (after death)
		foreach ($opt_tags as $tag) {
			$event = $person->getFirstFact($tag);
			if ($event) {
				$BirthDeath .= $event->summary();
			}
		}
	}

	// Output to template
	if ($show_full) {
		require WT_THEME_DIR . 'templates/personbox_template.php';
	} else {
		require WT_THEME_DIR . 'templates/compactbox_template.php';
	}
}

/**
 * Print HTML header meta links
 *
 * @param string $META_DESCRIPTION
 * @param string $META_ROBOTS
 * @param string $META_GENERATOR
 * @param string $LINK_CANONICAL
 *
 * @return string
 */
function header_links($META_DESCRIPTION, $META_ROBOTS, $META_GENERATOR, $LINK_CANONICAL) {
	$header_links = '';
	if ($LINK_CANONICAL) {
		$header_links .= '<link rel="canonical" href="' . $LINK_CANONICAL . '">';
	}
	if ($META_DESCRIPTION) {
		$header_links .= '<meta name="description" content="' . $META_DESCRIPTION . '">';
	}
	$header_links .= '<meta name="robots" content="' . $META_ROBOTS . '">';
	if ($META_GENERATOR) {
		$header_links .= '<meta name="generator" content="' . $META_GENERATOR . '">';
	}
	return $header_links;
}

/**
 * Prints Exection Statistics
 *
 * prints out the execution time and the databse queries
 */
function execution_stats() {
	global $start_time;

	return
		'<div class="execution_stats">'.
		WT_I18N::translate(
			'Execution time: %1$s seconds.  Database queries: %2$s.  Memory usage: %3$s KB.',
			WT_I18N::number(microtime(true) - $start_time, 3),
			WT_I18N::number(WT_DB::getQueryCount()),
			WT_I18N::number(memory_get_peak_usage(true)/1024)
		).
		'</div>';
}

/**
 * Generate a login link.
 *
 * @return string
 */
function login_link() {
	global $SEARCH_SPIDER;

	if ($SEARCH_SPIDER) {
		return '';
	} else {
		return
			'<a href="' . WT_LOGIN_URL . '?url='.rawurlencode(get_query_url()) . '" class="link">' .
			WT_I18N::translate('Login') .
			'</a>';
	}
}

/**
 * Generate a logout link.
 *
 * @return string
 */
function logout_link() {
	global $SEARCH_SPIDER;

	if ($SEARCH_SPIDER) {
		return '';
	} else {
		return '<a href="logout.php" class="link">' . WT_I18N::translate('Logout') . '</a>';
	}
}

/**
 * Generate Who is online list.
 *
 * @return string
 */
function whoisonline() {
	$NumAnonymous = 0;
	$loggedusers = array ();
	$content='';
	foreach (User::allLoggedIn() as $user) {
		if (Auth::isAdmin() || $user->getPreference('visibleonline')) {
			$loggedusers[] = $user;
		} else {
			$NumAnonymous++;
		}
	}
	$LoginUsers=count($loggedusers);
	$content .= '<div class="logged_in_count">';
	if ($NumAnonymous) {
		$content .= WT_I18N::plural('%d anonymous logged-in user', '%d anonymous logged-in users', $NumAnonymous, $NumAnonymous);
		if ($LoginUsers) {
			$content .=  '&nbsp;|&nbsp;';
		}
	}
	if ($LoginUsers) {
		$content .= WT_I18N::plural('%d logged-in user', '%d logged-in users', $LoginUsers, $LoginUsers);
	}
	$content .= '</div>';
	$content .= '<div class="logged_in_list">';
	if (Auth::check()) {
		foreach ($loggedusers as $user) {
			$content .= '<div class="logged_in_name">';
			$content .= WT_Filter::escapeHtml($user->getRealName()) . ' - ' . WT_Filter::escapeHtml($user->getUserName());
			if (Auth::id() != $user->getUserId() && $user->getPreference('contactmethod') != 'none') {
				$content .= ' <a class="icon-email" href="#" onclick="return message(\'' . WT_Filter::escapeJs($user->getUserName()) . '\', \'\', \'' . WT_Filter::escapeJs(get_query_url()) . '\');" title="' . WT_I18N::translate('Send a message').'"></a>';
			}
			$content .= '</div>';
		}
	}
	$content .= '</div>';
	return $content;
}

/**
 * Print a link to allow email/messaging contact with a user
 * Optionally specify a method (used for webmaster/genealogy contacts)
 *
 * @param integer $user_id
 *
 * @return string
 */
function user_contact_link($user_id) {
	$user = User::find($user_id);

	if ($user) {
		$method = $user->getPreference('contactmethod');

		switch ($method) {
		case 'none':
			return '';
		case 'mailto':
			return '<a href="mailto:' . WT_Filter::escapeHtml($user->getEmail()).'">'.WT_Filter::escapeHtml($user->getRealName()).'</a>';
		default:
			return "<a href='#' onclick='message(\"" . WT_Filter::escapeJs($user->getUserName()) . "\", \"" . $method . "\", \"" . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_Filter::escapeJs(get_query_url()) . "\", \"\");return false;'>" . WT_Filter::escapeHtml($user->getRealName()) . '</a>';
		}
	} else {
		return '';
	}
}

/**
 * Print links for genealogy and technical contacts.
 * This function will print appropriate links based on the preferred
 * contact methods for the genealogy contact user and the technical
 * support contact user.
 *
 * @param integer $ged_id
 *
 * @return string
 */
function contact_links($ged_id=WT_GED_ID) {
	$tree = WT_Tree::get($ged_id);

	$contact_user_id   = $tree->getPreference('CONTACT_USER_ID');
	$webmaster_user_id = $tree->getPreference('WEBMASTER_USER_ID');
	$supportLink       = user_contact_link($webmaster_user_id);
	if ($webmaster_user_id==$contact_user_id) {
		$contactLink = $supportLink;
	} else {
		$contactLink = user_contact_link($contact_user_id);
	}

	if (!$supportLink && !$contactLink) {
		return '';
	}

	if ($supportLink==$contactLink) {
		return '<div class="contact_links">'.WT_I18N::translate('For technical support or genealogy questions, please contact').' '.$supportLink.'</div>';
	} else {
		$returnText = '<div class="contact_links">';
		if ($supportLink) {
			$returnText .= WT_I18N::translate('For technical support and information contact').' '.$supportLink;
			if ($contactLink) {
				$returnText .= '<br>';
			}
		}
		if ($contactLink) {
			$returnText .= WT_I18N::translate('For help with genealogy questions contact').' '.$contactLink;
		}
		$returnText .= '</div>';
		return $returnText;
	}
}

/**
 * print a note record
 *
 * @param string $text
 * @param integer $nlevel   the level of the note record
 * @param string  $nrec     the note record to print
 * @param boolean $textOnly Don't print the "Note: " introduction
 *
 * @return string
 */
function print_note_record($text, $nlevel, $nrec, $textOnly=false) {
	global $WT_TREE;

	$text .= get_cont($nlevel, $nrec);

	// Check if shared note (we have already checked that it exists)
	if (preg_match('/^0 @('.WT_REGEX_XREF.')@ NOTE/', $nrec, $match)) {
		$note  = WT_Note::getInstance($match[1]);
		$label = 'SHARED_NOTE';
		// If Census assistant installed, allow it to format the note
		if (array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
			$html = GEDFact_assistant_WT_Module::formatCensusNote($note);
		} else {
			$html = WT_Filter::formatText($note->getNote(), $WT_TREE);
		}
	} else {
		$note  = null;
		$label = 'NOTE';
		$html  = WT_Filter::formatText($text, $WT_TREE);
	}

	if ($textOnly) {
		return strip_tags($text);
	}

	if (strpos($text, "\n") === false) {
		// A one-line note? strip the block-level tags, so it displays inline
		return WT_Gedcom_Tag::getLabelValue($label, strip_tags($html, '<a><strong><em>'));
	} elseif ($WT_TREE->getPreference('EXPAND_NOTES')) {
		// A multi-line note, and we're expanding notes by default
		return WT_Gedcom_Tag::getLabelValue($label, $html);
	} else {
		// A multi-line note, with an expand/collapse option
		$element_id = Uuid::uuid4();
		// NOTE: class "note-details" is (currently) used only by some third-party themes
		if ($note) {
			$first_line = '<a href="' . $note->getHtmlUrl() . '">' . $note->getFullName() . '</a>';
		} else {
			switch ($WT_TREE->getPreference('FORMAT_TEXT')) {
				case 'markdown':
					$text = WT_Filter::markdown($text);
					$text = html_entity_decode(strip_tags($text, '<a><strong><em>'), ENT_QUOTES, 'UTF-8');
					break;
			}
			list($text) = explode("\n", $text);
			$first_line = strlen($text) > 100 ? mb_substr($text, 0, 100) . WT_I18N::translate('…') : $text;
		}
		return
			'<div class="fact_NOTE"><span class="label">' .
			'<a href="#" onclick="expand_layer(\'' . $element_id . '\'); return false;"><i id="' . $element_id . '_img" class="icon-plus"></i></a> ' . WT_Gedcom_Tag::getLabel($label) . ':</span> ' . '<span id="' . $element_id . '-alt">' . $first_line . '</span>' .
			'</div>' .
			'<div class="note-details" id="' . $element_id . '" style="display:none">' . $html . '</div>';
	}
}

/**
 * Print all of the notes in this fact record
 *
 * @param string  $factrec  the factrecord to print the notes from
 * @param integer $level    The level of the factrecord
 * @param boolean $textOnly Don't print the "Note: " introduction
 *
 * @return string HTML
 */
function print_fact_notes($factrec, $level, $textOnly=false) {
	$data = "";
	$previous_spos = 0;
	$nlevel = $level+1;
	$ct = preg_match_all("/$level NOTE (.*)/", $factrec, $match, PREG_SET_ORDER);
	for ($j=0; $j<$ct; $j++) {
		$spos1 = strpos($factrec, $match[$j][0], $previous_spos);
		$spos2 = strpos($factrec."\n$level", "\n$level", $spos1+1);
		if (!$spos2) $spos2 = strlen($factrec);
		$previous_spos = $spos2;
		$nrec = substr($factrec, $spos1, $spos2-$spos1);
		if (!isset($match[$j][1])) $match[$j][1]="";
		if (!preg_match("/@(.*)@/", $match[$j][1], $nmatch)) {
			$data .= print_note_record($match[$j][1], $nlevel, $nrec, $textOnly);
		} else {
			$note = WT_Note::getInstance($nmatch[1]);
			if ($note) {
				if ($note->canShow()) {
					$noterec = $note->getGedcom();
					$nt = preg_match("/0 @$nmatch[1]@ NOTE (.*)/", $noterec, $n1match);
					$data .= print_note_record(($nt>0)?$n1match[1]:"", 1, $noterec, $textOnly);
					if (!$textOnly) {
						if (strpos($noterec, "1 SOUR")!==false) {
							require_once WT_ROOT.'includes/functions/functions_print_facts.php';
							$data .= print_fact_sources($noterec, 1);
						}
					}
				}
			} else {
				$data='<div class="fact_NOTE"><span class="label">'.WT_I18N::translate('Note').'</span>: <span class="field error">'.$nmatch[1].'</span></div>';
			}
		}
		if (!$textOnly) {
			if (strpos($factrec, "$nlevel SOUR")!==false) {
				$data .= "<div class=\"indent\">";
				$data .= print_fact_sources($nrec, $nlevel);
				$data .= "</div>";
			}
		}
	}
	return $data;
}

/**
 * Print a link for a popup help window.
 *
 * @param string $help_topic
 * @param string $module
 *
 * @return string
 */
function help_link($help_topic, $module='') {
	return '<span class="icon-help" onclick="helpDialog(\''.$help_topic.'\',\''.$module.'\'); return false;">&nbsp;</span>';
}

/**
 * Print an external help link to the wiki site, in a new window
 *
 * @param string $topic
 *
 * @return string
 */
function wiki_help_link($topic) {
	return '<a class="help icon-wiki" href="'.WT_WEBTREES_WIKI.$topic.'" title="'.WT_I18N::translate('webtrees wiki').'" target="_blank">&nbsp;</a>';
}

/**
 * When a user has searched for text, highlight any matches in
 * the displayed string.
 *
 * @param string $string
 *
 * @return string
 */
function highlight_search_hits($string) {
	global $controller;

	if ($controller instanceof WT_Controller_Search && $controller->query) {
		// TODO: when a search contains multiple words, we search independently.
		// e.g. searching for "FOO BAR" will find records containing both FOO and BAR.
		// However, we only highlight the original search string, not the search terms.
		// The controller needs to provide its "query_terms" array.
		$regex=array();
		foreach (array($controller->query) as $search_term) {
			$regex[]=preg_quote($search_term, '/');
		}
		// Match these strings, provided they do not occur inside HTML tags
		$regex='('.implode('|', $regex).')(?![^<]*>)';

		return preg_replace('/'.$regex.'/i', '<span class="search_hit">$1</span>', $string);
	} else {
		return $string;
	}
}

/**
 * Print the associations from the associated individuals in $event to the individuals in $record
 *
 * @param WT_Fact $event
 *
 * @return string
 */
function format_asso_rela_record(WT_Fact $event) {
	global $SEARCH_SPIDER;

	$parent = $event->getParent();
	// To whom is this record an assocate?
	if ($parent instanceof WT_Individual) {
		// On an individual page, we just show links to the person
		$associates = array($parent);
	} elseif ($parent instanceof WT_Family) {
		// On a family page, we show links to both spouses
		$associates = $parent->getSpouses();
	} else {
		// On other pages, it does not make sense to show associates
		return '';
	}

	preg_match_all('/^1 ASSO @('.WT_REGEX_XREF.')@((\n[2-9].*)*)/', $event->getGedcom(), $amatches1, PREG_SET_ORDER);
	preg_match_all('/\n2 _?ASSO @('.WT_REGEX_XREF.')@((\n[3-9].*)*)/', $event->getGedcom(), $amatches2, PREG_SET_ORDER);

	$html = '';
	// For each ASSO record
	foreach (array_merge($amatches1, $amatches2) as $amatch) {
		$person = WT_Individual::getInstance($amatch[1]);
		if ($person) {
			// Is there a "RELA" tag
			if (preg_match('/\n[23] RELA (.+)/', $amatch[2], $rmatch)) {
				// Use the supplied relationship as a label
				$label = WT_Gedcom_Code_Rela::getValue($rmatch[1], $person);
			} else {
				// Use a default label
				$label = WT_Gedcom_Tag::getLabel('ASSO', $person);
			}

			$values = array('<a href="' . $person->getHtmlUrl() . '">' . $person->getFullName() . '</a>');
			if (!$SEARCH_SPIDER) {
				foreach ($associates as $associate) {
					$relationship_name = get_associate_relationship_name($associate, $person);
					if (!$relationship_name) {
						$relationship_name = WT_Gedcom_Tag::getLabel('RELA');
					}

					if ($parent instanceof WT_Family) {
						// For family ASSO records (e.g. MARR), identify the spouse with a sex icon
						$relationship_name .= $associate->getSexImage();
					}

					$values[] = '<a href="relationship.php?pid1=' . $associate->getXref() . '&amp;pid2=' . $person->getXref() . '&amp;ged=' . WT_GEDURL . '">' . $relationship_name . '</a>';
				}
			}
			$value = implode(' — ', $values);

			// Use same markup as WT_Gedcom_Tag::getLabelValue()
			$asso = WT_I18N::translate('<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>', $label, $value);
		} else {
			$asso = WT_Gedcom_Tag::getLabelValue('ASSO', '<span class="error">' . $amatch[1] . '</span>');
		}
		$html .= '<div class="fact_ASSO">' . $asso . '</div>';
	}
	return $html;
}

/**
 * Format age of parents in HTML
 *
 * @param WT_Individual $person child
 * @param WT_Date       $birth_date
 *
 * @return string HTML
 */
function format_parents_age(WT_Individual $person, WT_Date $birth_date) {
	$html='';
	$families=$person->getChildFamilies();
	// Multiple sets of parents (e.g. adoption) cause complications, so ignore.
	if ($birth_date->isOK() && count($families)==1) {
		$family=current($families);
		foreach ($family->getSpouses() as $parent) {
			if ($parent->getBirthDate()->isOK()) {
				$sex=$parent->getSexImage();
				$age=WT_Date::getAge($parent->getBirthDate(), $birth_date, 2);
				$deatdate=$parent->getDeathDate();
				switch ($parent->getSex()) {
				case 'F':
					// Highlight mothers who die in childbirth or shortly afterwards
					if ($deatdate->isOK() && $deatdate->MinJD()<$birth_date->MinJD()+90) {
						$html.=' <span title="'.WT_Gedcom_Tag::getLabel('_DEAT_PARE', $parent).'" class="parentdeath">'.$sex.$age.'</span>';
					} else {
						$html.=' <span title="'.WT_I18N::translate('Mother’s age').'">'.$sex.$age.'</span>';
					}
					break;
				case 'M':
					// Highlight fathers who die before the birth
					if ($deatdate->isOK() && $deatdate->MinJD()<$birth_date->MinJD()) {
						$html.=' <span title="'.WT_Gedcom_Tag::getLabel('_DEAT_PARE', $parent).'" class="parentdeath">'.$sex.$age.'</span>';
					} else {
						$html.=' <span title="'.WT_I18N::translate('Father’s age').'">'.$sex.$age.'</span>';
					}
					break;
				default:
					$html.=' <span title="'.WT_I18N::translate('Parent’s age').'">'.$sex.$age.'</span>';
					break;
				}
			}
		}
		if ($html) {
			$html='<span class="age">'.$html.'</span>';
		}
	}
	return $html;
}

/**
 * Print fact DATE/TIME
 *
 * @param WT_Fact         $event  event containing the date/age
 * @param WT_GedcomRecord $record the person (or couple) whose ages should be printed
 * @param boolean         $anchor option to print a link to calendar
 * @param boolean         $time   option to print TIME value
 *
 * @return string
 */
function format_fact_date(WT_Fact $event, WT_GedcomRecord $record, $anchor=false, $time=false) {
	global $pid, $SEARCH_SPIDER, $SHOW_PARENTS_AGE;

	$factrec = $event->getGedcom();
	$html='';
	// Recorded age
	if (preg_match('/\n2 AGE (.+)/', $factrec, $match)) {
		$fact_age = $match[1];
	} else {
		$fact_age = '';
	}
	if (preg_match('/\n2 HUSB\n3 AGE (.+)/', $factrec, $match)) {
		$husb_age = $match[1];
	} else {
		$husb_age = '';
	}
	if (preg_match('/\n2 WIFE\n3 AGE (.+)/', $factrec, $match)) {
		$wife_age = $match[1];
	} else {
		$wife_age = '';
	}

	// Calculated age
	if (preg_match('/2 DATE (.+)/', $factrec, $match)) {
		$date = new WT_Date($match[1]);
		$html .= ' ' . $date->display($anchor && !$SEARCH_SPIDER);
		// time
		if ($time) {
			$timerec=get_sub_record(2, '2 TIME', $factrec);
			if ($timerec=='') {
				$timerec=get_sub_record(2, '2 DATE', $factrec);
			}
			if (preg_match('/[2-3] TIME (.*)/', $timerec, $tmatch)) {
				$html.='<span class="date"> - '.$tmatch[1].'</span>';
			}
		}
		$fact = $event->getTag();
		if ($record instanceof WT_Individual) {
			// age of parents at child birth
			if ($fact=='BIRT' && $SHOW_PARENTS_AGE) {
				$html .= format_parents_age($record, $date);
			}
			// age at event
			else if ($fact!='CHAN' && $fact!='_TODO') {
				$birth_date=$record->getBirthDate();
				// Can't use getDeathDate(), as this also gives BURI/CREM events, which
				// wouldn't give the correct "days after death" result for people with
				// no DEAT.
				$death_event=$record->getFirstFact('DEAT');
				if ($death_event) {
					$death_date=$death_event->getDate();
				} else {
					$death_date=new WT_Date('');
				}
				$ageText = '';
				if ((WT_Date::Compare($date, $death_date)<=0 || !$record->isDead()) || $fact=='DEAT') {
					// Before death, print age
					$age=WT_Date::GetAgeGedcom($birth_date, $date);
					// Only show calculated age if it differs from recorded age
					if ($age!='') {
						if (
							$fact_age!='' && $fact_age!=$age ||
							$fact_age=='' && $husb_age=='' && $wife_age=='' ||
							$husb_age!='' && $record->getSex()=='M' && $husb_age!=$age ||
							$wife_age!='' && $record->getSex()=='F' && $wife_age!=$age
						) {
							if ($age!="0d") {
								$ageText = '('.WT_I18N::translate('Age').' '.get_age_at_event($age, false).')';
							}
						}
					}
				}
				if ($fact!='DEAT' && WT_Date::Compare($date, $death_date)>=0) {
					// After death, print time since death
					$age=get_age_at_event(WT_Date::GetAgeGedcom($death_date, $date), true);
					if ($age!='') {
						if (WT_Date::GetAgeGedcom($death_date, $date)=="0d") {
							$ageText = '('.WT_I18N::translate('on the date of death').')';
						} else {
							$ageText = '('.$age.' '.WT_I18N::translate('after death').')';
							// Family events which occur after death are probably errors
							if ($event->getParent() instanceof WT_Family) {
								$ageText.='<i class="icon-warning"></i>';
							}
						}
					}
				}
				if ($ageText) $html .= ' <span class="age">'.$ageText.'</span>';
			}
		} elseif ($record instanceof WT_Family) {
			$indi = WT_Individual::getInstance($pid);
			if ($indi) {
				$birth_date=$indi->getBirthDate();
				$death_date=$indi->getDeathDate();
				$ageText = '';
				if (WT_Date::Compare($date, $death_date)<=0) {
					$age=WT_Date::GetAgeGedcom($birth_date, $date);
					// Only show calculated age if it differs from recorded age
					if ($age!='' && $age>0) {
						if (
							$fact_age!='' && $fact_age!=$age ||
							$fact_age=='' && $husb_age=='' && $wife_age=='' ||
							$husb_age!='' && $indi->getSex()=='M' && $husb_age!= $age ||
							$wife_age!='' && $indi->getSex()=='F' && $wife_age!=$age
						) {
							$ageText = '('.WT_I18N::translate('Age').' '.get_age_at_event($age, false).')';
						}
					}
				}
				if ($ageText) $html .= ' <span class="age">'.$ageText.'</span>';
			}
		}
	} else {
		// 1 DEAT Y with no DATE => print YES
		// 1 BIRT 2 SOUR @S1@ => print YES
		// 1 DEAT N is not allowed
		// It is not proper GEDCOM form to use a N(o) value with an event tag to infer that it did not happen.
		$factdetail = explode(' ', trim($factrec));
		if (isset($factdetail) && (count($factdetail) == 3 && strtoupper($factdetail[2]) == 'Y') || (count($factdetail) == 4 && $factdetail[2] == 'SOUR')) {
			$html.=WT_I18N::translate('yes');
		}
	}
	// print gedcom ages
	foreach (array(WT_Gedcom_Tag::getLabel('AGE')=>$fact_age, WT_Gedcom_Tag::getLabel('HUSB')=>$husb_age, WT_Gedcom_Tag::getLabel('WIFE')=>$wife_age) as $label=>$age) {
		if ($age!='') {
			$html.=' <span class="label">'.$label.':</span> <span class="age">'.get_age_at_event($age, false).'</span>';
		}
	}
	return $html;
}

/**
 * print fact PLACe TEMPle STATus
 *
 * @param WT_Fact $event       gedcom fact record
 * @param boolean $anchor      to print a link to placelist
 * @param boolean $sub_records to print place subrecords
 * @param boolean $lds         to print LDS TEMPle and STATus
 *
 * @return string HTML
 */
function format_fact_place(WT_Fact $event, $anchor=false, $sub_records=false, $lds=false) {
	global $SEARCH_SPIDER;

	if ($anchor) {
		// Show the full place name, for facts/events tab
		if ($SEARCH_SPIDER) {
			$html = $event->getPlace()->getFullName();
		} else {
			$html = '<a href="' . $event->getPlace()->getURL() . '">' . $event->getPlace()->getFullName() . '</a>';
		}
	} else {
		// Abbreviate the place name, for chart boxes
		return ' - ' . $event->getPlace()->getShortName();
	}

	if ($sub_records) {
		$placerec = get_sub_record(2, '2 PLAC', $event->getGedcom());
		if (!empty($placerec)) {
			if (preg_match_all('/\n3 (?:_HEB|ROMN) (.+)/', $placerec, $matches)) {
				foreach ($matches[1] as $match) {
					$wt_place=new WT_Place($match, WT_GED_ID);
					$html.=' - ' . $wt_place->getFullName();
				}
			}
			$map_lati="";
			$cts = preg_match('/\d LATI (.*)/', $placerec, $match);
			if ($cts>0) {
				$map_lati=$match[1];
				$html .= '<br><span class="label">' . WT_Gedcom_Tag::getLabel('LATI') . ': </span>' . $map_lati;
			}
			$map_long = '';
			$cts = preg_match('/\d LONG (.*)/', $placerec, $match);
			if ($cts > 0) {
				$map_long = $match[1];
				$html .= ' <span class="label">' . WT_Gedcom_Tag::getLabel('LONG') . ': </span>' . $map_long;
			}
			if ($map_lati && $map_long) {
				$map_lati = trim(strtr($map_lati, "NSEW,�", " - -. ")); // S5,6789 ==> -5.6789
				$map_long = trim(strtr($map_long, "NSEW,�", " - -. ")); // E3.456� ==> 3.456
				$html .= ' <a rel="nofollow" href="https://maps.google.com/maps?q=' . $map_lati . ',' . $map_long . '" class="icon-googlemaps" title="' . WT_I18N::translate('Google Maps™') . '"></a>';
				$html .= ' <a rel="nofollow" href="https://www.bing.com/maps/?lvl=15&cp=' . $map_lati . '~' . $map_long . '" class="icon-bing" title="' . WT_I18N::translate('Bing Maps™') . '"></a>';
				$html .= ' <a rel="nofollow" href="https://www.openstreetmap.org/#map=15/' . $map_lati . '/' . $map_long . '" class="icon-osm" title="' . WT_I18N::translate('OpenStreetMap™') . '"></a>';
			}
			if (preg_match('/\d NOTE (.*)/', $placerec, $match)) {
				$html .= '<br>' . print_fact_notes($placerec, 3);
			}
		}
	}
	if ($lds) {
		if (preg_match('/2 TEMP (.*)/', $event->getGedcom(), $match)) {
			$html.='<br>'.WT_I18N::translate('LDS temple').': '.WT_Gedcom_Code_Temp::templeName($match[1]);
		}
		if (preg_match('/2 STAT (.*)/', $event->getGedcom(), $match)) {
			$html.='<br>'.WT_I18N::translate('Status').': '.WT_Gedcom_Code_Stat::statusName($match[1]);
			if (preg_match('/3 DATE (.*)/', $event->getGedcom(), $match)) {
				$date=new WT_Date($match[1]);
				$html.=', '.WT_Gedcom_Tag::getLabel('STAT:DATE').': '.$date->display();
			}
		}
	}
	return $html;
}

/**
 * Check for facts that may exist only once for a certain record type.
 * If the fact already exists in the second array, delete it from the first one.
 *
 * @param string[]  $uniquefacts
 * @param WT_Fact[] $recfacts
 * @param string    $type
 *
 * @return string[]
 */
function CheckFactUnique($uniquefacts, $recfacts, $type) {
	foreach ($recfacts as $factarray) {
		$fact=false;
		if (is_object($factarray)) {
			/* @var $factarray Event */
			$fact = $factarray->getTag();
		}
		else {
			if (($type == "SOUR") || ($type == "REPO")) $factrec = $factarray[0];
			if (($type == "FAM") || ($type == "INDI")) $factrec = $factarray[1];

		$ft = preg_match("/1 (\w+)(.*)/", $factrec, $match);
		if ($ft>0) {
			$fact = trim($match[1]);
			}
		}
		if ($fact!==false) {
			$key = array_search($fact, $uniquefacts);
			if ($key !== false) unset($uniquefacts[$key]);
		}
	}
	return $uniquefacts;
}

/**
 * Print a new fact box on details pages
 *
 * @param string $id        the id of the person, family, source etc the fact will be added to
 * @param array  $usedfacts an array of facts already used in this record
 * @param string $type      the type of record INDI, FAM, SOUR etc
 */
function print_add_new_fact($id, $usedfacts, $type) {
	global $WT_SESSION, $WT_TREE;

	// -- Add from clipboard
	if ($WT_SESSION->clipboard) {
		$newRow = true;
		foreach (array_reverse($WT_SESSION->clipboard, true) as $fact_id=>$fact) {
			if ($fact["type"]==$type || $fact["type"]=='all') {
				if ($newRow) {
					$newRow = false;
					echo '<tr><td class="descriptionbox">';
					echo WT_I18N::translate('Add from clipboard'), '</td>';
					echo '<td class="optionbox wrap"><form method="get" name="newFromClipboard" action="?" onsubmit="return false;">';
					echo '<select id="newClipboardFact">';
				}
				echo '<option value="', WT_Filter::escapeHtml($fact_id), '">', WT_Gedcom_Tag::getLabel($fact['fact']);
				// TODO use the event class to store/parse the clipboard events
				if (preg_match('/^2 DATE (.+)/m', $fact['factrec'], $match)) {
					$tmp=new WT_Date($match[1]);
					echo '; ', $tmp->minDate()->format('%Y');
				}
				if (preg_match('/^2 PLAC ([^,\n]+)/m', $fact['factrec'], $match)) {
					echo '; ', $match[1];
				}
				echo '</option>';
			}
		}
		if (!$newRow) {
			echo '</select>';
			echo '&nbsp;&nbsp;<input type="button" value="', WT_I18N::translate('Add'), "\" onclick=\"return paste_fact('$id', '#newClipboardFact');\"> ";
			echo '</form></td></tr>', "\n";
		}
	}

	// -- Add from pick list
	switch ($type) {
	case "INDI":
		$addfacts   =preg_split("/[, ;:]+/", $WT_TREE->getPreference('INDI_FACTS_ADD'),    -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", $WT_TREE->getPreference('INDI_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", $WT_TREE->getPreference('INDI_FACTS_QUICK'),  -1, PREG_SPLIT_NO_EMPTY);
		break;
	case "FAM":
		$addfacts   =preg_split("/[, ;:]+/", $WT_TREE->getPreference('FAM_FACTS_ADD'),     -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", $WT_TREE->getPreference('FAM_FACTS_UNIQUE'),  -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", $WT_TREE->getPreference('FAM_FACTS_QUICK'),   -1, PREG_SPLIT_NO_EMPTY);
		break;
	case "SOUR":
		$addfacts   =preg_split("/[, ;:]+/", $WT_TREE->getPreference('SOUR_FACTS_ADD'),    -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", $WT_TREE->getPreference('SOUR_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", $WT_TREE->getPreference('SOUR_FACTS_QUICK'),  -1, PREG_SPLIT_NO_EMPTY);
		break;
	case "NOTE":
		$addfacts   =preg_split("/[, ;:]+/", $WT_TREE->getPreference('NOTE_FACTS_ADD'),    -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", $WT_TREE->getPreference('NOTE_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", $WT_TREE->getPreference('NOTE_FACTS_QUICK'),  -1, PREG_SPLIT_NO_EMPTY);
		break;
	case "REPO":
		$addfacts   =preg_split("/[, ;:]+/", $WT_TREE->getPreference('REPO_FACTS_ADD'),    -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", $WT_TREE->getPreference('REPO_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", $WT_TREE->getPreference('REPO_FACTS_QUICK'),  -1, PREG_SPLIT_NO_EMPTY);
		break;
	default:
		return;
	}
	$addfacts=array_merge(CheckFactUnique($uniquefacts, $usedfacts, $type), $addfacts);
	$quickfacts=array_intersect($quickfacts, $addfacts);
	$translated_addfacts=array();
	foreach ($addfacts as $addfact) {
		$translated_addfacts[$addfact] = WT_Gedcom_Tag::getLabel($addfact);
	}
	uasort($translated_addfacts, function ($x, $y) {
		return WT_I18N::strcasecmp(WT_I18N::translate($x), WT_I18N::translate($y));
	});
	echo '<tr><td class="descriptionbox">';
	echo WT_I18N::translate('Fact or event');
	echo help_link('add_facts'), '</td>';
	echo '<td class="optionbox wrap">';
	echo '<form method="get" name="newfactform" action="?" onsubmit="return false;">';
	echo '<select id="newfact" name="newfact">';
	echo '<option value="" disabled selected>' . WT_I18N::translate('&lt;select&gt;') . '</option>';
	foreach ($translated_addfacts as $fact=>$fact_name) {
		echo '<option value="', $fact, '">', $fact_name, '</option>';
	}
	if ($type == 'INDI' || $type == 'FAM') {
		echo '<option value="FACT">', WT_I18N::translate('Custom fact'), '</option>';
		echo '<option value="EVEN">', WT_I18N::translate('Custom event'), '</option>';
	}
	echo '</select>';
	echo '<input type="button" value="', WT_I18N::translate('Add'), '" onclick="add_record(\''.$id.'\', \'newfact\');">';
	echo '<span class="quickfacts">';
	foreach ($quickfacts as $fact) echo '<a href="#" onclick="add_new_record(\''.$id.'\', \''.$fact.'\');return false;">', WT_Gedcom_Tag::getLabel($fact), '</a>';
	echo '</span></form>';
	echo '</td></tr>';
}

/**
 * javascript declaration for calendar popup
 */
function init_calendar_popup() {
	global $WEEK_START, $controller;

	$controller->addInlineJavascript('
		cal_setMonthNames(
			"' . WT_I18N::translate_c('NOMINATIVE', 'January') . '",
			"' . WT_I18N::translate_c('NOMINATIVE', 'February') . '",
			"' . WT_I18N::translate_c('NOMINATIVE', 'March') . '",
			"' . WT_I18N::translate_c('NOMINATIVE', 'April') . '",
			"' . WT_I18N::translate_c('NOMINATIVE', 'May') . '",
			"' . WT_I18N::translate_c('NOMINATIVE', 'June') . '",
			"' . WT_I18N::translate_c('NOMINATIVE', 'July') . '",
			"' . WT_I18N::translate_c('NOMINATIVE', 'August') . '",
			"' . WT_I18N::translate_c('NOMINATIVE', 'September') . '",
			"' . WT_I18N::translate_c('NOMINATIVE', 'October') . '",
			"' . WT_I18N::translate_c('NOMINATIVE', 'November') . '",
			"' . WT_I18N::translate_c('NOMINATIVE', 'December') . '"
		)
		cal_setDayHeaders(
			"' . WT_I18N::translate('Sun') . '",
			"' . WT_I18N::translate('Mon') . '",
			"' . WT_I18N::translate('Tue') . '",
			"' . WT_I18N::translate('Wed') . '",
			"' . WT_I18N::translate('Thu') . '",
			"' . WT_I18N::translate('Fri') . '",
			"' . WT_I18N::translate('Sat') . '"
		)
		cal_setWeekStart(' . $WEEK_START . ');
	');
}

/**
 * @param string $element_id
 * @param string $indiname
 * @param string $ged
 *
 * @return string
 */
function print_findindi_link($element_id, $indiname='', $ged=WT_GEDCOM) {
	return '<a href="#" onclick="findIndi(document.getElementById(\''.$element_id.'\'), document.getElementById(\''.$indiname.'\'), \'' . WT_Filter::escapeHtml($ged) . '\'); return false;" class="icon-button_indi" title="'.WT_I18N::translate('Find an individual').'"></a>';
}

/**
 * @param string $element_id
 *
 * @return string
 */
function print_findplace_link($element_id) {
	return '<a href="#" onclick="findPlace(document.getElementById(\''.$element_id.'\'), WT_GEDCOM); return false;" class="icon-button_place" title="'.WT_I18N::translate('Find a place').'"></a>';
}

/**
 * @param string $element_id
 *
 * @return string
 */
function print_findfamily_link($element_id) {
	return '<a href="#" onclick="findFamily(document.getElementById(\''.$element_id.'\'), WT_GEDCOM); return false;" class="icon-button_family" title="'.WT_I18N::translate('Find a family').'"></a>';
}

/**
 * @param string $element_id
 *
 * @return string
 */
function print_specialchar_link($element_id) {
	return '<span onclick="findSpecialChar(document.getElementById(\''.$element_id.'\')); if (window.updatewholename) { updatewholename(); } return false;" class="icon-button_keyboard" title="'.WT_I18N::translate('Find a special character').'"></span>';
}

/**
 * @param string   $element_id
 * @param string[] $choices
 */
function print_autopaste_link($element_id, $choices) {
	echo '<small>';
	foreach ($choices as $choice) {
		echo '<span onclick="document.getElementById(\'', $element_id, '\').value=';
		echo '\'', $choice, '\';';
		echo " return false;\">", $choice, '</span> ';
	}
	echo '</small>';
}

/**
 * @param string $element_id
 * @param string $sourcename
 *
 * @return string
 */
function print_findsource_link($element_id, $sourcename='') {
	return '<a href="#" onclick="findSource(document.getElementById(\''.$element_id.'\'), document.getElementById(\''.$sourcename.'\'), WT_GEDCOM); return false;" class="icon-button_source" title="'.WT_I18N::translate('Find a source').'"></a>';
}

/**
 * @param string $element_id
 * @param string $notename
 *
 * @return string
 */
function print_findnote_link($element_id, $notename='') {
	return '<a href="#" onclick="findnote(document.getElementById(\''.$element_id.'\'), document.getElementById(\''.$notename.'\'), \'WT_GEDCOM\'); return false;" class="icon-button_note" title="'.WT_I18N::translate('Find a shared note').'"></a>';
}

/**
 * @param string $element_id
 *
 * @return string
 */
function print_findrepository_link($element_id) {
	return '<a href="#" onclick="findRepository(document.getElementById(\''.$element_id.'\'), WT_GEDCOM); return false;" class="icon-button_repository" title="'.WT_I18N::translate('Find a repository').'"></a>';
}

/**
 * @param string $element_id
 * @param string $choose
 *
 * @return string
 */
function print_findmedia_link($element_id, $choose='') {
	return '<a href="#" onclick="findMedia(document.getElementById(\''.$element_id.'\'), \''.$choose.'\', WT_GEDCOM); return false;" class="icon-button_media" title="'.WT_I18N::translate('Find a media object').'"></a>';
}

/**
 * @param string $element_id
 *
 * @return string
 */
function print_findfact_link($element_id) {
	return '<a href="#" onclick="findFact(document.getElementById(\''.$element_id.'\'), WT_GEDCOM); return false;" class="icon-button_find_facts" title="'.WT_I18N::translate('Find a fact or event').'"></a>';
}

/**
 * Summary of LDS ordinances.

 *
*@param WT_Individual $individual
 *
*@return string
 */
function get_lds_glance(WT_Individual $individual) {
	$BAPL = $individual->getFacts('BAPL') ? 'B' : '_';
	$ENDL = $individual->getFacts('ENDL') ? 'E' : '_';
	$SLGC = $individual->getFacts('SLGC') ? 'C' : '_';
	$SLGS = '_';

	foreach ($individual->getSpouseFamilies() as $family) {
		if ($family->getFacts('SLGS')) {
			$SLGS = '';
		}
	}

	return $BAPL . $ENDL . $SLGS . $SLGC;
}
