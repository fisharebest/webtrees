<?php namespace Fisharebest\Localization;

/**
 * Class LocaleDeAt - Austrian German
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDeAt extends LocaleDe {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Österreichisches Deutsch';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'OSTERREICHISCHES DEUTSCH';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryAt;
	}
}
