<?php namespace Fisharebest\Localization;

/**
 * Class LanguageLv - Representation of the Latvian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLv extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'lv';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryLv;
	}
}
