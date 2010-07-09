<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2010 John Finlay
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
 * @subpackage Modules
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
require_once WT_ROOT.'includes/classes/class_module.php';
require_once WT_ROOT.'includes/functions/functions_date.php';
require_once WT_ROOT.'includes/functions/functions_places.php';
require_once WT_ROOT.'library/tcpdf/config/lang/eng.php';
require_once WT_ROOT.'library/tcpdf/tcpdf.php';

class pedigree_report_WT_Module extends WT_Module implements WT_Module_Report {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Pedigree Chart');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('Pedigree Chart');
	}

	// Extend class WT_Module
	public function defaultAccessLevel() {
		return WT_PRIV_PUBLIC;
	}

	// Implement WT_Module_Report - a module can provide many reports
	public function getReportMenus() {
		global $controller, $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		if ($controller && isset($controller->pid)) {
			$pid='&amp;pid='.$controller->pid;
		} elseif ($controller && isset($controller->rootid)) {
			$pid='&amp;pid='.$controller->rootid;
		} else {
			$pid='';
		}
		
		$menus=array();
		$menu=new Menu($this->getTitle().' - '.i18n::translate('Portrait'), 'reportengine.php?ged='.urlencode(WT_GEDCOM).'&amp;action=setup&amp;report=modules/'.$this->getName().'/report_portrait.xml'.$pid);
		$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES['pedigree']['small']);
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff", "icon_small_reports");
		$menus[]=$menu;

		$menu=new Menu($this->getTitle().' - '.i18n::translate('Landscape'), 'reportengine.php?ged='.urlencode(WT_GEDCOM).'&amp;action=setup&amp;report=modules/'.$this->getName().'/report_landscape.xml'.$pid);
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff", "icon_small_reports");
		$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES['pedigree']['small']);
		$menus[]=$menu;

		$menu=new Menu($this->getTitle().' - '.i18n::translate('Singlepage'), 'reportengine.php?ged='.urlencode(WT_GEDCOM).'&amp;action=setup&amp;report=modules/'.$this->getName().'/report_singlepage.xml'.$pid);
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff", "icon_small_reports");
		$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES['pedigree']['small']);
		$menus[]=$menu;

		return $menus;
	}


  var $borderStyle=array('M'=>array('width'=>.1, 'cap' => 'round', 'join'=>'round', 'color'=>array(64,  64, 192)),
			 'F'=>array('width'=>.1, 'cap' => 'round', 'join'=>'round', 'color'=>array(192, 66, 66)),
			 'U'=>array('width'=>.1, 'cap' => 'round', 'join'=>'round', 'color'=>array(66,  66, 66)));
  var $fillColor = array('M'=>array(240, 240, 255),
			 'F'=>array(255, 240, 240),
			 'U'=>array(240, 240, 240));
  var $conectStyle=array('parents'=>array('width'=>.2,  'cap' => 'round', 'join'=>'round', 'color'=>array(0,   0,  80)),  // line to parents
			 'sibling'=>array('width'=>.15, 'cap' => 'butt',  'join'=>'round', 'color'=>array(80,  80, 160)), // line to sibling
			 'nextGen'=>array('width'=>.25, 'cap' => 'round', 'join'=>'round', 'color'=>array(0,   0,  80)));

  var $FontSize=4.9;
  var $yBrim=20;                             //  | page borders
  var $xBrim=20;                             //  | 
  var $spaceCompress=0.08;                   // additional space after compression
  var $conectPos=.6;                         // parents to children connection (0=child 1=parents)  |
  var $conectChildNoDisplay=.25;             // line for siblings which are not displayed           | relativ to 
                                             // (from  $conectChildNoDisplay to  $conectPos)        |  $xSpace
  var $radius=0.5;                           // radius of edges for the connection lines            |
  var $headerHeight=5;                       // height of header
  var $xSpace=null;                          //  |
  var $xWidth=null;                          //  | different for 
  var $ySpace=null;                          //  | 'portrait' and 'landscape'
  var $yWidth=null;                          //  |

  /**
   * scaling factor of person box in direction of same generation
   * an array 'DISPLAYTYPE'=> size where DISPLAYTYPE is 'full' or 'short'
   * used for display of siblings
   */
  var $scaleSameGen=array();
  /**
   * scaling factor of person box in direction of next generation
   * used for double displayed persons set in function setOrientation()
   */
  var $scaleNextGen=null;

  var $maxSpaceNoDisplay=.2;                 // maximum space between lines when siblings are not displayed
  var $addSpaceSiblings=0;                   // additional space between not displayed siblings and ancestors

  var $person=array();    // hold either ref to type Person or string to gedcom xref
                    // father of $person[$i] is $person[$i*2], mother is $person[$i*2+1]
                    // if equal null then parent of $person[int($i/2)] don't exists
  var $families   = array();  // all families, in $families[$i] is the primary child family of $person[$i] 
  var $children   = array();                 // 2-dim children of all families
  var $allPersons=array();                   // array (GedID => int number)
  var $allPersonsRev=array();                // array (int number => GedID)
  var $genShow=1;                            // how many Generation are shown

  var $positions  = array();                 // positions of direct ancestors of first person array(int number => float position)
  var $leaves     = array();                 // persons with no ancestors              array ( =>)
  var $leavesSort = array();                 // 
  var $leavePositions=array();               // positions of leaves, same style as $positions
  var $EventsShow = array();                 // 1-dim array to store which events are displayed    array (ID=> true|false)
  var $allSiblings= array();                 // array with siblings  array( GedID=>Person::getInstance())
  var $showSiblings=true;                    // should the siblings displayed in case of enough space in the tree 
  var $displaySiblings=array();              // 1-dim array how the siblings of each family are displayed array( int number => false|'full'|'short')

  var $output='PDF';                         // type of output 'PDF'|'HTML'
  var $pdf = null;                           // pdf file class TCPDF
  var $pageorient=null;

  /* ************************************************************************ */
  function ReportPedigree () { 

  }
  /* ************************************************************************ */
  /**
   *
   */
  function setup () {
    global $vars, $output, $SHOW_ID_NUMBERS, $SHOW_HIGHLIGHT_IMAGES, $DEBUG;
    $allset=true;
    foreach (array('pid','fonts','maxgen','pageorient','compress','showSiblings',
		   'SHOW_ID_NUMBERS','SHOW_HIGHLIGHT_IMAGES') as $a) {
      if(isset($vars[$a])) {
	if (isset($vars[$a]['id']) && $vars[$a]['id']) {
	  $$a=$vars[$a]['id'];
	} else {
	  $$a=false;
	}
      } else {
	$allset=false;
      }
    }                                   // foreach (array('pid','fonts','maxgen','pageorient', ...
    $DEBUG = isset($vars['DEBUG']) ? (int) $vars['DEBUG']['id']    : 0;
    if (!$allset) {
      print_header("Pedigree Single Page");
      echo "<h2>not all var set </h2>";
      if($DEBUG) {
	echo "<pre>vars=\n";
	print_r($vars);
	echo "</pre>\n";
	echo "compress=".( $compress ? "yes":"no") . " showSiblings=". ($showSiblings ? "yes":"no") . "<br />\n";
      }
      print_footer();
      exit;
    } else {                            // if (!$allset) {
      unset ($allset);
    }                                   // else if (!$allset) {
    $ped=new pedigree_report_WT_Module();
    $ped->create($pid, $maxgen, $pageorient,$showSiblings, $compress, $fonts);
    return $ped;
  }                                     // function setup () {
  /* ************************************************************************ */
  /**
   *
   *
   */
  function create ($pid, $maxgen, $pageorient,$showSiblings, $compress, $fonts) {
    global $output;

    $this->setOrientation($pageorient);
    $this->showSiblings=$showSiblings;

    if ($this->getAllPersons($pid, $maxgen)) {
      $this->initPDF($fonts);
      $this->calcLeavePositions();
      $this->calcnoLeavesPositions();
      if ($compress) {
	$this->compressPositions($output);
	// if the compression changes the display mode of siblings the compression is not total
	// the code will be executed again
	$this->compressPositions($output);
      }                                 // if ($compress) {
      $this->setPageSizeandTitle($output);
      $this->displayPersons($output);
      $this->displayEventInfo($output);
//      $this->pageBorder($output);
      if ($output=="PDF") {
	$this->Output();
      }                                 // if ($output=="PDF") {
    } else {                            // if ($pedigree->getAllPersons ...
      print_header(" Pedigree Single Page");
      echo 'internal ERROR pedigree_single no person'; 
      print_footer();
      exit;
    }                                   // else if ($pedigree->getAllPersons ...
  }                                     // function create ( ...
  /* ************************************************************************ */
  /**
   * Calculation of the position of children. Implements individum of direct 
   * ancestor and also of siblings
   *
   * @param int $famNr             number of family
   * @param int $father            number of father
   * @param int $mother            number of mother
   * @param int $child=-1          which child of family (-1 ancestor of first person)
   * @return $position or null if $position of father and mother are not set
   */
  function calcChildPos($famNr, $father, $mother, $child=-1) {
    if (isset($this->positions[$father]) && isset($this->positions[$mother])) {
      if ($this->showSiblings && isset($this->children) && isset($this->children[$famNr]) && count($this->children[$famNr])>2) {
	$nrSibl=(count($this->children[$famNr])-1);
	$diffParents=$this->positions[$mother] - $this->positions[$father];
	if ($child == -1) {
	  $nr=$this->children[$famNr][$nrSibl];
	} else {                        // if ($child == -1) {
	  $nr=$child;
	}                               // else if ($child == -1) {

	if ($diffParents > 1.01) {
	  foreach ($this->scaleSameGen as $type=>$size) {
	    $space=$size;
	    if (($diffParents - (1-$size)) > ($nrSibl-.5)*$size) {
	      $this->displaySiblings[$famNr]=$type;
	      $offset=($diffParents-$space*($nrSibl-1)-(1-$size))*0.5;
	      if ($nr == $this->children[$famNr][$nrSibl]) {
		$offset+=(1-$size)/2;
	      }                         // if ($nr == $this->children[$famNr][$nrSibl]) {
	      if ($nr > $this->children[$famNr][$nrSibl]) {
		$offset+=(1-$size);
	      }                         // if ($nr > $this->children[$famNr][$nrSibl]) {
	      return  $this->positions[$father] + $space*($nr) +$offset;
	    }                           // if (($diffParents - (1-$size)) > ($nrSibl-1)*$size) {
	  }                             // foreach ($this->scaleSameGen as $type=>$size) {	  
	}                               // if ($diffParents > 1) {
	$this->displaySiblings[$famNr]=false;
	$space=($diffParents-2*$this->addSpaceSiblings)/($nrSibl+2);
	if ($space > $this->maxSpaceNoDisplay){
	  $space=$this->maxSpaceNoDisplay;
	}                               // if ($space > $this->maxSpaceNoDisplay){
	if ($nr==$this->children[$famNr][$nrSibl]) {
	  $add=$this->addSpaceSiblings;
	} elseif ($nr < $this->children[$famNr][$nrSibl]) { // if ($nr == ...
	  $add=0;//-$this->addSpaceSiblings;
	} else {                        // elseif ($nr < ...
	  $add=2*$this->addSpaceSiblings;
	}                               // else elseif ($nr < ...
	return $this->positions[$father] + $space*$nr + ($diffParents-$space*($nrSibl-1)-2*$this->addSpaceSiblings)*0.5+$add;
      } else {                          // if ($this->showSiblings && isset($this->children) && ...
	return ($this->positions[$father] + $this->positions[$mother])/2;
      }                                 // else if ($this->showSiblings && isset($this->children) && ...
    } elseif (isset($this->positions[$father])){// if (isset($this->positions[$father]) && ...
      return $this->positions[$father];
    } else {                            // elseif (isset($this->positions[$father]))
      return $this->positions[$mother];
    }                                   // else elseif (isset($this->positions[$mother]))
    return null;
  }                                     // function calcChildPos(
  /* ************************************************************************ */
  /**
   * Calculate the positions of persons with no ancestors (leaves)
   * values stored in $this->leavePositions also the variables
   * $this->positions       positions of the persons
   * $this->leavesSort      array($index              => ${number of person}) 
   * $this->leavesSortR     array(${number of person} => $index) 
   *
   * @return nothing
   */
  function calcLeavePositions () {
    global $output, $DEBUG;
    if (!isset ($this->positions) || $this->positions==null) {
      for ($i=(1<<($this->genShow))/2;$i< (1<<($this->genShow)); ++$i ) {
	if (isset($this->person[$i]) && $this->person[$i]!=null && !isset($this->leaves[$i])) {
	  $this->leavesSort[]=$i;
	}                               // if (isset($this->person[$i]) && $this->person[$i]!=null && ...
      }                                 // for ($i=(1<<($maxgen))/2;$i< (1<<($maxgen)); ++$i ) {
      if (isset($this->leavesSort)) {
	if ($output=='HTML' && $DEBUG&4) {echo 'leaves='; print_r($this->leaves); echo "<br />\n"; }
	if ($output=='HTML' && $DEBUG&4) {echo 'unsorted leavesSort='; print_r($this->leavesSort); echo "<br />\n"; }

	sort($this->leavesSort);
	if ($output=='HTML' && $DEBUG&4) {echo 'leavesSort='; print_r($this->leavesSort); echo "<br />\n"; }
	for ($i=0; $i < count($this->leavesSort);++$i) {
	  $j=$this->leavesSort[$i];
	  while ($this->person[$j]==null ) {$j=$j/2;}
	  $this->positions[$j]=$i+.5;
	  $this->leavesSortR[$i]=$j;
	}                               // for ($i=0; $i < count($this->leavesSort);++$i) {
	if ($output=='HTML' && $DEBUG&4) {echo 'positions='; print_r($this->positions); echo "<br />\n"; }
	if ($output=='HTML' && $DEBUG&4) {echo 'leavesSortR='; print_r($this->leavesSortR); echo "<br />\n"; }
      }                                 // if (isset($this->leavesSort)) {
      $this->leavePositions=$this->positions;
    } else {                            // if (!isset ($this->positions) || $this->positions==null) {
      // XXX TODO
    }                                   // else if (!isset ($this->positions) || $this->positions==null) {
  }                                     // function calcLeavePositions () {
  /* ************************************************************************ */
  /**
   * Calculate the positions of all persons
   * for these calculation the positions of persons with no ancestors should already set 
   *
   * @return nothing
   */
  function calcnoLeavesPositions () {
    for ($i=(1<<($this->genShow))/2 -1 ;$i>=0; --$i ) {
      if (isset($this->person[$i]) && $this->person[$i]!=null && !is_string($this->person[$i]) && !isset($this->positions[$i])) {
	$this->positions[$i]=$this->calcChildPos($i, 2*$i, 2*$i+1); 
      }                                 // if (isset($this->person[$i]) && ...
    }                                   // for ($i=(1<<($showGen))/2 -1 ;$i>=0; --$i ) {
  }                                     // function function calcnoLeavesPositions () {
  /* ************************************************************************ */
  /**
   * Compress positions of Persons
   * the variables $this->positions and $this->leavePositions must be set and
   * the smallest position must be the first in the array 
   *
   * @param string $output    type of output (in case of 'HTML' additional DEBUG output posible)
   * @return nothing
   */
  function compressPositions ($output) {
    global $DEBUG;
    $PosOffset=0;               // offset to old $positions
    $MaxPosition=array();

    if ($output=='HTML' && $DEBUG & 32) {
      echo '<table border="1"><th>key</th><th>Pos</th><th>lastKey</th><th>l gen</th><th>l Pos</th><th>Array</th><th>P_diff</th><th></th>';
    }                                   // if ($output=='HTML' && $DEBUG & 32) {
    foreach ($this->leavePositions as $key=>$value) {
      $aGen=floor(log($key)/log(2))+1;
      if (!isset($lastKey)) { // $lastKey up to now not defined
	$lastKey=$key;   $lastGen=$aGen;
	$positions_compr[$key]=true;
	$MaxPosition[$aGen]=$value;
	for($i=$key/2,$g=$aGen-1; $i >1 && isset($this->positions[$i]) && 
	      $this->positions[$i]== $this->positions[$i*2];$i/=2, --$g) {
	  $MaxPosition[$g]=$value;
	}                               // for($i=$key/2,$g=$aGen-1; $i >1 && isset($this->positions[$i]) && ..
	continue;
      }                                 // if (!isset($lastKey)) {
      if (abs($key - $lastKey) > $aGen){
	$h=0;
	if ($aGen < $lastGen) {
	  $h=$this->positions[$lastKey>>($lastGen-$aGen)]-$this->positions[$key]+ 1 +$this->spaceCompress -$PosOffset;
	  if (isset($this->displaySiblings[$key-1]) ) {
	    $i=$key-1;
	    $last_per=$this->calcChildPos($i, 2*$i, 2*$i+1, count($this->children[$i])-2);
	    if ($this->displaySiblings[$i] == false) {
	      $h+=max($last_per-$this->positions[$i]-0.5 ,0);
	    } else {                    // if ($this->displaySiblings[$i] == false) {
	      if ($last_per != $this->positions[$i]) {
		$h+=$last_per-$this->positions[$i] - (1-$this->scaleSameGen[$this->displaySiblings[$i]])/2;
	      }                         // if ($last_per != $this->positions[$i]) {
	    }                           // else if ($this->displaySiblings[$i] == false) {
	  }                             // if (isset($this->displaySiblings[$key-1]) ) {
	  if ($output=='HTML' && $DEBUG & 64) {
	    echo '<tr><td>'.$key.' Smaller</td><td>p_a='.  $this->positions[$key] .
//	      '</td><td>lk='. ($lastKey>>($lastGen-$aGen)).
//	      '</td><td>po='. ($this->positions[$lastKey>>($lastGen-$aGen)]).
	      '</td><td>'. ($PosOffset) . 
	      '</td><td>h='.   $h .  
	      '</td></tr>';
	  }                               // if ($output=='HTML' && $DEBUG & 64) {
	  $PosOffset+=$h;
	} else {                          // if ($aGen < $lastGen) {
	  // get the number ($i) and generation ($g) for which MaxPositions is allready set
	  for ($i=$key,$g=$aGen;$i>2 && !isset($MaxPosition[$g]); $i/=2,--$g) {
	  }
	  unset($diff);
	  $diff=array();
	  $loopControl=($this->positions[$i] <= $this->positions[floor($i/2)]);
	  $gStart=$g;
	  for (;$i>2 && $loopControl || ($g==$gStart && $this->positions[$i] -$MaxPosition[$g] > 1); $i/=2,--$g) {
	    // this special loop control is nescesary so code is executed one time more
	    $loopControl=($this->positions[$i] <= $this->positions[floor($i/2)]);

	    if (isset($this->displaySiblings[$i])) {
	      $first_child_positions=$this->calcChildPos($i, 2*$i, 2*$i+1, 0);
	      if ($this->displaySiblings[$i] == false) {
		$first_child_positions=min($first_child_positions+.5, $this->calcChildPos($i, 2*$i, 2*$i+1));
	      }                         // if ($this->displaySiblings[$i] == false) {	      
	    } else {                    // if (isset($this->displaySiblings[$i])) {
	      $first_child_positions=$this->positions[$i];
	    }                           // else if (isset($this->displaySiblings[$i])) {
	    $diff[$i]=-($first_child_positions + $PosOffset -$MaxPosition[$g]-(1+$this->spaceCompress));

	    if ($this->positions[$key]+$PosOffset+$diff[$i] <0.5) {  // all positions should be >0
	      $diff[$i]=-($this->positions[$key]+$PosOffset)+0.5;
	    }                           // if ($positions[$key]+$PosOffset+$diff[$i] < 0) {
	  }                             // for (;$i>2 && $loopControl || ($g==$gStart &&

	  if (count($diff) && isset($MaxPosition[$aGen-1]) && $this->positions[$key]+$PosOffset+
	      max($diff)-$MaxPosition[$aGen-1] < 0.499 + $this->spaceCompress) {
	    unset ($diff);
	    $diff[0]=-($this->positions[$key]+$PosOffset-$MaxPosition[$aGen-1]-0.5-$this->spaceCompress);
	  }                             // if (count($diff) && isset($MaxPosition[$aGen-1]) && ...
	  if ($output=='HTML' && $DEBUG & 32) {
	    if (count($diff)==0) {$diff[0]=0;}

	    $p_n=($this->positions[$key] + $PosOffset+max($diff));
	    echo '<tr><td>'.$key.' larger</td><td>p_a='.   $this->positions[$key] .
	      '</td><td>p_di='.(isset($MaxPosition[$aGen-1])? $p_n-$MaxPosition[$aGen-1]:'UNDEF').
	      '</td><td>lastK='.$lastKey.
	      '</td><td>p_n='.  ($this->positions[$key] + $PosOffset+max($diff)). '</td><td>';
	    print_r($diff);
	    echo '</td><td>'.'</td><td>i=' .$i. '</td><td>g='.$g.'</td><td>'. max($diff) . '</td></tr>';
	  }                             // if ($output=='HTML' && $DEBUG & 32) {
	  if (count($diff)) {
	    $PosOffset+=max($diff);
	  }                             // if (count($diff)) {
	}                               // else if ($aGen < $lastGen) {
      }                                 // if (abs($key - $lastKey) > $aGen){

      if (isset($MaxPosition[$aGen])) {
	$MaxPosition[$aGen]=max($MaxPosition[$aGen],$value+$PosOffset);
      } else {                          // if (isset($MaxPosition[$aGen])) {
	$MaxPosition[$aGen]= $value+$PosOffset;
      }                                 // else if (isset($MaxPosition[$aGen])) {

      $this->positions[$key]+=$PosOffset;
      $this->leavePositions[$key]+=$PosOffset;
      $positions_compr[$key]=true;
      for ($i=$key; $i>1 && ((($i %2) == 1 && isset($this->person[$i-1])) // is woman and husband exists
			     ||(isset($this->families[$i/2]) && //family exists
			      !($this->families[$i/2]->getWifeId() && $this->families[$i/2]->getHusbId())));
	   $i/=2) {
	if (!isset($positions_compr[$i/2])) {
	  if ($this->families[$i/2]->getWifeId() && $this->families[$i/2]->getHusbId()) {
	    $this->positions[$i/2]=$this->calcChildPos($i/2, $i-1, $i);
	  } else {                      // else elseif ($families[$i/2]->getHusbId()) {
	    $this->positions[$i/2]=$this->positions[$i];
	  }                             // else if ($families[$i/2]->getWifeId() && $families[$i/2]->getHusbId()) {
	  $positions_compr[$i/2]= true;//$this->positions[$i/2];
	}                               // if (!isset($positions_compr[$i/2])) {
      }                                 // for ($i=$key; $i > 1 && ...
      for ($i=$aGen-1; $i > 0 && isset($positions_compr[$key>>($aGen-$i)]); --$i) {
	$p=$this->positions[$key>>($aGen-$i)];//+$PosOffset;
	$MaxPosition[$i]=isset($MaxPosition[$i])? max($MaxPosition[$i], $p) :$p ;
      }                                 // for ($i=$aGen-1; $i > 0; --$i) {  
      $lastKey=$key;
      $lastGen=$aGen;
    }                                   // foreach ($this->leavePositions as $key=>$value) {
    if ($output=='HTML' && $DEBUG & 32) {echo '</table>'; }
  }                                     // function compressPositions ( ...
  /* ************************************************************************ */
  /**
   * @param string $output 
   */
  function pageBorder ($output) {
    if ($output =='PDF') {
      if ($this->pageorient=='portrait') {
	$x1=$this->xBrim;
	$y1=$this->yBrim;
	$x2=$this->xBrim+$this->genShow*             ($this->xWidth+$this->xSpace);
	$y2=$this->yBrim+(max($this->positions)+0.5)*($this->yWidth+$this->ySpace)+$this->headerHeight;
      } else {
	$x1=$this->xBrim;
	$y1=$this->yBrim;
	$x2=$this->xBrim+(max($this->positions)+0.5)*($this->xWidth+$this->xSpace);
	$y2=$this->yBrim+$this->genShow*             ($this->yWidth+$this->ySpace)+$this->headerHeight;
      }
      $this->pdf->Line($x1, $y1, $x1, $y2, $this->conectStyle['sibling']); //left
      $this->pdf->Line($x1, $y2, $x2, $y2, $this->conectStyle['sibling']); //button
      $this->pdf->Line($x2, $y2, $x2, $y1, $this->conectStyle['sibling']); //right
      $this->pdf->Line($x2, $y1, $x1, $y1, $this->conectStyle['sibling']); //top
      $this->pdf->Line($x2, $y1+$this->headerHeight, $x1, $y1+$this->headerHeight, $this->conectStyle['siblings']); //header
    }
  }
  /* ************************************************************************ */
  /**
   * Conect two different persons in TCPDF file
   * conection style is stored in the array $this->conectStyle
   * the line width increases with decreasing generation
   *
   * @param string $how    style of conection 'sibling'|'parents'|'nextGen'
   * @param float $x1      first x-position
   * @param float $y1      first y-position
   * @param float $x2      second x-position
   * @param float $y2      second y-position
   * @param int gen        generation default=0
   */
  function conectPersons ($how, $x1, $y1, $x2, $y2, $gen=0) {
    /*if (0) {
    switch ($how) {
    case 'sibling':
    case 'nextGen':
      $this->pdf->Line($x1, $y1, $x2, $y2, $this->conectStyle[$how]);
      break;                            // case 'sibling': case 'nextGen':
    case 'parents': 
      $r=min($this->conectPos, $this->conectChildNoDisplay, $this->radius);
      if ($this->pageorient == 'portrait') {
	$r*=$this->xSpace;
	if ($y1==$y2) {
	  $this->pdf->Line($x1, $y1, $x2, $y2, $this->conectStyle[$how]);
	} else {                          // if ($y1==$y2) {
	  if ($y1>$y2) {
	    $sign=-1;
	    $alpha1=270;
	    $alpha2=360;
	  } else {                      // if ($y1>$y2) {
	    $sign=1;
	    $alpha1=0;
	    $alpha2=90;
	  }                             // else ($y1>$y2) {
	  $this->pdf->Line($x1, $y1, $x1+$this->xSpace*$this->conectPos-$r, $y1, $this->conectStyle[$how]);
	  $this->pdf->Line($x1+$this->xSpace*$this->conectPos, $y1+$r*$sign,
			   $x1+$this->xSpace*$this->conectPos, $y2-$r*$sign, $this->conectStyle[$how]);
	  $this->pdf->Line($x2-$this->xSpace*$this->conectChildNoDisplay+$r, $y2, $x2, $y2, $this->conectStyle[$how]);
	  $this->pdf->Circle($x1+($this->xSpace*$this->conectPos-$r),            $y1+$r*$sign, $r, $alpha1,     $alpha2);
	  $this->pdf->Circle($x2-($this->xSpace*$this->conectChildNoDisplay-$r), $y2-$r*$sign, $r, $alpha1+180, $alpha2+180);
	}                               // else if ($y1==$y2) {
      } else {                          // if ($this->pageorient == 'portrait') {
	$r*=$this->ySpace;
	if ($x1==$x2) {
	  $this->pdf->Line($x1, $y1, $x2, $y2, $this->conectStyle[$how]);
	} else {                        // if ($x1==$x2) {
	  if ($x1>$x2) {
	    $sign=-1;
	    $alpha1=0;
	    $alpha2=90;
	  } else {                      // if ($x1>$x2) {
	    $sign=1;
	    $alpha1=90;
	    $alpha2=180;
	  }                             // else ($x1>$x2) {
	  $this->pdf->Line($x1, $y1, $x1, $y1-($this->ySpace*$this->conectPos)+$r, $this->conectStyle[$how]);
	  $this->pdf->Line($x1+$r*$sign, $y1-($this->ySpace*$this->conectPos),
			   $x2-$r*$sign, $y1-($this->ySpace*$this->conectPos), $this->conectStyle[$how]);
	  $this->pdf->Line($x2, $y2+($this->ySpace*$this->conectChildNoDisplay-$r), $x2, $y2, $this->conectStyle[$how]);
	  $this->pdf->Circle($x1+$r*$sign, $y1-($this->ySpace*$this->conectPos)+$r, $r, $alpha1,     $alpha2);
	  $this->pdf->Circle($x2-$r*$sign, $y2+($this->ySpace*$this->conectChildNoDisplay-$r), $r, $alpha1+180, $alpha2+180);
	}                               // else if ($x1==$x2) {
      }                                 // else if ($this->pageorient == 'portrait') {
      break;                            // case 'parents':
    }                                   // switch ($how) {
    } else */{
      $w1=min($this->conectPos, $this->conectChildNoDisplay, $this->radius)*max($this->xSpace,$this->ySpace)*.9;
      if ($how=='sibling') {
	$w1*=.6;
      } else {                          // else if ($how=='sibling') {
	$w1*=(1- 0.4*($gen/(max(1,$this->genShow-1)))); // line thickness depends on generation firstGen=100% lastGen=60%
      }                                 // else if ($how=='sibling') {
      $w2=$w1*.5;

      $mirror=false;
      $rotate=false;
      if ($this->pageorient == 'portrait') {
	$h=($y2-$y1);
	$dG=($x2-$x1);
      } else {                          // if ($this->pageorient == 'portrait') {
	$rotate=true;
	$h=($x2-$x1);
	$dG=($y1-$y2);
      }                                 // else if ($this->pageorient == 'portrait') {
      $l=2*min($this->conectPos, $this->conectChildNoDisplay, $this->radius)*$dG;
      $d1=$this->conectPos*$dG-$l*0.5;
      $d2=$dG-($d1+$l);
      if ($h < 0) {
	$mirror=true;
	$h=-$h;
      }                                 // if ($h < 0) {
      $circ=0.25;//maximal 0.5
      if (abs($h) >= $l+$w1-$w2) {
	$points=
	  array(array( 0,-$w1,                                  $d1,-$w1,                                         $d1,-$w1) // Line 1
		,array($d1+$circ*($w2+$w2+$l),-$w1,             $d1+($w2+$w2+$l)*0.5,(-$w2-$w2+$l)*(0.5-$circ),   $d1+($w2+$w2+$l)*0.5,(-$w2-$w2+$l)*0.5)     //C2 
		,array($d1+($w2+$w2+$l)*0.5,(-$w2-$w2+$l)*0.5,  $d1+($w2+$w2+$l)*0.5,(-$w2-$w2+$l)*0.5, 	  $d1+($w2+$w2+$l)*0.5,$h-$w2-($l-$w2-$w2)*0.5)//L3 
		,array($d1+($w2+$w2+$l)*0.5,$h-$w2-($l-$w2-$w2)*(0.5-$circ),   $d1+$l+$circ*($w2+$w2-$l),$h-$w2,  $d1+$l,$h-$w2) //C4
		,array($d1+$l,$h-$w2,                           $d1+$l,$h-$w2,                                    $d1+$l+$d2,$h-$w2) //line 5
		,array($d1+$l+$d2,$h-$w2,                       $d1+$l+$d2,$h-$w2,                                $d1+$l+$d2,$h+$w2) //line 6
		,array($d1+$l,$h+$w2,                           $d1+$l,$h+$w2,                                    $d1+$l,$h+$w2)  //line 7
		,array($d1+$l-$circ*($l+$w1+$w2),$h+$w2,        $d1+($l-$w2-$w2)*.5,$h+$w2-($l+$w2+$w2)*(0.5-$circ), $d1+($l-$w2-$w2)*.5,$h+$w2-($l+$w2+$w2)*.5)// C8
		,array($d1+($l-$w2-$w2)*.5,($l-$w2-$w2)*.5,     $d1+($l-$w2-$w2)*.5,$w1-$w2+($l-$w2-$w2)*.5,      $d1+($l-$w2-$w2)*.5,$w1-$w2-$w2+($l)*.5) //L9
		,array($d1+($l-$w2-$w2)*.5,$w1-(4*$w2-$l)*(0.5-$circ),  $d1+$circ*($l-$w2-$w2),$w1,               $d1+0,$w1)  //c 10
		,array($d1,$w1,                                 $d1+0,$w1,                                        0,$w1)      //Line 11
		,array(0,$w1,                                   0,$w1,                                            0,-$w1)      //line 12		     
		);
      } else {                          // if (abs($h) >= $l+$w1-$w2) {
	if ($h == 0) {
	  $w2=$w1;
	}                               // if ($h == 0) {
	$a1=0;      $b1=-$w1;
	$a2=$d1;    $b2=-$w2+$h;
	$a3=$dG-$d2;$b3=$w2+$h;
	$a4=$dG;    $b4=$w1;
	$points=array(array($a1,$b1, $a2,$b1, $a2,$b1) // L
		      ,array($a2+$circ*($a3),$b1, .5*($a3+$a2),.5*($b2+$b1), .5*($a3+$a2),.5*($b2+$b1)) // C
		      ,array(.5*($a3+$a2),.5*($b2+$b1), $a3+$circ*($a2-$a3),$b2, $a3,$b2) // C
		      ,array($a3,$b2, $a4,$b2, $a4,$b2) // L
		      ,array($a4,$b2, $a4,$b3, $a4,$b3) // L
		      ,array($a4,$b3, $a3,$b3, $a3,$b3) // L
		      ,array($a3+$circ*($a2-$a3),$b3, 0.5*($a2+$a3),0.5*($b4+$b3), 0.5*($a2+$a3),0.5*($b4+$b3)) // C
		      ,array(0.5*($a2+$a3),0.5*($b4+$b3), $a2+$circ*($a3-$a2),$b4, $a2,$b4) // C
		      ,array($a2,$b4, $a1,$b4, $a1,$b4) // L
		      ,array($a1,$b4, $a1,$b1, $a1,$b1) // L
		      );
      }                                 // else if (abs($h) >= $l+$w1-$w2) {
      $this->pdf->StartTransform();
      $this->pdf->Translate($x1,$y1);
      if ($mirror) {
	if ($this->pageorient == 'portrait') {
	  $this->pdf->MirrorV(0);
	} else {                        // if ($this->pageorient == 'portrait') {
	  $this->pdf->MirrorH(0);
	}                               // else if ($this->pageorient == 'portrait') {
      }                                 // if ($mirror){
      if ($rotate) {
	$this->pdf->Rotate(90,0,0);
      }                                 // if ($rotate){
      $this->pdf->Polycurve(0,-$w1, $points,'DF', $this->conectStyle[$how], array(255));
      $this->pdf->StopTransform();
    }                                   // 
  }                                     // function conectPersons ($how, $x1, $y1, $x2, $y2) {
  /* ************************************************************************ */
  /**
   * Display info about used images of events
   *
   * @param string $output    type of output
   * @return nothing
   */
  function displayEventInfo ($output) {
    if ($output == 'PDF') {
      if ($this->pageorient == 'portrait') {
	$y_pos=$this->yBrim+$this->yOffset+ ($this->yWidth+$this->ySpace)*(min($this->positions)-0.5);
      } else {                          // if ($this->pageorient == 'portrait') {
	$y_pos=$this->yBrim+$this->yOffset+ ($this->genShow-0.7)*($this->yWidth+$this->ySpace)+$this->ySpace;
      }                                 // else if ($this->pageorient == 'portrait') {
      $this->pdf->MultiCell ($this->xWidth, $this->yWidth, $this->getEventImg('all', null),
			     0, 'L', 0, 0, $this->xBrim, $y_pos, true, 0, true, false, 0);
    } else {                            // if ($output == 'PDF') {
      echo $this->getEventImg('all', null) .'<br />';
      echo timestamp_to_gedcom_date(mktime(0,0,0,date("m"),date("d"),date("Y")))->Display(). '<br /></center>';
    }                                   // else if ($output == 'PDF') {
  }                                     // function displayEventInfo () {
  /* ************************************************************************ */
  /**
   * Display all Persons
   *
   * @param string $output    type of output
   * @return nothing
   */
  function displayPersons($output) {
    for ($i=1;$i< (2<<($this->genShow))/2; ++$i ) {
      if (isset($this->person[$i]) && $this->person[$i]!=null ) {
	$aGen=floor(log($i)/log(2));
	if ($output == 'PDF') {
	  if ($this->pageorient == 'portrait') {
	    $a=$this->xBrim+ ($this->xWidth+$this->xSpace) * $aGen;
	    $b=$this->yBrim+$this->yOffset+ ($this->yWidth+$this->ySpace) * $this->positions[$i];
	  } else {		        // if ($this->pageorient == 'portrait') {
	    $a=$this->xBrim+ ($this->xWidth+$this->xSpace) * $this->positions[$i];
	    $b=$this->yBrim+$this->yOffset+ ($this->yWidth+$this->ySpace) * ($this->genShow-$aGen-1) +$this->ySpace;
	  }                             // else if ($this->pageorient == 'portrait') {
	  if ($this->showSiblings && isset($this->children[$i]) && count($this->children[$i]) >2 ) {
	    $n=(count($this->children[$i])-1);
	    if (isset($this->positions[$i*2]) && isset($this->positions[$i*2+1])) {
	      for ($j=0; $j < $n; ++$j) {
		if ($j != $this->children[$i][$n]) {
		  if ($this->pageorient == 'portrait') {
		    $pX=$a;
		    $pY=$this->calcChildPos($i, 2*$i, 2*$i+1, $j) * ($this->yWidth+$this->ySpace) + $this->yBrim+$this->yOffset;
		    $pW=$this->xWidth;
		    $pH=$this->yWidth*($this->displaySiblings[$i] ?$this->scaleSameGen[$this->displaySiblings[$i]]:1);
		    $lY=$pY;
		    $lX2=$a+$this->xWidth+$this->xSpace*$this->conectPos;
		    $lY2=$pY;
		  } else {              // if ($this->pageorient == 'portrait') {
		    $pX=$this->calcChildPos($i, 2*$i, 2*$i+1, $j) * ($this->xWidth+$this->xSpace) + $this->xBrim ;
		    $pY=$b-($this->yWidth*(1-$this->scaleNextGen));
		    $pW=$this->xWidth*($this->displaySiblings[$i] ?$this->scaleSameGen[$this->displaySiblings[$i]]:1);
		    $pH=$this->yWidth;
		    $lX=$pX;
		    $lX2=$pX;
		    $lY2=$b-$this->ySpace*$this->conectPos;
		  }                     // else if ($this->pageorient == 'portrait') {
		  $per=false;
		  if (isset($this->displaySiblings[$i]) && $this->displaySiblings[$i] !=false) {
		    $boxSize='child_'.$this->displaySiblings[$i];
		    if (isset($this->allPersons[$this->children[$i][$j]])) {
		      $boxSize.='_ref';
		      $per=$this->person[$this->allPersons[$this->children[$i][$j]]];
		    } else {            // if (isset($this->allPersons[$this->children[$i][$j]])) {
		      $per=$this->allSiblings[$this->children[$i][$j]];
		    }                   // else if (isset($this->allPersons[$this->children[$i][$j]])) {
		    if ($this->pageorient == 'portrait') {
		      $lX=$a+$this->xWidth;
		    } else {            // if ($this->pageorient == 'portrait') {
		      $lY=$b;
		    }                   // else if ($this->pageorient == 'portrait') {
		  } else {              // if (isset($this->displaySiblings[$i]) && $this->displaySiblings[$i] !=false) {
		    if ($this->pageorient == 'portrait') {
		      $lX=$a+$this->xWidth+$this->xSpace*$this->conectChildNoDisplay;
		    } else {            // if ($this->pageorient == 'portrait') {
		      $lY=$b-$this->ySpace*$this->conectChildNoDisplay;
		    }                   // else if ($this->pageorient == 'portrait') {
		  }                     // else if (isset($this->displaySiblings[$i]) && $this->displaySiblings[$i] !=false) {
		  $this->conectPersons('sibling', $lX, $lY, $lX2, $lY2, $aGen);
		  if ($per) {
		    $this->pdfPerson($boxSize, $pX, $pY, $pW,$pH, $this->children[$i][$j], $per, sprintf ("%d%c. ", $i, $j+97));
		  }                     // if ($per) {
		}                       // if ($j != $this->children[$i][$n]) {
	      }                         // for ($j=0; $j < $n; ++$j) {
	    }                           // if (isset(positions[$i*2] && ...
	  }                             // if ($this->showSiblings && isset($this->children[$i]) && count($this->children[$i]) > 2 ) {
	  if ($this->pageorient == 'portrait') {
	    if ($aGen < $this->genShow ) {
	      foreach (array($i*2, $i*2+1) as $j) {
		if(isset($this->positions[$j])) {
		  $this->conectPersons('parents', $a+$this->xWidth, $b, $a+$this->xWidth+$this->xSpace,
				       $this->yBrim+$this->yOffset+$this->positions[$j]*($this->yWidth+$this->ySpace),$aGen);
		}                       // if(isset($this->positions[$i*2])) {
	      }                         // foreach (array($i*2, $i*2+1) as $j) {
	    }                           // if (floor(log($i)/log(2))< $maxgen) {
	    if ($aGen == $this->genShow-1 && !isset($this->leaves[$i])) {
	      $this->conectPersons('nextGen',$a+$this->xWidth, $b, $a+$this->xWidth+$this->xSpace*$this->conectPos, $b, $aGen);
	    }                           // if ($aGen == $maxgen-1 && ...
	    if (is_string($this->person[$i]) && isset($this->families[$this->allPersons[$this->person[$i]]])) {
	      if (isset($this->person[2*$this->allPersons[$this->person[$i]]]) &&
		  isset($this->person[2*$this->allPersons[$this->person[$i]]+1])) {
		$this->conectPersons('parents',$a+$this->xWidth*($this->scaleNextGen), $b,
				     $a+$this->xWidth+$this->xSpace*$this->conectChildNoDisplay, $b-$this->yWidth/3.,$aGen);
		$this->conectPersons('parents',$a+$this->xWidth*($this->scaleNextGen), $b,
				     $a+$this->xWidth+$this->xSpace*$this->conectChildNoDisplay, $b+$this->yWidth/3., $aGen);
	      } else {                  // if (isset($this->person[2*$this->allPersons[$this->person[$i]]]) && ...
		$this->conectPersons('parents',$a+$this->xWidth*($this->scaleNextGen), $b,
				     $a+$this->xWidth+$this->xSpace*$this->conectChildNoDisplay, $b, $aGen);
	      }                         // else if (isset($this->person[2*$this->allPersons[$this->person[$i]]]) && ...
	    }                           // if (is_string($this->person[$i]) && isset($this->families ...
	  } else {                      // if ($this->pageorient == 'portrait') {
	    if ($aGen< $this->genShow) {
	      foreach (array($i*2, $i*2+1) as $j) {
		if(isset($this->positions[$j])) {
		  $this->conectPersons('parents', $a, $b, $this->xBrim+($this->xWidth+$this->xSpace)*$this->positions[$j], $b-$this->ySpace, $aGen);
		}                       // if(isset($this->positions[$i*2])) {
	      }                         // foreach (array($i*2, $i*2+1) as $j) {
	    }                           // if ($aGen< $this->genShow) {
	    if ($aGen == $this->genShow-1 && !isset($this->leaves[$i])) { 
	      $this->conectPersons('nextGen',$a, $b, $a, $b-$this->ySpace*$this->conectPos, $aGen);
	    }                           // if ($aGen == $this->genShow-1 && !isset($this->leaves[$i])) {
	    if (is_string($this->person[$i]) && isset($this->families[$this->allPersons[$this->person[$i]]])) {
	      if (isset($this->person[2*$this->allPersons[$this->person[$i]]]) &&
		  isset($this->person[2*$this->allPersons[$this->person[$i]]+1])) {
		$this->conectPersons('parents',$a, $b+($this->yWidth*(1-$this->scaleNextGen)),
				     $a-$this->xWidth/3., $b-$this->ySpace*$this->conectChildNoDisplay, $aGen);
		$this->conectPersons('parents',$a, $b+($this->yWidth*(1-$this->scaleNextGen)),
				     $a+$this->xWidth/3., $b-$this->ySpace*$this->conectChildNoDisplay, $aGen);
	      } else {                  // if (isset($this->person[2*$this->allPersons[$this->person[$i]]]) && ...
		$this->conectPersons('parents',$a, $b+($this->yWidth*(1-$this->scaleNextGen)),
				     $a, $b-$this->ySpace*$this->conectChildNoDisplay, $aGen);
	      }                         // else if (isset($this->person[2*$this->allPersons[$this->person[$i]]]) && ...
	    }                           // if (is_string($this->person[$i]) && isset($this->families ...
	  }                             // else if ($this->pageorient == 'portrait') {

	  if (!is_string($this->person[$i])) {
	    $this->pdfPerson('full', $a, $b, $this->xWidth, $this->yWidth, $this->allPersonsRev[$i],
			     $this->person[$i], $i . ". ");
	  } else {                      // if (!is_string($this->person[$i])) {
	    //Person already displayed
	    $this->pdfPerson('full_ref', $a, $b, $this->xWidth, $this->yWidth, $this->allPersonsRev[$i],
			     $this->person[$this->allPersons[$this->person[$i]]], $i . ". ");
	  }                             // else if (!is_string($this->person[$i])) {
	}                               // if ($output == 'PDF') {

	if ($output=='HTML') {
	  if (!is_string($this->person[$i])) {
	    $this->printPersonHtml('full', $this->allPersonsRev[$i], $this->person[$i], $i, '');
	  } else {                      // if (!is_string($this->person[$i])) {
	    $this->printPersonHtml(false, $this->allPersonsRev[$i], $this->person[$this->allPersons[$this->person[$i]]], $i, '');
	  }                             // else if (!is_string($this->person[$i])) {
	  if ($this->showSiblings && isset($this->children[$i]) && count($this->children[$i])>2) {
	    $n=(count($this->children[$i])-1);
	    for ($j=0; $j < $n; ++$j) {
	      if ($j != $this->children[$i][$n]) {
		if (isset($this->allSiblings[$this->children[$i][$j]])) {
		  $how="full";
		  $per=$this->allSiblings[$this->children[$i][$j]];
		} else {                // if (isset($this->allSiblings[$this->children[$i][$j]])) {
		  $how="double";
		  $per=$this->person[$this->allPersons[$this->children[$i][$j]]];
		}                       // else if (isset($this->allSiblings[$this->children[$i][$j]])) {
		$this->printPersonHtml($how, $this->children[$i][$j],$per ,$i,sprintf('%c',$j+97));
	      }                         // if ($j != $this->children[$i][$n]) {
	    }                           // for ($j=0; $j < $n; ++$j) {
	  }                             // if ($this->showSiblings && isset($this->children[$i]) && count($this->children[$i])>2) {
	}                               // if ($output=='HTML') {
      }                                 // if (isset($this->person[$i]) && $this->person[$i]!=null && ...
    }                                   // for ($i=1;$i< (2<<($maxgen))/2; ++$i ) {
    if ($output=='HTML') {
      echo '</table><br />';
    }                                   // if ($output=='HTML') {
  }                                     // function displayPersons() {
  /* ************************************************************************ */
  /**
   * get the persons from the database
   *
   * @param string $pid              ID of first person
   * @param int $maxgen              number of generations
   * @return true of false
   */
  function getAllPersons ($pid, $maxgen) {
    if ($this->getFirstPerson($pid) != null) {
      $this->getPersons($maxgen);
      return true;
    } else {                           // if ($this->getFirstPerson($pid) != null) {
      return false;
    }                                  // else if ($this->getFirstPerson($pid) != null) {
  }
  /* ************************************************************************ */
  /**
   * Create HTML output for Image of an Event
   *
   * global variables:
   *  $FontSize, $EventsShow
   *
   * @parm $event        which event one of birt|deat (not case sensitive) 
   * @parm $person=null  which person
   * @return string      Html code of image or empty string
   */
  function getEventImg($event, $person=null) {

    if ($person != null) {
      if (strtolower($event) == 'birt') {
	if ($person->getAllEventDates('CHR') ||
	    $person->getAllEventPlaces('CHR'))  { $event='chr'; }
	if ($person->getAllEventDates('BIRT') ||
	    $person->getAllEventPlaces('BIRT')) { $event='birt'; }
	elseif ($event != 'chr')                { $event=''; }
      } elseif (strtolower($event) == 'deat') {
	if ($person->getAllEventDates('BURI') ||
	    $person->getAllEventPlaces('BURI')) { $event='buri'; }
	if ($person->getAllEventDates('DEAT') ||
	    $person->getAllEventPlaces('DEAT')) { $event='deat'; }
      }                                 // elseif (strtolower($event) == 'deat') {
    } else {                            // if ($person != null) {
    }                                   // else if ($person != null) {
    $imgs =array(0=>'modules/'.$this->getName().'/images/birth.png',
		 1=>'modules/'.$this->getName().'/images/chris.png',
		 2=>'modules/'.$this->getName().'/images/marr.png',
		 3=>'modules/'.$this->getName().'/images/death.png',
		 4=>'modules/'.$this->getName().'/images/buri.png');
    $names=array(0=>translate_fact('BIRT'),
		 1=>translate_fact('CHR'),
		 2=>translate_fact('MARR'),
		 3=>translate_fact('DEAT'),
		 4=>translate_fact('BURI'));
    if (strtolower($event) == 'birt') {$i=0;}
    if (strtolower($event) == 'chr')  {$i=1;}
    if (strtolower($event) == 'marr') {$i=2;}
    if (strtolower($event) == 'deat') {$i=3;}
    if (strtolower($event) == 'buri') {$i=4;}
    if (isset($i)) {
      $this->EventsShow[$i]=true;     // which images are used
      $imgSize=getimagesize($imgs[$i]);
      return '<img src="'.$imgs[$i].'" height="' .(0.8*$this->FontSize) . '" width="'.
	($this->FontSize*.8)*$imgSize[0]/$imgSize[1].'" >&nbsp;' ;
    }                                   // if (isset($i)) {
    if (strtolower($event) == 'all')  {
      $str='<b></b>';//='<table>';
      for($i=0;$i < count($imgs);++$i) {
	if(isset($this->EventsShow[$i])) {  // only print used images
	  $imgSize=getimagesize($imgs[$i]);
	  $str.='<img alt="birth" src="'.$imgs[$i].'" height="' . (0.8*$this->FontSize) . '" width="'.
	    ($this->FontSize*.8)*$imgSize[0]/$imgSize[1]. '" > &nbsp; ' . $names[$i] .'<br />';
	}                               // if(isset($this->EventsShow[$i])) {
      }                                 // for($i=0;$i < count($imgs);++$i)
      return $str;
    }                                   // if (strtolower($event) == 'all')  {
    return '';
  }                                     // function getEventIMG($event, ...
  /* ************************************************************************ */
  /**
   * get the first person
   * 
   * set the variables
   * $this->person         array ()
   * $this->allPersons     array ()
   * $this->allPersonsRev  array ()
   *
   * @param string $pid              ID of first person
   * @return Person::getInstance($pid)
   */
  function getFirstPerson($pid) {
    $this->person[1] = Person::getInstance($pid);
    $this->allPersons[$pid] = 1;
    $this->allPersonsRev[1] = $pid;
    return $this->person[1];
  }                                     // function getFirstPersons($pid) {
  /* ************************************************************************ */
  /**
   * get the persons from the database
   *
   * set the variables
   * $this->genShow        how many generation where found mayby smaller than parameter $maxgen
   * $this->person         array ()
   * $this->allPersons     array ()
   * $this->allPersonsRev  array ()
   * $this->leaves         array ()
   * $this->leavesSort     array ()
   *
   * @param int $maxgen           number of generations
   * @return true of false
   */
  function getPersons($maxgen) {
    for ($i=1;$i< (2<<($maxgen))/2; ++$i) {
      if ($this->person[$i] !=null && !is_string($this->person[$i])) {
	$this->families[$i]=$this->person[$i]->getPrimaryChildFamily();
	$this->genShow=max(floor(log($i)/log(2))+1,$this->genShow);
	if ($this->families[$i]!=null) {
	  if ($this->families[$i]->getHusbId()) {      // Father exists
	    if (!isset($this->allPersons[$this->families[$i]->getHusbId()])) { 
	      $this->person[$i*2]=Person::getInstance($this->families[$i]->getHusbId());
	      $this->allPersons[$this->families[$i]->getHusbId()]=$i*2;
	    } else {                    // if (!isset($this->allPersons[$this->families[$i]->getHusbId()])) {
	      $this->person[$i*2]=$this->families[$i]->getHusbId();
	    }                           // else if (!isset($allPersons[$this->families[$i]->getHusbId()])) {
	    $this->allPersonsRev[$i*2]=$this->families[$i]->getHusbId();
	  } else {                      // if ($this->families[$i]->getHusbId()) {
	    $this->person[$i*2]=null;
	  }                             // else if ($this->families[$i]->getHusbId()) {

	  if ($this->families[$i]->getWifeId() ) {     // Mother exists
	    if (!isset($this->allPersons[$this->families[$i]->getWifeId()])) {
	      $this->person[$i*2+1]=Person::getInstance($this->families[$i]->getWifeId());
	      $this->allPersons[$this->families[$i]->getWifeId()]=$i*2+1;
	    } else {                  // if (!isset($this->allPersons[$this->families[$i]->getWifeId()])) {
	      $this->person[$i*2+1]=$this->families[$i]->getWifeId();
	    }                         // else if (!isset($this->allPersons[$this->families[$i]->getWifeId()])) {
	    $this->allPersonsRev[$i*2+1]=$this->families[$i]->getWifeId();
	  } else {                    // if ($this->families[$i]->getWifeId()) {
	    $this->person[$i*2+1]=null;
	  }                           // else if ($this->families[$i]->getWifeId()) {
	  $this->children[$i]=$this->families[$i]->getChildrenIds();
	  if (count($this->children[$i]) > 1) {
	    $j=0;
	    foreach ($this->children[$i] as $key=>$cId) { 
	      if (isset($this->allPersons[$cId])) {
		if ($cId == $this->allPersonsRev[$i]) {
		  $this->children[$i][]=$j;
		}                       // if ($cId == $this->allPersonsRev[$i]) {
	      } else {                  // if (isset($this->allPersons[$cId])) {
		$this->allSiblings[$cId]=Person::getInstance($cId);
	      }                         // else if (isset($this->allPersons[$cId])) {
	      ++$j;
	    }                           // foreach ($children as $key=>$cId) {
	  }                             // if (count($children) > 1) {
	} else {                        // if ($this->families[$i]!=null) {
	  $this->person[$i*2]=null;
	  $this->person[$i*2+1]=null;
  
	  $this->leaves[$i]=floor(log($i)/log(2))+1;
	  $this->leavesSort[]= ($i*(2<<($maxgen-$this->leaves[$i]))/2);
	}                               // else if ($this->families[$i]!=null) {
      } else {                          // if ($this->person[$i]!=null && !is_string($this->person[$i])) {
	$this->person[$i*2]=null;
	$this->person[$i*2+1]=null;
      }                                 // else if ($this->person[$i]!=null && !is_string($this->person[$i])) {
      if (is_string($this->person[$i])) {
	$this->leaves[$i]=floor(log($i)/log(2))+1;
	$this->leavesSort[]= ($i*(2<<($maxgen-$this->leaves[$i]))/2);
      }                                 // if (is_string($this->person[$i])) {
    }                                   // for ($i=1;$i< (2<<($maxgen))/2; ++$i) {
  }                                     // function getPersons($gen) {
  /* ************************************************************************ */
  /**
   * initialise PDF file
   *
   * @param string $fonts
   * @return TCPDF class instance
   */
  function initPDF($fonts) {
    global $l;

    // create new PDF document
    $pdf = new TCPDF('P', 'mm', 'a0', true, 'UTF-8', false); 
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('webtrees');
    $pdf->SetTitle('webtrees Pedigree '.$this->underlinestar($this->person[1]->getFullName()));
    //set margins
    $pdf->SetMargins($this->xBrim, $this->yBrim, $this->xBrim);
    // remove default header/footer
    $pdf->setPrintHeader(false);
    //  $pdf->SetHeaderMargin(10);
    //  $pdf->SetHeaderData("", 10, 'a', 'STRING');
    $pdf->setPrintFooter(false);
    //set auto page breaks
    $pdf->SetAutoPageBreak(FALSE, $this->yBrim);
    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
    //set some language-dependent strings
    $pdf->setLanguageArray($l); 
    // set font
    $pdf->SetFont($fonts, '', $this->FontSize);
    // add a page
    $pdf->AddPage();
    $this->pdf=$pdf;
  }                                 // function initPDF ($fonts) {
  /* ************************************************************************ */
  /**
   * Close PDF file and create output file
   */
  function Output () {
    // reset pointer to the last page
    $this->pdf->lastPage();
    //Close and output PDF document
    $this->pdf->Output($this->allPersonsRev[1]. '_' . $this->genShow . '-gen.pdf', 'I');
  }                                     // function Output () {
  /* ************************************************************************ */
  /**
   * Format age of parents in HTML
   *
   * global variables:
   * $SHOW_PARENTS_AGE;
   * 
   * @param string $pid         string child ID
   * @return string with HTML code
   */
  function parents_age($pid) {
    global $SHOW_PARENTS_AGE;

    $html='';
    if ($SHOW_PARENTS_AGE) {
      $person=Person::getInstance($pid);
      $families=$person->getChildFamilies();
      // Multiple sets of parents (e.g. adoption) cause complications, so ignore.
      $birth_date=$person->getBirthDate();
      if ($birth_date->isOK() && count($families)==1) {
	$family=current($families);
	// Allow for same-sex parents
	foreach (array($family->getHusband(), $family->getWife()) as $parent) {
	  if ($parent && $age=GedcomDate::GetAgeYears($parent->getBirthDate(), $person->getBirthDate())) {
	    $html.=$this->sexImage($parent->getSex()).$age;
	  }                             // if ($parent && ...
	}                               // foreach (array($family->getHusband(), ... 
      }                                 // if ($birth_date->isOK() && count($families)==1) {
    }                                   // if ($SHOW_PARENTS_AGE) {
    return $html;
  }                                     // function parents_age($pid) {
  /* ************************************************************************ */
  /**
   * Display a Person in an TCPDF::MultiCell
   *
   * global variables:
   *  $SHOW_ID_NUMBERS, $PEDIGREE_SHOW_GENDER
   *  $DEBUG
   *
   * @param string $boxSize      Size of Box         /(child|child)?_(full|short)_(ref)?/
   * @param float  $x            x Position of Box
   * @param float  $y            y Position of Box
   * @param float  $xWidth       Box width x direction
   * @param float  $yWidth       Box width y direction
   * @param string $id           person id from GEDCOM File
   * @param Person $l_person     Person to display
   * @param string $nr=""        number relativ to first person
   * @return empty
   */
  function pdfPerson($boxSize, $x, $y, $xWidth, $yWidth, $id, $l_person, $nr="") {
    global $SHOW_ID_NUMBERS, $SHOW_HIGHLIGHT_IMAGES, $PEDIGREE_SHOW_GENDER;
    global $DEBUG;
    //    return;
    if ($DEBUG & 32) {
      if ($this->pageorient=='portrait') {
	$addstr=" y=". (($y-$this->yBrim-$this->headerHeight)/($this->yWidth+$this->ySpace)-0.5);
      } else {                          // if ($this->pageorient=='portrait') {
	$addstr=" x=". (($x-$this->xBrim)/($this->xWidth+$this->xSpace)-0.5);
      }                                 // else if ($this->pageorient=='portrait') {
    } else {                            // if ($DEBUG & 32) {
      $addstr='';
    }                                   // else if ($DEBUG & 32) {
    if ($this->pageorient=='portrait') {
      $y-=($yWidth/2.0);
    } else {                            // if ($this->pageorient=='portrait') {
      $x-=($xWidth/2.0);
      if (preg_match('/short|ref/', $boxSize)) {
	$y+=$yWidth*(1-$this->scaleNextGen);
	$yWidth-=($yWidth*(1-$this->scaleNextGen));
      }                                 // if (preg_match('/short|ref/', $boxSize)) {
    }                                   // else if ($this->pageorient=='portrait') {
    // sex information
    $sex=$l_person->getSex();
    if ($PEDIGREE_SHOW_GENDER) {
      $imgSex=$this->sexImage($sex);
    } else {                            // if ($PEDIGREE_SHOW_GENDER) {
      $imgSex='';
    }                                   // else if ($PEDIGREE_SHOW_GENDER) {
    // marriage information displayed in case of person is female and no child
    if (!preg_match('/child/',$boxSize) && isset($this->families[floor($nr/2)]) && ($sex == 'F')) {
      $i=floor($nr/2);
      $mdate=$this->families[$i]->getMarriageDate()->Display();
      $mplace=get_place_short($this->families[$i]->getMarriagePlace()) ;
      $what=(strlen($mdate) > 7 ? 2:0)  /* XXXX why >7 ?? */ + (strlen($mplace)>0?1:0);
      if ($what) {
	$marrstr=$this->getEventImg('marr',$l_person).$mdate.($what%2 && $what&2 ? ' -- ' : '') . $mplace;
      } else {                          // if ($what) {
	$marrstr='';
      }                                 // else if ($what) {
    } else {
      $marrstr='';
    }                                   // else if (!preg_match('/child/',$boxSize) && ... 
    if (preg_match('/full/', $boxSize) && ! preg_match('/ref/', $boxSize)) {        /// XXX not efficent
      //Birth information
      $bdate=$l_person->getBirthDate();
      $Dstr=$bdate!=null ? $bdate->Display().' ' :'';
      $Pstr=get_place_short($l_person->getBirthPlace());
      $what=(strlen($Dstr) > 7 ? 2:0)  /* XXXX why >7 ?? */ + (strlen($Pstr)>0?1:0);
      $birth=$this->getEventImg('birt', $l_person) . $Dstr . $this->parents_age($id) .
	($what%2 && $what &2 ? ' -- ':'') . // date AND place exists
	($what % 2 ? $Pstr : '') . ($what >0 ? '; ':'');
      // Death information
      if ($l_person->isDead()) {
	$ddate=$l_person->getDeathDate();
	$Dstr=$bdate!=null ? $ddate->Display().' ' :'';
	$Pstr=get_place_short($l_person->getDeathPlace());
	$age=GedcomDate::GetAgeYears($bdate, $ddate);
	$what=(strlen($Dstr) > 7 ? 2:0)  /* XXXX why >7 ?? */ + (strlen($Pstr)>0?1:0);
	$img=$this->getEventImg('deat', $l_person);
	$death=$img . ($what > 0 ?    // date OR place exists
		       ($what & 2 ?   // date exists
			$Dstr .($age ? '('.i18n::translate('Age').' '.$age.') ':''):'') .
		       ($what%2 && $what &2 ? ' -- ':'') . // date AND place exists
		       $Pstr: i18n::translate('yes') ). ';';
      } else {                          // if ($l_person->isDead()) {
	$death='';
      }                                 // else if ($l_person->isDead()) {

      if (preg_match('/child/',$boxSize)) {
	if ($this->pageorient=='portrait') {
	  $x+=$xWidth*(1-$this->scaleNextGen);
	  $xWidth*=$this->scaleNextGen;
	} else {                        // if ($this->pageorient=='portrait') {
	  $y+=$yWidth*(1-$this->scaleNextGen);
	  $yWidth*=$this->scaleNextGen;
	}                               // else if ($this->pageorient=='portrait') {
      }                                 // if (preg_match('/child/',$boxSize)) {

      $this->personBorder($boxSize, $x, $y,  $xWidth, $yWidth, $sex, $id, $nr);

      if ($SHOW_HIGHLIGHT_IMAGES && ($a=$l_person->findHighlightedMedia()) && (($xWidth/$yWidth) > 3)) {
	if (isset($a['thumb'])){
	  $img=$a['thumb'];
	} else {                        // if (isset($a['thumb'])){
	  $img=$a['file'];
	}                               // else if (isset($a['thumb'])){
	$space=min($this->xSpace,$this->ySpace)*0.5;
	$imgSize=getimagesize($img);
	$s=min($xWidth,$yWidth)/max(1,$imgSize[1]/$imgSize[0]);
	$this->pdf->Image($img, $x+$space, $y+$space, $s-2*$space,($s-2*$space)*$imgSize[1]/$imgSize[0]);
	$xWidth-=$s;
	$x+=$s;
      }                                 // if ($SHOW_HIGHLIGHT_IMAGES && ($a= ...

// int MultiCell( float $w, float $h, string $txt, [mixed $border = 0], [string $align = 'J'], [int $fill = 0], [int $ln = 1], [float $x = ''], [float $y = ''],
//    [boolean $reseth = true], [int $stretch = 0], [boolean $ishtml = false], [boolean $autopadding = true], [float $maxh = 0])

      $this->pdf->MultiCell($xWidth, $yWidth, '<font size="+1"><b>'.$nr . 
			    $this->underlinestar($l_person->getFullName()) .'</b></font>'. $imgSex .' ' .
			    ($SHOW_ID_NUMBERS?'('.$id.')':'') .'<br />'.
			    $birth . $death . $marrstr. $addstr,
			    0, 'L', 0, 0, $x-.5 ,$y-.6 , false, 0,true,true, $yWidth); /// XXX Why offset
    } else {                            // if (preg_match('/full/', $boxSize) && ! preg_match('/ref/', $boxSize)) {
      //Birth information
      $Dstr=$l_person->getBirthYear();
      $Pstr='';
      $what=(isset($Dstr) ? 2:0);
      $birth=$this->getEventImg('birt', $l_person) . $Dstr . $this->parents_age($id) .
	($what%2 && $what &2 ? ' -- ':'') . // date AND place exists
	($what % 2 ? $Pstr : '') . ($what >0 ? '; ':'');
      //Death information
      if ($l_person->isDead()) {
	$Dstr=$l_person->getDeathYear();
	$Pstr='';
	$age=GedcomDate::GetAgeYears($l_person->getBirthDate(), $l_person->getDeathDate());
	$what=(isset($Dstr)? 2:0);
	$img=$this->getEventImg('deat', $l_person);
	$death=$img . ($what > 0 ?    // date OR place exists
		       ($what & 2 ?   // date exists
			$Dstr .($age ? ' ('.i18n::translate('Age').' '.$age.') ':''):'') .
		       ($what%2 && $what &2 ? ' -- ':'') . // date AND place exists
		       $Pstr: i18n::translate('yes') );
      } else {                          // if ($l_person->isDead()) {
	$death='';
      }                                 // else if ($l_person->isDead()) {
      if ($this->pageorient=='portrait') {
	if (preg_match('/child/', $boxSize)) {
	  if (preg_match('/ref/', $boxSize)) {
	    $x+=$xWidth*(1-$this->scaleNextGen*(1-(1-$this->scaleNextGen)/2));
	    $xWidth*=(1-(1-$this->scaleNextGen)/2);  // second scaling a little bit less
	  } else {                      // if (preg_match('/ref/', $boxSize)) {
	    $x+=$xWidth*(1-$this->scaleNextGen);
	  }                             // else if (preg_match('/ref/', $boxSize)) {
	}                               // if (preg_match('/child/', $boxSize)) {
	$xWidth*=$this->scaleNextGen;
      } else {                          // if ($this->pageorient=='portrait') {
	if (preg_match('/child_.*_ref/', $boxSize)) {
	  $yWidth*=(1-(1-$this->scaleNextGen)/2);  // second scaling a little bit less
	}                               // if (preg_match('/child_.*_ref/', $boxSize)) {
      }                                 // else if ($this->pageorient=='portrait') {
      $ref=false;
      if (preg_match('/ref/', $boxSize)) {$ref=true;}
      
      $this->personBorder($boxSize, $x, $y,  $xWidth, $yWidth, $sex, $id, $nr);
      $this->pdf->MultiCell($xWidth, $yWidth, '<font size="+1"><b>'.$nr .
			    $this->underlinestar($l_person->getFullName()) .'</b></font> '. (!$ref ? $imgSex :'').
			    ($SHOW_ID_NUMBERS?'('.$id.')':'') . '<br />'  .
			    ($birth || $death? $birth . $death . '<br />': '') .
			    (isset($marrstr) && strlen($marrstr)>0? $marrstr . '<br />': '') .
			    ($ref ? i18n::translate('see person %s', $this->allPersons[$id]) : '') . $addstr,
			    0, 'L', 0, 0, $x ,$y , false, 0, true, false, $yWidth);
    }                                   // else if (preg_match('/full/', $boxSize) && ! preg_match('/ref/', $boxSize)) {
  }                                     // function pdfPerson($boxSize, $x, $y, ...
  /* ************************************************************************ */
  /**
   * HTML output from a person

   */
  function personBorder($boxSize, $x, $y,  $xWidth, $yWidth, $sex, $id, $nr) {
    $this->setBGColor($sex);

    if (preg_match("/^child/i", $boxSize)) {
      $type='child';
    } else {                            // if (preg_match("/^child/i", $boxSize)) {
      if (isset($this->allPersons[$id]) && isset($this->leavePositions[$this->allPersons[$id]]) &&
	  ! isset($this->families[$this->allPersons[$id]])) {
	$type='leave';
      } else {                          // if (isset($this->allPersons[$id]) && ...
	$type='inside';
      }                                 // else if (isset($this->allPersons[$id]) && ...
    }                                   // else if (preg_match("/^child/i", $boxSize)) {
    $this->pdf->StartTransform();
    $this->pdf->Translate($x,$y+$yWidth*0.5);
    if ($this->pageorient=='landscape') {
      $this->pdf->Rotate(90, $xWidth*0.5,0);
      $this->pdf->Scale(100.0*$yWidth/$xWidth,100.0*$xWidth/$yWidth, $xWidth*0.5,0);
    }                                   // if ($this->pageorient=='landscape') {

    $this->pdf->Polycurve(0,0, $this->pointsBorder[$boxSize][$type],'DF', $this->borderStyle[$sex]);
    $this->pdf->MirrorV(0);
    $this->pdf->Polycurve(0,0, $this->pointsBorder[$boxSize][$type],'DF', $this->borderStyle[$sex]);
    $this->pdf->StopTransform();
//    $this->pdf->Line($x,$y,$x+$xWidth,$y+$yWidth);
//    $this->pdf->Line($x+$xWidth,$y,$x,$y+$yWidth);
//    $this->pdf->Text($x,$y, $boxSize . ' '.  $type);
  }                                     // function personBorder($how, $x, $y,  $xWidth, $yWidth, $sex, $id) {
  /* ************************************************************************ */
  /**
   * HTML output from a person
   * 
   * global variables:
   * $SHOW_ID_NUMBERS, $DEBUG 
   *
   * @param string $how        'full'|'ref'
   * @param string $ref        GEDCOM xref
   * @param Person $l_person   which person Person::getInstance()
   * @param int $i             number of person
   * @param string $child=''   string additionaly displayed for siblings
   *
   * @return nothing
   */
  function printPersonHtml($how, $ref, $l_person, $i, $child='') {
    global $SHOW_ID_NUMBERS, $DEBUG;

    echo '<tr><td class="list_value_wrap" align="';
    echo ($child ? 'right':'left');
    echo '">'. $i. $child. '</td>';
    echo '<td class="list_value_wrap" align="center">'.(floor(log($i)/log(2))+1).'</td>';
    if ($how =="full") {
      $date=$l_person->getBirthDate();
      echo '<td class="list_value_wrap" align="left">'. $l_person->getFullName(). $l_person->getSexImage(). 
	($SHOW_ID_NUMBERS? ' '.$ref:'') . '</td>'.
	'<td class="list_value_wrap" align="left">'.$this->getEventImg('birt',$l_person). $date->Display().
	'</td><td class="list_value_wrap" align="left">'.get_place_short($l_person->getBirthPlace()).'</td>'.
	"\n";
      echo '<td class="list_value_wrap" align="left">'.$this->parents_age($ref). '</td>';
      if ($l_person->isDead()) {
	    $date=$l_person->getDeathDate();
	    echo '<td class="list_value_wrap" align="left">'. $this->getEventImg('deat', $l_person). $date->Display().
	    '</td><td class="list_value_wrap" align="left">' .get_place_short($l_person->getDeathPlace()).'</td>';
      } else {                          // if ($l_person[$i]->isDead()) {
	echo '<td></td><td></td>';
      }
    } else {                            // if ($how=="full") {
      echo '<td class="list_value_wrap" align="right">' .
		i18n::translate('see person %s', i18n::translate('#%d', $this->allPersons[$ref]).' '.$l_person->getFullName()) . $l_person->getSexImage(). 
	($SHOW_ID_NUMBERS? ' '.$ref:'').'</td>';
      echo '<td colspan="2" class="list_value_wrap" align="left">' . $this->getEventImg('birt', $l_person). $l_person->getBirthYear() . '</td>';
      echo '<td colspan="1" class="list_value_wrap" align="center">' . $this->parents_age($ref) . '</td>';
      echo '<td colspan="2" class="list_value_wrap" align="left">' . $this->getEventImg('deat', $l_person). $l_person->getDeathYear() . '</td>';
    }                                   // else if ($how=="full") {
    if ($DEBUG) {
      echo '<td>d='. $DEBUG.'</td>';
      if ($DEBUG & 8) {
	if (isset($this->leaves[$i]) && $this->leaves[$i]) {
	  echo "<td>No Parents $this->leaves[$i]/$this->genShow i=". ($i*(2<<($this->genShow-$this->leaves[$i]))/2). "</td>";
	} else {                        // if (isset($this->leaves[$i]) && $this->leaves[$i]) {
	  echo "<td></td>";
	}                               // else if (isset($this->leaves[$i]) && $this->leaves[$i]) {
      }                                 // if ($DEBUG & 8) {
      if ($DEBUG & 128) {
	if (isset($this->children[$i]) && !$child) {
	  $n=(count($this->children[$i])-1);
	  echo '<td>'  . ($this->children[$i][$n]) .'/'. $n .  ' ; ';
	  print_r($this->children[$i]);
	  echo '</td>';
	} else {                        // if (isset($this->children[$i]) && !$child) {
	  echo "<td></td>";
	}                               // else if (isset($this->children[$i]) && !$child) {
      }                                 // if ($DEBUG & 128) {
      if ($DEBUG & 16) {
	if (isset($this->positions[$i])) {
	  echo '<td>p='.$this->positions[$i].'</td>'."\n";
	} else {                        // if (isset($this->positions[$i])) {
	  echo '<td align="right"><font color="red">no y</td>'."\n";
	}                               // else if (isset($this->positions[$i])) {
      }                                 // if ($DEBUG & 16) {
    }                                   // if ($DEBUG)
    echo "</tr>\n";
    return;
  }                                     // function printPersonHtml
  /* ************************************************************************ */
  /**
   * Set fill collor of TCPDF for different sex
   *
   * @param string $sex         Sex of person
   * @return empty
   */
  function setBGColor ($sex) {
    if (isset($this->fillColor[$sex])) {
      $this->pdf->SetFillColorArray($this->fillColor[$sex]);
    } else {
      $this->pdf->SetFillColorArray($this->fillColor['U']);
    }
  }                                     // function setBGColor ($sex) {
  /* ************************************************************************ */
  /**
   * Set page orientation
   * the sizes of the boxes and spaces are also set
   *
   * @param string $pageorient
   * @return empty
   */
  function setOrientation($pageorient) {
    $this->pageorient=$pageorient;
    if ($pageorient == 'portrait') {
      $this->xSpace=4;
      $this->xWidth=47;
      $this->ySpace=1.5;
      $this->yWidth=14;
      $this->scaleSameGen=array('full'=>.8,'short'=>.6);
      $this->scaleNextGen=0.9;
      $this->yOffset=$this->headerHeight;
      $H=$this->yWidth;
      $L=$this->xWidth;      
    } else {                            // if ($pageorient == 'portrait') {
      $this->xSpace=1;
      $this->xWidth=21;
      $this->ySpace=4;
      $this->yWidth=25;
      $this->scaleNextGen=0.85;
      $this->scaleSameGen=array('full'=>.9,'short'=>.75);
      $this->yOffset=$this->headerHeight;
      $H=$this->xWidth;
      $L=$this->yWidth;
    }                                   // else if ($pageorient == 'portrait') {
    $wH=$H/6;
    $wL=$L/6;
    $wS=min($this->xSpace, $this->ySpace)/4;
    $how=array('full'=>array(1.0,1.0),'full_ref'=>array($this->scaleNextGen,1.0)
	       ,'child_full'     =>array($this->scaleNextGen,$this->scaleSameGen['full'])
	       ,'child_full_ref' =>array($this->scaleNextGen+($this->scaleNextGen-1)*.5,$this->scaleSameGen['full'])
	       ,'child_short'    =>array($this->scaleNextGen,$this->scaleSameGen['short'])
	       ,'child_short_ref'=>array($this->scaleNextGen+($this->scaleNextGen-1)*.5,$this->scaleSameGen['short'])
	       );
      
    $this->pointsBorder=array();
    foreach ($how as $key=>$value) {
      $H=$this->yWidth*$value[$pageorient == 'portrait'?1:0];
      $L=$this->xWidth*$value[$pageorient == 'portrait'?0:1];
      $this->pointsBorder[$key]=
      array('leave'=>array(array(-$wS,-$wH,            -$wS,-$H/2+$wS,     0,-$H/2)
			   ,array($wS,-$H/2-$wS,       $L/2-$wL,-$H/2-$wS, $L/2,-$H/2)
			   ,array($L/2+$wL,-$H/2-$wS,  $L-$wS,-$H/2-$wS,   $L,-$H/2)
			   ,array($L,-$H/2+$wS*3,      $L,0,               $L+3*$wS,0)),
	    'inside'=>array(array(0,-$wH,              0,-$H/2+$wH,        -$wS,-$H/2-$wS)
			    ,array($wL,-$H/2,          $L-$wL,-$H/2,       $L+$wS,-$H/2-$wS)
			    ,array($L,-$H/2+$wH,       $L,+$wH,            $L,0)),
	    'child'=>array(array(0,-$wH,               -$wS,-$H/2.+$wS,    0,-$H/2.)
			   ,array($wS,(-$H-$wS)/2.,    $L-$wS,(-$H-$wS)/2.,$L,-$H/2.)
			   ,array($L+$wS,-$H/2.+$wS,   $L,+$wH,            $L,0)
			   )
	    );
    }                                   // foreach ($how as $key=>$value) {  
  }                                     // function setOrientation($pageorient) {
  /* ************************************************************************ */
  /**
   * Set page size and title
   * @param string $output         type of output
   * @return empty
   */
  function setPageSizeandTitle ($output) {
    if ($output=="PDF") {
      if ($this->pageorient == 'portrait') {
	$h=2*$this->yBrim+$this->headerHeight+(max($this->positions)+.5)*($this->yWidth+$this->ySpace);
	$w=2*$this->xBrim+($this->genShow)*($this->xWidth+$this->xSpace);
	$dO='L';
	$dX=$this->xBrim;
	$dY=max($this->yBrim+(max($this->positions)+.5)*($this->yWidth+$this->ySpace)-$this->FontSize/2.5,0)+$this->headerHeight;
      } else {                         // if ($pageorient == 'portrait') {
	$h=2*$this->yBrim+$this->headerHeight+($this->genShow)*($this->yWidth+$this->ySpace);
	$w=2*$this->xBrim+(max($this->positions)+0.5)*($this->xWidth+$this->xSpace);
	$dO='R';
	$dX=$this->xBrim+(max($this->positions)-.5)*($this->xWidth+$this->xSpace)-$this->xSpace;
	$dY=$this->yBrim+$this->genShow* ($this->yWidth+$this->ySpace)+$this->headerHeight-$this->FontSize/2.5;
      }                                 // else if ($pageorient == 'portrait') {
      $this->pdf->setPageFormat(array($w, $h),'P');
      //actual date
      $this->pdf->MultiCell ($this->xWidth, $this->FontSize/2.5,
			     timestamp_to_gedcom_date(mktime(0,0,0,date("m"),date("d"),date("Y")))->Display(),
			     0, $dO, 0, 0, $dX, $dY, true, 0,true,false);
      //title
      $this->pdf->MultiCell ($w ,$this->FontSize*2, "<h2>".
			     i18n::translate('%1$s: %2$d Generation Pedigree Chart', $this->underlinestar($this->person[1]->getFullName()), $this->genShow). 
				 "</h2>",
			     0, 'C', 0, 0, 0, $this->yBrim, true, 0, true, false, $this->FontSize*2);
    } else {                            // if ($output=="PDF") {
      $this->FontSize=10;
      print_header(" Pedigree Single Page");
      echo '<br /><center> <table border="0">'."\n";
      echo ' <tr><th class="list_label">&nbsp;</th>
  <th class="list_label">'.i18n::translate('Generation').'</th>
  <th class="list_label">' . i18n::translate('Name') . '</th>
  <th class="list_label" colspan="3">' . translate_fact('BIRT') .'</th>
  <th class="list_label" colspan="2">' . translate_fact('DEAT') .'</th>
 </tr>'."\n";
    }                                   // else if ($output=="PDF") {
  }                                     // function setPageSizeandTitle ($pageorient) {
  /* ************************************************************************ */
  /**
   * create HTML output of sexImage in a way which TCPDF can handle 
   *
   * @param string $sex
   * @return string with HTML code
   */
  function sexImage($sex) {
	global $WT_IMAGE_DIR, $WT_IMAGES;
    if ($sex=="M") {
      $imgSex=' <img src="'.$WT_IMAGE_DIR.'/'.$WT_IMAGES["sex"]["small"].'"  alt="M"   align="bottom" height="'.(0.9*$this->FontSize).'" >';
    } elseif ($sex=="F") {              // if ($sex=="M") {
      $imgSex=' <img src="'.$WT_IMAGE_DIR.'/'.$WT_IMAGES["sexf"]["small"].'" alt="F"  align="bottom" height="'.(0.9*$this->FontSize).'" >';
    } else {                            // elseif ($sex=="F") {
      $imgSex=' <img src="'.$WT_IMAGE_DIR.'/'.$WT_IMAGES["sexn"]["small"].'" alt="U" align="bottom" height="'.(0.9*$this->FontSize).'" >';
    }                                   // else  elseif ($sex=="F") {
    return $imgSex;
  }                                     // function sexImage($sex) {
  /* ************************************************************************ */
  /**
   * Replace CSS class "starredname" by simple "<u></u>" which TCPDF can handle
   *
   * @param string $name
   * @return string with HTML code
   */
  function underlinestar($name) {
    return preg_replace('#<span class="starredname">([^<]*)</span>#i','<u>\1</u>', $name); 
  }                                     // function underlinestar($name) {
  /* ************************************************************************ */

}
?>