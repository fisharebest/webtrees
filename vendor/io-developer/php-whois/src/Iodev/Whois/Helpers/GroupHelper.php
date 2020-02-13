<?php

namespace Iodev\Whois\Helpers;

class GroupHelper
{
    /**
     * @param array $group
     * @param bool $keysOnly
     * @return array
     */
    public static function toLowerCase($group, $keysOnly = false)
    {
        return $keysOnly
            ? self::mapRecursiveKeys($group, 'mb_strtolower')
            : self::mapRecursive($group, 'mb_strtolower');
    }

    /**
     * @param array $group
     * @param callable $callback
     * @return array
     */
    public static function mapRecursive($group, $callback) {
        $out = [];
        array_walk($group, function($val, $key) use (&$out, $callback) {
            $out[$callback($key)] = is_array($val) ? self::mapRecursive($val, $callback) : $callback($val);
        });
        return $out;
    }

    /**
     * @param array $group
     * @param callable $callback
     * @return array
     */
    public static function mapRecursiveKeys($group, $callback) {
        $out = [];
        array_walk($group, function($val, $key) use (&$out, $callback) {
            $out[$callback($key)] = is_array($val) ? self::mapRecursiveKeys($val, $callback) : $val;
        });
        return $out;
    }

    /**
     * @param array $group
     * @param string[] $keys
     * @param bool $ignoreCase
     * @return string|string[]
     */
    public static function matchFirst($group, $keys, $ignoreCase = true)
    {
        $matches = self::match($group, $keys, $ignoreCase, true);
        return empty($matches) ? "" : reset($matches);
    }

    /**
     * @param array $group
     * @param string[] $keys
     * @param bool $ignoreCase
     * @param bool $firstOnly
     * @return string|string[]
     */
    public static function match($group, $keys, $ignoreCase = true, $firstOnly = false)
    {
        $matches = [];
        if (empty($group)) {
            return [];
        }
        if ($ignoreCase) {
            $group = self::toLowerCase($group, true);
        }
        foreach ($keys as $k) {
            if (is_array($k)) {
                $vals = self::matchAll($group, $k, $ignoreCase);
                if (count($vals) > 1) {
                    $matches[] = $vals;
                } elseif (count($vals) == 1) {
                    $matches[] = $vals[0];
                } else {
                    $matches[] = "";
                }
            } else {
                $k = $ignoreCase ? mb_strtolower($k) : $k;
                if (isset($group[$k])) {
                    $matches[] = $group[$k];
                }
            }
            if ($firstOnly && count($matches) > 0) {
                return $matches;
            }
        }
        return $matches;
    }

    /**
     * @param array $group
     * @param string[] $keys
     * @param bool $ignoreCase
     * @return string[]
     */
    private static function matchAll($group, $keys, $ignoreCase = true)
    {
        $vals = [];
        foreach ($keys as $k) {
            $v = self::matchFirst($group, [$k], $ignoreCase);
            if (is_array($v)) {
                $vals = array_merge($vals, $v);
            } elseif (!empty($v)) {
                $vals[] = $v;
            }
        }
        return $vals;
    }


    /**
     * @param array $groups
     * @param string[] $keys
     * @param bool $ignoreCase
     * @return string
     */
    public static function matchFirstIn($groups, $keys, $ignoreCase = true)
    {
        foreach ($groups as $group) {
            $v = self::matchFirst($group, $keys, $ignoreCase);
            if (!empty($v)) {
                return $v;
            }
        }
        return "";
    }

    /**
     * @param array $subsets
     * @param array $params
     * @return array
     */
    public static function renderSubsets($subsets, $params)
    {
        array_walk_recursive($subsets, function(&$val) use ($params) {
            $val = preg_replace_callback('~\\$[a-z\d]+~ui', function($m) use ($params) {
                $arg = $m[0];
                return isset($params[$arg]) ? $params[$arg] : $arg;
            }, $val);
        });
        return $subsets;
    }

    /**
     * @param array $groups
     * @param array $subsets
     * @param bool $ignoreCase
     * @return array|null
     */
    public static function findGroupHasSubsetOf($groups, $subsets, $ignoreCase = true)
    {
        $foundGroups = self::findGroupsHasSubsetOf($groups, $subsets, $ignoreCase, true);
        return empty($foundGroups) ? null : $foundGroups[0];
    }

    /**
     * @param array $groups
     * @param array $subsets
     * @param bool $ignoreCase
     * @param bool $stopnOnFirst
     * @return array
     */
    public static function findGroupsHasSubsetOf($groups, $subsets, $ignoreCase = true, $stopnOnFirst = false)
    {
        $foundGroups = [];
        $preparedGroups = [];
        foreach ($groups as $group) {
            $preparedGroups[] = $ignoreCase ? self::toLowerCase($group) : $group;
        }
        $subsets = $ignoreCase ? self::toLowerCase($subsets) : $subsets;
        foreach ($subsets as $subset) {
            foreach ($preparedGroups as $index => $group) {
                if (self::hasSubset($group, $subset)) {
                    $foundGroups[] = $groups[$index];
                    if ($stopnOnFirst) {
                        break;
                    }
                }
            }
        }
        return $foundGroups;
    }

    /**
     * @param array $group
     * @param array $subsets
     * @return bool
     */
    public static function hasSubsetOf($group, $subsets)
    {
        foreach ($subsets as $subset) {
            if (self::hasSubset($group, $subset)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $group
     * @param array $subset
     * @return bool
     */
    public static function hasSubset($group, $subset)
    {
        foreach ($subset as $k => $v) {
            if (!isset($group[$k])) {
                return false;
            }
            if (empty($v)) {
                continue;
            }
            if (is_array($group[$k])) {
                foreach ($group[$k] as $sub) {
                    if (strval($sub) == strval($v)) {
                        $found = true;
                    }
                }
            } else {
                $found = (strval($group[$k]) == strval($v));
            }
            if (empty($found)) {
                 return false;
            }
        }
        return true;
    }

    /**
     * @param array $groups
     * @param string $domain
     * @param string[] $domainKeys
     * @return array
     */
    public static function findDomainGroup($groups, $domain, $domainKeys)
    {
        $foundGroups = self::findDomainGroups($groups, $domain, $domainKeys, true);
        return empty($foundGroups) ? null : $foundGroups[0];
    }

    /**
     * @param array $groups
     * @param string $domain
     * @param string[] $domainKeys
     * @param bool $stopOnFirst
     * @return array
     */
    public static function findDomainGroups($groups, $domain, $domainKeys, $stopOnFirst = false)
    {
        $foundGroups = [];
        foreach ($groups as $group) {
            $foundDomain = self::getAsciiServer($group, $domainKeys);
            if ($foundDomain && DomainHelper::compareNames($foundDomain, $domain)) {
                $foundGroups[] = $group;
                if ($stopOnFirst) {
                    break;
                }
            }
        }
        return $foundGroups;
    }

    /**
     * @param array $group
     * @param string[] $keys
     * @return string
     */
    public static function getAsciiServer($group, $keys)
    {
        $servers = self::getAsciiServers($group, $keys);
        return empty($servers) ? "" : $servers[0];
    }

    /**
     * @param array $group
     * @param string[] $keys
     * @return string[]
     */
    public static function getAsciiServers($group, $keys)
    {
        $raws = self::matchFirst($group, $keys);
        $raws = !empty($raws) ? $raws : [];
        $raws = is_array($raws) ? $raws : [ $raws ];
        $servers = [];
        foreach ($raws as $raw) {
            $s = DomainHelper::toAscii($raw);
            if (!empty($s)) {
                $servers[] = $s;
            }
        }
        return $servers;
    }
}
