<?php namespace Fisharebest\Localization;

/**
 * Class LanguageTh - Representation of the Thai language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTh extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'th';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptThai;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTh;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule0;
	}
}
