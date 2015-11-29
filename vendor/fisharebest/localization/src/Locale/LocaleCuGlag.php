<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptGlag;

/**
 * Class LocaleCuGlag - Old Church Slavonic
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */

class LocaleCuGlag extends LocaleCu {
	public function endonym() {
		return 'ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ';
	}

	public function endonymSortable() {
		return 'ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ';
	}

	public function script() {
		return new ScriptGlag;
	}
}
