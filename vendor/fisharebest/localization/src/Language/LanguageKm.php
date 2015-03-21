<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKm - Representation of the Central Khmer language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKm extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'km';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptKhmr;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKh;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule0;
	}
}
