/**
 * @author  AMJones <am@jonesiscoding.com>
 * @license MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 */
(function(w, d){

  var de = d.documentElement;

  /**
   * @typedef {Object} NetworkInformation
   * @property {string} effectiveType
   * @property {string} type
   * @property {boolean} saveData
   * @property {boolean} metered
   *
   * @typedef  {Object} Navigator
   * @property {NetworkInformation} connection
   * @property {NetworkInformation} mozConnection
   * @property {NetworkInformation} webkitConnection
   *
   * @typedef  {Object} w
   * @property {function(string)} webkitMatchMedia Webkit Prefixed matchMedia
   * @property {function(string)} mozMatchMedia    Mozilla Prefixed matchMedia
   * @property {function(string)} oMatchMedia      Opera Prefixed matchMedia
   * @property {function(string)} msMatchMedia     Microsoft Prefixed matchMedia
   */

  var nv = w.navigator || {};

  function _isArray(a) {
    return ( Object.prototype.toString.call( a ) !== '[object Array]' );
  }

  var Tests = function() {
    this.bool = function( v, arg) {
      var args = _isArray() ? arg : null;
      for ( var i = 0; i < v.length; i++ ) {
        if ( typeof v[ i ] === "function" ) {
          v[ i ] = v[ i ].apply( null, args );
        }

        if ( v[ i ] !== undefined && v[ i ] !== null ) return v[ i ];
      }

      return null;
    }

    this.in = function(p,o) { return p in o; }

    this.css = function(p) { return this.in(p,de.style); }

    /**
     * Performs a media query
     *
     * @param {string} q
     * @returns {boolean}
     */
    this.mq = function( q ) {
      var mm = w.matchMedia || w.webkitMatchMedia || w.mozMatchMedia || w.oMatchMedia || w.msMatchMedia || false;
      return true === ( mm && mm( q ).matches );
    }

    /** Performs a user agent query.
     *
     * @param {string|RegExp} a
     * @returns {boolean}
     */
    this.ua = function( a ) {
      var pt = ( a instanceof RegExp ) ? a : new RegExp( "(" + a + ")", "i" );

      return true === ( pt.test( nv.userAgent ) );
    }
  }

  var t = new Tests();

  var Connection = function() {
    var cn = nv.connection || nv.mozConnection || nv.webkitConnection || {};
    var tp = cn.type || 'wifi'

    // These connection types are typically metered. 4g is sometimes metered, but less common
    this.metered = t.bool([ cn.metered, /(wimax|cellular|bluetooth|unknown|3g|2g)/.test(tp)]);
    // These connection types are considered cellular
    this.type = tp.replace( /(4g|3g|2g)/, 'cellular' );
    // Default to 4G unless the navigator.connection.type uses old values, then derive from that
    this.effectiveType = cn.effectiveType || (/(4g|3g|2g)/.test(tp) ? tp.replace('-slow','') : '4g');
    // Consider all metered connections to have a preference to save data
    this.saveData = t.bool([ cn.saveData, this.metered ]);
  }

  var Pointers = function () {
    this.coarse = t.mq( "(pointer: coarse)" ) || t.ua( "iPhone|iPad" );
    this.fine = !this.coarse && t.mq( "(pointer: fine)" );
    this.none = !this.coarse && !this.fine;
    this.anyFine = t.mq( "(any-pointer: fine)" ) || this.fine;
    this.anyNone = this.none;
    this.anyCoarse = t.mq( "(any-pointer: coarse)" ) ||
        this.coarse ||
        ( nv.maxTouchPoints | nv.msMaxTouchPoints || 0 ) > 0 ||
        'ontouchstart' in w ||
        t.ua( 'touch' ) ||
        t.mq( 'screen and (-moz-touch-enabled)' )
    ;

    // Establish which is primary
    this.first = this.none ? "none" : (this.coarse ? "coarse" : "fine");

    // Build all
    this.all = [this.first];
    if(this.first !== 'coarse' && this.anyCoarse && !this.anyNone) { this.all.push('coarse'); }
    if(this.first !== 'fine' && this.anyFine && !this.anyNone) { this.all.push('fine'); }
  }

  var Screen = function() {
    var s = w.screen;

    function _dpr() {
      var dev = s.deviceXDPI;
      var log = s.logicalXPDI;

      return ( dev && log ) ? ( dev / log ) : 1;
    }

    this.coarse = t.mq( "(pointer: coarse)" ) || t.ua( "iPhone|iPad" );
    this.fine   = t.mq( "(pointer: fine)" ) && !t.ua( "iPhone|iPad" );

    this.width = s.availWidth || s.width || 1024;
    this.height = s.availHeight || s.height || 768;
    this.dpr = w.devicePixelRatio || _dpr();
  }

  var Device = function() {
    this.screen = this.screen || new Screen();
    this.pointers = this.pointers || new Pointers();
    this.viewport = { width: w.innerWidth || this.screen.width, height: w.innerHeight || this.screen.height };
    this.connection = this.connection || new Connection();
    this.test = t;
    this.feature = {};

    function _key(k) {
      return k.split(/(?=[A-Z])/).join('-').toLowerCase();
    }

    function _min(k) {
      return k.length <= 3 ? k : k.match(/[A-Za-z][a-z]*/g).map(function(v) { return v.charAt(0); }).join('').toLowerCase();
    }

    function _str(o) {
      var a = [];
      for (var i in o) {
        if (o.hasOwnProperty(i)) {
          if(o[i] !== null) {
            // var s = "\"" + k + "\": ";
            var s = _min(i) + ':';
            var t = typeof o[i];
            switch(t) {
              case "object":
                s += _str(o[i]);
                break;
              case "string":
                s += "\"" + o[i] + "\"";
                break;
              case "boolean":
                s += (o[i]) ? 1 : 0;
                break;
              default:
                s += o[i];
            }
            a.push(s);
          }
        }
      }

      return a.join(",");
    }

    function _classes(vals, prefix, dCls) {
      dCls = dCls || de.className.toString();
      var pFx  = "string" === typeof prefix ? prefix + '-' : "";
      for(var key in vals) {
        var kk = pFx + _key(key);
        if(vals.hasOwnProperty(key)) {
          if("object" === typeof vals[key]) {
            dCls = " " + _classes(vals[key],kk, dCls);
          } else {
            if ( vals[ key ] && typeof vals[ key ] === "boolean" && dCls.indexOf(kk) === -1 ) {
              dCls += " " + kk;
            } else if( !vals[ kk ] ) {
              dCls = dCls.replace( new RegExp( "(?:^|\\s)" + kk + "((?:\\s|$))", "g" ), "$1" );
            }
          }
        }
      }

      return dCls;
    }

    function _hasCookie(cName) {
      return true === ("cookie" in d && new RegExp("([;\s]+)?" + cName + "=").test(d.cookie));
    }

    this.save = function(cookieName, refresh) {
      var recipe = this.feature;
      recipe.deviceWidth = this.screen.width;
      recipe.deviceHeight = this.screen.height;
      recipe.viewportWidth = this.viewport.width;
      recipe.viewportHeight = this.viewport.height;
      recipe.dpr = this.screen.dpr;
      recipe.ect = this.connection.effectiveType;
      recipe.saveData = this.connection.saveData;
      recipe.pointers = this.pointers.all.join(',');

      var cookie = typeof cookieName === "string" ? cookieName : "CH";
      var hadCookie = _hasCookie( cookie );
      var reload = typeof refresh !== "undefined" ? ( refresh && !hadCookie ) : false;

      if ( !hadCookie ) {
        d.cookie = cookie + "=" + _str( recipe ) + ";path=/";
      }

      if ( reload && _hasCookie( cookie ) ) {
        location.reload();
      } else {
        de.className = _classes(recipe).replace( /(^|\s)no-js(\s|$)/gm, "$1js$2" );
        de.setAttribute( "data-user-agent", nv.userAgent );
        for(var k in recipe) {
          if(recipe.hasOwnProperty(k)) {
            if ( recipe[ k ] && typeof recipe[ k ] === "string" ) {
              de.setAttribute('data-' + _key(k), recipe[k]);
            }
          }
        }
      }
    }
  }

  w.device = new Device();
  w.device.feature.cssFlex = t.css('flexBasis');
  w.device.feature.cssGrid = t.css('gridTemplateRows');
  w.device.feature.cssFontDisplay = t.css( 'fontDisplay' );
  w.device.feature.jsPromise = t.in('Promise', w);
  w.device.feature.jsArrayIncludes = t.in('includes', Array.prototype);
  w.device.feature.htmlImageSrcset = t.in('srcset', HTMLImageElement.prototype);
  w.device.feature.htmlImageLoading = t.in('loading', HTMLImageElement.prototype);
  w.device.feature.prefersColorScheme = t.mq("(prefers-color-scheme: dark)") ? "dark" : "light";
  w.device.feature.prefersReducedMotion = t.mq( "(prefers-reduced-motion: reduce)" );
})(window, document);
