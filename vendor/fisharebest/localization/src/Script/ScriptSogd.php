<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSyrc - Representation of the Sodgian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2018 Greg Roach
 * @license   GPLv3+
 */
class ScriptSogd extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Sogd';
    }

    public function number()
    {
        return '141';
    }

    public function unicodeName()
    {
        return 'Sogdian';
    }
}
