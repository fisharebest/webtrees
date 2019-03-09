<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

/** @global Controller\SimpleController $controller */
global $controller;

/** @global Tree $WT_TREE */
global $WT_TREE;

if (!Filter::checkCsrf()) {
    require WT_ROOT . WT_MODULES_DIR . 'GEDFact_assistant/census/census-edit.php';

    return;
}

// We are creating a CENS/NOTE record linked to these individuals
$pid_array = Filter::post('pid_array');

if (empty($pid_array)) {
    $xref = '';
} else {
    $NOTE   = Filter::post('NOTE');
    $gedcom = '0 @XREF@ NOTE ' . preg_replace('/\r?\n/', "\n1 CONT ", trim($NOTE));
    $xref   = $WT_TREE->createRecord($gedcom)->getXref();
}

$controller
    ->addInlineJavascript('window.opener.set_pid_array("' . $pid_array . '");')
    ->addInlineJavascript('openerpasteid("' . $xref . '");')
    ->setPageTitle(I18N::translate('Create a shared note using the census assistant'))
    ->pageHeader();
?>

<div id="edit_interface-page">
    <h4><?php echo $controller->getPageTitle() ?></h4>
</div>
