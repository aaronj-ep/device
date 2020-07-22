/**
 * @author  AMJones <am@jonesiscoding.com>
 * @license MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 */
(function(w, d){

  var de = d.documentElement;
  /**
   * @typedef  {Object} nv
   * @property {Object} connection
   * @property {Object} mozConnection
   * @property {Object} webkitConnection
   */
  var nv = navigator;
  /**
   * @typedef  {Object} cn
   * @property {string} effectiveType
   * @property {string} saveData
   */
  var cn = nv.connection || nv.mozConnection || nv.webkitConnection || { effectiveType: "4g", saveData: false };
  /**
   * @typedef  {Object} w
   * @property {function(string)} webkitMatchMedia Webkit Prefixed matchMedia
   * @property {function(string)} mozMatchMedia    Mozilla Prefixed matchMedia
   * @property {function(string)} oMatchMedia      Opera Prefixed matchMedia
   * @property {function(string)} msMatchMedia     Microsoft Prefixed matchMedia
   */
  var mm = w.matchMedia || w.webkitMatchMedia || w.mozMatchMedia || w.oMatchMedia || w.msMatchMedia || false;

  /**
   * @param {Object} rent
   * @param {Object} kid
   * @private
   */
  function _extend (rent, kid) {
    var faux = function () {}
    faux.prototype = rent.prototype;
    kid.prototype = new faux();
    kid.prototype.constructor = kid;
  }

  /**
   * Performs a media query
   *
   * @param {string} q
   * @returns {boolean}
   */
  function mq( q ) {
    return true === ( mm && mm( q ).matches );
  }

  /**
   * Performs a user agent query.
   *
   * @param {string|RegExp} a
   * @returns {boolean}
   */
  function ua( a ) {
    var pt = ( a instanceof RegExp ) ? a : new RegExp( "(" + a + ")", "i" );

    return true === ( pt.test( nv.userAgent ) );
  }

  /**
   * @constructor
   */
  var Resolver = function() {};

  Resolver.prototype.key = function(val) {
    if ( val.toLowerCase() !== val ) {
      return val.match(/[A-Za-z][a-z]*/g).map(function(v) { return v.charAt(0); }).join('').toLowerCase();
    } else {
      return val;
    }
  }

  Resolver.prototype.resolve = function(v) {
    for (var i = 0; i < v.length; i++) {
      if (typeof v[i] === "function") v[i] = v[i]();
      if (v[i] !== undefined && v[i] !== null) return v[i];
    }

    return null;
  }

  Resolver.prototype.get = function ( k, arg ) {
    if ( k !== 'resolve' || k !== 'get' ) {
      var rk = (!this.hasOwnProperty(k)) ? this.key(k) : k;
      if ( this.hasOwnProperty( rk ) ) {
        var args = (Object.prototype.toString.call(arg) !== '[object Array]') ? arg : null;

        return ( typeof this[rk] === "function" ) ? this[ rk ].apply( null, args ) : this[ rk ];
      }
    }

    return null;
  };

  /**
   * @constructor
   */
  var Features = function() {
    Resolver.apply( this );
    var f = this;

    f.cdg = "gridTemplateRows" in de.style;
    f.cdf = "flexBasis" in de.style;
    f.il  = "loading" in HTMLImageElement.prototype;
    f.iss = "srcset" in HTMLImageElement.prototype;
    f.jai = "includes" in Array.prototype;
    f.jp  = "Promise" in w;
  };

  /**
   * @constructor
   */
  var Hardware = function() {
    Resolver.apply(this);
    var h = this;

    function _dpr() {
      /**
       * @typedef {Object} screen
       * @property {int} deviceXDPI
       * @property {int} logicalXPDI
       */

      var dXDPI = h.resolve( [ screen.deviceXDPI, 0 ] );
      var lXDPI = h.resolve( [ screen.logicalXPDI, 0 ] );

      return ( dXDPI && lXDPI ) ? ( dXDPI / lXDPI ) : 1;
    }

    function _touch() {
      var mtp = h.resolve( [ nv.maxTouchPoints, nv.msMaxTouchPoints, 0 ] );
      return h.resolve( [ w.ontouchstart, ( mtp > 0 ), mq( "screen and (-moz-touch-enabled)" ), ua( "touch" ), ua( "iPhone" ), ua( "iPad" ) ] );
    }

    function _ect() {
      var tp = cn.type;
      if(tp === "2g") return "2g";
      if(tp === "3g" || tp === "cellular") return "3g";
      return "4g";
    }

    h.dh  = h.resolve( [ w.screen.availHeight, w.screen.height, 768 ] );
    h.dpr = h.resolve( [ w.devicePixelRatio, _dpr ] );
    h.dw  = h.resolve( [ w.screen.availWidth, w.screen.height, 1024 ] );
    h.ect = h.resolve([cn.effectiveType, _ect])
    h.pc  = mq( "(pointer:  coarse)" );
    h.t   = _touch();
    h.vh  = h.resolve( [ w.innerHeight, w.screen.availWidth, w.screen.height, 768 ] );
    h.vw  = h.resolve( [ w.innerWidth, w.screen.availWidth, w.screen.width, 1024 ] );
  }

  /**
   * @constructor
   */
  var Preferences = function () {
    Resolver.apply(this);

    var p = this;
    var ect = Hardware.prototype.ect;

    p.sd = p.resolve( [ cn.saveData, cn.metered, (ect === "3g" || ect === "2g" || ect === "slow-2g") ] );
    p.dm = mq( "(prefers-color-scheme:  dark)" )
    p.rm = mq( "(prefers-reduced-motion: reduce)" );
  };

  _extend( Resolver, Features );
  _extend( Resolver, Hardware );
  _extend( Resolver, Preferences );

  /**
   * @constructor
   */
  var Device = function() {
    var _d = this;

    // Set up Prototypes for easy addition of tests
    _d.addFeature = Features.prototype;
    _d.addPreference = Preferences.prototype;
    _d.addHardware = Hardware.prototype;

    // Set up actual objects
    _d.feature = _d.f = new Features();
    _d.hardware = _d.h = new Hardware();
    _d.preference = _d.p = new Preferences();

    function _classes(vals, prefix, dCls) {
      dCls = dCls || de.className.toString();
      var pFx  = "string" === typeof prefix ? prefix + '-' : "";
      for(var key in vals) {
        var kk = pFx + key;
        if(vals.hasOwnProperty(key)) {
          if("object" === typeof vals[key]) {
            dCls = " " + _classes(vals[key],kk, dCls);
          } else {
            if ( vals[ key ] && typeof vals[key] === "boolean" && dCls.indexOf(kk) === -1 ) {
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

    function _str(o) {
      var a = [];
      for (var i in o) {
        if (o.hasOwnProperty(i)) {
          if(o[i] !== null) {
            var s = "\"" + i + "\": ";
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

      return "{ " + a.join(", ") + "}";
    }

    function _results(obj, keys) {
      var rv = {};
      for (var key in keys) {
        if(keys.hasOwnProperty(key)) {
          if("object" === typeof keys[key]) {
            var nk = Resolver.prototype.key( key );
            var nO = (obj.hasOwnProperty(key)) ? obj[key] : (obj.hasOwnProperty(nk)) ? obj[nk] : null;
            if ( nO ) { rv[nk] = _results(nO, keys[key]); }
          } else {
            rv[key] = obj.get(key, keys[key]);
          }
        }
      }

      return rv;
    }

    _d.ua = ua;
    _d.mq = mq;
    _d.save    = function( tests, cookieName, refresh ) {
      var recipe = _results( _d, tests );
      var cookie = typeof cookieName !== "undefined" ? cookieName : "djs";
      var reload = typeof refresh !== "undefined" ? ( refresh && !_hasCookie( cookie ) ) : false;

      d.cookie = cookie + "=" + encodeURIComponent( _str( recipe ) ) + ";path=/";

      if ( reload && _hasCookie( cookie ) ) {
        location.reload();
      } else {
        de.className = _classes(recipe).replace( /(^|\s)no-js(\s|$)/gm, "$1js$2" );
        de.setAttribute( "data-user-agent", nv.userAgent );
      }

      return _d;
    }
  }

  w.device = new Device();
})(window, document);