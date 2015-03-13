<?php namespace Fisharebest\Localization;

/**
 * Class LocaleLkt - Lakota
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLkt extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Lakȟólʼiyapi';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'LAKHOLIYAPI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLkt;
	}
}
