[![Build Status](https://travis-ci.org/fisharebest/algorithm.svg)](https://travis-ci.org/fisharebest/algorithm)
[![Coverage Status](https://coveralls.io/repos/fisharebest/algorithm/badge.svg?branch=master)](https://coveralls.io/r/fisharebest/algorithm?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4997a2c6-fb22-433e-92c5-ae7285f1a5a0/mini.png)](https://insight.sensiolabs.com/projects/4997a2c6-fb22-433e-92c5-ae7285f1a5a0)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fisharebest/algorithm/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fisharebest/algorithm/?branch=master)
[![Code Climate](https://codeclimate.com/github/fisharebest/algorithm/badges/gpa.svg)](https://codeclimate.com/github/fisharebest/algorithm)

# fisharebest/algorithm

General purpose algorithms in PHP

## Installation

Use [composer](https://getcomposer.org), and add `"fisharebest/algorithm": "*"` to the dependencies in your `composer.json`.


## Dijkstra

[Dijkstra's algorithm](https://en.wikipedia.org/wiki/Dijkstra%27s_algorithm) finds the
shortest path(s) between two nodes in a weighted, directed graph.

Graphs are specified as an array of edges, each with a cost.  The example below is
an undirected graph (i.e. if D→E is 9, then E→D is also 9.), because it is easy to
understand and easy to draw.  However, the algorithm works equally well for undirected
graphs, where links can be one-way only or have different costs in each direction.
```
     D---9---E
    / \       \
  14   2       6
  /     \       \
 A---9---B--11--C
  \     /      /
   7  10      /
    \ /      /
     F-----15       G
```

Sample code for the above graph.

``` php
use Fisharebest\Algorithm\Dijkstra;

$graph = array(
  'A' => array('B' => 9, 'D' => 14, 'F' => 7),
  'B' => array('A' => 9, 'C' => 11, 'D' => 2, 'F' => 10),
  'C' => array('B' => 11, 'E' => 6, 'F' => 15),
  'D' => array('A' => 14, 'B' => 2, 'E' => 9),
  'E' => array('C' => 6, 'D' => 9),
  'F' => array('A' => 7, 'B' => 10, 'C' => 15),
  'G' => array(),
);

$dijkstra = new Dijkstra($graph);

// There can be zero, one or more shortest (i.e. same total cost) paths.

// No shortest path.
$path = $dijkstra->shortestPaths('A', 'G'); // array()

// Exactly one shortest path.
$path = $dijkstra->shortestPaths('A', 'E'); // array(array('A', 'B', 'D', 'E'))

// Multiple solutions with the same shortest path.
$path = $dijkstra->shortestPaths('E', 'F'); // array(array('E', 'D', 'B', 'F'), array('E', 'C', 'F'))

// To find next-shortest paths, exclude one or intermediate nodes from the shortest path.
$path = $dijkstra->shortestPaths('A', 'E'); // array(array('A', 'B', 'D', 'E'))
$path = $dijkstra->shortestPaths('A', 'E', array('B')); // array(array('A', 'B', 'D', 'E'))
$path = $dijkstra->shortestPaths('A', 'E', array('D')); // array(array('A', 'B', 'C', 'E'))
$path = $dijkstra->shortestPaths('A', 'E', array('B', 'D')); // array(array('A', 'F', 'C', 'E'))


```

