<?php

namespace Iodev\Whois\Helpers;

class ParserHelper
{
    /**
     * @param string $text
     * @return string[]
     */
    public static function splitLines($text)
    {
        return preg_split('~\r\n|\r|\n~ui', strval($text));
    }

    /**
     * @param string[] $lines
     * @param string $header
     * @return array
     */
    public static function linesToGroups($lines, $header = '$header')
    {
        $groups = [];
        $group = [];
        $headerLines = [];
        $lines[] = '';
        foreach ($lines as $line) {
            $trimChars = " \t\n\r\0\x0B";
            $isComment = mb_strlen($line) != mb_strlen(ltrim($line, "%#;:"));
            $line = ltrim(rtrim($line, "%#*=$trimChars"), "%#*=;$trimChars");
            $headerLine = trim($line, ':[]');
            $headerLines[] = $headerLine;
            $kv = $isComment ? [] : self::lineToKeyVal($line, ":$trimChars");
            if (count($kv) == 2) {
                $group = array_merge_recursive($group, [$kv[0] => ltrim($kv[1], ".")]);
                continue;
            }
            if (empty($group[$header]) && count($group) > 0) {
                $group[$header] = self::linesToBestHeader($headerLines);
            }
            if (count($group) > 1) {
                $groups[] = array_filter($group);
                $group = [];
                $headerLines = [$headerLine];
            }
        }
        return $groups;
    }

    /**
     * @param string $line
     * @param string $trimChars
     * @return string[]
     */
    public static function lineToKeyVal($line, $trimChars = " \t\n\r\0\x0B")
    {
        if (preg_match('~^\s*(\.{2,})?\s*(.+?)\s*(\.{2,})?\s*:(?![\\/:])(?<!::)(.*)$~ui', $line, $m)) {
            return [trim($m[2], $trimChars), trim($m[4], $trimChars)];
        }
        return [trim($line, $trimChars)];
    }

    /**
     * @param string[] $lines
     * @return int|null|string
     */
    public static function linesToBestHeader($lines)
    {
        $map = [];
        $empty = 1;
        foreach ($lines as $line) {
            if (empty($line)) {
                $empty++;
                continue;
            }
            if ($empty > 0) {
                $empty = 0;
                $map[$line] = mb_strlen($line) + count(preg_split('~\s+~ui', $line));
            }
        }
        $header = '';
        if (!empty($map)) {
            asort($map, SORT_NUMERIC);
            $header = key($map);
        }
        return $header;
    }

    /**
     * @param string[] $lines
     * @param callable $validateStoplineFn
     * @return array
     */
    public static function linesToSpacedBlocks($lines, $validateStoplineFn = null)
    {
        $lines[] = '';
        $blocks = [];
        $block = [];
        foreach ($lines as $line) {
            $tline = trim($line);
            if (!empty($tline) && empty($block) && is_callable($validateStoplineFn) && !$validateStoplineFn($line)) {
                break;
            } elseif (!empty($tline)) {
                $block[] = $line;
            } elseif (!empty($block)) {
                $blocks[] = $block;
                $block = [];
            }
        }
        return $blocks;
    }

    /**
     * @param array $block
     * @param callable $biasIndentFn
     * @param int $maxDepth
     * @return array
     */
    public static function blockToIndentedNodes($block, $biasIndentFn = null, $maxDepth = 10)
    {
        $nodes = [];
        $node = [];
        $nodePad = 999999;
        foreach ($block as $line) {
            $pad = self::calcIndent($line, $biasIndentFn);
            if ($pad <= $nodePad) {
                $nodePad = $pad;
                $nodes[] = [
                    'line' => $line,
                    'children' => [],
                ];
                $node = &$nodes[count($nodes) - 1];
            } else {
                $node['children'][] = $line;
            }
        }
        unset($node);
        foreach ($nodes as &$node) {
            if (!empty($node['children']) && $maxDepth > 1) {
                $node['children'] = self::blockToIndentedNodes($node['children'], $maxDepth - 1);
            }
            if (empty($node['children'])) {
                $node = $node['line'];
            }
        }
        return $nodes;
    }

    /**
     * @param string $line
     * @param callable $biasFn
     * @return int
     */
    public static function calcIndent($line, $biasFn = null)
    {
        $pad = strlen($line) - strlen(ltrim($line));
        if (is_callable($biasFn)) {
            $pad += $biasFn($line);
        }
        return $pad;
    }

    /**
     * @param array $nodes
     * @param int $maxKeyLength
     * @return array
     */
    public static function nodesToDict($nodes, $maxKeyLength = 32)
    {
        $dict = [];
        foreach ($nodes as $node) {
            $node = is_array($node) ? $node : ['line' => $node, 'children' => []];
            $k = '';
            $v = '';
            $kv = self::lineToKeyVal($node['line']);
            if (count($kv) == 2) {
                list ($k, $v) = $kv;
                if (empty($v)) {
                    $v = self::nodesToDict($node['children']);
                } elseif (strlen($k) <= $maxKeyLength) {
                    $v = array_merge([$v], $node['children']);
                    $v = array_map('trim', $v);
                    $v = array_filter($v, 'strlen');
                    $v = empty($v) ? [''] : $v;
                } else {
                    $kv = [$node['line']];
                }
            }
            if (count($kv) == 1) {
                $k = trim($kv[0]);
                $v = self::nodesToDict($node['children']);
                if (empty($v)) {
                    $v = $k;
                    $k = '';
                }
            }
            if (!empty($k)) {
                $v = is_array($v)
                    ? (count($v) > 1 ? $v : reset($v))
                    : $v;
                $dict = array_merge_recursive($dict, [$k => $v]);
            } else {
                $dict[] = $v;
            }
        }
        return $dict;
    }

    /**
     * @param array $dict
     * @param string $header
     * @return array
     */
    public static function dictToGroup($dict, $header = '$header') {
        if (empty($dict) || count($dict) > 1) {
            return $dict;
        }
        $k = array_keys($dict)[0];
        $v = array_values($dict)[0];
        if (!is_string($k) || !is_array($v)) {
            return $dict;
        }
        $vk = array_keys($v)[0];
        if (is_string($vk)) {
            return array_merge([$header => $k], $v);
        }
        $dict[$header] = $k;
        return $dict;
    }

    /**
     * @param array $groups
     * @return array
     */
    public static function joinParentlessGroups($groups) {
        $lastGroup = null;
        foreach ($groups as &$group) {
            if (count($group) == 1 && is_string(key($group)) && reset($group) === false) {
                $lastGroup = &$group;
                unset($group);
            } elseif (isset($lastGroup) && count($group) > 0 && is_string(key($group)) && reset($group)) {
                $lastGroup[key($lastGroup)] = $group;
                unset($lastGroup);
            }
        }
        unset($lastGroup);
        unset($group);
        return $groups;
    }

    /**
     * @param string[]|string $rawstates
     * @param bool $removeExtra
     * @return string[]
     */
    public static function parseStates($rawstates, $removeExtra = true)
    {
        $states = [];
        $rawstates = is_array($rawstates) ? $rawstates : [ strval($rawstates) ];
        foreach ($rawstates as $rawstate) {
            if (preg_match('/^\s*((\d{3}\s+)?[a-z]{2,}.*)\s*/ui', $rawstate, $m)) {
                $state = mb_strtolower($m[1]);
                $state = $removeExtra ? trim(preg_replace('~\(.+?\)|((- )?http|<a href).+~ui', '', $state)) : $state;

                if (!empty($state)) {
                    $states[] = $state;
                }
            }
        }
        return (count($states) == 1) ? array_filter(array_map('trim', explode(',', $states[0]))) : $states;
    }

    /**
     * @param string[] $lines
     * @return string[]
     */
    public static function autofixTldLines($lines)
    {
        $emptyBefore = false;
        $kvBefore = false;
        $needIndent = false;
        $outLines = [];
        foreach ($lines as $i => $line) {
            if ($emptyBefore && preg_match('~^\w+(\s+\w+){0,2}$~', trim(rtrim($line, ':')))) {
                $line = trim(rtrim($line, ':')) . ':';
            }
            // .jp style
            if (preg_match('~([a-z]\.)?\s*\[(.+?)\]\s+(.*)$~', $line, $m)) {
                $line = sprintf('%s: %s', $m[2], $m[3]);
            }
            $isHeader = preg_match('~^\w+(\s+\w+){0,2}:$~', $line);
            if ($isHeader) {
                $outLines[] = '';
            }
            $needIndent = $needIndent || $isHeader;
            if (!empty($line) || !$kvBefore) {
                if ($needIndent && !$isHeader && !empty($line)) {
                    $indent = '    ';
                    $nextLinePad = empty($lines[$i + 1]) || strlen(trim($lines[$i + 1])) == 0 ? 0 : self::calcIndent($lines[$i + 1]);
                    if ($nextLinePad <= 2 && self::calcIndent($lines[$i]) == 0) {
                        $indent .= str_repeat(' ', $nextLinePad);
                    }
                    $outLines[] = $indent . $line;
                } else {
                    $outLines[] = $line;
                }
            }
            $emptyBefore = empty($line);
            $kvBefore = preg_match('~^\w+(\s+\w+){0,2}:\s*\S+~', $line);
        }
        return $outLines;
    }

    /**
     * Removes unnecessary empty lines inside block
     * @param string[] $lines
     * @param callable|null $biasIndentFn
     * @return string[]
     */
    public static function removeInnerEmpties($lines, $biasIndentFn = null)
    {
        $prevPad = 0;
        $outLines = [];
        foreach ($lines as $index => $line) {
            if (empty($line)) {
                $nextLine = isset($lines[$index + 1]) ? $lines[$index + 1] : '';
                if (!empty($nextLine) && $prevPad > 0 && $prevPad == self::calcIndent($nextLine, $biasIndentFn)) {
                    continue;
                }
            }
            $prevPad = empty($line) ? 0 : self::calcIndent($line, $biasIndentFn);
            $outLines[] = $line;
        }
        return $outLines;
    }
}
