<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKsb - Representation of the Shambala language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKsb extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ksb';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}
}
