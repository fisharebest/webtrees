var _ga = _ga || {};
var _gaq = _gaq || [];
_ga.getSocialActionTrackers_ = function(
    network, socialAction, opt_target, opt_pagePath) {
  return function() {
    var trackers = _gat._getTrackers();
    for (var i = 0, tracker; tracker = trackers[i]; i++) {
      tracker._trackSocial(network, socialAction, opt_target, opt_pagePath);
    }
  };
};


_ga.trackFacebook = function(opt_pagePath) {
  try {
    if (FB && FB.Event && FB.Event.subscribe) {
      FB.Event.subscribe('edge.create', function(opt_target) {
        _gaq.push(_ga.getSocialActionTrackers_('facebook', 'like',
            opt_target, opt_pagePath));
      });
      FB.Event.subscribe('edge.remove', function(opt_target) {
        _gaq.push(_ga.getSocialActionTrackers_('facebook', 'unlike',
            opt_target, opt_pagePath));
      });
      FB.Event.subscribe('message.send', function(opt_target) {
        _gaq.push(_ga.getSocialActionTrackers_('facebook', 'send',
            opt_target, opt_pagePath));
      });
    }
  } catch (e) {}
};
_ga.trackTwitterHandler_ = function(intent_event, opt_pagePath) {
  var opt_target; //Default value is undefined
  if (intent_event && intent_event.type == 'tweet' ||
          intent_event.type == 'click') {
    if (intent_event.target.nodeName == 'IFRAME') {
      opt_target = _ga.extractParamFromUri_(intent_event.target.src, 'url');
    }
    var socialAction = intent_event.type + ((intent_event.type == 'click') ?
        '-' + intent_event.region : ''); //append the type of click to action
    _gaq.push(_ga.getSocialActionTrackers_('twitter', socialAction, opt_target,
        opt_pagePath));
  }
};
_ga.trackTwitter = function(opt_pagePath) {
  intent_handler = function(intent_event) {
    _ga.trackTwitterHandler_(intent_event, opt_pagePath);
  };

  //bind twitter Click and Tweet events to Twitter tracking handler
  twttr.events.bind('click', intent_handler);
  twttr.events.bind('tweet', intent_handler);
};

_ga.extractParamFromUri_ = function(uri, paramName) {
  if (!uri) {
    return;
  }
  var regex = new RegExp('[\\?&#]' + paramName + '=([^&#]*)');
  var params = regex.exec(uri);
  if (params != null) {
    return unescape(params[1]);
  }
  return;
};
//<!-- Load Twitter JS-API asynchronously -->
    window.twttr = (function (d,s,id) {
      var t, js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
      js.src="//platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
      return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
    }(document, "script", "twitter-wjs"));
    // Wait for the asynchronous resources to load
    twttr.ready(function(twttr) {
        _ga.trackTwitter(); //Google Analytics tracking
    });