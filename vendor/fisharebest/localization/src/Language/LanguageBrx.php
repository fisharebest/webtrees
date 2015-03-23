<?php namespace Fisharebest\Localization;

/**
 * Class LanguageBrx - Representation of the Bodo (India) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBrx extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'brx';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
