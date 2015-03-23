<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSw - Representation of the Swahili language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSw extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'sw';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
