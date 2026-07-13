// jQuery 4 removed these utilities; shim them for remaining legacy plugins.
import $ from 'jquery';

$.isArray = Array.isArray;
$.isFunction = function (obj) { return typeof obj === 'function'; };
$.proxy = function (fn, context) { return fn.bind(context); };

window.$ = window.jQuery = $;

window.webtreesLegacy = window.webtreesLegacy || {};

window.webtreesLegacy.configureAjaxCsrf = function () {
  const csrf = document.head.querySelector('meta[name=csrf]');
  const token = csrf?.getAttribute('content') ?? '';

  if (token === '') {
	return;
  }

  $.ajaxSetup({
	headers: {
	  'X-CSRF-TOKEN': token,
	},
  });
};

window.webtreesLegacy.initializeTypeahead = function (element, options) {
  if (typeof $.fn?.typeahead !== 'function') {
	throw new Error('Typeahead plugin is not available.');
  }

  $(element).typeahead(null, options);
};

