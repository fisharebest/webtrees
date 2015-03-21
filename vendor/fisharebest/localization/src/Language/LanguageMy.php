<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMy - Representation of the Burmese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMy extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'my';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptMymr;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMm;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule0;
	}
}
