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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the family page.
 */
class FamilyController extends BaseController {
	/**
	 * Show a family's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function show(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$record = Family::getInstance($xref, $tree);

		if ($record === null) {
			return $this->notFound();
		} elseif (!$record->canShow()) {
			return $this->notAllowed();
		} else {
			return $this->viewResponse('family-page', [
				'record' => $record,
				'facts'  => $record->getFacts(null, true),
				'menu'   => $this->menu($record),
			]);
		}
	}

	/**
	 * @return Response
	 */
	private function notAllowed(): Response {
		return $this->viewResponse('alerts/danger', [
			'alert' => I18N::translate('This family does not exist or you do not have permission to view it.'),
		], Response::HTTP_FORBIDDEN);
	}

	/**
	 * @return Response
	 */
	private function notFound(): Response {
		return $this->viewResponse('alerts/danger', [
			'alert' => I18N::translate('This family does not exist or you do not have permission to view it.'),
		], Response::HTTP_NOT_FOUND);
	}

	/**
	 * @param Family $record
	 *
	 * @return Menu|null
	 */
	private function menu(Family $record) {
		if ($record->isPendingDeletion()) {
			return null;
		}

		$menu = new Menu(I18N::translate('Edit'), '#', 'menu-fam');

		if (Auth::isEditor($record->getTree())) {
			$menu->addSubmenu(new Menu(I18N::translate('Change family members'), e(Html::url('edit_interface.php', ['action' => 'changefamily', 'ged' => $record->getTree()->getName(), 'xref' => $record->getXref()])), 'menu-fam-change'));

			$menu->addSubmenu(new Menu(I18N::translate('Add a child to this family'), 'edit_interface.php?action=add_child_to_family&amp;ged=' . $record->getTree()->getNameHtml() . '&amp;xref=' . $record->getXref() . '&amp;gender=U', 'menu-fam-addchil'));

			if ($record->getNumberOfChildren() > 1) {
				$menu->addSubmenu(new Menu(I18N::translate('Re-order children'), 'edit_interface.php?action=reorder-children&amp;ged=' . $record->getTree()->getNameHtml() . '&amp;xref=' . $record->getXref(), 'menu-fam-orderchil'));
			}

			$menu->addSubmenu(new Menu(I18N::translate('Delete'), '#', 'menu-fam-del', [
				'onclick' => 'return delete_record("' . I18N::translate('Deleting the family will unlink all of the individuals from each other but will leave the individuals in place. Are you sure you want to delete this family?') . '", "' . $record->getXref() . '");',
			]));
		}

		if (Auth::isAdmin() || Auth::isEditor($record->getTree()) && $record->getTree()->getPreference('SHOW_GEDCOM_RECORD')) {
			$menu->addSubmenu(new Menu(I18N::translate('Edit the raw GEDCOM'), 'edit_interface.php?action=editraw&amp;ged=' . $record->getTree()->getNameHtml() . '&amp;xref=' . $record->getXref(), 'menu-fam-editraw'));
		}

		return $menu;
	}
}
