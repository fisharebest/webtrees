<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
namespace Fisharebest\Webtrees\Module\ClippingsCart;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsExport;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\User;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

/**
 * The clippings cart.
 */
class ClippingsCartController {
	/** @var string Data to be downloaded. */
	private $download_data;

	/** @var string[] List of files to include */
	private $media_list;

	/** @var string The type of the record being added */
	public $type;

	/** @var string The XREF of the record being added */
	public $id;

	/** @var string Whether to include media files for media objects */
	private $IncludeMedia;

	/** @var string The privacy level to apply to the download */
	private $privatize_export;

	/** @var string Whether to download as ZIP file */
	private $Zip;

	/** @var int The number of ancestor generations (individuals) to add */
	public $level1;

	/** @var int The number of ancestor generations (families) to add */
	public $level2;

	/** @var int The number of descendent generations to add */
	public $level3;

	/** @var string[][] The contents of the cart */
	private $cart;

	/**
	 * Create the clippings controller
	 */
	public function __construct() {
		global $WT_TREE;

		// Our cart is an array of items in the session
		$this->cart = Session::get('cart', []);

		if (!array_key_exists($WT_TREE->getTreeId(), $this->cart)) {
			$this->cart[$WT_TREE->getTreeId()] = [];
		}

		$this->action           = Filter::get('action');
		$this->id               = Filter::get('id');
		$convert                = Filter::get('convert', 'yes|no', 'no');
		$this->privatize_export = Filter::get('privatize_export', 'none|visitor|user|gedadmin', 'visitor');
		$this->level1           = Filter::getInteger('level1');
		$this->level2           = Filter::getInteger('level2');
		$this->level3           = Filter::getInteger('level3');
		$others                 = Filter::get('others');
		$this->type             = Filter::get('type');

		if (($this->privatize_export === 'none' || $this->privatize_export === 'none') && !Auth::isManager($WT_TREE)) {
			$this->privatize_export = 'visitor';
		}
		if ($this->privatize_export === 'user' && !Auth::isMember($WT_TREE)) {
			$this->privatize_export = 'visitor';
		}

		if ($this->action === 'add') {
			if (empty($this->type) && !empty($this->id)) {
				$obj = GedcomRecord::getInstance($this->id, $WT_TREE);
				if ($obj) {
					$this->type = $obj::RECORD_TYPE;
				} else {
					$this->type   = '';
					$this->id     = '';
					$this->action = '';
				}
			} elseif (empty($this->id)) {
				$this->action = '';
			}
			if (!empty($this->id) && $this->type !== 'FAM' && $this->type !== 'INDI' && $this->type !== 'SOUR') {
				$this->action = 'add1';
			}
		}

		if ($this->action === 'add1') {
			$obj = GedcomRecord::getInstance($this->id, $WT_TREE);
			$this->addClipping($obj);
			if ($this->type === 'SOUR') {
				if ($others === 'linked') {
					foreach ($obj->linkedIndividuals('SOUR') as $indi) {
						$this->addClipping($indi);
					}
					foreach ($obj->linkedFamilies('SOUR') as $fam) {
						$this->addClipping($fam);
					}
				}
			}
			if ($this->type === 'FAM') {
				if ($others === 'parents') {
					$this->addClipping($obj->getHusband());
					$this->addClipping($obj->getWife());
				} elseif ($others === 'members') {
					$this->addFamilyMembers(Family::getInstance($this->id, $WT_TREE));
				} elseif ($others === 'descendants') {
					$this->addFamilyDescendancy(Family::getInstance($this->id, $WT_TREE));
				}
			} elseif ($this->type === 'INDI') {
				if ($others === 'parents') {
					foreach (Individual::getInstance($this->id, $WT_TREE)->getChildFamilies() as $family) {
						$this->addFamilyMembers($family);
					}
				} elseif ($others === 'ancestors') {
					$this->addAncestorsToCart(Individual::getInstance($this->id, $WT_TREE), $this->level1);
				} elseif ($others === 'ancestorsfamilies') {
					$this->addAncestorsToCartFamilies(Individual::getInstance($this->id, $WT_TREE), $this->level2);
				} elseif ($others === 'members') {
					foreach (Individual::getInstance($this->id, $WT_TREE)->getSpouseFamilies() as $family) {
						$this->addFamilyMembers($family);
					}
				} elseif ($others === 'descendants') {
					foreach (Individual::getInstance($this->id, $WT_TREE)->getSpouseFamilies() as $family) {
						$this->addClipping($family);
						$this->addFamilyDescendancy($family, $this->level3);
					}
				}
				uksort($this->cart[$WT_TREE->getTreeId()], [$this, 'compareClippings']);
			}
		} elseif ($this->action === 'remove') {
			unset($this->cart[$WT_TREE->getTreeId()][$this->id]);
		} elseif ($this->action === 'empty') {
			$this->cart[$WT_TREE->getTreeId()] = [];
		} elseif ($this->action === 'download') {
			// Create a new/empty .ZIP file
			$temp_zip_file  = tempnam(sys_get_temp_dir(), 'webtrees-zip-');
			$zip_filesystem = new Filesystem(new ZipArchiveAdapter($temp_zip_file));

			// Media file prefix
			$path = $WT_TREE->getPreference('MEDIA_DIRECTORY');

			// GEDCOM file header
			$filetext = FunctionsExport::gedcomHeader($WT_TREE);

			// Include SUBM/SUBN records, if they exist
			$subn =
				Database::prepare("SELECT o_gedcom FROM `##other` WHERE o_type=? AND o_file=?")
				->execute(['SUBN', $WT_TREE->getTreeId()])
				->fetchOne();
			if ($subn) {
				$filetext .= $subn . "\n";
			}
			$subm =
				Database::prepare("SELECT o_gedcom FROM `##other` WHERE o_type=? AND o_file=?")
				->execute(['SUBM', $WT_TREE->getTreeId()])
				->fetchOne();
			if ($subm) {
				$filetext .= $subm . "\n";
			}

			switch ($this->privatize_export) {
				case 'gedadmin':
					$access_level = Auth::PRIV_NONE;
					break;
				case 'user':
					$access_level = Auth::PRIV_USER;
					break;
				case 'visitor':
					$access_level = Auth::PRIV_PRIVATE;
					break;
				case 'none':
				default:
					$access_level = Auth::PRIV_HIDE;
					break;
			}

			foreach (array_keys($this->cart[$WT_TREE->getTreeId()]) as $xref) {
				$object = GedcomRecord::getInstance($xref, $WT_TREE);
				// The object may have been deleted since we added it to the cart....
				if ($object) {
					$record = $object->privatizeGedcom($access_level);
					// Remove links to objects that aren't in the cart
					preg_match_all('/\n1 ' . WT_REGEX_TAG . ' @(' . WT_REGEX_XREF . ')@(\n[2-9].*)*/', $record, $matches, PREG_SET_ORDER);
					foreach ($matches as $match) {
						if (!array_key_exists($match[1], $this->cart[$WT_TREE->getTreeId()])) {
							$record = str_replace($match[0], '', $record);
						}
					}
					preg_match_all('/\n2 ' . WT_REGEX_TAG . ' @(' . WT_REGEX_XREF . ')@(\n[3-9].*)*/', $record, $matches, PREG_SET_ORDER);
					foreach ($matches as $match) {
						if (!array_key_exists($match[1], $this->cart[$WT_TREE->getTreeId()])) {
							$record = str_replace($match[0], '', $record);
						}
					}
					preg_match_all('/\n3 ' . WT_REGEX_TAG . ' @(' . WT_REGEX_XREF . ')@(\n[4-9].*)*/', $record, $matches, PREG_SET_ORDER);
					foreach ($matches as $match) {
						if (!array_key_exists($match[1], $this->cart[$WT_TREE->getTreeId()])) {
							$record = str_replace($match[0], '', $record);
						}
					}
					$savedRecord = $record; // Save this for the "does this file exist" check
					if ($convert === 'yes') {
						$record = utf8_decode($record);
					}
					switch ($object::RECORD_TYPE) {
						case 'INDI':
						case 'FAM':
							$filetext .= $record . "\n";
							$filetext .= "1 SOUR @WEBTREES@\n";
							$filetext .= '2 PAGE ' . WT_BASE_URL . $object->url() . "\n";
							break;
						case 'SOUR':
							$filetext .= $record . "\n";
							$filetext .= '1 NOTE ' . WT_BASE_URL . $object->url() . "\n";
							break;
						case 'OBJE':
							// Add the file to the archive
							foreach ($object->mediaFiles() as $media_file) {
								if (file_exists($media_file->getServerFilename())) {
									$fp = fopen($media_file->getServerFilename(), 'r');
									$zip_filesystem->writeStream($path . $media_file->filename(), $fp);
									fclose($fp);
								}
							}
							$filetext .= $record . "\n";
							break;
						default:
							$filetext .= $record . "\n";
							break;
					}
				}
			}

			// Create a source, to indicate the source of the data.
			$filetext .= "0 @WEBTREES@ SOUR\n1 TITL " . WT_BASE_URL . "\n";
			$author = User::find($WT_TREE->getPreference('CONTACT_EMAIL'));
			if ($author !== null) {
				$filetext .= '1 AUTH ' . $author->getRealName() . "\n";
			}
			$filetext .= "0 TRLR\n";

			// Make sure the preferred line endings are used
			$filetext = preg_replace("/[\r\n]+/", Gedcom::EOL, $filetext);

			if ($convert === 'yes') {
				$filetext = str_replace('UTF-8', 'ANSI', $filetext);
				$filetext = utf8_decode($filetext);
			}

			// Finally add the GEDCOM file to the .ZIP file.
			$zip_filesystem->write('clippings.ged', $filetext);

			// Need to force-close the filesystem
			$zip_filesystem = null;

			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="clippings.zip"');
			header('Content-length: ' . filesize($temp_zip_file));
			readfile($temp_zip_file);
			unlink($temp_zip_file);

			exit;
		}
		Session::put('cart', $this->cart);
	}

	/**
	 * Inserts a clipping into the clipping cart
	 *
	 * @param GedcomRecord $record
	 */
	public function addClipping(GedcomRecord $record) {
		if ($record->canShowName()) {
			$this->cart[$record->getTree()->getTreeId()][$record->getXref()] = true;
			// Add directly linked records
			preg_match_all('/\n\d (?:OBJE|NOTE|SOUR|REPO) @(' . WT_REGEX_XREF . ')@/', $record->getGedcom(), $matches);
			foreach ($matches[1] as $match) {
				$this->cart[$record->getTree()->getTreeId()][$match] = true;
			}
		}
	}

	/**
	 * Recursive function to traverse the tree
	 *
	 * @param Family|null $family
	 * @param int         $level
	 */
	public function addFamilyDescendancy(Family $family = null, $level = PHP_INT_MAX) {
		if (!$family) {
			return;
		}
		foreach ($family->getSpouses() as $spouse) {
			$this->addClipping($spouse);
		}
		foreach ($family->getChildren() as $child) {
			$this->addClipping($child);
			foreach ($child->getSpouseFamilies() as $child_family) {
				$this->addClipping($child_family);
				if ($level > 0) {
					$this->addFamilyDescendancy($child_family, $level - 1); // recurse on the childs family
				}
			}
		}
	}

	/**
	 * Add a family, and all its members
	 *
	 * @param Family|null $family
	 */
	public function addFamilyMembers(Family $family = null) {
		if (!$family) {
			return;
		}
		$this->addClipping($family);
		foreach ($family->getSpouses() as $spouse) {
			$this->addClipping($spouse);
		}
		foreach ($family->getChildren() as $child) {
			$this->addClipping($child);
		}
	}

	/**
	 * Recursively add direct-line ancestors to cart
	 *
	 * @param Individual|null $person
	 * @param int             $level
	 */
	public function addAncestorsToCart(Individual $person = null, $level = 0) {
		if (!$person) {
			return;
		}
		$this->addClipping($person);
		if ($level > 0) {
			foreach ($person->getChildFamilies() as $family) {
				$this->addClipping($family);
				$this->addAncestorsToCart($family->getHusband(), $level - 1);
				$this->addAncestorsToCart($family->getWife(), $level - 1);
			}
		}
	}

	/**
	 * Recursively adds direct-line ancestors and their families to the cart
	 *
	 * @param Individual|null $person
	 * @param int             $level
	 */
	public function addAncestorsToCartFamilies(Individual $person = null, $level = 0) {
		if (!$person) {
			return;
		}
		if ($level > 0) {
			foreach ($person->getChildFamilies() as $family) {
				$this->addFamilyMembers($family);
				$this->addAncestorsToCartFamilies($family->getHusband(), $level - 1);
				$this->addAncestorsToCartFamilies($family->getWife(), $level - 1);
			}
		}
	}

	/**
	 * Helper function to sort records by type/name
	 *
	 * @param string $a
	 * @param string $b
	 *
	 * @return int
	 */
	private static function compareClippings($a, $b) {
		global $WT_TREE;

		$a = GedcomRecord::getInstance($a, $WT_TREE);
		$b = GedcomRecord::getInstance($b, $WT_TREE);
		if ($a && $b) {
			switch ($a::RECORD_TYPE) {
				case 'INDI': $t1 = 1; break;
				case 'FAM':  $t1 = 2; break;
				case 'SOUR': $t1 = 3; break;
				case 'REPO': $t1 = 4; break;
				case 'OBJE': $t1 = 5; break;
				case 'NOTE': $t1 = 6; break;
				default:     $t1 = 7; break;
			}
			switch ($b::RECORD_TYPE) {
				case 'INDI': $t2 = 1; break;
				case 'FAM':  $t2 = 2; break;
				case 'SOUR': $t2 = 3; break;
				case 'REPO': $t2 = 4; break;
				case 'OBJE': $t2 = 5; break;
				case 'NOTE': $t2 = 6; break;
				default:     $t2 = 7; break;
			}
			if ($t1 != $t2) {
				return $t1 - $t2;
			} else {
				return GedcomRecord::compare($a, $b);
			}
		} else {
			return 0;
		}
	}
}
