<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Elements\AbstractXrefElement;
use Fisharebest\Webtrees\Elements\MultimediaFileReference;
use Fisharebest\Webtrees\Elements\MultimediaFormat;
use Fisharebest\Webtrees\Elements\SubmitterText;
use Fisharebest\Webtrees\Elements\UnknownElement;
use Fisharebest\Webtrees\Elements\XrefFamily;
use Fisharebest\Webtrees\Elements\XrefIndividual;
use Fisharebest\Webtrees\Elements\XrefLocation;
use Fisharebest\Webtrees\Elements\XrefMedia;
use Fisharebest\Webtrees\Elements\XrefNote;
use Fisharebest\Webtrees\Elements\XrefRepository;
use Fisharebest\Webtrees\Elements\XrefSource;
use Fisharebest\Webtrees\Elements\XrefSubmission;
use Fisharebest\Webtrees\Elements\XrefSubmitter;
use Fisharebest\Webtrees\Factories\ElementFactory;
use Fisharebest\Webtrees\Factories\ImageFactory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Mime;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submission;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\Expression;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_key_exists;
use function array_slice;
use function e;
use function implode;
use function preg_match;
use function route;
use function str_contains;
use function str_starts_with;
use function strtoupper;
use function substr_count;

/**
 * Check a tree for errors.
 */
class CheckTree implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function __construct(private readonly Gedcom $gedcom, private readonly TimeoutService $timeout_service)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $tree    = Validator::attributes($request)->tree();
        $skip_to = Validator::queryParams($request)->string('skip_to', '');

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
                if ($row->type === '' && str_starts_with($row->gedcom, '0 HEAD')) {
                    $row->type = 'HEAD';
                }

                if ($row->type === '' && preg_match('/^0 @[^@]*@ ([_A-Z0-9]+)/', $row->gedcom, $match) === 1) {
                    $row->type = $match[1];
                }

                return $row;
            });

        $records = [];
        $xrefs   = [];

        foreach ($rows as $row) {
            if ($row->gedcom !== '') {
                // existing or updated record
                $records[$row->xref] = $row;
            } else {
                // deleted record
                unset($records[$row->xref]);
            }

            $xrefs[strtoupper($row->xref)] = $row->xref;
        }

        unset($rows);

        $errors   = [];
        $warnings = [];
        $infos    = [];

        $element_factory = new ElementFactory();
        $this->gedcom->registerTags($element_factory, false);

        foreach ($records as $record) {
            // If we are nearly out of time, then stop processing here
            if ($skip_to === $record->xref) {
                $skip_to = '';
            } elseif ($skip_to !== '') {
                continue;
            } elseif ($this->timeout_service->isTimeNearlyUp()) {
                $skip_to = $record->xref;
                break;
            }

            $lines = explode("\n", $record->gedcom);
            array_shift($lines);

            $last_level = 0;
            $hierarchy  = [$record->type];

            foreach ($lines as $line_number => $line) {
                if (preg_match('/^(\d+) (\w+) ?(.*)/', $line, $match) !== 1) {
                    $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, I18N::translate('Invalid GEDCOM record.'), '');
                    break;
                }

                $level = (int) $match[1];
                if ($level > $last_level + 1) {
                    $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, I18N::translate('Invalid GEDCOM level number.'), '');
                    break;
                }

                $tag               = $match[2];
                $value             = $match[3];
                $hierarchy[$level] = $tag;
                $full_tag          = implode(':', array_slice($hierarchy, 0, 1 + $level));
                $element           = $element_factory->make($full_tag);
                $last_level        = $level;

                if ($tag === 'CONT') {
                    $element = new SubmitterText('CONT');
                }

                if ($element instanceof UnknownElement) {
                    if (str_starts_with($tag, '_') || str_starts_with($full_tag, '_') || str_contains($full_tag, ':_')) {
                        $message    = I18N::translate('Custom GEDCOM tags are discouraged. Try to use only standard GEDCOM tags.');
                        $warnings[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag);
                    } else {
                        $message  = I18N::translate('Invalid GEDCOM tag.') . ' ' . $full_tag;
                        $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag);
                    }
                } elseif ($element instanceof AbstractXrefElement) {
                    if (preg_match('/@(' . Gedcom::REGEX_XREF . ')@/', $value, $match) === 1) {
                        $xref1  = $match[1];
                        $xref2  = $xrefs[strtoupper($xref1)] ?? null;
                        $linked = $records[$xref2] ?? null;

                        if ($linked === null) {
                            $message  = I18N::translate('%s does not exist.', e($xref1));
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $tag . '-' . $xref1);
                        } elseif ($element instanceof XrefFamily && $linked->type !== Family::RECORD_TYPE) {
                            $message  = $this->linkErrorMessage($tree, $xref1, $linked->type, Family::RECORD_TYPE);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-type');
                        } elseif ($element instanceof XrefIndividual && $linked->type !== Individual::RECORD_TYPE) {
                            $message  = $this->linkErrorMessage($tree, $xref1, $linked->type, Individual::RECORD_TYPE);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-type');
                        } elseif ($element instanceof XrefMedia && $linked->type !== Media::RECORD_TYPE) {
                            $message  = $this->linkErrorMessage($tree, $xref1, $linked->type, Media::RECORD_TYPE);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-type');
                        } elseif ($element instanceof XrefNote && $linked->type !== Note::RECORD_TYPE) {
                            $message  = $this->linkErrorMessage($tree, $xref1, $linked->type, Note::RECORD_TYPE);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-type');
                        } elseif ($element instanceof XrefSource && $linked->type !== Source::RECORD_TYPE) {
                            $message  = $this->linkErrorMessage($tree, $xref1, $linked->type, Source::RECORD_TYPE);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-type');
                        } elseif ($element instanceof XrefRepository && $linked->type !== Repository::RECORD_TYPE) {
                            $message  = $this->linkErrorMessage($tree, $xref1, $linked->type, Repository::RECORD_TYPE);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-type');
                        } elseif ($element instanceof XrefSubmitter && $linked->type !== Submitter::RECORD_TYPE) {
                            $message  = $this->linkErrorMessage($tree, $xref1, $linked->type, Submitter::RECORD_TYPE);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-type');
                        } elseif ($element instanceof XrefSubmission && $linked->type !== Submission::RECORD_TYPE) {
                            $message  = $this->linkErrorMessage($tree, $xref1, $linked->type, Submission::RECORD_TYPE);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-type');
                        } elseif ($element instanceof XrefLocation && $linked->type !== Location::RECORD_TYPE) {
                            $message  = $this->linkErrorMessage($tree, $xref1, $linked->type, Location::RECORD_TYPE);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-type');
                        } elseif (($full_tag === 'FAM:HUSB' || $full_tag === 'FAM:WIFE') && !str_contains($linked->gedcom, "\n1 FAMS @" . $record->xref . '@')) {
                            $link1    = $this->recordLink($tree, $linked->xref);
                            $link2    = $this->recordLink($tree, $record->xref);
                            $message  = I18N::translate('%1$s does not have a link back to %2$s.', $link1, $link2);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-FAMS');
                        } elseif ($full_tag === 'FAM:CHIL' && !str_contains($linked->gedcom, "\n1 FAMC @" . $record->xref . '@')) {
                            $link1    = $this->recordLink($tree, $linked->xref);
                            $link2    = $this->recordLink($tree, $record->xref);
                            $message  = I18N::translate('%1$s does not have a link back to %2$s.', $link1, $link2);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-FAMC');
                        } elseif ($full_tag === 'INDI:FAMC' && !str_contains($linked->gedcom, "\n1 CHIL @" . $record->xref . '@')) {
                            $link1    = $this->recordLink($tree, $linked->xref);
                            $link2    = $this->recordLink($tree, $record->xref);
                            $message  = I18N::translate('%1$s does not have a link back to %2$s.', $link1, $link2);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-CHIL');
                        } elseif ($full_tag === 'INDI:FAMS' && !str_contains($linked->gedcom, "\n1 HUSB @" . $record->xref . '@') && !str_contains($linked->gedcom, "\n1 WIFE @" . $record->xref . '@')) {
                            $link1    = $this->recordLink($tree, $linked->xref);
                            $link2    = $this->recordLink($tree, $record->xref);
                            $message  = I18N::translate('%1$s does not have a link back to %2$s.', $link1, $link2);
                            $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-HUSB-WIFE');
                        } elseif ($xref1 !== $xref2) {
                            $message    = I18N::translate('%1$s does not exist. Did you mean %2$s?', e($xref1), e($xref2));
                            $warnings[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $tag . '-' . $xref1);
                        }
                    } elseif ($tag === 'SOUR') {
                        $message    = I18N::translate('Inline-source records are discouraged.');
                        $warnings[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-inline');
                    } else {
                        $message  = I18N::translate('Invalid GEDCOM value.');
                        $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-value-' . e($value));
                    }
                } elseif ($element->canonical($value) !== $value) {
                    $expected = e($element->canonical($value));
                    $actual   = strtr(e($value), ["\t" => '&rarr;']);
                    $message  = I18N::translate('“%1$s” should be “%2$s”.', $actual, $expected);
                    if (strtoupper($element->canonical($value)) !== strtoupper($value)) {
                        // This will be relevant for GEDCOM 7.0.  It's not relevant now, and causes confusion.
                        $infos[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-value');
                    }
                } elseif ($element instanceof MultimediaFormat) {
                    $mime = Mime::TYPES[$value] ?? Mime::DEFAULT_TYPE;

                    if ($mime === Mime::DEFAULT_TYPE) {
                        $message    = I18N::translate('webtrees does not recognise this file format.');
                        $warnings[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-' . e($value));
                    } elseif (str_starts_with($mime, 'image/') && !array_key_exists($mime, ImageFactory::SUPPORTED_FORMATS)) {
                        $message    = I18N::translate('webtrees cannot create thumbnails for this file format.');
                        $warnings[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '-' . e($value));
                    }
                } elseif ($element instanceof MultimediaFileReference && $value === 'gedcom.ged') {
                    $message  = I18N::translate('This filename is not compatible with the GEDZIP file format.');
                    $errors[] = $this->lineError($tree, $record->type, $record->xref, $line_number, $line, $message, $full_tag . '_' . e($value));
                }
            }

            if ($record->type === Family::RECORD_TYPE) {
                if (substr_count($record->gedcom, "\n1 HUSB @") > 1) {
                    $message  = I18N::translate('%s occurs too many times.', 'FAM:HUSB');
                    $errors[] = $this->recordError($tree, $record->type, $record->xref, $message, 'FAM:HUSB-count');
                }
                if (substr_count($record->gedcom, "\n1 WIFE @") > 1) {
                    $message  = I18N::translate('%s occurs too many times.', 'FAM:WIFE');
                    $errors[] = $this->recordError($tree, $record->type, $record->xref, $message, 'FAM:WIFE-count');
                }
            }
        }

        $title = I18N::translate('Check for errors') . ' — ' . e($tree->title());

        if ($skip_to === '') {
            $more_url = '';
        } else {
            $more_url = route(self::class, ['tree' => $tree->name(), 'skip_to' => $skip_to]);
        }

        return $this->viewResponse('admin/trees-check', [
            'errors'   => $errors,
            'infos'    => $infos,
            'more_url' => $more_url,
            'title'    => $title,
            'tree'     => $tree,
            'warnings' => $warnings,
        ]);
    }

    private function recordType(string $type): string
    {
        $types = [
            Family::RECORD_TYPE     => I18N::translate('Family'),
            Header::RECORD_TYPE     => I18N::translate('Header'),
            Individual::RECORD_TYPE => I18N::translate('Individual'),
            Location::RECORD_TYPE   => I18N::translate('Location'),
            Media::RECORD_TYPE      => I18N::translate('Media object'),
            Note::RECORD_TYPE       => I18N::translate('Note'),
            Repository::RECORD_TYPE => I18N::translate('Repository'),
            Source::RECORD_TYPE     => I18N::translate('Source'),
            Submission::RECORD_TYPE => I18N::translate('Submission'),
            Submitter::RECORD_TYPE  => I18N::translate('Submitter'),
        ];

        return $types[$type] ?? e($type);
    }

    private function recordLink(Tree $tree, string $xref): string
    {
        $url = route(GedcomRecordPage::class, ['xref' => $xref, 'tree' => $tree->name()]);

        return '<a href="' . e($url) . '">' . e($xref) . '</a>';
    }

    private function linkErrorMessage(Tree $tree, string $xref, string $type1, string $type2): string
    {
        $link  = $this->recordLink($tree, $xref);
        $type1 = $this->recordType($type1);
        $type2 = $this->recordType($type2);

        return I18N::translate('%1$s is a %2$s but a %3$s is expected.', $link, $type1, $type2);
    }

    /**
     * Format a link to a record.
     *
     * @return object{message:string,tag:string}
     */
    private function lineError(
        Tree $tree,
        string $type,
        string $xref,
        int $line_number,
        string $line,
        string $message,
        string $tag
    ): object {
        $message =
            I18N::translate('%1$s: %2$s', $this->recordType($type), $this->recordLink($tree, $xref)) .
            ' — ' .
            I18N::translate('%1$s: %2$s', I18N::translate('Line number'), I18N::number($line_number)) .
            ' — ' .
            '<code>' . e($line) . '</code>' .
            '<br>' . $message;

        return (object) [
            'message' => $message,
            'tag'     => $tag,
        ];
    }

    /**
     * Format a link to a record.
     *
     * @return object{message:string,tag:string}
     */
    private function recordError(Tree $tree, string $type, string $xref, string $message, string $tag): object
    {
        $message = I18N::translate('%1$s: %2$s', $this->recordType($type), $this->recordLink($tree, $xref)) . ' — ' . $message;

        return (object) [
            'message' => $message,
            'tag'     => $tag,
        ];
    }
}
