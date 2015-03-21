<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMn - Representation of the Mongolian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMn extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'mn';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMn;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
