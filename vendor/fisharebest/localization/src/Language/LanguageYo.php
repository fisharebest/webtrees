<?php namespace Fisharebest\Localization;

/**
 * Class LanguageYo - Representation of the Yoruba language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageYo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'yo';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNg;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule0;
	}
}
