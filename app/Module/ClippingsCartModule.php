<?php
namespace Fisharebest\Webtrees;

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

use Zend_Session;

/**
 * Class ClippingsCartModule
 */
class ClippingsCartModule extends Module implements ModuleMenuInterface, ModuleSidebarInterface {

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Clippings cart');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Clippings cart” module */ I18N::translate('Select records from your family tree and save them as a GEDCOM file.');
	}

	/** {@inheritdoc} */
	public function defaultAccessLevel() {
		return WT_PRIV_USER;
	}

	/** {@inheritdoc} */
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'ajax':
			$html = $this->getSidebarAjaxContent();
			Zend_Session::writeClose();
			header('Content-Type: text/html; charset=UTF-8');
			echo $html;
			break;
		case 'index':
			global $controller, $WT_SESSION, $WT_TREE;

			$MAX_PEDIGREE_GENERATIONS = $WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS');

			$clip_ctrl = new ClippingsCart;

			$controller = new PageController;
			$controller
				->setPageTitle($this->getTitle())
				->PageHeader()
				->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
				->addInlineJavascript('autocomplete();');

			echo '<script>';
			echo 'function radAncestors(elementid) {var radFamilies=document.getElementById(elementid);radFamilies.checked=true;}';
			echo '</script>';

			if (!$WT_SESSION->cart[WT_GED_ID]) {
				echo '<h2>', I18N::translate('Family tree clippings cart'), '</h2>';
			}

			if ($clip_ctrl->action == 'add') {
				$person = GedcomRecord::getInstance($clip_ctrl->id);
				echo '<h3><a href="', $person->getHtmlUrl(), '">' . $person->getFullName(), '</a></h3>';
				if ($clip_ctrl->type === 'FAM') { ?>
					<form action="module.php" method="get">
					<input type="hidden" name="mod" value="clippings">
					<input type="hidden" name="mod_action" value="index">
					<table>
						<tr><td class="topbottombar"><?php echo I18N::translate('Which other links from this family would you like to add?'); ?>
						<input type="hidden" name="id" value="<?php echo $clip_ctrl->id; ?>">
						<input type="hidden" name="type" value="<?php echo $clip_ctrl->type; ?>">
						<input type="hidden" name="action" value="add1"></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" checked value="none"><?php echo I18N::translate('Add just this family record.'); ?></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" value="parents"><?php echo I18N::translate('Add parents’ records together with this family record.'); ?></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" value="members"><?php echo I18N::translate('Add parents’ and children’s records together with this family record.'); ?></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" value="descendants"><?php echo I18N::translate('Add parents’ and all descendants’ records together with this family record.'); ?></td></tr>
						<tr><td class="topbottombar"><input type="submit" value="<?php echo I18N::translate('Continue adding'); ?>"></td></tr>

					</table>
					</form>
				<?php } elseif ($clip_ctrl->type === 'INDI') { ?>
					<form action="module.php" method="get">
					<input type="hidden" name="mod" value="clippings">
					<input type="hidden" name="mod_action" value="index">
					<table>
						<tr><td class="topbottombar"><?php echo I18N::translate('Which links from this individual would you also like to add?'); ?>
						<input type="hidden" name="id" value="<?php echo $clip_ctrl->id; ?>">
						<input type="hidden" name="type" value="<?php echo $clip_ctrl->type; ?>">
						<input type="hidden" name="action" value="add1"></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" checked value="none"><?php echo I18N::translate('Add just this individual.'); ?></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" value="parents"><?php echo I18N::translate('Add this individual, his parents, and siblings.'); ?></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" value="ancestors" id="ancestors"><?php echo I18N::translate('Add this individual and his direct line ancestors.'); ?><br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo I18N::translate('Number of generations:'); ?> <input type="text" size="5" name="level1" value="<?php echo $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('ancestors');"></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" value="ancestorsfamilies" id="ancestorsfamilies"><?php echo I18N::translate('Add this individual, his direct line ancestors, and their families.'); ?><br >
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo I18N::translate('Number of generations:'); ?> <input type="text" size="5" name="level2" value="<?php echo $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('ancestorsfamilies');"></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" value="members"><?php echo I18N::translate('Add this individual, his spouse, and children.'); ?></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" value="descendants" id="descendants"><?php echo I18N::translate('Add this individual, his spouse, and all descendants.'); ?><br >
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo I18N::translate('Number of generations:'); ?> <input type="text" size="5" name="level3" value="<?php echo $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('descendants');"></td></tr>
						<tr><td class="topbottombar"><input type="submit" value="<?php echo I18N::translate('Continue adding'); ?>">
					</table>
					</form>
				<?php } elseif ($clip_ctrl->type === 'SOUR') { ?>
					<form action="module.php" method="get">
					<input type="hidden" name="mod" value="clippings">
					<input type="hidden" name="mod_action" value="index">
					<table>
						<tr><td class="topbottombar"><?php echo I18N::translate('Which records linked to this source should be added?'); ?>
						<input type="hidden" name="id" value="<?php echo $clip_ctrl->id; ?>">
						<input type="hidden" name="type" value="<?php echo $clip_ctrl->type; ?>">
						<input type="hidden" name="action" value="add1"></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" checked value="none"><?php echo I18N::translate('Add just this source.'); ?></td></tr>
						<tr><td class="optionbox"><input type="radio" name="others" value="linked"><?php echo I18N::translate('Add this source and families/individuals linked to it.'); ?></td></tr>
						<tr><td class="topbottombar"><input type="submit" value="<?php echo I18N::translate('Continue adding'); ?>">
					</table>
					</form>
				<?php }
				}

			if (!$WT_SESSION->cart[WT_GED_ID]) {
				if ($clip_ctrl->action != 'add') {

					echo I18N::translate('The clippings cart allows you to take extracts (“clippings”) from this family tree and bundle them up into a single file for downloading and subsequent importing into your own genealogy program.  The downloadable file is recorded in GEDCOM format.<br><ul><li>How to take clippings?<br>This is really simple.  Whenever you see a clickable name (individual, family, or source) you can go to the Details page of that name.  There you will see the <b>Add to clippings cart</b> option.  When you click that link you will be offered several options to download.</li><li>How to download?<br>Once you have items in your cart, you can download them just by clicking the “Download” link.  Follow the instructions and links.</li></ul>');
					?>
					<form method="get" name="addin" action="module.php">
					<input type="hidden" name="mod" value="clippings">
					<input type="hidden" name="mod_action" value="index">
					<table>
					<tr>
						<td colspan="2" class="topbottombar" style="text-align:center; ">
							<?php echo I18N::translate('Enter an individual, family, or source ID'); ?>
						</td>
					</tr>
					<tr>
						<td class="optionbox">
							<input type="hidden" name="action" value="add">
							<input type="text" data-autocomplete-type="IFSRO" name="id" id="cart_item_id" size="5">
						</td>
						<td class="optionbox">
							<?php echo print_findindi_link('cart_item_id'); ?>
							<?php echo print_findfamily_link('cart_item_id'); ?>
							<?php echo print_findsource_link('cart_item_id', ''); ?>
							<input type="submit" value="<?php echo I18N::translate('Add'); ?>">

						</td>
					</tr>
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
					<tr><td class="descriptionbox width50 wrap"><?php echo I18N::translate('Zip file(s)'), help_link('zip'); ?></td>
					<td class="optionbox"><input type="checkbox" name="Zip" value="yes"></td></tr>

					<tr><td class="descriptionbox width50 wrap"><?php echo I18N::translate('Include media (automatically zips files)'), help_link('include_media'); ?></td>
					<td class="optionbox"><input type="checkbox" name="IncludeMedia" value="yes"></td></tr>

					<?php if (WT_USER_GEDCOM_ADMIN) {	?>
						<tr><td class="descriptionbox width50 wrap"><?php echo I18N::translate('Apply privacy settings'), help_link('apply_privacy'); ?></td>
						<td class="optionbox">
							<input type="radio" name="privatize_export" value="none" checked> <?php echo I18N::translate('None'); ?><br>
							<input type="radio" name="privatize_export" value="gedadmin"> <?php echo I18N::translate('Manager'); ?><br>
							<input type="radio" name="privatize_export" value="user"> <?php echo I18N::translate('Member'); ?><br>
							<input type="radio" name="privatize_export" value="visitor"> <?php echo I18N::translate('Visitor'); ?>
						</td></tr>
					<?php } elseif (WT_USER_CAN_ACCESS) {	?>
						<tr><td class="descriptionbox width50 wrap"><?php echo I18N::translate('Apply privacy settings'), help_link('apply_privacy'); ?></td>
						<td class="optionbox">
							<input type="radio" name="privatize_export" value="user" checked> <?php echo I18N::translate('Member'); ?><br>
							<input type="radio" name="privatize_export" value="visitor"> <?php echo I18N::translate('Visitor'); ?>
						</td></tr>
					<?php } ?>

					<tr><td class="descriptionbox width50 wrap"><?php echo I18N::translate('Convert from UTF-8 to ISO-8859-1'), help_link('utf8_ansi'); ?></td>
					<td class="optionbox"><input type="checkbox" name="convert" value="yes"></td></tr>

					<tr><td class="descriptionbox width50 wrap"><?php echo I18N::translate('Add the GEDCOM media path to filenames'), help_link('GEDCOM_MEDIA_PATH'); ?></td>
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
					<tr>
						<td colspan="2" class="topbottombar" style="text-align:center; ">
							<?php echo I18N::translate('Enter an individual, family, or source ID'); ?>
						</td>
					</tr>
					<tr>
						<td class="optionbox">
							<input type="hidden" name="action" value="add">
							<input type="text" data-autocomplete-type="IFSRO" name="id" id="cart_item_id" size="8">
						</td>
						<td class="optionbox">
							<?php echo print_findindi_link('cart_item_id'); ?>
							<?php echo print_findfamily_link('cart_item_id'); ?>
							<?php echo print_findsource_link('cart_item_id'); ?>
							<input type="submit" value="<?php echo I18N::translate('Add'); ?>">

						</td>
					</tr>
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
				foreach (array_keys($WT_SESSION->cart[WT_GED_ID]) as $xref) {
					$record = GedcomRecord::getInstance($xref);
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

	/** {@inheritdoc} */
	public function defaultMenuOrder() {
		return 20;
	}

	/** {@inheritdoc} */
	public function getMenu() {
		global $controller;

		if (Auth::isSearchEngine()) {
			return null;
		}
		//-- main clippings menu item
		$menu = new Menu($this->getTitle(), 'module.php?mod=clippings&amp;mod_action=index&amp;ged=' . WT_GEDURL, 'menu-clippings');
		if (isset($controller->record)) {
			$submenu = new Menu($this->getTitle(), 'module.php?mod=clippings&amp;mod_action=index&amp;ged=' . WT_GEDURL, 'menu-clippingscart');
			$menu->addSubmenu($submenu);
		}
		if (!empty($controller->record) && $controller->record->canShow()) {
			$submenu = new Menu(I18N::translate('Add to clippings cart'), 'module.php?mod=clippings&amp;mod_action=index&amp;action=add&amp;id=' . $controller->record->getXref(), 'menu-clippingsadd');
			$menu->addSubmenu($submenu);
		}
		return $menu;
	}

	/** {@inheritdoc} */
	public function defaultSidebarOrder() {
		return 60;
	}

	/** {@inheritdoc} */
	public function hasSidebarContent() {
		if (Auth::isSearchEngine()) {
			return false;
		} else {
			// Creating a controller has the side effect of initialising the cart
			new ClippingsCart;

			return true;
		}
	}

	/** {@inheritdoc} */
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
		global $WT_SESSION;

		$clip_ctrl         = new ClippingsCart;
		$add               = Filter::get('add', WT_REGEX_XREF);
		$add1              = Filter::get('add1', WT_REGEX_XREF);
		$remove            = Filter::get('remove', WT_REGEX_XREF);
		$others            = Filter::get('others');
		$clip_ctrl->level1 = Filter::getInteger('level1');
		$clip_ctrl->level2 = Filter::getInteger('level2');
		$clip_ctrl->level3 = Filter::getInteger('level3');
		if ($add) {
			$record = GedcomRecord::getInstance($add);
			if ($record) {
				$clip_ctrl->id   = $record->getXref();
				$clip_ctrl->type = $record::RECORD_TYPE;
				$clip_ctrl->addClipping($record);
			}
		} elseif ($add1) {
			$record = Individual::getInstance($add1);
			if ($record) {
				$clip_ctrl->id = $record->getXref();
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
			unset ($WT_SESSION->cart[WT_GED_ID][$remove]);
		} elseif (isset($_REQUEST['empty'])) {
			$WT_SESSION->cart[WT_GED_ID] = array();
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
		global $WT_SESSION;

		// Keep track of the INDI from the parent page, otherwise it will
		// get lost after ajax updates
		$pid = Filter::get('pid', WT_REGEX_XREF);

		if (!$WT_SESSION->cart[WT_GED_ID]) {
			$out = I18N::translate('Your clippings cart is empty.');
		} else {
			$out = '<ul>';
			foreach (array_keys($WT_SESSION->cart[WT_GED_ID]) as $xref) {
				$record = GedcomRecord::getInstance($xref);
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

		if ($WT_SESSION->cart[WT_GED_ID]) {
			$out .=
				'<br><a href="module.php?mod=' . $this->getName() . '&amp;mod_action=ajax&amp;sb_action=clippings&amp;empty=true&amp;pid=' . $pid . '" class="remove_cart">' .
				I18N::translate('Empty the clippings cart') .
				'</a>' .
				'<br>' .
				'<a href="module.php?mod=' . $this->getName() . '&amp;mod_action=ajax&amp;sb_action=clippings&amp;download=true&amp;pid=' . $pid . '" class="add_cart">' .
				I18N::translate('Download') .
				'</a>';
		}
		$record = Individual::getInstance($pid);
		if ($record && !array_key_exists($record->getXref(), $WT_SESSION->cart[WT_GED_ID])) {
			$out .= '<br><a href="module.php?mod=' . $this->getName() . '&amp;mod_action=ajax&amp;sb_action=clippings&amp;add=' . $pid . '&amp;pid=' . $pid . '" class="add_cart"><i class="icon-clippings"></i> ' . I18N::translate('Add %s to the clippings cart', $record->getFullName()) . '</a>';
		}
		return $out;
	}

	/**
	 * @param Individual $person
	 *
	 * @return string
	 */
	public function askAddOptions(Individual $person) {
		$MAX_PEDIGREE_GENERATIONS = $person->getTree()->getPreference('MAX_PEDIGREE_GENERATIONS');

		$out = '<h3><a href="' . $person->getHtmlUrl() . '">' . $person->getFullName() . '</a></h3>';
		$out .= '<script>';
		$out .= 'function radAncestors(elementid) {var radFamilies=document.getElementById(elementid);radFamilies.checked=true;}
			function continueAjax(frm) {
				var others = jQuery("input[name=\'others\']:checked").val();
				var link = "module.php?mod='.$this->getName() . '&mod_action=ajax&sb_action=clippings&add1="+frm.pid.value+"&others="+others+"&level1="+frm.level1.value+"&level2="+frm.level2.value+"&level3="+frm.level3.value;
				jQuery("#sb_clippings_content").load(link);
			}';
		$out .= '</script>';
		if ($person instanceof Family) {
			$out .= '<form action="module.php" method="get" onsubmit="continueAjax(this); return false;">
			<input type="hidden" name="mod" value="clippings">
			<input type="hidden" name="mod_action" value="index">
			<table>
			<tr><td class="topbottombar">' . I18N::translate('Which other links from this family would you like to add?') . '
			<input type="hidden" name="pid" value="'.$person->getXref() . '">
			<input type="hidden" name="type" value="'.$person::RECORD_TYPE . '">
			<input type="hidden" name="action" value="add1"></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" checked value="none">'. I18N::translate('Add just this family record.') . '</td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="parents">'. I18N::translate('Add parents’ records together with this family record.') . '</td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="members">'. I18N::translate('Add parents’ and children’s records together with this family record.') . '</td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="descendants">'. I18N::translate('Add parents’ and all descendants’ records together with this family record.') . '</td></tr>
			<tr><td class="topbottombar"><input type="submit" value="'. I18N::translate('Continue adding') . '"></td></tr>
			</table>
			</form>';
		} elseif ($person instanceof Individual) {
			$out .= '<form action="module.php" method="get" onsubmit="continueAjax(this); return false;">
			<input type="hidden" name="mod" value="clippings">
			<input type="hidden" name="mod_action" value="index">
		' . I18N::translate('Which links from this individual would you also like to add?') . '
		<input type="hidden" name="pid" value="'.$person->getXref() . '">
		<input type="hidden" name="type" value="'.$person::RECORD_TYPE . '">
		<input type="hidden" name="action" value="add1">
		<ul>
		<li><input type="radio" name="others" checked value="none">'. I18N::translate('Add just this individual.') . '</li>
		<li><input type="radio" name="others" value="parents">'. I18N::translate('Add this individual, his parents, and siblings.') . '</li>
		<li><input type="radio" name="others" value="ancestors" id="ancestors">'. I18N::translate('Add this individual and his direct line ancestors.') . '<br>
				'. I18N::translate('Number of generations:') . '<input type="text" size="4" name="level1" value="' . $MAX_PEDIGREE_GENERATIONS . '" onfocus="radAncestors(\'ancestors\');"></li>
		<li><input type="radio" name="others" value="ancestorsfamilies" id="ancestorsfamilies">'. I18N::translate('Add this individual, his direct line ancestors, and their families.') . '<br>
				'. I18N::translate('Number of generations:') . ' <input type="text" size="4" name="level2" value="' . $MAX_PEDIGREE_GENERATIONS . '" onfocus="radAncestors(\'ancestorsfamilies\');"></li>
		<li><input type="radio" name="others" value="members">'. I18N::translate('Add this individual, his spouse, and children.') . '</li>
		<li><input type="radio" name="others" value="descendants" id="descendants">'. I18N::translate('Add this individual, his spouse, and all descendants.') . '<br >
				'. I18N::translate('Number of generations:') . ' <input type="text" size="4" name="level3" value="' . $MAX_PEDIGREE_GENERATIONS . '" onfocus="radAncestors(\'descendants\');"></li>
		</ul>
		<input type="submit" value="'. I18N::translate('Continue adding') . '">
		</form>';
		} else if ($person instanceof Source) {
			$out .= '<form action="module.php" method="get" onsubmit="continueAjax(this); return false;">
		<input type="hidden" name="mod" value="clippings">
		<input type="hidden" name="mod_action" value="index">
		<table>
		<tr><td class="topbottombar">' . I18N::translate('Which records linked to this source should be added?') . '
		<input type="hidden" name="pid" value="'.$person->getXref() . '">
		<input type="hidden" name="type" value="'.$person::RECORD_TYPE . '">
		<input type="hidden" name="action" value="add1"></td></tr>
		<tr><td class="optionbox"><input type="radio" name="others" checked value="none">'. I18N::translate('Add just this source.') . '</td></tr>
		<tr><td class="optionbox"><input type="radio" name="others" value="linked">'. I18N::translate('Add this source and families/individuals linked to it.') . '</td></tr>
		<tr><td class="topbottombar"><input type="submit" value="'. I18N::translate('Continue adding') . '">
		</table>
		</form>';
		} else {
			return $this->getSidebarContent();
		}
		return $out;
	}

	/**
	 * @param ClippingsCart $clip_ctrl
	 *
	 * @return string
	 */
	public function downloadForm(ClippingsCart $clip_ctrl) {
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
		<input type="hidden" name="pid" value="' .$pid . '">
		<input type="hidden" name="action" value="download">
		<table>
		<tr><td colspan="2" class="topbottombar"><h2>'. I18N::translate('Download') . '</h2></td></tr>
		<tr><td class="descriptionbox width50 wrap">'. I18N::translate('Zip file(s)') . help_link('zip') . '</td>
		<td class="optionbox"><input type="checkbox" name="Zip" value="yes" checked></td></tr>

		<tr><td class="descriptionbox width50 wrap">'. I18N::translate('Include media (automatically zips files)') . help_link('include_media') . '</td>
		<td class="optionbox"><input type="checkbox" name="IncludeMedia" value="yes" checked></td></tr>
		';

		if (WT_USER_GEDCOM_ADMIN) {
			$out .=
				'<tr><td class="descriptionbox width50 wrap">' . I18N::translate('Apply privacy settings') . help_link('apply_privacy') . '</td>' .
				'<td class="optionbox">' .
				'	<input type="radio" name="privatize_export" value="none" checked> ' . I18N::translate('None') . '<br>' .
				'	<input type="radio" name="privatize_export" value="gedadmin"> ' . I18N::translate('Manager') . '<br>' .
				'	<input type="radio" name="privatize_export" value="user"> ' . I18N::translate('Member') . '<br>' .
				'	<input type="radio" name="privatize_export" value="visitor"> ' . I18N::translate('Visitor') .
				'</td></tr>';
		} elseif (WT_USER_CAN_ACCESS) {
			$out .=
				'<tr><td class="descriptionbox width50 wrap">' . I18N::translate('Apply privacy settings') . help_link('apply_privacy') . '</td>' .
				'<td class="list_value">' .
				'	<input type="radio" name="privatize_export" value="user" checked> ' . I18N::translate('Member') . '<br>' .
				'	<input type="radio" name="privatize_export" value="visitor"> ' . I18N::translate('Visitor') .
				'</td></tr>';
		}

		$out .= '
		<tr><td class="descriptionbox width50 wrap">'. I18N::translate('Convert from UTF-8 to ISO-8859-1') . help_link('utf8_ansi') . '</td>
		<td class="optionbox"><input type="checkbox" name="convert" value="yes"></td></tr>

		<tr>
		<td class="descriptionbox width50 wrap">'. I18N::translate('Add the GEDCOM media path to filenames') . help_link('GEDCOM_MEDIA_PATH') . '</td>
		<td class="optionbox">
		<input type="checkbox" name="conv_path" value="' . Filter::escapeHtml($WT_TREE->getPreference('GEDCOM_MEDIA_PATH')) . '">
		<span dir="auto">' . Filter::escapeHtml($WT_TREE->getPreference('GEDCOM_MEDIA_PATH')) . '</span></td>
		</tr>

		<input type="hidden" name="conv_path" value="'.$clip_ctrl->conv_path . '">

		</td></tr>

		<tr><td class="topbottombar" colspan="2">
		<input type="button" value="'. I18N::translate('Cancel') . '" onclick="cancelDownload();">
		<input type="submit" value="'. I18N::translate('Download') . '">
		</form>';

		return $out;
	}

}
