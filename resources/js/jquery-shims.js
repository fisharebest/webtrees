// jQuery 4 removed these utilities; shim them for legacy plugins (corejs-typeahead, jquery-colorbox).
import $ from 'jquery';

$.isArray = Array.isArray;
$.isFunction = function (obj) { return typeof obj === 'function'; };
$.proxy = function (fn, context) { return fn.bind(context); };

window.$ = window.jQuery = $;
