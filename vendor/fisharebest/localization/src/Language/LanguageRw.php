<?php namespace Fisharebest\Localization;

/**
 * Class LanguageRw - Representation of the Kinyarwanda language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageRw extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'rw';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryRw;
	}
}
