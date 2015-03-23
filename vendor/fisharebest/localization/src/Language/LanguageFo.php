<?php namespace Fisharebest\Localization;

/**
 * Class LanguageFo - Representation of the Faroese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'fo';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryFo;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
