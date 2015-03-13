<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMas - Representation of the Masai language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMas extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'mas';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
