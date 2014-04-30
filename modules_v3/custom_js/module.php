<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id: module.php 8218 2010-05-09 07:39:07Z greg $

if (!defined('WT_WEBTREES')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

class custom_js_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Menu {

    // Extend WT_Module
    public function getTitle() {
        return WT_I18N::translate('Custom JavaScript');
    }

    // Extend WT_Module
    public function getDescription() {
        return WT_I18N::translate('Allows you to easily add Custom JavaScript to your webtrees site.');
    }

    // Extend WT_Module
    public function modAction($mod_action) {
        switch ($mod_action) {
            case 'admin_config':
                $controller = new WT_Controller_Page;
                $controller
                    ->requireAdminLogin()
                    ->setPageTitle($this->getTitle())
            ->addInlineJavascript("
                jQuery('head')
                .append(\"<style type='text/css'> \
                    form {width:600px} \
                    form fieldset{border:none} \
                </style>\");")
                    ->pageHeader();

                $action = WT_Filter::post("action");

                if ($action == 'update') {
                    set_module_setting('custom_js', 'CJS_FOOTER', WT_Filter::post('NEW_CJS_FOOTER'));
                    AddToLog($this->getTitle() . ' config updated', 'config');
                }

                $CJS_FOOTER = get_module_setting('custom_js', 'CJS_FOOTER');
                ?>
                <h3><?php echo WT_I18N::translate('Custom Javascript for Footer'); ?></h3>
                <form method="post" name="configform" action="<?php echo $this->getConfigLink(); ?>">
                    <input type="hidden" name="action" value="update">
                    <fieldset>
                        <textarea rows="10" cols="60" name="NEW_CJS_FOOTER"><?php echo $CJS_FOOTER; ?></textarea>
                    </fieldset>
                    <input type="submit" value="<?php echo WT_I18N::translate('Save configuration'); ?>">
                    <input type="reset" value="<?php echo WT_I18N::translate('Reset'); ?>">
                </form>
                <?php
                break;
            default:
                header('HTTP/1.0 404 Not Found');
        }
    }

    // Implement WT_Module_Config
    public function getConfigLink() {
        return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin_config';
    }

    // Implement WT_Module_Menu
    public function defaultMenuOrder() {
        return 999;
    }

    // Implement WT_Module_Menu
    public function getMenu() {
        // We don't actually have a menu - this is just a convenient "hook" to execute
        // code at the right time during page execution
        global $controller;

        $cjs_footer = get_module_setting('custom_js', 'CJS_FOOTER', '');
        if (strpos($cjs_footer, '#') !== false) {
            # parse for embedded keywords
            $stats = new WT_Stats(WT_GEDCOM);
            $cjs_footer = $stats->embedTags($cjs_footer);
        }
        $controller->addInlineJavaScript($cjs_footer, WT_Controller_Base::JS_PRIORITY_LOW);

        return null;
    }

}

