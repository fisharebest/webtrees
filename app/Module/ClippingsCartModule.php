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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module\ClippingsCart\ClippingsCartController;
use Fisharebest\Webtrees\Session;

/**
 * Class ClippingsCartModule
 */
class ClippingsCartModule extends AbstractModule implements ModuleMenuInterface, ModuleSidebarInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Clippings cart');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Clippings cart” module */ I18N::translate('Select records from your family tree and save them as a GEDCOM file.');
	}

	/**
	 * What is the default access level for this module?
	 *
	 * Some modules are aimed at admins or managers, and are not generally shown to users.
	 *
	 * @return int
	 */
	public function defaultAccessLevel() {
		return Auth::PRIV_USER;
	}

	/**
	 * This is a general purpose hook, allowing modules to respond to routes
	 * of the form module.php?mod=FOO&mod_action=BAR
	 *
	 * @param string $mod_action
	 */
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'ajax':
			$html = $this->getSidebarAjaxContent();
			header('Content-Type: text/html; charset=UTF-8');
			echo $html;
			break;
		case 'index':
			global $controller, $WT_TREE;

			$MAX_PEDIGREE_GENERATIONS = $WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS');

			$clip_ctrl = new ClippingsCartController;
			$cart      = Session::get('cart');

			$controller = new PageController;
			$controller
				->setPageTitle($this->getTitle())
				->PageHeader()
				->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
				->addInlineJavascript('autocomplete();');

			echo '<script>';
			echo 'function radAncestors(elementid) {var radFamilies=document.getElementById(elementid);radFamilies.checked=true;}';
			echo '</script>';

			if (!$cart[$WT_TREE->getTreeId()]) {
				echo '<h2>', I18N::translate('Family tree clippings cart'), '</h2>';
			}

			if ($clip_ctrl->action == 'add') {
				$record = GedcomRecord::getInstance($clip_ctrl->id, $WT_TREE);
				if ($clip_ctrl->type === 'FAM') { ?>
				<form action="module.php" method="get">
					<input type="hidden" name="mod" value="clippings">
					<input type="hidden" name="mod_action" value="index">
					<input type="hidden" name="id" value="<?php echo $clip_ctrl->id; ?>">
					<input type="hidden" name="type" value="<?php echo $clip_ctrl->type; ?>">
					<input type="hidden" name="action" value="add1">
					<table>
						<thead>
							<tr>
								<td class="topbottombar">
									<?php echo I18N::translate('Add to the clippings cart'); ?>
								</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="optionbox">
									<input type="radio" name="others" value="parents">
									<?php echo $record->getFullName(); ?>
								</td>
							</tr>
							<tr>
								<td class="optionbox">
									<input type="radio" name="others" value="members" checked>
									<?php echo /* I18N: %s is a family (husband + wife) */ I18N::translate('%s and their children', $record->getFullName()); ?>
								</td>
							</tr>
							<tr>
								<td class="optionbox">
									<input type="radio" name="others" value="descendants">
									<?php echo /* I18N: %s is a family (husband + wife) */ I18N::translate('%s and their descendants', $record->getFullName()); ?>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td class="topbottombar"><input type="submit" value="<?php echo I18N::translate('continue'); ?>">
								</td>
							</tr>
						</tfoot>
					</table>
				</form>
				<?php } elseif ($clip_ctrl->type === 'INDI') { ?>
				<form action="module.php" method="get">
					<input type="hidden" name="mod" value="clippings">
					<input type="hidden" name="mod_action" value="index">
					<input type="hidden" name="id" value="<?php echo $clip_ctrl->id; ?>">
					<input type="hidden" name="type" value="<?php echo $clip_ctrl->type; ?>">
					<input type="hidden" name="action" value="add1"></td></tr>
					<table>
						<thead>
							<tr>
								<td class="topbottombar">
									<?php echo I18N::translate('Add to the clippings cart'); ?>
								</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="optionbox">
									<label>
										<input type="radio" name="others" checked value="none">
										<?php echo $record->getFullName(); ?>
									</label>
								</td>
							</tr>
							<tr>
								<td class="optionbox">
									<label>
										<input type="radio" name="others" value="parents">
										<?php
										if ($record->getSex() === 'F') {
											echo /* I18N: %s is a woman's name */ I18N::translate('%s, her parents and siblings', $record->getFullName());
										} else {
											echo /* I18N: %s is a man's name */ I18N::translate('%s, his parents and siblings', $record->getFullName());
										}
										?>
									</label>
								</td>
							</tr>
							<tr>
								<td class="optionbox">
									<label>
										<input type="radio" name="others" value="members">
										<?php
										if ($record->getSex() === 'F') {
											echo /* I18N: %s is a woman's name */ I18N::translate('%s, her spouses and children', $record->getFullName());
										} else {
											echo /* I18N: %s is a man's name */ I18N::translate('%s, his spouses and children', $record->getFullName());
										}
										?>
									</label>
								</td>
							</tr>
							<tr>
								<td class="optionbox">
									<label>
										<input type="radio" name="others" value="ancestors" id="ancestors">
										<?php
										if ($record->getSex() === 'F') {
										echo /* I18N: %s is a woman's name */ I18N::translate('%s and her ancestors', $record->getFullName());
										} else {
										echo /* I18N: %s is a man's name */ I18N::translate('%s and his ancestors', $record->getFullName());
										}
									?>
									</label>
									<br>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php echo I18N::translate('Number of generations'); ?>
									<input type="text" size="5" name="level1" value="<?php echo $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('ancestors');">
								</td>
							</tr>
							<tr>
								<td class="optionbox">
									<label>
										<input type="radio" name="others" value="ancestorsfamilies" id="ancestorsfamilies">
										<?php
										if ($record->getSex() === 'F') {
											echo /* I18N: %s is a woman's name */ I18N::translate('%s, her ancestors and their families', $record->getFullName());
										} else {
											echo /* I18N: %s is a man's name */ I18N::translate('%s, his ancestors and their families', $record->getFullName());
										}
										?>
									</label>
									<br >
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php echo I18N::translate('Number of generations'); ?>
									<input type="text" size="5" name="level2" value="<?php echo $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('ancestorsfamilies');">
								</td>
							</tr>
							<tr>
								<td class="optionbox">
									<label>
										<input type="radio" name="others" value="descendants" id="descendants">
										<?php
										if ($record->getSex() === 'F') {
											echo /* I18N: %s is a woman's name */ I18N::translate('%s, her spouses and descendants', $record->getFullName());
										} else {
											echo /* I18N: %s is a man's name */ I18N::translate('%s, his spouses and descendants', $record->getFullName());
										}
										?>
									</label>
									<br >
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php echo I18N::translate('Number of generations'); ?>
									<input type="text" size="5" name="level3" value="<?php echo $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('descendants');">
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td class="topbottombar">
									<input type="submit" value="<?php echo I18N::translate('continue'); ?>">
								</td>
							</tr>
						</tfoot>
					</table>
				</form>
				<?php } elseif ($clip_ctrl->type === 'SOUR') { ?>
				<form action="module.php" method="get">
					<input type="hidden" name="mod" value="clippings">
					<input type="hidden" name="mod_action" value="index">
					<input type="hidden" name="id" value="<?php echo $clip_ctrl->id; ?>">
					<input type="hidden" name="type" value="<?php echo $clip_ctrl->type; ?>">
					<input type="hidden" name="action" value="add1"></td></tr>
					<table>
						<thead>
							<tr>
								<td class="topbottombar">
									<?php echo I18N::translate('Add to the clippings cart'); ?>
								</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="optionbox">
									<label>
										<input type="radio" name="others" checked value="none">
										<?php echo $record->getFullName(); ?>
									</label>
								</td>
							</tr>
							<tr>
								<td class="optionbox">
									<label>
										<input type="radio" name="others" value="linked">
										<?php echo /* I18N: %s is the name of a source */ I18N::translate('%s and the individuals that reference it.', $record->getFullName()); ?>
									</label>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td class="topbottombar">
									<input type="submit" value="<?php echo I18N::translate('continue'); ?>">
								</td>
							</tr>
						</tfoot>
					</table>
				</form>
				<?php }
				}

			if (!$cart[$WT_TREE->getTreeId()]) {
				if ($clip_ctrl->action != 'add') {
					echo I18N::translate('The clippings cart allows you to take extracts (“clippings”) from this family tree and bundle them up into a single file for downloading and subsequent importing into your own genealogy program.  The downloadable file is recorded in GEDCOM format.<br><ul><li>How to take clippings?<br>This is really simple.  Whenever you see a clickable name (individual, family, or source) you can go to the Details page of that name.  There you will see the <b>Add to clippings cart</b> option.  When you click that link you will be offered several options to download.</li><li>How to download?<br>Once you have items in your cart, you can download them just by clicking the “Download” link.  Follow the instructions and links.</li></ul>');
					?>
					<form method="get" name="addin" action="module.php">
					<input type="hidden" name="mod" value="clippings">
					<input type="hidden" name="mod_action" value="index">
						<table>
							<thead>
								<tr>
									<td colspan="2" class="topbottombar">
										<?php echo I18N::translate('Add to the clippings cart'); ?>
									</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="optionbox">
										<input type="hidden" name="action" value="add">
										<input type="text" data-autocomplete-type="IFSRO" name="id" id="cart_item_id" size="5">
									</td>
									<td class="optionbox">
										<?php echo FunctionsPrint::printFindIndividualLink('cart_item_id'); ?>
										<?php echo FunctionsPrint::printFindFamilyLink('cart_item_id'); ?>
										<?php echo FunctionsPrint::printFindSourceLink('cart_item_id', ''); ?>
										<input type="submit" value="<?php echo I18N::translate('Add'); ?>">
									</td>
								</tr>
							</tbody>
						</table>
					</form>
					<?php
				}

				// -- end new lines
				echo I18N::translate('Your clippings cart is empty.');
			} else {
				// Keep track of the INDI from the parent page, otherwise it will
				// get lost after ajax updates
				$pid = Filter::get('pid', WT_REGEX_XREF);

				if ($clip_ctrl->action != 'download' && $clip_ctrl->action != 'add') { ?>
					<table><tr><td class="width33" valign="top" rowspan="3">
					<form method="get" action="module.php">
					<input type="hidden" name="mod" value="clippings">
					<input type="hidden" name="mod_action" value="index">
					<input type="hidden" name="action" value="download">
					<input type="hidden" name="pid" value="<?php echo $pid; ?>">
					<table>
					<tr><td colspan="2" class="topbottombar"><h2><?php echo I18N::translate('Download'); ?></h2></td></tr>
					<tr>
						<td class="descriptionbox width50 wrap">
							<?php echo I18N::translate('To reduce the size of the download, you can compress the data into a .ZIP file.  You will need to uncompress the .ZIP file before you can use it.'); ?>
						</td>
						<td class="optionbox wrap">
							<input type="checkbox" name="Zip" value="yes">
							<?php echo I18N::translate('Zip file(s)'); ?>
						</td>
					</tr>
					<tr>
						<td class="descriptionbox width50 wrap">
							<?php echo I18N::translate('Include media (automatically zips files)'); ?>
						</td>
					<td class="optionbox"><input type="checkbox" name="IncludeMedia" value="yes"></td></tr>

					<?php if (Auth::isManager($WT_TREE)) {	?>
						<tr><td class="descriptionbox width50 wrap"><?php echo I18N::translate('Apply privacy settings'); ?></td>
						<td class="optionbox">
							<input type="radio" name="privatize_export" value="none" checked> <?php echo I18N::translate('None'); ?><br>
							<input type="radio" name="privatize_export" value="gedadmin"> <?php echo I18N::translate('Manager'); ?><br>
							<input type="radio" name="privatize_export" value="user"> <?php echo I18N::translate('Member'); ?><br>
							<input type="radio" name="privatize_export" value="visitor"> <?php echo I18N::translate('Visitor'); ?>
						</td></tr>
					<?php } elseif (Auth::isMember($WT_TREE)) { ?>
						<tr><td class="descriptionbox width50 wrap"><?php echo I18N::translate('Apply privacy settings'); ?></td>
						<td class="optionbox">
							<input type="radio" name="privatize_export" value="user" checked> <?php echo I18N::translate('Member'); ?><br>
							<input type="radio" name="privatize_export" value="visitor"> <?php echo I18N::translate('Visitor'); ?>
						</td></tr>
					<?php } ?>

					<tr><td class="descriptionbox width50 wrap"><?php echo I18N::translate('Convert from UTF-8 to ISO-8859-1'); ?></td>
					<td class="optionbox"><input type="checkbox" name="convert" value="yes"></td></tr>

					<tr><td class="descriptionbox width50 wrap"><?php echo I18N::translate('Add the GEDCOM media path to filenames'); ?></td>
					<td class="optionbox">
						<input type="checkbox" name="conv_path" value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('GEDCOM_MEDIA_PATH')); ?>">
						<span dir="auto"><?php echo Filter::escapeHtml($WT_TREE->getPreference('GEDCOM_MEDIA_PATH')); ?></span>
					</td></tr>

					<tr><td class="topbottombar" colspan="2">
					<input type="submit" value="<?php echo I18N::translate('Download'); ?>">
					</form>
					</td></tr>
					</table>
					</td></tr>
					</table>
					<br>

					<form method="get" name="addin" action="module.php">
						<input type="hidden" name="mod" value="clippings">
						<input type="hidden" name="mod_action" value="index">
						<table>
							<thead>
								<tr>
									<td colspan="2" class="topbottombar" style="text-align:center; ">
										<?php echo I18N::translate('Add to the clippings cart'); ?>
									</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="optionbox">
										<input type="hidden" name="action" value="add">
										<input type="text" data-autocomplete-type="IFSRO" name="id" id="cart_item_id" size="8">
									</td>
									<td class="optionbox">
										<?php echo FunctionsPrint::printFindIndividualLink('cart_item_id'); ?>
										<?php echo FunctionsPrint::printFindFamilyLink('cart_item_id'); ?>
										<?php echo FunctionsPrint::printFindSourceLink('cart_item_id'); ?>
										<input type="submit" value="<?php echo I18N::translate('Add'); ?>">
									</td>
								</tr>
								</tbody>
						</table>
					</form>


				<?php } ?>
				<br><a href="module.php?mod=clippings&amp;mod_action=index&amp;action=empty"><?php echo I18N::translate('Empty the clippings cart'); ?></a>
				</td></tr>

				<tr><td class="topbottombar"><h2><?php echo I18N::translate('Family tree clippings cart'); ?></h2></td></tr>

				<tr><td valign="top">
				<table id="mycart" class="sortable list_table width100">
					<tr>
						<th class="list_label"><?php echo I18N::translate('Record'); ?></th>
						<th class="list_label"><?php echo I18N::translate('Remove'); ?></th>
					</tr>
			<?php
				foreach (array_keys($cart[$WT_TREE->getTreeId()]) as $xref) {
					$record = GedcomRecord::getInstance($xref, $WT_TREE);
					if ($record) {
						switch ($record::RECORD_TYPE) {
						case 'INDI': $icon = 'icon-indis'; break;
						case 'FAM':  $icon = 'icon-sfamily'; break;
						case 'SOUR': $icon = 'icon-source'; break;
						case 'REPO': $icon = 'icon-repository'; break;
						case 'NOTE': $icon = 'icon-note'; break;
						case 'OBJE': $icon = 'icon-media'; break;
						default:     $icon = 'icon-clippings'; break;
						}
						?>
						<tr><td class="list_value">
							<i class="<?php echo $icon; ?>"></i>
						<?php
						echo '<a href="', $record->getHtmlUrl(), '">', $record->getFullName(), '</a>';
						?>
						</td>
						<td class="list_value center vmiddle"><a href="module.php?mod=clippings&amp;mod_action=index&amp;action=remove&amp;id=<?php echo $xref; ?>" class="icon-remove" title="<?php echo I18N::translate('Remove'); ?>"></a></td>
					</tr>
					<?php
					}
				}
			?>
				</table>
				</td></tr></table>
			<?php
			}
			break;
		default:
			http_response_code(404);
			break;
		}
	}

	/**
	 * The user can re-order menus.  Until they do, they are shown in this order.
	 *
	 * @return int
	 */
	public function defaultMenuOrder() {
		return 20;
	}

	/**
	 * A menu, to be added to the main application menu.
	 *
	 * @return Menu|null
	 */
	public function getMenu() {
		global $controller, $WT_TREE;

		$submenus = array();
		if (isset($controller->record)) {
			$submenus[] = new Menu($this->getTitle(), 'module.php?mod=clippings&amp;mod_action=index&amp;ged=' . $WT_TREE->getNameUrl(), 'menu-clippingscart', array('rel' => 'nofollow'));
		}
		if (!empty($controller->record) && $controller->record->canShow()) {
			$submenus[] = new Menu(I18N::translate('Add to the clippings cart'), 'module.php?mod=clippings&amp;mod_action=index&amp;action=add&amp;id=' . $controller->record->getXref(), 'menu-clippingsadd', array('rel' => 'nofollow'));
		}

		if ($submenus) {
			return new Menu($this->getTitle(), '#', 'menu-clippings', array('rel' => 'nofollow'), $submenus);
		} else {
			return new Menu($this->getTitle(), 'module.php?mod=clippings&amp;mod_action=index&amp;ged=' . $WT_TREE->getNameUrl(), 'menu-clippings', array('rel' => 'nofollow'));
		}
	}

	/** {@inheritdoc} */
	public function defaultSidebarOrder() {
		return 60;
	}

	/** {@inheritdoc} */
	public function hasSidebarContent() {
		// Creating a controller has the side effect of initialising the cart
		new ClippingsCartController;

		return true;
	}

	/**
	 * Load this sidebar synchronously.
	 *
	 * @return string
	 */
	public function getSidebarContent() {
		global $controller;

		$controller->addInlineJavascript('
				jQuery("#sb_clippings_content").on("click", ".add_cart, .remove_cart", function() {
					jQuery("#sb_clippings_content").load(this.href);
					return false;
				});
			');

		return '<div id="sb_clippings_content">' . $this->getCartList() . '</div>';
	}

	/** {@inheritdoc} */
	public function getSidebarAjaxContent() {
		global $WT_TREE;

		$cart = Session::get('cart');

		$clip_ctrl         = new ClippingsCartController;
		$add               = Filter::get('add', WT_REGEX_XREF);
		$add1              = Filter::get('add1', WT_REGEX_XREF);
		$remove            = Filter::get('remove', WT_REGEX_XREF);
		$others            = Filter::get('others');
		$clip_ctrl->level1 = Filter::getInteger('level1');
		$clip_ctrl->level2 = Filter::getInteger('level2');
		$clip_ctrl->level3 = Filter::getInteger('level3');
		if ($add) {
			$record = GedcomRecord::getInstance($add, $WT_TREE);
			if ($record) {
				$clip_ctrl->id   = $record->getXref();
				$clip_ctrl->type = $record::RECORD_TYPE;
				$clip_ctrl->addClipping($record);
			}
		} elseif ($add1) {
			$record = Individual::getInstance($add1, $WT_TREE);
			if ($record) {
				$clip_ctrl->id   = $record->getXref();
				$clip_ctrl->type = $record::RECORD_TYPE;
				if ($others == 'parents') {
					foreach ($record->getChildFamilies() as $family) {
						$clip_ctrl->addClipping($family);
						$clip_ctrl->addFamilyMembers($family);
					}
				} elseif ($others == 'ancestors') {
					$clip_ctrl->addAncestorsToCart($record, $clip_ctrl->level1);
				} elseif ($others == 'ancestorsfamilies') {
					$clip_ctrl->addAncestorsToCartFamilies($record, $clip_ctrl->level2);
				} elseif ($others == 'members') {
					foreach ($record->getSpouseFamilies() as $family) {
						$clip_ctrl->addClipping($family);
						$clip_ctrl->addFamilyMembers($family);
					}
				} elseif ($others == 'descendants') {
					foreach ($record->getSpouseFamilies() as $family) {
						$clip_ctrl->addClipping($family);
						$clip_ctrl->addFamilyDescendancy($family, $clip_ctrl->level3);
					}
				}
			}
		} elseif ($remove) {
			unset($cart[$WT_TREE->getTreeId()][$remove]);
			Session::put('cart', $cart);
		} elseif (isset($_REQUEST['empty'])) {
			$cart[$WT_TREE->getTreeId()] = array();
			Session::put('cart', $cart);
		} elseif (isset($_REQUEST['download'])) {
			return $this->downloadForm($clip_ctrl);
		}

		return $this->getCartList();
	}

	/**
	 * A list for the side bar.
	 *
	 * @return string
	 */
	public function getCartList() {
		global $WT_TREE;

		$cart = Session::get('cart', array());
		if (!array_key_exists($WT_TREE->getTreeId(), $cart)) {
			$cart[$WT_TREE->getTreeId()] = array();
		}
		$pid = Filter::get('pid', WT_REGEX_XREF);

		if (!$cart[$WT_TREE->getTreeId()]) {
			$out = I18N::translate('Your clippings cart is empty.');
		} else {
			$out = '<ul>';
			foreach (array_keys($cart[$WT_TREE->getTreeId()]) as $xref) {
				$record = GedcomRecord::getInstance($xref, $WT_TREE);
				if ($record instanceof Individual || $record instanceof Family) {
					switch ($record::RECORD_TYPE) {
					case 'INDI':
						$icon = 'icon-indis';
						break;
					case 'FAM':
						$icon = 'icon-sfamily';
						break;
					}
					$out .= '<li>';
					if (!empty($icon)) {
						$out .= '<i class="' . $icon . '"></i>';
					}
					$out .= '<a href="' . $record->getHtmlUrl() . '">';
					if ($record instanceof Individual) {
						$out .= $record->getSexImage();
					}
					$out .= ' ' . $record->getFullName() . ' ';
					if ($record instanceof Individual && $record->canShow()) {
						$out .= ' (' . $record->getLifeSpan() . ')';
					}
					$out .= '</a>';
					$out .= '<a class="icon-remove remove_cart" href="module.php?mod=' . $this->getName() . '&amp;mod_action=ajax&amp;sb_action=clippings&amp;remove=' . $xref . '&amp;pid=' . $pid . '" title="' . I18N::translate('Remove') . '"></a>';
					$out .= '</li>';
				}
			}
			$out .= '</ul>';
		}

		if ($cart[$WT_TREE->getTreeId()]) {
			$out .=
				'<br><a href="module.php?mod=' . $this->getName() . '&amp;mod_action=ajax&amp;sb_action=clippings&amp;empty=true&amp;pid=' . $pid . '" class="remove_cart">' .
				I18N::translate('Empty the clippings cart') .
				'</a>' .
				'<br>' .
				'<a href="module.php?mod=' . $this->getName() . '&amp;mod_action=ajax&amp;sb_action=clippings&amp;download=true&amp;pid=' . $pid . '" class="add_cart">' .
				I18N::translate('Download') .
				'</a>';
		}
		$record = Individual::getInstance($pid, $WT_TREE);
		if ($record && !array_key_exists($record->getXref(), $cart[$WT_TREE->getTreeId()])) {
			$out .= '<br><a href="module.php?mod=' . $this->getName() . '&amp;mod_action=ajax&amp;sb_action=clippings&amp;add=' . $pid . '&amp;pid=' . $pid . '" class="add_cart"><i class="icon-clippings"></i> ' . I18N::translate('Add %s to the clippings cart', $record->getFullName()) . '</a>';
		}

		return $out;
	}

	/**
	 * A form to choose the download options.
	 *
	 * @param ClippingsCartController $clip_ctrl
	 *
	 * @return string
	 */
	public function downloadForm(ClippingsCartController $clip_ctrl) {
		global $WT_TREE;

		$pid = Filter::get('pid', WT_REGEX_XREF);

		$out = '<script>';
		$out .= 'function cancelDownload() {
				var link = "module.php?mod=' . $this->getName() . '&mod_action=ajax&sb_action=clippings&pid=' . $pid . '";
				jQuery("#sb_clippings_content").load(link);
			}';
		$out .= '</script>';
		$out .= '<form method="get" action="module.php">
		<input type="hidden" name="mod" value="clippings">
		<input type="hidden" name="mod_action" value="index">
		<input type="hidden" name="pid" value="' . $pid . '">
		<input type="hidden" name="action" value="download">
		<table>
		<tr><td colspan="2" class="topbottombar"><h2>' . I18N::translate('Download') . '</h2></td></tr>
		<tr><td class="descriptionbox width50 wrap">' . I18N::translate('Zip file(s)') . '</td>
		<td class="optionbox"><input type="checkbox" name="Zip" value="yes" checked></td></tr>

		<tr><td class="descriptionbox width50 wrap">' . I18N::translate('Include media (automatically zips files)') . '</td>
		<td class="optionbox"><input type="checkbox" name="IncludeMedia" value="yes" checked></td></tr>
		';

		if (Auth::isManager($WT_TREE)) {
			$out .=
				'<tr><td class="descriptionbox width50 wrap">' . I18N::translate('Apply privacy settings') . '</td>' .
				'<td class="optionbox">' .
				'	<input type="radio" name="privatize_export" value="none" checked> ' . I18N::translate('None') . '<br>' .
				'	<input type="radio" name="privatize_export" value="gedadmin"> ' . I18N::translate('Manager') . '<br>' .
				'	<input type="radio" name="privatize_export" value="user"> ' . I18N::translate('Member') . '<br>' .
				'	<input type="radio" name="privatize_export" value="visitor"> ' . I18N::translate('Visitor') .
				'</td></tr>';
		} elseif (Auth::isMember($WT_TREE)) {
			$out .=
				'<tr><td class="descriptionbox width50 wrap">' . I18N::translate('Apply privacy settings') . '</td>' .
				'<td class="list_value">' .
				'	<input type="radio" name="privatize_export" value="user" checked> ' . I18N::translate('Member') . '<br>' .
				'	<input type="radio" name="privatize_export" value="visitor"> ' . I18N::translate('Visitor') .
				'</td></tr>';
		}

		$out .= '
		<tr><td class="descriptionbox width50 wrap">' . I18N::translate('Convert from UTF-8 to ISO-8859-1') . '</td>
		<td class="optionbox"><input type="checkbox" name="convert" value="yes"></td></tr>

		<tr>
		<td class="descriptionbox width50 wrap">' . I18N::translate('Add the GEDCOM media path to filenames') . '</td>
		<td class="optionbox">
		<input type="checkbox" name="conv_path" value="' . Filter::escapeHtml($WT_TREE->getPreference('GEDCOM_MEDIA_PATH')) . '">
		<span dir="auto">' . Filter::escapeHtml($WT_TREE->getPreference('GEDCOM_MEDIA_PATH')) . '</span></td>
		</tr>

		<input type="hidden" name="conv_path" value="' . $clip_ctrl->conv_path . '">

		</td></tr>

		<tr><td class="topbottombar" colspan="2">
		<input type="button" value="' . I18N::translate('Cancel') . '" onclick="cancelDownload();">
		<input type="submit" value="' . I18N::translate('Download') . '">
		</form>';

		return $out;
	}
}
