<?php namespace Fisharebest\Localization;

/**
 * Class LanguageTo - Representation of the Tonga (Tonga Islands) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'to';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTo;
	}
}
