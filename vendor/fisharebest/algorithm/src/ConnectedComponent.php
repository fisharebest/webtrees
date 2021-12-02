<?php

namespace Fisharebest\Algorithm;

/**
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2021 Greg Roach <greg@subaqua.co.uk>
 * @license   GPL-3.0+
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses>.
 */

/**
 * Class ConnectedComponent - Use a depth-first search to find connected
 * components of an undirected graph.
 */
class ConnectedComponent
{
    /** @var integer[][] The graph, where $graph[node1][node2]=cost */
    protected $graph;

    /** @var int[] The component number for each node */
    protected $nodes;

    /** @var int The next connected-component to find. */
    protected $component;

    /**
     * @param integer[][] $graph
     */
    public function __construct($graph)
    {
        $this->graph = $graph;
        $this->nodes = array_fill_keys(array_keys($graph), 0);
        $this->component = 0;
    }

    /**
     * An array of components (arrays).
     *
     * @return array
     */
    public function findConnectedComponents()
    {
        // Find the first unallocated node
        $node = array_search(0, $this->nodes, true);

        while ($node !== false) {
            // Find the next connected-component.
            $this->component++;
            $this->depthFirstSearch($node, $this->component);

            // Find the next unallocated node.
            $node = array_search(0, $this->nodes, true);
        }

        return $this->groupResults();
    }

    /**
     * Group the nodes by component.
     *
     * @return array
     */
    private function groupResults()
    {
        $result = array();
        foreach ($this->nodes as $node => $component) {
            if (array_key_exists($component, $result)) {
                $result[$component][] = $node;
            } else {
                $result[$component] = array($node);
            }
        }

        return $result;
    }

    /**
     * Find all nodes connected to $node and mark them as part of
     * component $component.
     *
     * @param $node
     * @param $component
     */
    private function depthFirstSearch($node, $component)
    {
        $this->nodes[$node] = $component;

        foreach (array_keys($this->graph[$node]) as $next) {
            if ($this->nodes[$next] === 0) {
                $this->depthFirstSearch($next, $component);
            }
        }
    }
}
