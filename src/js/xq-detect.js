/**
 * xqDetect v3.0 (https://github.com/exactquery/xq-detect)
 * @author  Aaron M Jones [am@jonesiscoding.com]
 * @licence MIT (https://github.com/exactquery/xq-detect/blob/master/LICENSE)
 */
(function() {
    // Define our constructor
    this.Detect = function() {
        // Set Needed variables
        this.Conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection || false;
        this.CookieName = (typeof arguments[0] !== "undefined") ? arguments[0] : "djs";

        // Media Query PolyFill
        if (!hasMediaQuery()) {
            addMediaQuery();
        }

        // Set Detection Variables
        this.Width = screen.width;
        this.Height = screen.height;
        this.Browser = (this.isBrowserBaseline()) ? 'baseline' : (this.isBrowserFallback()) ? 'fallback' : 'modern';
    };

    /**
     * Detects if a device is running Android.  IMPORTANT NOTE: This uses the UserAgent, and therefore can be spoofed.
     * It should be used for aesthetics only.
     *
     * @returns {boolean}
     */
    Detect.prototype.isDeviceAndroid = function() {
        return true === (/(android)/i.test(navigator.userAgent));
    };

    /**
     * Detects if a device is running iOS.  IMPORTANT NOTE: This uses the UserAgent, and therefore can be spoofed.
     * It should be used for aesthetics only.
     *
     * @returns {boolean}
     */
    Detect.prototype.isDeviceIOS = function() {
        return true === (/(ipod|iphone|ipad)/i.test(navigator.userAgent));
    };

    /**
     * Determines if a browser is 'baseline', based on the detection of specific CSS3 functionality.
     *
     * @returns {boolean}
     */
    Detect.prototype.isBrowserBaseline = function() {
      return true === (!('flexBasis' in document.documentElement.style
        || 'msFlexPreferredSize' in document.documentElement.style
        || 'WebkitFlexBasis' in document.documentElement.style));
    };

    /**
     * Determines if a browser is 'fallback', based on the detection of specific Media Query 4 functionality.
     *
     * @returns {boolean}
     */
    Detect.prototype.isBrowserFallback = function() {
        return true === (!hasMediaQuery() || !(xMatchMedia('(pointer:fine')
        || xMatchMedia('(pointer:coarse)')
        || xMatchMedia('(-moz-touch-enabled')))
    };

    /**
     * Determines if a HiDPI screen is being used, such as an Apple Retina display.
     *
     * @returns {boolean}
     */
    Detect.prototype.isHighResDevice = function() {
        // Primary method, as this doesn't fall victim to issues with zooming.
        var testQuery = '(-webkit-min-device-pixel-ratio: 1.0), (min-resolution: 96dpi), (min-resolution: 1dppx)';
        if (hasMediaQuery() && xMatchMedia(testQuery)) {
            var mediaQuery = '(-webkit-min-device-pixel-ratio: 1.5), (min-resolution: 144dpi), (min-resolution: 1.5dppx)';
            return xMatchMedia(mediaQuery);
        }

        // Fallback for older versions & mobile versions of IE
        var deviceXDPI = (typeof window.screen.deviceXDPI !== 'undefined') ? window.screen.deviceXDPI : null;
        var logicalXDPI = (typeof window.screen.logicalXPDI !== 'undefined') ? window.screen.logicalXPDI : null;
        if (deviceXDPI && logicalXDPI) {
            return true === ((deviceXDPI / logicalXDPI) > 1.5);
        }

        // Final fallback, which WILL report HiDPI if the window is zoomed.
        var devicePixelRatio = window.devicePixelRatio || 1;
        return true === (devicePixelRatio > 1.5);
    };

    /**
     * Detects if a device is reporting that it uses a metered connection.
     *
     * @returns {boolean}
     */
    Detect.prototype.isMetered = function() {
        return true === (this.Conn && this.Conn.metered);
    };

    /**
     * Detects if a device has a touch screen.
     *
     * @returns {boolean}
     */
    Detect.prototype.isTouchDevice = function() {
        if (hasMediaQuery() && (xMatchMedia('(pointer:coarse)') || xMatchMedia('(-moz-touch-enabled)'))) {
            return true;
        }
        if ("ontouchstart" in window) {
            return true;
        }
        if ((navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0)) {
            return true;
        }

        return true === (/(touch)/i.test(navigator.userAgent));
    };

    Detect.prototype.setCookie = function() {

        var cookieValue = '{ "width": "' + this.Width
            + '", "height": "' + this.Height
            + '", "browser": "' + this.Browser
            + '", "hidpi": "' + this.isHighResDevice()
            + '", "metered": "' + this.isMetered()
            + '", "touch": "' + this.isTouchDevice()
            + '", "android": "' + this.isDeviceAndroid()
            + '", "ios": "' + this.isDeviceIOS() + '" }';

        document.cookie = this.CookieName+"="+encodeURIComponent(cookieValue)
            + ";path=/";
    };

    Detect.prototype.setTags = function() {

        var d = document.documentElement;

        // Replace the no-js tag, because if we're running, we have JavaScript.
        d.className = d.className.replace('no-js', 'js');
        // Replace the 'fallback' browser tag, which is only there in case of no-js.
        d.className = d.className.replace(' fallback', '');

        // Other HTML Tag Changes
        var addTag = '';
        if (this.Browser !== 'modern') {
            addTag += " " + this.Browser;
        }
        if(this.isTouchDevice()) {
            addTag += " touch";
            if (this.isDeviceAndroid()) {
                addTag += " android";
            }
            if (this.isDeviceIOS()) {
                addTag += " ios";
            }
        }

        d.className += addTag;
        d.setAttribute('data-user-agent', navigator.userAgent);
    };

    /**
     * Private method for performing a media query cross platforms & specifications.  Use of this method should always
     * be paired with hasMediaQuery() to prevent false positives.
     *
     * @param   media       A media query
     * @returns {boolean|null}
     */
    function xMatchMedia(media) {
        if (typeof window.matchMedia !== "undefined") {
            return window.matchMedia(media).matches;
        }

        return null;
    }

    /**
     * Private method for detecting if the device is capable of using media queries.
     *
     * @returns {boolean}
     */
    function hasMediaQuery() {
        return (typeof window.matchMedia !== "undefined");
    }

    /**
     * Polyfill for window.matchMedia on IE9/10 and older versions of webkit.
     */
    function addMediaQuery() {
        var mqPoly;
        if (mqPoly = (window.webkitMatchMedia || window.mozMatchMedia || window.oMatchMedia || window.msMatchMedia)) {
            window.matchMedia = mqPoly;
        } else {
            if (mqPoly = (window.styleMedia || window.media)) {
                (window.matchMedia = function () {
                    "use strict";
                    return function (media) {
                        return {
                            matches: mqPoly.matchMedium(media || 'all'),
                            media: media || 'all'
                        };
                    };
                });
            }
        }
    }
}());

var detect = new Detect();
detect.setTags();
detect.setCookie();