import $ from 'jquery';


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


