<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSn - Representation of the Shona language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSn extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'sn';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryZw;
	}
}
