<?php
/**
* Class file for the tree navigator
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
* @version $Id$
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_TREENAV_PHP', '');

require_once WT_ROOT.'includes/classes/class_person.php';
require_once WT_ROOT.'includes/functions/functions_charts.php';

class TreeNav {
	var $rootPerson = null;
	var $bwidth = 170;
	var $zoomLevel = 0;
	var $name = 'nav';
	var $generations = 4;
	var $allSpouses = true;
	var $images = true;

	/**
	* Tree Navigator Constructor
	* @param string $rootid the rootid of the person
	* @param int $zoom The starting zoom level
	*/
	function __construct($rootid='', $name='nav', $zoom=0) {
		global $SHOW_PRIVATE_RELATIONSHIPS;

		$SHOW_PRIVATE_RELATIONSHIPS = true;		// Interactive Tree doesn't work if this is "false"

		if ($rootid!='none') {
			$rootid = check_rootid($rootid);
			$this->zoomLevel = $zoom;
			$this->rootPerson = Person::getInstance($rootid);
			if (is_null($this->rootPerson)) $this->rootPerson = new Person('');
		}

		$this->name = $name;
		//-- handle AJAX requests
		if (!empty($_REQUEST['navAjax'])) {
			//-- embedded tree for mashups
			if ($_REQUEST['navAjax']=='embed') {
				global $SERVER_URL;
				global $stylesheet;
				?>
				document.writeln('<link rel="stylesheet" href="<?php print $SERVER_URL.$stylesheet; ?>" type="text/css" media="all" />');
				document.writeln('<script type="text/javascript" src="<?php print $SERVER_URL; ?>/js/webtrees.js"></script>');
				<?php
				ob_start();
				$w = safe_GET('width', WT_REGEX_INTEGER, '');
				$h = safe_GET('height', WT_REGEX_INTEGER, '');
				if (!empty($w)) $w.="px";
				if (!empty($h)) $h.="px";
				$this->drawViewport($rootid, $w, $h);
				$output = ob_get_clean();
				$lines = preg_split("/\r?\n/", $output);
				foreach($lines as $line)
					print "document.writeln('".str_replace("'", "\\'", $line)."');\n";
				exit;
			}
			if (isset($_REQUEST['allSpouses'])) {
				if ($_REQUEST['allSpouses']=='false' || $_REQUEST['allSpouses']==false) $this->allSpouses = false;
				else $this->allSpouses = true;
			}
			if (!empty($_REQUEST['details'])) {
				header('Content-type: text/html; charset=UTF-8');
				$this->getDetails($this->rootPerson);
			}
			else if (!empty($_REQUEST['newroot'])) {
				if (!empty($_REQUEST['drawport'])) $this->drawViewport('', "", "150px");
				else {
					$fam = null;
					if ($this->allSpouses) $this->drawPersonAllSpouses($this->rootPerson, 4, 0);
					else $this->drawPerson($this->rootPerson, 4, 0, $fam);
				}
			}
			else if (!empty($_REQUEST['parent'])) {
				$person = $this->rootPerson;
				if ($_REQUEST['parent']=='f') {
					$cfamily = $person->getPrimaryChildFamily();
					if (!empty($cfamily)) {
						$father = $cfamily->getHusband();
						if (!empty($father)) {
							$fam = null;
							$this->drawPerson($father, 2, 1, $fam);
						}
						else print "<br />\n";
					}
					else print "<br />\n";
				}
				else {
					$spouse = $person->getCurrentSpouse();
					if (!empty($spouse)) {
						$cfamily = $spouse->getPrimaryChildFamily();
						if (!empty($cfamily)) {
							$mother = $cfamily->getHusband();
							if (!empty($mother)) {
								$fam = null;
								$this->drawPerson($mother, 2, 1, $fam);
							}
							else print "<br />\n";
						}
						else print "<br />\n";
					}
					else print "<br />\n";
				}
			}
			else {
				$fams = $this->rootPerson->getSpouseFamilies();
				$family = end($fams);
				if (!$this->allSpouses) $this->drawChildren($family, 2);
				else $this->drawAllChildren($this->rootPerson, 2);
			}
			exit;
		}
	}

	/**
	* Draw the view port which creates the draggable/zoomable framework
	* @param string $id an id to use for the starting HTML elements
	* @param string $width the width parameter for the outer style
	* @param string $height the height parameter for the outer style
	*/
	function drawViewport($id='', $width='', $height='') {
		global $WT_IMAGE_DIR, $WT_IMAGES, $GEDCOM, $SERVER_URL;
		if (empty($id)) $id = $this->rootPerson->getXref();
		$widthS = "";
		$heightS = "";
		if (!empty($width)) $widthS = "width: $width; ";
		if (!empty($height)) $heightS = "height: $height; ";
		?>
		<?php $this->setupJS(); ?>

		<div id="out_<?php print $this->name; ?>" dir="ltr" style="position: relative; <?php print $widthS.$heightS; ?>text-align: center; overflow: hidden; border: 1px solid;">
			<div id="in_<?php print $this->name; ?>" style="position: relative; left: -20px; width: auto; cursor: move;" onmousedown="dragStart(event, 'in_<?php print $this->name; ?>', <?php print $this->name; ?>);" onmouseup="dragStop(event);">
			<?php $parent=null;
			//if ($this->rootPerson!=null && !$this->rootPerson->canDisplayDetails()) print_privacy_error();
			if (!$this->allSpouses) $this->drawPerson($this->rootPerson, $this->generations, 0, $parent);
			else $this->drawPersonAllSpouses($this->rootPerson, $this->generations, 0);?>
			</div>
			<div id="controls" style="position: absolute; left: 0px; top: 0px; z-index: 100; background-color: #EEEEEE">
			<table>
				<tr><td><a href="#" onclick="<?php print $this->name; ?>.zoomIn(); return false;"><img src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['zoomin']['other'];?>" border="0" alt="zoomin" /></a></td></tr>
				<tr><td><a href="#" onclick="<?php print $this->name; ?>.zoomOut(); return false;"><img src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['zoomout']['other'];?>" border="0" alt="zoomout" /></a></td></tr>
				<tr><td <?php if (is_null($this->rootPerson) || WT_SCRIPT_NAME=='treenav.php') print "style=\"display: none;\"";?>><a id="biglink" href="#" onclick="<?php print $this->name; ?>.loadBigTree('<?php if (!is_null($this->rootPerson)) print $this->rootPerson->getXref();?>','<?php print htmlentities($GEDCOM,ENT_COMPAT,'UTF-8');?>'); return false;" title="<?php print i18n::translate('View this tree in the full page interactive tree'); ?>"><img src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['tree']['small'];?>" border="0" alt="" /></a></td></tr>
				<tr><td><a href="#" onclick="<?php print $this->name; ?>.toggleSpouses('<?php if ($this->rootPerson!=null) print $this->rootPerson->getXref(); ?>'); return false;" title="<?php print i18n::translate('Show or hide multiple spouses'); ?>"><img src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['sfamily']['small']; ?>" border="0" alt="" /></a></td></tr>
				<tr><td><?php echo help_link('help_treenav.php'); ?></td></tr>
				<tr><td><img id="<?php print $this->name; ?>_loading" src="<?php print $SERVER_URL; ?>images/loading.gif" style="display: none;" alt="Loading..." /></td></tr>
			</table>
			</div>
		</div>
		<script type="text/javascript">
		<!--
		var <?php print $this->name; ?> = new NavTree("out_<?php print $this->name; ?>","in_<?php print $this->name; ?>", '<?php print $this->name; ?>', '<?php print $id; ?>');
		<?php print $this->name; ?>.zoom = <?php print $this->zoomLevel; ?>;
		<?php print $this->name; ?>.center();
		//-->
		</script>
		<?php
	}

	/**
	* Setup the JavaScript for the tree navigator
	*/
	function setupJS() {
		global $SERVER_URL;
		require_once WT_ROOT.'js/prototype.js.htm';
		require_once WT_ROOT.'js/behaviour.js.htm';
		require_once WT_ROOT.'js/overlib.js.htm';
		require_once WT_ROOT.'js/scriptaculous.js.htm';
		?>
	<script type="text/javascript" src="<?php print $SERVER_URL; ?>js/treenav.js"></script>
	<script type="text/javascript">
	<!--
		var myrules = {
		'#out_<?php print $this->name; ?> .person_box' : function(element) {
			element.onmouseout = function() {
				if (<?php print $this->name; ?>.zoom>=-2) return false;
				return nd(); // hide helptext
			}
			element.onmouseover = function() { // show helptext
				if (<?php print $this->name; ?>.zoom>=-2) return false;
				bid = element.id.split("_");
				if (<?php print $this->name; ?>.opennedBox[bid[1]]) return false;
				helptext = this.title;
				if (helptext=='') helptext = this.value;
				if (helptext=='' || helptext==undefined) helptext = element.innerHTML;
				this.title = helptext; if (document.all) return; // IE = title
				this.value = helptext; this.title = ''; // Firefox = value
				// show images
				helptext=helptext.replace(/display: none;/gi, "display: inline;");
				return overlib(helptext, BGCOLOR, "#000000", FGCOLOR, "#FFFFE0");
			}
		},
		'.draggable' : function(element) {
			new Draggable(element.id, {revert:true});
		}
		}
		Behaviour.register(myrules);
		/* not used yet
		function dragObserver() {
			this.parent = null;
			this.onEnd = function(eventName, draggable, event) {
				this.parent.appendChild(draggable.element);
				<?php print $this->name; ?>.collapseBox = false;
			}
			this.onStart = function(eventName, draggable, event) {
				this.parent = draggable.element.parentNode;
				document.body.appendChild(draggable.element);
			}
		}
		Draggables.addObserver(new dragObserver());
		*/
	//-->
	</script>
		<?php
	}

	/**
	* Get the details for a person and their spouse
	* @param Person $person the person to print the details for
	*/
	function getDetails(&$person) {
		global $SHOW_ID_NUMBERS, $USE_SILHOUETTE, $WT_IMAGE_DIR, $WT_IMAGES, $GEDCOM, $SERVER_URL;
		global $TEXT_DIRECTION;

		if (empty($person)) $person = $this->rootPerson;
		//if (!$person->canDisplayDetails()) return;

		$families = array();
		if (!empty($_REQUEST['famid'])) {
			$famid = $_REQUEST['famid'];
			if ($famid!='all') {
				$family = Family::getInstance($_REQUEST['famid']);
				if (!empty($family)) $families[] = $family;
			}
			else {
				$fams = $person->getSpouseFamilies();
				foreach($fams as $fam) {
					$families[] = $fam;
				}
			}
		}
		else {
			if ($this->allSpouses) {
				$fams = $person->getSpouseFamilies();
				foreach($fams as $fam) {
					$families[] = $fam;
				}
			}
			else {
				$fams = $person->getSpouseFamilies();
				$families[] = end($fams);
			}
		}

		$name = $person->getFullName();
		if ($SHOW_ID_NUMBERS)
		$name.=" (".$person->getXref().")";

		?>
		<span class="name1">
		<?php $thumb = $this->getThumbnail($person); 
		if (!empty($thumb)) {
			echo $thumb;
		} else if ($USE_SILHOUETTE && isset($WT_IMAGES["default_image_U"]["other"])) {
			$class = "pedigree_image_portrait";
			if ($TEXT_DIRECTION == "rtl") $class .= "_rtl";
			$sex = $person->getSex();
			$thumbnail = "<img src=\"";
			if ($sex == 'F') {
				$thumbnail .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_F"]["other"];
			}
			else if ($sex == 'M') {
				$thumbnail .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_M"]["other"];
			}
			else {
				$thumbnail .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_U"]["other"];
			} 
			$thumbnail .="\" class=\"".$class."\" border=\"none\" alt=\"\" />";
			echo $thumbnail;
		} ?>
		<a href="<?php print $person->getLinkUrl(); ?>" onclick="if (!<?php print $this->name;?>.collapseBox) return false;"><?php print $person->getSexImage().PrintReady($name); ?></a>
		<img src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES["tree"]["small"];?>" border="0" width="15" onclick="<?php print $this->name;?>.newRoot('<?php print $person->getXref();?>', <?php print $this->name;?>.innerPort, '<?php print htmlentities($GEDCOM,ENT_COMPAT,'UTF-8'); ?>');" />
		</span><br />
		<div class="details1 indent">
			<?php
				echo '<b>', abbreviate_fact('BIRT'), '</b> ', $person->getBirthDate()->Display(), ' ', PrintReady($person->getBirthPlace()), '<br />';
				if ($person->isDead()) {
					echo '<b>', abbreviate_fact('DEAT'), '</b> ', $person->getDeathDate()->Display(), ' ', PrintReady($person->getDeathPlace());
				}
			?>
		</div>
		<br />
		<span class="name1"><?php
		foreach($families as $family) {
			if (!empty($family)) $spouse = $family->getSpouse($person);
			if (!empty($spouse)) {
				$name = $spouse->getFullName();
				if ($SHOW_ID_NUMBERS)
				$name.=" (".$spouse->getXref().")";
				?>
				<?php $thumb = $this->getThumbnail($spouse); 
				if (!empty($thumb)) {
					echo $thumb;
				} else if ($USE_SILHOUETTE && isset($WT_IMAGES["default_image_U"]["other"])) {
					$class = "pedigree_image_portrait";
					if ($TEXT_DIRECTION == "rtl") $class .= "_rtl";
					$sex = $spouse->getSex();
					$thumbnail = "<img src=\"";
					if ($sex == 'F') {
						$thumbnail .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_F"]["other"];
					}
					else if ($sex == 'M') {
						$thumbnail .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_M"]["other"];
					}
					else {
						$thumbnail .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_U"]["other"];
					} 
					$thumbnail .="\" class=\"".$class."\" border=\"none\" alt=\"\" />";
					echo $thumbnail;
				} ?>
				<a href="<?php print $spouse->getLinkUrl(); ?>" onclick="if (!<?php print $this->name;?>.collapseBox) return false;">
				<?php print $spouse->getSexImage().PrintReady($name); ?></a>
				<img src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES["tree"]["small"];?>" border="0" width="15" onclick="<?php print $this->name;?>.newRoot('<?php print $spouse->getXref();?>', <?php print $this->name;?>.innerPort, '<?php print htmlentities($GEDCOM,ENT_COMPAT,'UTF-8'); ?>');" />
				<br />
				<div class="details1 indent">
					<?php
						echo '<b>', abbreviate_fact('BIRT'), '</b> ', $spouse->getBirthDate()->Display(), ' ', PrintReady($spouse->getBirthPlace()), '<br />';
						echo '<b>', abbreviate_fact('MARR'), '</b> ', $family->getMarriageDate()->Display(), ' ', $family->getMarriagePlace();
					?>
					<a href="family.php?famid=<?php print $family->getXref(); ?>" onclick="if (!<?php print $this->name;?>.collapseBox) return false;"><img id="d_<?php print $family->getXref(); ?>" alt="<?php print $family->getXref(); ?>" class="draggable" src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['family']['button']; ?>" border="0" /></a><br />
					<?php
						if ($spouse->isDead()) {
							echo '<b>', abbreviate_fact('DEAT'), '</b> ', $spouse->getDeathDate()->Display(), ' ', PrintReady($spouse->getDeathPlace()), '<br />';
				} ?>
				</div>
				<?php
			}
		}
		?>
		</span>
		<?php
	}

	/**
	* Draw the children for a family
	* @param Family $family The family to draw the children for
	* @param int $gen The number of generations of descendents to draw
	*/
	function drawChildren(&$family, $gen=2) {
		if (!empty($family) && $gen>0) {
			$children = $family->getChildren();
			foreach($children as $ci=>$child) {
				$fam = null;
				$this->drawPerson($child, $gen-1, -1, $fam);
			}
		}
	}

	/**
	* Draw all of the children for a person
	* @param Person $person The person to draw the children for
	* @param int $gen The number of generations of descendents to draw
	*/
	function drawAllChildren(&$person, $gen=2) {
		if (!empty($person) && $gen>0) {
			$fams = $person->getSpouseFamilies();
			foreach($fams as $famid=>$family) {
				$children = $family->getChildren();
				foreach($children as $ci=>$child) {
					$fam = null;
					$this->drawPersonAllSpouses($child, $gen-1, -1);
				}
			}
		}
	}

	/**
	* Get the thumbnail image for the given person
	*
	* @param Person $person
	* @return string
	*/
	function getThumbnail(&$person) {
		global $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $TEXT_DIRECTION, $USE_MEDIA_VIEWER, $SERVER_URL;
		$thumbnail = "";
		if ($MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES && showFact("OBJE", $person->getXref())) {
			$object = $person->findHighlightedMedia();
			if (!empty($object)) {
				$whichFile = thumb_or_main($object);	// Do we send the main image or a thumbnail?
				$size = findImageSize($whichFile);
				$class = "pedigree_image_portrait";
				if ($size[0]>$size[1]) $class = "pedigree_image_landscape";
				if ($TEXT_DIRECTION == "rtl") $class .= "_rtl";
				// NOTE: IMG ID
				$imgsize = findImageSize($object["file"]);
				$imgwidth = $imgsize[0]+50;
				$imgheight = $imgsize[1]+150;

				if (!empty($object['mid']) && $USE_MEDIA_VIEWER) {
					$thumbnail .= "<a href=\"".encode_url("mediaviewer.php?mid=".$object['mid'])."\" >";
				} else {
					$thumbnail .= "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($object["file"])."',$imgwidth, $imgheight);\">";
				}
				$thumbnail .= "<img src=\"".$SERVER_URL.$whichFile."\" vspace=\"0\" hspace=\"0\" class=\"$class\" alt=\"\" title=\"\"";
				if ($imgsize) $thumbnail .= " /></a>";
				else $thumbnail .= " />";
			}
		}
		return $thumbnail;
	}

	/**
	* Draw a person for the chart but include all of their spouses instead of just one
	* @param Person $person The Person object to draw the box for
	* @param int $gen The number of generations up or down to print
	* @param int $state Whether we are going up or down the tree, -1 for descendents +1 for ancestors
	*/
	function drawPersonAllSpouses(&$person, $gen, $state) {
		global $SHOW_ID_NUMBERS, $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $SERVER_URL;

		if ($gen<0) {
			return;
		}
		if ($this->zoomLevel < -2) $style = "display: none;";
		else $style = "width: ".(10+$this->zoomLevel)."; height: ".(10+$this->zoomLevel).";";
		if (empty($person)) $person = $this->rootPerson;
		if (empty($person)) return;
		$mother = null;
		$father = null;

		if ($state>=0) {
			$cfamily = $person->getPrimaryChildFamily();
			if (!empty($cfamily)) {
				$father = $cfamily->getHusband();
				if (empty($father)) $father = $cfamily->getWife();
			}
			$fams = $person->getSpouseFamilies();
			$fams = array_reverse($fams);
			//-- find the last spouse family that has a known spouse
			foreach($fams as $family) {
				if (!empty($family)) $spouse = $family->getSpouse($person);
				if (!empty($spouse)) {
					$mcfamily = $spouse->getPrimaryChildFamily();
					if (!empty($mcfamily)) {
						$mother = $mcfamily->getHusband();
						//-- a mother's father was found so break out
						break;
					}
				}
			}
		}
		?>
		<table border="0" cellpadding="0" cellspacing="0" style="margin-top: 0px; margin-bottom: 1px;">
			<tbody>
				<tr>
					<?php /* print the children */
					if ($state<=0) {
						$hasChildren = false;
						if ($person->getNumberOfChildren()>0) $hasChildren = true;
					?>
					<td id="ch_<?php print $person->getXref();?>" align="right" <?php if ($gen==0 && $hasChildren) print 'id="'.$this->name.'_cload" name="'.$this->name.'_cload" onclick="'.$this->name.'.loadChild(this, \''.$person->getXref().'\');"'; ?>>
						<?php
							$this->drawAllChildren($person, $gen);
						?>
					</td>
					<?php
					if ($hasChildren && $person->getNumberOfChildren()>1) { ?><td valign="top"><img style="position: absolute;" id="cline_<?php print $person->getXref();?>" name="vertline" src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['vline']['other']; ?>" width="3" alt="" /></td><?php }
					else if ($hasChildren) { ?><td valign="top"><img style="position: absolute;" id="cline_<?php print $person->getXref();?>" name="vertline" src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['hline']['other']; ?>" width="3"  alt=""/></td><?php }
					}
					if ($state>0) {
						?><td><img src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['hline']['other']; ?>" width="8" height="3" alt="" /></td><?php
					}
					/* print the person */ ?>
					<td>
						<div class="person_box" dir="<?php print $TEXT_DIRECTION; ?>" id="box_<?php print $person->getXref();?>" style="text-align: <?php echo $TEXT_DIRECTION=="rtl" ? "right":"left"; ?>; cursor: pointer; font-size: <?php print 10 + $this->zoomLevel;?>px; width: <?php print ($this->bwidth+($this->zoomLevel*18));?>px; margin-left: 3px; direction: <?php print $TEXT_DIRECTION; ?>" onclick="<?php print $this->name; ?>.expandBox(this, '<?php print $person->getXref(); ?>', 'all');">
						<?php
							$name = $person->getFullName();

							print PrintReady($person->getSexImage('small', $style)." ".$name);
						?><br />
						<?php
						$fams = $person->getSpouseFamilies();
						foreach($fams as $famid=>$family) {
							$spouse = $family->getSpouse($person);
							if (!is_null($spouse)) {
								$name = $spouse->getFullName();
								print PrintReady($spouse->getSexImage('small', $style)." ".$name);
								print "<br />\n";
							} else print "<br />\n";
						}
						?>
						</div>
					</td>
					<?php
					if ($state<0) {
						?><td><img src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['hline']['other']; ?>" width="8" height="3" alt="" /></td><?php
					}
					/* print the father */
					if ($state>=0 && (!empty($father) || !empty($mother))) {
						$lineid = "pline_";
						if (!empty($father)) $lineid.=$father->getXref();
						$lineid.="_";
						if (!empty($mother)) $lineid.=$mother->getXref();
						?>
					<?php if (!empty($father) && (!empty($mother))) { ?><td><img style="position: absolute;" id="<?php print $lineid;?>" name="pvertline" src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['vline']['other']; ?>" width="3" alt="" /></td><?php } ?>
					<td align="left">
						<table cellpadding="0" cellspacing="0" border="0">
							<tbody>
								<tr>
									<?php /* there is a IE JavaScript bug where the "id" has to be the same as the "name" in order to use the document.getElementsByName() function */ ?>
									<td <?php if ($gen==0 && !empty($father)) print 'id="'.$this->name.'_pload" name="'.$this->name.'_pload" onclick="'.$this->name.'.loadParent(this, \''.$person->getXref().'\', \'f\');"'; ?>>
										<?php if (!empty($father)) $this->drawPerson($father, $gen-1, 1, $cfamily); else print "<br />\n";?>
									</td>
								</tr>
								<?php
								$fams = $person->getSpouseFamilies();
								foreach($fams as $famid=>$family) {
									$spouse = $family->getSpouse($person);
									$mother = null;
									if ($spouse!=null) {
										$mcfamily = $spouse->getPrimaryChildFamily();
										if (!empty($mcfamily)) {
											$mother = $mcfamily->getHusband();
										}
									}
									if (!is_null($mother)) {
								?>
								<tr>
									<td <?php if ($gen==0 && !empty($mother)) print 'id="'.$this->name.'_pload" name="'.$this->name.'_pload" onclick="'.$this->name.'.loadParent(this, \''.$person->getXref().'\', \'m\');"'; ?>>
										<?php if (!empty($mother)) $this->drawPerson($mother, $gen-1, 1, $mcfamily); else print"<br />\n";?>
									</td>
								</tr>
								<?php } } ?>
							</tbody>
						</table>
					</td>
					<?php } ?>
				</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	* Draw a person for the chart
	* @param Person $person The Person object to draw the box for
	* @param int $gen The number of generations up or down to print
	* @param int $state Whether we are going up or down the tree, -1 for descendents +1 for ancestors
	* @param Family $pfamily
	*/
	function drawPerson(&$person, $gen, $state, &$pfamily) {
		global $SHOW_ID_NUMBERS, $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $SERVER_URL;

		$gen++;
		if ($gen<0) {
			return;
		}
		if ($this->zoomLevel < -2) $style = "display: none;";
		else $style = "width: ".(10+$this->zoomLevel)."; height: ".(10+$this->zoomLevel).";";
		if (empty($person)) $person = $this->rootPerson;
		if (empty($person)) return;
		$mother = null;
		$father = null;
		if (!empty($pfamily)) $spouse = $pfamily->getSpouse($person);
		else {
			$spouse = $person->getCurrentSpouse();
			$fams = $person->getSpouseFamilies();
			$pfamily = end($fams);
		}
		if ($state<=0) {
			$fams = $person->getSpouseFamilies();
			$family = end($fams);
		}
		if ($state>=0) {
			$cfamily = $person->getPrimaryChildFamily();
			if (!empty($cfamily)) {
				$father = $cfamily->getHusband();
			}
			if (!empty($spouse)) {
				$mcfamily = $spouse->getPrimaryChildFamily();
				if (!empty($mcfamily)) {
					$mother = $mcfamily->getHusband();
				}
			}
		}
		?>
		<table border="0" cellpadding="0" cellspacing="0" style="margin-top: 0px; margin-bottom: 1px;">
			<tbody>
				<tr>
					<?php /* print the children */
					if ($state<=0) {
						$hasChildren = false;
						if (!empty($family) && $family->getNumberOfChildren()>0) $hasChildren = true;
					?>
					<td id="ch_<?php print $person->getXref();?>" align="right" <?php if ($gen==0 && $hasChildren) print 'id="'.$this->name.'_cload" name="'.$this->name.'_cload" onclick="'.$this->name.'.loadChild(this, \''.$person->getXref().'\');"'; ?>>
						<?php
							$this->drawChildren($family, $gen);
						?>
					</td>
					<?php
					if ($hasChildren && $family->getNumberOfChildren()>1) { ?><td valign="top"><img style="position: absolute;" id="cline_<?php print $person->getXref();?>" name="vertline" src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['vline']['other']; ?>" width="3" alt="" /></td><?php }
					}
					if ($state>0) {
						?><td><img src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['hline']['other']; ?>" width="8" height="3" alt="" /></td><?php
					}
					/* print the person */ ?>
					<td>
						<div class="person_box" dir="<?php print $TEXT_DIRECTION; ?>" id="box_<?php print $person->getXref();?>" style="text-align: <?php echo $TEXT_DIRECTION=="rtl" ? "right":"left"; ?>; cursor: pointer; font-size: <?php print 10 + $this->zoomLevel;?>px; width: <?php print ($this->bwidth+($this->zoomLevel*18));?>px; direction: <?php print $TEXT_DIRECTION; ?>" onclick="<?php print $this->name; ?>.expandBox(this, '<?php print $person->getXref(); ?>', '<?php if (!empty($pfamily)) print $pfamily->getXref(); ?>');">
						<?php
							$name = $person->getFullName();
							print PrintReady($person->getSexImage('small', $style)." ".$name);
						?><br />
						<?php if (!is_null($spouse)) {$name = $spouse->getFullName();
						print PrintReady($spouse->getSexImage('small', $style)." ".$name);
						} else print "<br />\n"; ?>

						</div>
					</td>
					<?php
					if ($state<0) {
						?><td><img src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['hline']['other']; ?>" width="8" height="3" alt="" /></td><?php
					}
					/* print the father */
					if ($state>=0 && (!empty($father) || !empty($mother))) {
						$lineid = "pline_";
						if (!empty($father)) $lineid.=$father->getXref();
						$lineid.="_";
						if (!empty($mother)) $lineid.=$mother->getXref();
						?>
					<?php if (!empty($father) && (!empty($mother))) { ?><td><img style="position: absolute;" id="<?php print $lineid;?>" name="pvertline" src="<?php print $SERVER_URL.$WT_IMAGE_DIR."/".$WT_IMAGES['vline']['other']; ?>" width="3" alt="" /></td><?php } ?>
					<td align="left">
						<table cellpadding="0" cellspacing="0" border="0">
							<tbody>
								<tr>
									<?php /* there is a IE JavaScript bug where the "id" has to be the same as the "name" in order to use the document.getElementsByName() function */ ?>
									<td <?php if ($gen==0 && !empty($father)) print 'id="'.$this->name.'_pload" name="'.$this->name.'_pload" onclick="'.$this->name.'.loadParent(this, \''.$person->getXref().'\', \'f\');"'; ?>>
										<?php if (!empty($father)) $this->drawPerson($father, $gen-1, 1, $cfamily); else print "<br />\n";?>
									</td>
								</tr>
								<tr>
								<?php /* print the mother */ ?>
									<td <?php if ($gen==0 && !empty($mother)) print 'id="'.$this->name.'_pload" name="'.$this->name.'_pload" onclick="'.$this->name.'.loadParent(this, \''.$person->getXref().'\', \'m\');"'; ?>>
										<?php if (!empty($mother)) $this->drawPerson($mother, $gen-1, 1, $mcfamily); else print"<br />\n";?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<?php } ?>
				</tr>
			</tbody>
		</table>
		<?php
	}
}
?>
