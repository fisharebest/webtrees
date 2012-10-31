<?php
//	Controller for the compact chart
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Controller_Compact extends WT_Controller_Chart {
	// Data for the view
	public $show_thumbs=false;

	// Date for the controller
	private $treeid=array();

	public function __construct() {
		parent::__construct();

		// Extract the request parameters
		$this->show_thumbs=safe_GET_bool('show_thumbs');

		if ($this->root && $this->root->canDisplayName()) {
			$this->setPageTitle(
				/* I18N: %s is a person's name */
			WT_I18N::translate('Compact tree of %s', $this->root->getFullName())
		);
			$this->treeid=ancestry_array($this->rootid, 5);
		} else {
			$this->setPageTitle(WT_I18N::translate('Compact tree'));
		}
	}
	
	function sosa_person($n) {
		global $SHOW_HIGHLIGHT_IMAGES;

		$text = "";
		$pid = $this->treeid[$n];

		if ($pid) {
			$indi=WT_Person::getInstance($pid);
			$name=$indi->getFullName();
			$addname=$indi->getAddName();

			if ($this->show_thumbs && $SHOW_HIGHLIGHT_IMAGES) {
				$object=find_highlighted_object($indi);
				$birth_date=$indi->getBirthDate();
				$death_date=$indi->getDeathDate();
				$img_title=$name.' - '.$birth_date->Display(false).' - '.$death_date->Display(false);
				$img_id='box-'.$pid;
				if (!empty($object)) {
					$mediaobject=WT_Media::getInstance($object['mid']);
					$text=$mediaobject->displayMedia(array('display_type'=>'pedigree_person','img_id'=>$img_id,'img_title'=>$img_title));
				} else {
					$text=display_silhouette(array('sex'=>$indi->getSex(),'display_type'=>'pedigree_person','img_id'=>$img_id,'img_title'=>$img_title)); // may return ''
				}
			}

			$text .= '<a class="name1" href="'.$indi->getHtmlUrl().'">';
			$text .= $name;
			if ($addname) $text .= '<br>' . $addname;
			$text .= '</a>';
			$text .= '<br>';
			if ($indi->canDisplayDetails()) {
				$text.='<span class="details1">'.$indi->getLifeSpan().'</span>';
			}
		}

		// -- empty box
		if (empty($text)) {
			$text = '&nbsp;';
		}
		// -- box color
		$isF="";
		if ($n==1) {
			if ($indi->getSex()=='F') {
				$isF="F";
			}
		} elseif ($n%2) {
			$isF="F";
		}
		// -- box size
		if ($n==1) {
			$text='<td class="person_box'.$isF.'" style="text-align:center; vertical-align:top;">'.$text.'</td>';
		} else {
			$text='<td class="person_box'.$isF.'" style="text-align:center; vertical-align:top;" width="15%">'.$text.'</td>';
		}
		return $text;
	}

	function sosa_arrow($n, $arrow_dir) {
		global $TEXT_DIRECTION;

		$pid = $this->treeid[$n];

		$arrow_swap = array("l"=>"0", "r"=>"1", "u"=>"2", "d"=>"3");

		$arrow_dir = substr($arrow_dir,0,1);
		if ($TEXT_DIRECTION=="rtl") {
			if ($arrow_dir=="l") {
				$arrow_dir="r";
			} elseif ($arrow_dir=="r") {
				$arrow_dir="l";
			}
		}

		if ($pid) {
			$indi=WT_Person::getInstance($pid);
			$title=WT_I18N::translate('Compact tree of %s', $indi->getFullName());
			$text = '<a class="icon-'.$arrow_dir.'arrow" title="'.strip_tags($title).'" href="?rootid='.$pid;
			if ($this->show_thumbs) $text .= "&amp;show_thumbs=".$this->show_thumbs;
			$text .= "\"></a>";
		} else {
			$text = '<i class="icon-'.$arrow_dir.'arrow"></i>';
		}

		return $text;
	}
}
