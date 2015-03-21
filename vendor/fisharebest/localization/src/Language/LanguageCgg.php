<?php namespace Fisharebest\Localization;

/**
 * Class LanguageCgg - Representation of the Chiga language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageCgg extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'cgg';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryUg;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
