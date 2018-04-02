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
  function add(name, func) {
    if (!(name in _dt) && typeof func === "function") {
      _dt[name] = func;
    }
    
    return _dt;
  }
  
  function mq(q) {
    return true === (mm && mm(q));
  }
  
  function save(tests, cookieName) {
    var recipe = {};
    var cName = cookieName || 'djs';
    for (var key in tests) {
      if (tests.hasOwnProperty(key) && (key in _dt)) {
        var args = ('object' === typeof tests[key]) ? tests[key] : [tests[key]];
        recipe[key] = _dt[key](args);
        if (recipe[key]) {
          de.classList.add(key);
        } else {
          de.classList.remove(key);
        }
      }
    }
    de.className = de.className.replace('no-js', 'js');
    document.cookie = cName + '=' + JSON.stringify(recipe) + ';path=/';
  }
  
  function ua(arg) {
    var pattern = ( arg instanceof RegExp ) ? arg : new RegExp('/(' + arg + ')/i');
    
    return true === ( pattern.test( navigator.userAgent ) );
  }
  
  // TEST FUNCTIONS
  function getScrollbar() {
    var sb = d.getElementById( 'xqsbM' ) ||
      ( function () {
        var sbel = '<div style="width:100px;overflow:scroll;position:absolute;top:-9999px;"><div id="xqsbM" style="margin-right:calc(100px - 100%);"></div></div>';
        d.body.insertAdjacentHTML( 'beforeend', sbel );
        return d.getElementById( 'xqsbM' );
      } )();
    
    return getComputedStyle( sb ).marginRight;
  }
  
  function isBaseline() {
    return true === (!('localStorage' in w && mm && 'opacity' in de.style && 'borderRadius' in de.style));
  }
  
  function isBreakpoint(points) {
    var query = w.getComputedStyle(d.querySelector('body'), ':before').getPropertyValue('content').replace(/"/g, '') || null;
    if ( !Array === points.constructor ) { points = [ points ]; }
    
    return (null !== query) ? (points.indexOf(query) !== -1) : null;
  }
  
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
  
  function isMetered() {
    var conn = nav.connection || nav.mozConnection || nav.webkitConnection || false;
    
    return true === ( conn && conn.metered );
  }
  
  function isModern() {
    return true === !( isBaseline() || isFallback() );
  }
  
  /**
   * Detects if a device has a touch screen.
   *
   * @returns {boolean}
   */
  function isTouch() {
    if ( mq( '(pointer:coarse)' ) || mq( '(-moz-touch-enabled)' ) ) { return true; }
    if ( "ontouchstart" in w ) { return true; }
    if ( ( nav.maxTouchPoints > 0 || nav.msMaxTouchPoints > 0 ) ) { return true; }
    
    return true === ua('touch');
  }
  
  _dt.android    = ua('android');
  _dt.browser    = (isModern()) ? 'modern' : (isFallback()) ? 'fallback' : 'baseline';
  _dt.ios        = ua('iphone|ipod|ipad');
  _dt.add        = add;
  _dt.baseline   = isBaseline;
  _dt.breakpoint = isBreakpoint;
  _dt.fallback   = isFallback;
  _dt.modern     = isModern;
  _dt.highres    = isHighRes;
  _dt.hidpi      = isHighRes;
  _dt.metered    = isMetered;
  _dt.retina     = isHighRes;
  _dt.save       = save;
  _dt.scrollbar  = getScrollbar;
  _dt.touch      = isTouch;
  
  return _dt;
  
}(window, document);