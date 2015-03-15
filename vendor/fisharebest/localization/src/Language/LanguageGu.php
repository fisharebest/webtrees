<?php namespace Fisharebest\Localization;

/**
 * Class LanguageGu - Representation of the Gujarati language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGu extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'gu';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptGujr;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}
}
