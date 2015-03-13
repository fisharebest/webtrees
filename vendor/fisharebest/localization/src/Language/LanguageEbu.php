<?php namespace Fisharebest\Localization;

/**
 * Class LanguageEbu - Representation of the Embu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEbu extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ebu';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
