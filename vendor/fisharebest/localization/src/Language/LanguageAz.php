<?php namespace Fisharebest\Localization;

/**
 * Class LanguageAz - Representation of the Azerbaijani language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAz extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'az';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIr;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
