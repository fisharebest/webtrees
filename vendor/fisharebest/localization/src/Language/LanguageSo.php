<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSo - Representation of the Somali language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'so';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritorySo;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
