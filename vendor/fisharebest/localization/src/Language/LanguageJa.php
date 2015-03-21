<?php namespace Fisharebest\Localization;

/**
 * Class LanguageJa - Representation of the Japanese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageJa extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ja';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptJpan;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryJp;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule0;
	}
}
