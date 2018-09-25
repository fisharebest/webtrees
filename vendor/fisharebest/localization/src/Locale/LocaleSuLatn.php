<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;

/**
 * Class LocaleSuLatn
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2018 Greg Roach
 * @license   GPLv3+
 */
class LocaleSuLatn extends LocaleSu implements LocaleInterface
{
    public function endonym()
    {
        return 'Basa Sunda';
    }

    public function endonymSortable()
    {
        return 'BASA SUNDA';
    }

    public function script()
    {
        return new ScriptLatn();
    }
}
