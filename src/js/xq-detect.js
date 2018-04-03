/**
 * xqDetect v3.0 (https://github.com/exactquery/xq-detect)
 * @author  Aaron M Jones [am@jonesiscoding.com]
 * @licence MIT (https://github.com/exactquery/xq-detect/blob/master/LICENSE)
 */
var detect = function (w, d) {
  'use strict';
  var mm    = w.matchMedia || w.webkitMatchMedia || w.mozMatchMedia || w.oMatchMedia || w.msMatchMedia || false;
  var de    = d.documentElement;
  var nav   = navigator;
  var _dt   = { width: screen.width, height: screen.height };
  
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
   * Performs a media match using the appropriate function for this browser.  If this browser has no media query
   * functionality, always returns false.
   *
   * @param   {string}   q    The media query to match.
   * @returns {boolean}
   */
  function mq(q) {
    return true === (mm && mm(q));
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
    for ( var key in tests ) {
      if ( tests.hasOwnProperty( key ) && ( key in _dt ) ) {
        var args = ( 'object' === typeof tests[ key ] ) ? tests[ key ] : [ tests[ key ] ];
        recipe[ key ] = ( ( key in _dt ) && ( typeof _dt[ key ] === "function" ) ) ? _dt[ key ]( args ) : _dt[ key ] || false;
        if ( recipe[ key ] && typeof recipe[key] === "boolean" ) {
          de.classList.add( key );
        } else {
          de.classList.remove( key );
        }
      }
    }
    de.className = de.className.replace( 'no-js', 'js' );
    de.setAttribute( 'data-user-agent', nav.userAgent );
    document.cookie = cName + '=' + JSON.stringify( recipe ) + ';path=/';
  }
  
  /**
   * Tests for the given string in this browser's user agent.
   *
   * @param   {string}    arg
   * @returns {boolean}
   */
  function ua(arg) {
    var pattern = ( arg instanceof RegExp ) ? arg : new RegExp('/(' + arg + ')/i');
    
    return true === ( pattern.test( nav.userAgent ) );
  }
  
  // TEST FUNCTIONS
  /**
   * Returns the pixel width of the scrollbar.
   *
   * @returns {number}
   */
  function getScrollbar() {
    var sb = d.getElementById( 'xqsbM' ) ||
      ( function () {
        var sbel = '<div style="width:100px;overflow:scroll;position:absolute;top:-9999px;"><div id="xqsbM" style="margin-right:calc(100px - 100%);"></div></div>';
        d.body.insertAdjacentHTML( 'beforeend', sbel );
        return d.getElementById( 'xqsbM' );
      } )();
    
    return parseInt(getComputedStyle( sb ).marginRight);
  }
  
  function isBreakpoint(points) {
    var query = window.getComputedStyle(document.querySelector('body'), ':before').getPropertyValue('content').replace(/\"/g, '') || null;
    if ( !Array === points.constructor ) { points = [ points ]; }
    
    return (null !== query) ? (points.indexOf(query) !== -1) : null;
  }
  
  /**
   * Determines if a browser is 'baseline', based on the detection of specific HTML4 and CSS2 functionality.
   *
   * @returns {boolean}
   */
  function isBaseline() {
    return true === (!('localStorage' in w && mm && 'opacity' in de.style && 'borderRadius' in de.style));
  }
  
  /**
   * Determines if a browser is 'fallback', based on the detection of specific CSS3 functionality.
   *
   * @returns {boolean}
   */
  function isFallback() {
    return true === (!('flexBasis' in de.style || 'msFlexPreferredSize' in de.style || 'WebkitFlexBasis' in de.style));
  }
  
  /**
   * Determines if a HiDPI screen is being used, such as an Apple Retina display.
   *
   * @returns {boolean}
   */
  function isHighRes(tRatio) {
    var ratio = tRatio || 1.5;
    var minRes = ratio * 96;
    
    // Primary method, as this doesn't fall victim to issues with zooming.
    var testQuery = '(-webkit-min-device-pixel-ratio: 1.0), (min-resolution: 96dpi), (min-resolution: 1dppx)';
    if ( mq( testQuery ) ) {
      var mediaQuery = '(-webkit-min-device-pixel-ratio: ' + ratio + '), (min-resolution: ' + minRes + 'dpi), (min-resolution: ' + ratio + 'dppx)';
      return mq( mediaQuery );
    }
    
    // Fallback for older versions & mobile versions of IE
    var deviceXDPI = ( typeof w.screen.deviceXDPI !== 'undefined' ) ? w.screen.deviceXDPI : null;
    var logicalXDPI = ( typeof w.screen.logicalXPDI !== 'undefined' ) ? w.screen.logicalXPDI : null;
    if ( deviceXDPI && logicalXDPI ) {
      return true === ( ( deviceXDPI / logicalXDPI ) > ratio );
    }
    
    // Final fallback, which WILL report HiDPI if the window is zoomed.
    var devicePixelRatio = w.devicePixelRatio || 1;
    return true === ( devicePixelRatio > ratio );
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
   * Detects if a device has a touch screen,
   *
   * @returns {boolean}
   */
  function isTouch() {
    if ( mq( '(pointer:coarse)' ) || mq( '(-moz-touch-enabled)' ) ) { return true; }
    if ( "ontouchstart" in w ) { return true; }
    if ( ( nav.maxTouchPoints > 0 || nav.msMaxTouchPoints > 0 ) ) { return true; }
    
    return true === ua('touch');
  }
  
  // Special Functions
  _dt.add        = add;
  _dt.save       = save;
  
  // Static Properties (these don't change during session)
  _dt.android    = ua( 'android' );
  _dt.browser    = ( isBaseline() ) ? 'baseline' : ( isFallback() ) ? 'fallback' : 'modern';
  _dt.ios        = ua( 'iphone|ipod|ipad' );
  _dt.baseline   = isBaseline();
  _dt.fallback   = isFallback();
  _dt.modern     = !( isBaseline() || isFallback() );
  _dt.baseline   = isBaseline();
  
  // Functions (results of these tests can change during session)
  _dt.breakpoint = isBreakpoint;
  _dt.highres    = isHighRes;
  _dt.hidpi      = isHighRes;
  _dt.metered    = isMetered;
  _dt.retina     = isHighRes;
  _dt.scrollbar  = getScrollbar;
  _dt.touch      = isTouch;
  
  return _dt;
  
}(window, document);