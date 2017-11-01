# league/commonmark

[![Join the chat at https://gitter.im/thephpleague/commonmark](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/thephpleague/commonmark?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[![Latest Version](https://img.shields.io/packagist/v/league/commonmark.svg?style=flat-square)](https://packagist.org/packages/league/commonmark)
[![Total Downloads](https://img.shields.io/packagist/dt/league/commonmark.svg?style=flat-square)](https://packagist.org/packages/league/commonmark)
[![Software License](https://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/thephpleague/commonmark/master.svg?style=flat-square)](https://travis-ci.org/thephpleague/commonmark)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark)
[![CII Best Practices](https://bestpractices.coreinfrastructure.org/projects/126/badge)](https://bestpractices.coreinfrastructure.org/projects/126)

**league/commonmark** is a Markdown parser for PHP which supports the full [CommonMark] spec.  It is based on the [CommonMark JS reference implementation][commonmark.js] by [John MacFarlane] \([@jgm]\).

## Goals

* Fully support the CommonMark spec (100% compliance)
* Match the C and JavaScript implementations of CommonMark to make a logical and similar API
* Continuously improve performance without sacrificing quality or compliance
* Provide an extensible parser/renderer which users may customize as needed

## Installation

This project can be installed via [Composer]:

``` bash
$ composer require league/commonmark
```

**Note:** See [Versioning](#versioning) for important information on which version constraints you should use.

## Basic Usage

The `CommonMarkConverter` class provides a simple wrapper for converting CommonMark to HTML:

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
echo $converter->convertToHtml('# Hello World!');

// <h1>Hello World!</h1>
```

## Advanced Usage & Customization

The actual conversion process requires two steps:

 1. Parsing the Markdown input into an AST
 2. Rendering the AST document as HTML

Although the `CommonMarkConverter` wrapper simplifies this process for you, advanced users will likely want to do this themselves:

```php
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;

// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
$environment = Environment::createCommonMarkEnvironment();

// Optional: Add your own parsers, renderers, extensions, etc. (if desired)
// For example:  $environment->addInlineParser(new TwitterHandleParser());

// Define your configuration:
$config = ['html_input' => 'escape'];

// Create the converter
$converter = new CommonMarkConverter($config, $environment);

// Here's our sample input
$markdown = '# Hello World!';

// Let's render it!
echo $converter->convertToHtml($markdown);

// The output should be:
// <h1>Hello World!</h1>
```

This approach allows you to access/modify the AST before rendering it.

You can also add custom parsers/renderers by [registering them with the `Environment` class](https://commonmark.thephpleague.com/customization/environment/).
The [documentation][docs] provides several [customization examples][docs-examples] such as:

- [Parsing Twitter handles into profile links][docs-example-twitter]
- [Converting smilies into emoticon images][docs-example-smilies]

You can also reference the core CommonMark parsers/renderers as they use the same functionality available to you.

## Documentation

Documentation can be found at [commonmark.thephpleague.com][docs].

## Related Packages

### Integrations

- [CakePHP 3](https://github.com/gourmet/common-mark)
- [Drupal](https://www.drupal.org/project/commonmark)
- [Laravel 4 & 5](https://github.com/GrahamCampbell/Laravel-Markdown)
- [Sculpin](https://github.com/bcremer/sculpin-commonmark-bundle)
- [Symfony](https://github.com/webuni/commonmark-bundle)
- [Twig](https://github.com/webuni/commonmark-twig-renderer)

### CommonMark Extras

You can find several examples of useful extensions and customizations in the [league/commonmark-extras package](https://github.com/thephpleague/commonmark-extras).  You can add them to your parser or use them as examples to [develop your own custom features](https://commonmark.thephpleague.com/customization/overview/).

### Community Extensions

Custom parsers/renderers can be bundled into extensions which extend CommonMark.  Here are some that you may find interesting:

 - [Markua](https://github.com/dshafik/markua) - Markdown parser for PHP which intends to support the full Markua spec.
 - [CommonMark Table Extension](https://github.com/webuni/commonmark-table-extension) - Adds the ability to create tables in CommonMark documents.
 - [CommonMark Attributes Extension](https://github.com/webuni/commonmark-attributes-extension) - Adds a syntax to define attributes on the various HTML elements.
 - [Alt Three Emoji](https://github.com/AltThree/Emoji) An emoji parser for CommonMark.

If you build your own, feel free to submit a PR to add it to this list!

### Others

Check out the other cool things people are doing with `league/commonmark`: <https://packagist.org/packages/league/commonmark/dependents>

## Compatibility with CommonMark ##

This project aims to fully support the entire [CommonMark spec]. Other flavors of Markdown may work but are not supported.  Any/all changes made to the [spec][CommonMark spec] or [JS reference implementation][commonmark.js] should eventually find their way back into this codebase.

The following table shows which versions of league/commonmark are compatible with which version of the CommonMark spec:

<table>
    <thead>
        <tr>
            <th>league/commonmark</th>
            <th>CommonMark spec</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>0.15.3</strong><br>0.15.2</td>
            <td><strong><a href="http://spec.commonmark.org/0.27/">0.27</a></strong></td>
        </tr>
        <tr>
            <td>0.15.1<br>0.15.0</td>
            <td><a href="http://spec.commonmark.org/0.26/">0.26</a></td>
        </tr>
        <tr>
            <td>0.14.0<br>0.13.4<br>0.13.3<br>0.13.2</td>
            <td><a href="http://spec.commonmark.org/0.25/">0.25</a></td>
        </tr>
        <tr>
            <td>0.13.1<br>0.13.0</td>
            <td><a href="http://spec.commonmark.org/0.24/">0.24</a></td>
        </tr>
        <tr>
            <td>0.12.x<br>0.11.x</td>
            <td><a href="http://spec.commonmark.org/0.22/">0.22</a></td>
        </tr>
        <tr>
            <td>0.10.0</td>
            <td><a href="http://spec.commonmark.org/0.21/">0.21</a></td>
        </tr>
        <tr>
            <td>0.9.0</td>
            <td><a href="http://spec.commonmark.org/0.20/">0.20</a>
        </tr>
        <tr>
            <td>0.8.0</td>
            <td><a href="http://spec.commonmark.org/0.19/">0.19</a>
        </tr>
        <tr>
            <td>0.7.2<br>0.7.1<br>0.7.0<br>0.6.1</td>
            <td><a href="http://spec.commonmark.org/0.18/">0.18</a><br><a href="http://spec.commonmark.org/0.17/">0.17</a></td>
        </tr>
        <tr>
            <td>0.6.0</td>
            <td><a href="http://spec.commonmark.org/0.16/">0.16</a><br><a href="http://spec.commonmark.org/0.15/">0.15</a><br><a href="http://spec.commonmark.org/0.14/">0.14</a></td>
        </tr>
        <tr>
            <td>0.5.x<br>0.4.0</td>
            <td><a href="http://spec.commonmark.org/0.13/">0.13</a></td>
        </tr>
        <tr>
            <td>0.3.0</td>
            <td><a href="http://spec.commonmark.org/0.12/">0.12</a></td>
        </tr>
        <tr>
            <td>0.2.x</td>
            <td><a href="http://spec.commonmark.org/0.10/">0.10</a></td>
        </tr>
        <tr>
            <td>0.1.x</td>
            <td><a href="https://github.com/jgm/CommonMark/blob/2cf0750a7a507eded4cf3c9a48fd1f924d0ce538/spec.txt">0.01</a></td>
        </tr>
    </tbody>
</table>

This package is **not** part of CommonMark, but rather a compatible derivative.

## Testing

``` bash
$ ./vendor/bin/phpunit
```

This will also test league/commonmark against the latest supported spec.

## Performance Benchmarks

You can compare the performance of **league/commonmark** to other popular parsers by running the included benchmark tool:

``` bash
$ ./tests/benchmark/benchmark.php
```

## Versioning

[SemVer](http://semver.org/) will be followed closely.  0.x.0 versions will introduce breaking changes to the codebase, so be careful which version constraints you use. **It's highly recommended that you use [Composer's caret operator](https://getcomposer.org/doc/articles/versions.md#caret) to ensure compatibility**; for example: `^0.15`.  This is equivalent to `>=0.15.0 <0.16.0`.

0.x.y releases should not introduce breaking changes to the codebase; however, they might change the resulting AST or HTML output of parsed Markdown (due to bug fixes, minor spec changes, etc.)  As a result, you might get slightly different HTML, but any custom code built onto this library will still function correctly.

If you're only using the `CommonMarkConverter` class to convert Markdown (no other class references, custom parsers, etc.), then it should be safe to use a broader constraint like `~0.15`, `>0.15`, etc.  I personally promise to never break this specific class in any future 0.x release.

## Stability

While this package does work well, the underlying code should not be considered "stable" yet.  The original spec and JS parser may undergo changes in the near future which will result in corresponding changes to this code.  Any methods tagged with `@api` are not expected to change, but other methods/classes might.

Major release 1.0.0 will be reserved for when both CommonMark and this project are considered stable (see [outstanding CommonMark spec issues](http://talk.commonmark.org/t/issues-to-resolve-before-1-0-release/1287)).  0.x.y will be used until that happens.

## Contributing

If you encounter a bug in the spec, please report it to the [CommonMark] project.  Any resulting fix will eventually be implemented in this project as well.

For now, I'd like to maintain similar logic as the [JS reference implementation][commonmark.js] until everything is stable.  I'll gladly accept any contributions which:

 * Mirror fixes made to the [reference implementation][commonmark.js]
 * Optimize existing methods or regular expressions
 * Fix issues with adhering to the spec examples

Major refactoring should be avoided for now so that we can easily follow updates made to [the reference implementation][commonmark.js].  This restriction will likely be lifted once the CommonMark specs and implementations are considered stable.

Please see [CONTRIBUTING](https://github.com/thephpleague/commonmark/blob/master/CONTRIBUTING.md) for additional details.

## Security

If you discover any security related issues, please email your report privately to colinodell@gmail.com instead of using the issue tracker.

## Credits & Acknowledgements

- [Colin O'Dell][@colinodell]
- [John MacFarlane][@jgm]
- [All Contributors]

This code is a port of the [CommonMark JS reference implementation][commonmark.js] which is written, maintained and copyrighted by [John MacFarlane].  This project simply wouldn't exist without his work.

Also a huge thank you to [JetBrains](https://www.jetbrains.com/) for supporting the development of this project with complimentary [PhpStorm](https://www.jetbrains.com/phpstorm/) licenses.

## License ##

**league/commonmark** is licensed under the BSD-3 license.  See the `LICENSE` file for more details.

[CommonMark]: http://commonmark.org/
[CommonMark spec]: http://spec.commonmark.org/
[commonmark.js]: https://github.com/jgm/commonmark.js
[John MacFarlane]: http://johnmacfarlane.net
[docs]: https://commonmark.thephpleague.com/
[docs-examples]: https://commonmark.thephpleague.com/customization/overview/#examples
[docs-example-twitter]: https://commonmark.thephpleague.com/customization/inline-parsing#example-1---twitter-handles
[docs-example-smilies]: https://commonmark.thephpleague.com/customization/inline-parsing#example-2---emoticons
[All Contributors]: https://github.com/thephpleague/commonmark/contributors
[@colinodell]: https://github.com/colinodell
[@jgm]: https://github.com/jgm
[jgm/stmd]: https://github.com/jgm/stmd
[Composer]: https://getcomposer.org/
