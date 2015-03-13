<?php namespace Fisharebest\Localization;

/**
 * Class LanguageAsa - Representation of the Asu (Tanzania) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAsa extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'asa';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}
}
