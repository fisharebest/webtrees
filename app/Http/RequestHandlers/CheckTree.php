<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_key_exists;
use function assert;
use function e;
use function in_array;
use function preg_match;
use function preg_match_all;
use function route;
use function strtoupper;

use const PREG_SET_ORDER;

/**
 * Check a tree for errors.
 */
class CheckTree implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        // We need to work with raw GEDCOM data, as we are looking for errors
        // which may prevent the GedcomRecord objects from working.

        $q1 = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->select(['i_id AS xref', 'i_gedcom AS gedcom', new Expression("'INDI' AS type")]);
        $q2 = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->select(['f_id AS xref', 'f_gedcom AS gedcom', new Expression("'FAM' AS type")]);
        $q3 = DB::table('media')
            ->where('m_file', '=', $tree->id())
            ->select(['m_id AS xref', 'm_gedcom AS gedcom', new Expression("'OBJE' AS type")]);
        $q4 = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->select(['s_id AS xref', 's_gedcom AS gedcom', new Expression("'SOUR' AS type")]);
        $q5 = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
            ->select(['o_id AS xref', 'o_gedcom AS gedcom', 'o_type']);
        $q6 = DB::table('change')
            ->where('gedcom_id', '=', $tree->id())
            ->where('status', '=', 'pending')
            ->orderBy('change_id')
            ->select(['xref', 'new_gedcom AS gedcom', new Expression("'' AS type")]);

        $rows = $q1
            ->unionAll($q2)
            ->unionAll($q3)
            ->unionAll($q4)
            ->unionAll($q5)
            ->unionAll($q6)
            ->get()
            ->map(static function (object $row): object {
                // Extract type for pending record
                if ($row->type === '' && preg_match('/^0 @[^@]*@ ([_A-Z0-9]+)/', $row->gedcom, $match)) {
                    $row->type = $match[1];
                }

                return $row;
            });

        $records = [];

        foreach ($rows as $row) {
            if ($row->gedcom !== '') {
                // existing or updated record
                $records[$row->xref] = $row;
            } else {
                // deleted record
                unset($records[$row->xref]);
            }
        }

        // LOOK FOR BROKEN LINKS
        $XREF_LINKS = [
            'NOTE'          => 'NOTE',
            'SOUR'          => 'SOUR',
            'REPO'          => 'REPO',
            'OBJE'          => 'OBJE',
            'SUBM'          => 'SUBM',
            'FAMC'          => 'FAM',
            'FAMS'          => 'FAM',
            //'ADOP'=>'FAM', // Need to handle this case specially. We may have both ADOP and FAMC links to the same FAM, but only store one.
            'HUSB'          => 'INDI',
            'WIFE'          => 'INDI',
            'CHIL'          => 'INDI',
            'ASSO'          => 'INDI',
            '_ASSO'         => 'INDI',
            // A webtrees extension
            'ALIA'          => 'INDI',
            'AUTH'          => 'INDI',
            // A webtrees extension
            'ANCI'          => 'SUBM',
            'DESI'          => 'SUBM',
            '_WT_OBJE_SORT' => 'OBJE',
            '_LOC'          => '_LOC',
        ];

        $RECORD_LINKS = [
            'INDI' => [
                'NOTE',
                'OBJE',
                'SOUR',
                'SUBM',
                'ASSO',
                '_ASSO',
                'FAMC',
                'FAMS',
                'ALIA',
                '_WT_OBJE_SORT',
                '_LOC',
            ],
            'FAM'  => [
                'NOTE',
                'OBJE',
                'SOUR',
                'SUBM',
                'ASSO',
                '_ASSO',
                'HUSB',
                'WIFE',
                'CHIL',
                '_LOC',
            ],
            'SOUR' => [
                'NOTE',
                'OBJE',
                'REPO',
                'AUTH',
                '_LOC',
            ],
            'REPO' => ['NOTE'],
            'OBJE' => ['NOTE'],
            // The spec also allows SOUR, but we treat this as a warning
            'NOTE' => [],
            // The spec also allows SOUR, but we treat this as a warning
            'SUBM' => [
                'NOTE',
                'OBJE',
            ],
            'SUBN' => ['SUBM'],
            '_LOC' => [
                'SOUR',
                'OBJE',
                '_LOC',
                'NOTE',
            ],
        ];

        $errors   = [];
        $warnings = [];

        // Generate lists of all links
        $all_links   = [];
        $upper_links = [];
        foreach ($records as $record) {
            $all_links[$record->xref]               = [];
            $upper_links[strtoupper($record->xref)] = $record->xref;
            preg_match_all('/\n\d (' . Gedcom::REGEX_TAG . ') @([^#@\n][^\n@]*)@/', $record->gedcom, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $all_links[$record->xref][$match[2]] = $match[1];
            }
        }

        foreach ($all_links as $xref1 => $links) {
            // PHP converts array keys to integers.
            $xref1 = (string) $xref1;

            $type1 = $records[$xref1]->type;
            foreach ($links as $xref2 => $type2) {
                // PHP converts array keys to integers.
                $xref2 = (string) $xref2;

                $type3 = isset($records[$xref2]) ? $records[$xref2]->type : '';
                if (!array_key_exists($xref2, $all_links)) {
                    if (array_key_exists(strtoupper($xref2), $upper_links)) {
                        $warnings[] =
                            $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) . ' ' .
                            /* I18N: placeholders are GEDCOM XREFs, such as R123 */
                            I18N::translate('%1$s does not exist. Did you mean %2$s?', $this->checkLink($tree, $xref2), $this->checkLink($tree, $upper_links[strtoupper($xref2)]));
                    } else {
                        /* I18N: placeholders are GEDCOM XREFs, such as R123 */
                        $errors[] = $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) . ' ' . I18N::translate('%s does not exist.', $this->checkLink($tree, $xref2));
                    }
                } elseif ($type2 === 'SOUR' && $type1 === 'NOTE') {
                    // Notes are intended to add explanations and comments to other records. They should not have their own sources.
                } elseif ($type2 === 'SOUR' && $type1 === 'OBJE') {
                    // Media objects are intended to illustrate other records, facts, and source/citations. They should not have their own sources.
                } elseif ($type2 === 'OBJE' && $type1 === 'REPO') {
                    $warnings[] =
                        $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) .
                        ' ' .
                        I18N::translate('This type of link is not allowed here.');
                } elseif (!array_key_exists($type1, $RECORD_LINKS) || !in_array($type2, $RECORD_LINKS[$type1], true) || !array_key_exists($type2, $XREF_LINKS)) {
                    $errors[] =
                        $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) .
                        ' ' .
                        I18N::translate('This type of link is not allowed here.');
                } elseif ($XREF_LINKS[$type2] !== $type3) {
                    // Target XREF does exist - but is invalid
                    $errors[] =
                        $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) . ' ' .
                        /* I18N: %1$s is an internal ID number such as R123. %2$s and %3$s are record types, such as INDI or SOUR */
                        I18N::translate('%1$s is a %2$s but a %3$s is expected.', $this->checkLink($tree, $xref2), $this->formatType($type3), $this->formatType($type2));
                } elseif (
                    $this->checkReverseLink($type2, $all_links, $xref1, $xref2, 'FAMC', ['CHIL']) ||
                    $this->checkReverseLink($type2, $all_links, $xref1, $xref2, 'FAMS', ['HUSB', 'WIFE']) ||
                    $this->checkReverseLink($type2, $all_links, $xref1, $xref2, 'CHIL', ['FAMC']) ||
                    $this->checkReverseLink($type2, $all_links, $xref1, $xref2, 'HUSB', ['FAMS']) ||
                    $this->checkReverseLink($type2, $all_links, $xref1, $xref2, 'WIFE', ['FAMS'])
                ) {
                    /* I18N: %1$s and %2$s are internal ID numbers such as R123 */
                    $errors[] = $this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) . ' ' . I18N::translate('%1$s does not have a link back to %2$s.', $this->checkLink($tree, $xref2), $this->checkLink($tree, $xref1));
                }
            }
        }

        $title = I18N::translate('Check for errors') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-check', [
            'errors'   => $errors,
            'title'    => $title,
            'tree'     => $tree,
            'warnings' => $warnings,
        ]);
    }

    /**
     * @param string               $type
     * @param array<array<string>> $links
     * @param string               $xref1
     * @param string               $xref2
     * @param string               $link
     * @param array<string>        $reciprocal
     *
     * @return bool
     */
    private function checkReverseLink(string $type, array $links, string $xref1, string $xref2, string $link, array $reciprocal): bool
    {
        return $type === $link && (!array_key_exists($xref1, $links[$xref2]) || !in_array($links[$xref2][$xref1], $reciprocal, true));
    }

    /**
     * Create a message linking one record to another.
     *
     * @param Tree   $tree
     * @param string $type1
     * @param string $xref1
     * @param string $type2
     * @param string $xref2
     *
     * @return string
     */
    private function checkLinkMessage(Tree $tree, string $type1, string $xref1, string $type2, string $xref2): string
    {
        /* I18N: The placeholders are GEDCOM XREFs and tags. e.g. “INDI I123 contains a FAMC link to F234.” */
        return I18N::translate(
            '%1$s %2$s has a %3$s link to %4$s.',
            $this->formatType($type1),
            $this->checkLink($tree, $xref1),
            $this->formatType($type2),
            $this->checkLink($tree, $xref2)
        );
    }

    /**
     * Format a link to a record.
     *
     * @param Tree   $tree
     * @param string $xref
     *
     * @return string
     */
    private function checkLink(Tree $tree, string $xref): string
    {
        return '<b><a href="' . e(route(GedcomRecordPage::class, [
                'xref' => $xref,
                'tree' => $tree->name(),
            ])) . '">' . $xref . '</a></b>';
    }

    /**
     * Format a record type.
     *
     * @param string $type
     *
     * @return string
     */
    private function formatType(string $type): string
    {
        return '<b>' . $type . '</b>';
    }
}
