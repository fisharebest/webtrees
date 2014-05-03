/*!
	Wheelzoom 2.0.0
	(c) 2014 Jack Moore - http://www.jacklmoore.com/wheelzoom
	license: http://www.opensource.org/licenses/mit-license.php
	dependencies: jQuery 1.9+ or 2.0+
	supports: modern browsers, and IE9 and up
*/
(function($){
	var defaults = {
		zoom: 0.10
	};
	var wheel;

	function setSrcToBackground(img) {
		var transparentPNG = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==";

		// Explicitly set the size to the current dimensions,
		// as the src is about to be changed to a 1x1 transparent png.
		img.width = img.width;
		img.height = img.height;

		img.style.backgroundImage = "url("+img.src+")";
		img.style.backgroundRepeat = 'no-repeat';
		img.src = transparentPNG;
	}

	if ( document.onmousewheel !== undefined ) { // Webkit/Opera/IE
		wheel = 'onmousewheel';
	}
	else if ( document.onwheel !== undefined) { // FireFox 17+
		wheel = 'onwheel';
	}

	$.fn.wheelzoom = function(options){
		var settings = $.extend({}, defaults, options);

		if (!this[0] || !wheel || !('backgroundSize' in this[0].style)) { // do nothing in IE8 and lower
			return this;
		}

		return this.each(function(){
			var img = this,
				$img = $(img);

			function loaded() {
				var width = $img.width(),
					height = $img.height(),
					bgWidth = width,
					bgHeight = height,
					bgPosX = 0,
					bgPosY = 0;

				function reset() {
					bgWidth = width;
					bgHeight = height;
					bgPosX = bgPosY = 0;
					updateBgStyle();
				}

				function updateBgStyle() {
					if (bgPosX > 0) {
						bgPosX = 0;
					} else if (bgPosX < width - bgWidth) {
						bgPosX = width - bgWidth;
					}

					if (bgPosY > 0) {
						bgPosY = 0;
					} else if (bgPosY < height - bgHeight) {
						bgPosY = height - bgHeight;
					}

					img.style.backgroundSize = bgWidth+'px '+bgHeight+'px';
					img.style.backgroundPosition = bgPosX+'px '+bgPosY+'px';
				}

				setSrcToBackground(img);

				$img.css({
					backgroundSize: width+'px '+height+'px',
					backgroundPosition: '0 0'
				}).bind('wheelzoom.reset', reset);

				img[wheel] = function (e) {
					var deltaY = 0;

					e.preventDefault();

					if (e.deltaY) { // FireFox 17+ (IE9+, Chrome 31+?)
						deltaY = e.deltaY;
					} else if (e.wheelDelta) {
						deltaY = -e.wheelDelta;
					}

					// As far as I know, there is no good cross-browser way to get the cursor position relative to the event target.
					// We have to calculate the target element's position relative to the document, and subtrack that from the
					// cursor's position relative to the document.
					var offsetParent = $img.offset();
					var offsetX = e.pageX - offsetParent.left;
					var offsetY = e.pageY - offsetParent.top;

					// Record the offset between the bg edge and cursor:
					var bgCursorX = offsetX - bgPosX;
					var bgCursorY = offsetY - bgPosY;
					
					// Use the previous offset to get the percent offset between the bg edge and cursor:
					var bgRatioX = bgCursorX/bgWidth;
					var bgRatioY = bgCursorY/bgHeight;

					// Update the bg size:
					if (deltaY < 0) {
						bgWidth += bgWidth*settings.zoom;
						bgHeight += bgHeight*settings.zoom;
					} else {
						bgWidth -= bgWidth*settings.zoom;
						bgHeight -= bgHeight*settings.zoom;
					}

					// Take the percent offset and apply it to the new size:
					bgPosX = offsetX - (bgWidth * bgRatioX);
					bgPosY = offsetY - (bgHeight * bgRatioY);

					// Prevent zooming out beyond the starting size
					if (bgWidth <= width || bgHeight <= height) {
						reset();
					} else {
						updateBgStyle();
					}
				};

				// Make the background draggable
				img.onmousedown = function(e){
					var last = e;

					e.preventDefault();

					function drag(e) {
						e.preventDefault();
						bgPosX += (e.pageX - last.pageX);
						bgPosY += (e.pageY - last.pageY);
						last = e;
						updateBgStyle();
					}

					$(document)
					.on('mousemove', drag)
					.one('mouseup', function () {
						$(document).unbind('mousemove', drag);
					});
				};
			}

			if (img.complete) {
				loaded();
			} else {
				$img.one('load', loaded);
			}

		});
	};

	$.fn.wheelzoom.defaults = defaults;

}(window.jQuery));