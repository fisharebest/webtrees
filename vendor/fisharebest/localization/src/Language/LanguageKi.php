<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKi - Representation of the Kikuyu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKi extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ki';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKe;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
