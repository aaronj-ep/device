 /**
 * xqDetect v2.1.1 (https://github.com/exactquery/xq-detect)
 * @author  Aaron M Jones [aaron@jonesiscoding.com]
 * @licence MIT (https://github.com/exactquery/xq-detect/blob/master/LICENSE)
 */
(function() {
    // Define our constructor
    this.Detect = function() {
        // Set Needed variables
        this.Conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection || false;
        this.CookieName = (typeof arguments[0] != "undefined") ? arguments[0] : "d";

        // Set Detection Variables
        this.Width = screen.width;
        this.Height = screen.height;
        this.Browser = (this.isBrowserBaseline()) ? 'baseline' : (this.isBrowserFallback()) ? 'fallback' : 'modern';
    };

    /**
     * Detects a low battery in devices that support this API.
     *
     * @returns {boolean}
     */
    Detect.prototype.isBatteryLow = function() {
        var battery = navigator.battery || navigator.webkitBattery || navigator.mozBattery || navigator.msBattery || false;
        return true == (battery && battery.level < .3);
    };

    /**
     * Detects if a device is running Android.  IMPORTANT NOTE: This uses the UserAgent, and therefore can be spoofed.
     * It should be used for aesthetics only.
     *
     * @returns {boolean}
     */
    Detect.prototype.isDeviceAndroid = function() {
        return true == (/(android)/i.test(navigator.userAgent));
    };

    /**
     * Detects if a device is running iOS.  IMPORTANT NOTE: This uses the UserAgent, and therefore can be spoofed.
     * It should be used for aesthetics only.
     *
     * @returns {boolean}
     */
    Detect.prototype.isDeviceIOS = function() {
        return true == (/(ipod|iphone|ipad)/i.test(navigator.userAgent));
    };

    /**
     * Determines if a browser is 'baseline', based on the detection of specific HTML4 and CSS2 functionality.
     *
     * @returns {boolean}
     */
    Detect.prototype.isBrowserBaseline = function() {
        return true == (!'localStorage' in window
            || !hasMediaQuery()
            || !'opacity' in document.documentElement
            || !'borderRadius' in document.documentElement );
    };

    /**
     * Determines if a browser is 'fallback', based on the detection of specific CSS3 functionality.
     *
     * @returns {boolean}
     */
    Detect.prototype.isBrowserFallback = function() {
        return true == (!('columnCount' in document.documentElement.style
            || 'MozColumnCount' in document.documentElement.style
            || 'WebkitColumnCount' in document.documentElement.style));
    };

    /**
     * Determines if a HiDPI screen is being used, such as an Apple Retina display.
     *
     * @returns {boolean}
     */
    Detect.prototype.isHighResDevice = function() {
        var devicePixelRatio = window.devicePixelRatio;
        if(typeof devicePixelRatio != "undefined") {
            return true == (devicePixelRatio > 1);
        }

        // Fallback for older versions of Safari, Chrome, Firefox & Opera
        var mediaQuery = '(-webkit-min-device-pixel-ratio: 1.5), (min--moz-device-pixel-ratio: 1.5), (-o-min-device-pixel-ratio: 3/2), (min-resolution: 1.5dppx)';
        if(hasMediaQuery()) { return xMatchMedia(mediaQuery); }

        // Fallback for older versions & mobile versions of IE
        if(typeof window.screen.deviceXDPI != "undefined") {
            return true == ((window.screen.deviceXDPI / window.screen.logicalXDPI) > 1);
        }

        return false;
    };

    /**
     * Detects if a device is reporting that it uses a lower speed connection.  This is based on the Network Information
     * API, for which work has been halted.  Some mobile devices still report this information, however, so until a better
     * way comes along, it's still being detected.
     *
     * @returns {boolean}
     */
    Detect.prototype.isLowSpeed = function() {
        if(this.Conn) {
            if ( typeof this.Conn.bandwidth != "undefined" ) {
                return true == (this.Conn.bandwidth === Infinity || connection.bandwidth > 2 );
            }

            if ( typeof this.Conn.type != "undefined" ) {
                return true == (this.Conn.type == this.Conn.CELL_3G || this.Conn.type == this.Conn.CELL_2G);
            }
        }

        return false;
    };

    /**
     * Detects if a device is reporting that it uses a metered connection.
     *
     * @returns {boolean}
     */
    Detect.prototype.isMetered = function() {
        return true == (this.Conn && this.Conn.metered);
    };

    /**
     * Detects if a device has a touch screen.
     *
     * @returns {boolean}
     */
    Detect.prototype.isTouchDevice = function() {
        if(hasMediaQuery() && xMatchMedia('(pointer:coarse)' )) { return true; }
        return true == ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch);
    };

    Detect.prototype.setCookie = function() {

        var cookieValue = '{ "width": "' + this.Width
            + '", "height": "' + this.Height
            + '", "browser": "' + this.Browser
            + '", "hidpi": "' + this.isHighResDevice()
            + '", "metered": "' + this.isMetered()
            + '", "low_speed": "' + this.isLowSpeed()
            + '", "low_battery": "' + this.isBatteryLow()
            + '", "touch": "' + this.isTouchDevice()
            + '", "android": "' + this.isDeviceAndroid()
            + '", "ios": "' + this.isDeviceIOS() + '" }';

        document.cookie = this.CookieName+"="+encodeURIComponent(cookieValue)
            + ";path=/";
    };

    Detect.prototype.setTags = function() {
        // Replace the no-js tag, because if we're running, we have JavaScript.
        document.documentElement.className = document.documentElement.className.replace('no-js', 'js');

        // Other HTML Tag Changes
        var addTag = '';
        if(this.Browser != 'modern') { addTag += " " + this.Browser; }
        if(this.isTouchDevice()) {
            addTag += " touch";
            if(this.isDeviceAndroid()) { addTag += " android"; }
            if(this.isDeviceIOS()) { addTag += " ios"; }
        }

        document.documentElement.className += addTag;
    };

    /**
     * Private method for performing a media query cross platforms & specifications.  Use of this method should always
     * be paired with hasMediaQuery() to prevent false positives.
     *
     * @param   media       A media query
     * @returns bool|null
     */
    function xMatchMedia(media) {
        if(typeof window.matchMedia != "undefined") { return window.matchMedia( media ).matches; }
        if(typeof window.msMatchMedia != "undefined") { return window.msMatchMedia( media ).matches; }

        return null;

    }

    /**
     * Private method for detecting if the device is capable of using media queries.
     *
     * @returns {boolean}
     */
    function hasMediaQuery()
    {
        return (typeof window.matchMedia != "undefined" || typeof window.msMatchMedia != "undefined");
    }
}());

var detect = new Detect();
detect.setTags();
detect.setCookie();