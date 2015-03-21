<?php namespace Fisharebest\Localization;

/**
 * Class LanguageRm - Representation of the Romansh language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageRm extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'rm';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCh;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
