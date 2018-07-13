/**
 * Device v3.1 (https://github.com/jonesiscoding/device)
 * @author  Aaron M Jones [am@jonesiscoding.com]
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 */
var detect = function (w, d) {
  'use strict';
  var mm    = w.matchMedia || w.webkitMatchMedia || w.mozMatchMedia || w.oMatchMedia || w.msMatchMedia || false;
  var de    = d.documentElement;
  var nav   = navigator;
  var _dt   = { width: screen.width, height: screen.height, grade: getGrade() };
  
  // HELPER FUNCTIONS
  /**
   * Adds a test into the detection object.
   *
   * @param {string}   name
   * @param {function} func
   * @returns {object}
   */
  function add(name, func) {
    if (!(name in _dt) && typeof func === "function") {
      _dt[name] = func;
    }
    
    return _dt;
  }
  
  /**
   * Evaluate the browser's grade based on feature detection.
   *
   * @returns {number}
   */
  function getGrade() {
    var grade = 0;
    if ( 'flexBasis' in de.style || 'msFlexPreferredSize' in de.style || 'WebkitFlexBasis' in de.style ) {
      grade++;
      if ( 'flexBasis' in de.style ) {
        grade++;
        if ( 'Promise' in w && 'includes' in Array.prototype ) {
          grade++;
        }
      }
    }
    
    return grade;
  }
  
  
  /**
   * Performs a media match using the appropriate function for this browser.  If this browser has no media query
   * functionality, always returns false.
   *
   * @param   {string}   q    The media query to match.
   * @returns {boolean}
   */
  function mq(q) {
    return true === (mm && mm(q).matches);
  }
  
  /**
   * Saves the results of the given tests in the HTML tag as well as a cookie with the given cookie name.
   *
   * @param {object} tests        An object of tests in the format of {testName: args}.  If no args, use TRUE.
   * @param {string} cookieName   The name of the cookie.  Defaults to 'djs'.
   */
  function save( tests, cookieName ) {
    var recipe = {};
    var cName = cookieName || 'djs';
    _dt.first = !hasCookie( cName );
    for ( var key in tests ) {
      if ( tests.hasOwnProperty( key ) && ( key in _dt ) ) {
        var args = ( null !== tests[key] && 'object' === typeof tests[ key ] ) ? tests[ key ] : [ tests[ key ] ];
        recipe[ key ] = ( ( key in _dt ) && ( typeof _dt[ key ] === "function" ) ) ? _dt[ key ].apply( null, args ) : _dt[ key ] || false;
        if ( recipe[ key ] && typeof recipe[key] === "boolean" ) {
          de.className += ' ' + key;
        } else {
          de.className.replace( new RegExp( '?:^|\\s)' + key + '(?!\\S)' ), '' );
        }
      }
    }
    de.className = de.className.replace( 'no-js', 'js' );
    de.setAttribute( 'data-user-agent', nav.userAgent );
    d.cookie = cName + '=' + JSON.stringify( recipe ) + ';path=/';
  }
  
  /**
   * Tests for the given string in this browser's user agent.
   *
   * @param   {string}    arg
   * @returns {boolean}
   */
  function ua(arg) {
    var pattern = ( arg instanceof RegExp ) ? arg : new RegExp('(' + arg + ')','i');
    
    return true === ( pattern.test( nav.userAgent ) );
  }
  
  // TEST FUNCTIONS
  /**
   * Returns the pixel width of the scrollbar.
   *
   * @returns {number}
   */
  function getScrollbar() {
    var sb = d.createElement("div");
    sb.setAttribute('style','width:100px;height: 100px;overflow-y:scroll;position:absolute;top:-9999px;-ms-overflow-style:auto;');
    d.body.appendChild(sb);
    var width = sb.offsetWidth - sb.clientWidth;
    d.body.removeChild(sb);
  
    return width;
  }
  
  /**
   * Determines if a cookie with the specified name has been set.
   *
   * @param cName
   * @returns {boolean}
   */
  function hasCookie(cName) {
    return true === ('cookie' in d && d.cookie.match(new RegExp('([;\s]+)?' + cName + '=')));
  }
  
  /**
   * Determines if a HiDPI screen is being used, such as an Apple Retina display.
   *
   * @returns {boolean}
   */
  function isHighRes(tRatio) {
    var ratio = (isNaN(parseFloat(tRatio)) || tRatio < 1) ? 1.5 : tRatio;
    var minRes = ratio * 96;
    var pWmdpr = '-webkit-min-device-pixel-ratio: ';
    var pMr    = 'min-resolution: ';
    
    // Primary method, as this doesn't fall victim to issues with zooming.
    if ( mq( '(' + pWmdpr + '1.0), (' + pMr + '96dpi), (' + pMr + '1dppx)' ) ) {
      return mq( '(' + pWmdpr + ratio + '), (' + pMr + minRes + 'dpi), (' + pMr + ratio + 'dppx)' );
    }
    
    // Fallback for older versions & mobile versions of IE
    var dXDPI = ( typeof w.screen.deviceXDPI !== 'undefined' ) ? w.screen.deviceXDPI : null;
    var lXDPI = ( typeof w.screen.logicalXPDI !== 'undefined' ) ? w.screen.logicalXPDI : null;
    if ( dXDPI && lXDPI ) {
      return true === ( ( dXDPI / lXDPI ) > ratio );
    }
    
    // Final fallback, which WILL report HiDPI if the window is zoomed.
    return true === ( (w.devicePixelRatio || 1) > ratio );
  }
  
  /**
   * Detects if a device is reporting that it uses a metered connection via a deprecated API.
   *
   * @returns {boolean}
   */
  function isMetered() {
    var conn = nav.connection || nav.mozConnection || nav.webkitConnection || false;
    
    return true === ( conn && conn.metered );
  }
  
  /**
   * Determines if the device is using a "coarse" pointer.
   *
   * @param   {boolean} noMoz
   * @returns {boolean}
   */
  function isCoarse(noMoz) {
    return true === ( mq( '(pointer:coarse)' ) || ( !noMoz && mq( 'screen and (-moz-touch-enabled)' ) ) );
  }
  
  /**
   * Legacy function for detecting if a device has a touch screen.
   *
   * @returns {boolean}
   */
  function isTouch() {
    var mtp = nav.maxTouchPoints || nav.msMaxTouchPoints || 0;
    
    return true === ( isCoarse( true ) || ( 'ontouchstart' in w ) || mtp > 0 || ua( 'touch' ) );
  }
  
  // Special Functions
  _dt.add        = add;
  _dt.save       = save;
  
  // Static Properties (these don't change during session)
  _dt.android    = ua( 'android' );
  _dt.ios        = ua( 'iphone|ipod|ipad' );
  _dt.sunset     = (_dt.grade === 0);
  _dt.baseline   = (_dt.grade === 1);
  _dt.fallback   = (_dt.grade === 2);
  _dt.modern     = (_dt.grade === 3);
  
  // Functions (results of these tests can change during session)
  _dt.cookie     = hasCookie;
  _dt.highres    = isHighRes;
  _dt.hidpi      = isHighRes;
  _dt.metered    = isMetered;
  _dt.retina     = isHighRes;
  _dt.scrollbar  = getScrollbar;
  _dt.touch      = isTouch;
  _dt.coarse     = isCoarse;
  _dt.mq         = mq;
  _dt.ua         = ua;
  
  return _dt;
  
}(window, document);