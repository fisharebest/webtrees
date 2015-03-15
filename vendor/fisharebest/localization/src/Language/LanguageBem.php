<?php namespace Fisharebest\Localization;

/**
 * Class LanguageBem - Representation of the Bemba (Zambia) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBem extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'bem';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryZm;
	}
}
