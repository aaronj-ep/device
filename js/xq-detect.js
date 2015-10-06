/**
 * Detects browser, screen, and connection features for interpretation by the DeviceFeatureInfo PHP class.
 */

detectPlusInit();

function detectPlusInit(addTag) {

    if(addTag == undefined) { addTag = ''; }
    // Remove the NO-JS tag from the html tag (because if we're running, we have JS support)
    document.documentElement.className = document.documentElement.className.replace('no-js', 'js');
    // Screen Size
    var sWidth = screen.width;
    var sHeight = screen.height;
    // Connection
    var speed = estNetworkSpeed();
    // Browser Capabilities
    var isModern = isModernBrowser();
    // Device Capabilities
    var isTouch = isTouchDevice();
    var isHiDPI = isHighResDevice();
    if(isTouch) {
        var isAndroid = isAndroidDevice();
        if(!isAndroid) {
            var isIOS = isIOSDevice();
        }
    }
    else
    {
        isAndroid = false;
        isIOS = false;
    }

    window.media = "handheld";
    // Force Touch Interface for Mobile Viewports
    if(window.matchMedia("handheld")) {

    }

    // Modify HTML Tag for Capabilities
    if(!isModern) { addTag += " fallback"; }
    if(isTouch) {
        addTag += " touch";
        if(isAndroid) { addTag += " android"; }
        if(isIOS) { addTag += " ios"; }
    }
    document.documentElement.className += addTag;


    // Set the Cookie
    document.cookie = 'EPVIEW={"width":"' + sWidth + '","height":"' + sHeight + '","retina":"' + isHiDPI + '","speed":"' + speed + '","modern":"' + isModern + '","touch":"' + isTouch + '","android":"' + isAndroid + '","ios":"' + isIOS + '"}; path=/;';
}

/**
 * Detects if a device is running Android.
 *
 * IMPORTANT NOTE: This uses the UserAgent, and therefore can be spoofed.  It should be used for aesthetics only.
 *
 * @returns {boolean}
 */
function isAndroidDevice() {
    return true == (/(android)/i.test(navigator.userAgent));
}

/**
 * Detects if a device is running iOS.
 *
 * IMPORTANT NOTE: This uses the UserAgent, and therefore can be spoofed.  It should be used for aesthetics only.
 *
 * @returns {boolean}
 */
function isIOSDevice() {
    return true == (/(ipod|iphone|ipad)/i.test(navigator.userAgent));
}

/**
 * Determines if a HiDPI screen is being used, such as an Apple Retina display.
 *
 * @returns {boolean}
 */
function isHighResDevice() {

    var devicePixelRatio = window.devicePixelRatio;
    if(devicePixelRatio != undefined) {
        return true == (devicePixelRatio > 1);
    }

    // Fallback for older versions of Safari, Chrome, Firefox & Opera
    var mediaQuery = '(-webkit-min-device-pixel-ratio: 1.5), (min--moz-device-pixel-ratio: 1.5), (-o-min-device-pixel-ratio: 3/2), (min-resolution: 1.5dppx)';
    if(window.matchMedia != undefined) {
        return true == (window.matchMedia(mediaQuery).matches);
    }

    // Fallback for older versions & mobile versions of IE
    if(window.screen.deviceXDPI != undefined) {
        return true == ((window.screen.deviceXDPI / window.screen.logicalXDPI) > 1);
    }

    return false;
}

/**
 * Determines if a modern browser is being used.  In this case, modern is defined as HTML4 or HTML5 capable.  If you
 * wish to add SVG support detection,
 *
 * @returns {boolean}
 */
function isModernBrowser() {
    return true == ('querySelector' in document
            && 'localStorage' in window
            && 'addEventListener' in window
            // && document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#Image", "1.1")
        );
}

/**
 * Determine if the device is touch-based.
 *
 * @returns {boolean}
 */
function isTouchDevice(){
    return true == ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch);
}

/**
 * Estimates the network speed based on the browser's Navigator.Connection value.  Does not work on all browsers.
 *
 * @returns {string}
 */
function estNetworkSpeed() {

    var netspdtxt = "unknown";

    var nav = navigator,
        conn = nav.mozConnection || nav.webkitConnection || nav.connection;

    // Browser Doesn't Support Navigator.Connection
    if(!conn) {
        return "other";
    }

    // Older Responses
    if(conn.type) {
        switch (conn.type) {
            case conn.CELL_3G:
                // 3G
                netspdtxt = "3G";
                break;
            case conn.CELL_2G:
                // 2G
                netspdtxt = "2G";
                break;
            default:
                if(conn.type == "cellular") {
                    // If it's not 2G or 3G and it's cellular, it probably is 4G
                    netspdtxt = "4G";
                } else {
                    // WIFI, ETHERNET, UNKNOWN
                    netspdtxt = "other";
                }
        }
        // Newer Responses
    } else {
        if(conn.bandwidth) {
            if(conn.bandwidth === Infinity) {
                netspdtxt = "other";
            } else {
                if(conn.bandwidth > 2) {
                    netspdtxt = "other";
                } else {
                    if(conn.bandwidth < 1) {
                        netspdtxt = "2G";
                    } else {
                        netspdtxt = "3G";
                    }
                }
            }
        }
    }

    if(conn.metered) {
        netspdtxt = "metered";
    }

    return netspdtxt;
}