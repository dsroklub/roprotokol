/*jslint node: true */
'use strict';


angular.module('gym.utilities.urldecode', []).filter('urldecode', function () {
  return function (text) {
    return window.decodeURIComponent(text);
  };
});

angular.module('gym.utilities.urlencode', []).filter('urlencode', function () {
  return function (text) {
    return window.encodeURIComponent(text);
  };
});

angular.module('gym.utilities.nodsr', []).filter('nodsr', function () {
  return function (text) {
    if (text === "DSR") {
      return "";
    } else {
      return text;
    }
  };
});

angular.module('gym.utilities.totime', []).filter('totime', function () {
  return function(hours) {
    var hrs = Math.floor(hours);
    var min = Math.round(hours % 1 * 60);
    min = min < 10 ? "0"+min : min.toString();
    return hrs + ":" + min;
  };
});


angular.module('gym.utilities.txttotime', []).filter('txttotime', function () {
  return function(txt) {
    if (!txt) return null;
    var t=txt.split(/[- :T]/);
    var dd=new Date(t[0], t[1]-1, t[2], t[3]||0, t[4]||0, t[5]||0);
    return dd;
  };
});

angular.module('gym.utilities.ifNull', []).filter('ifNull', function () {
  return function( val, defaultVal, suffix) {
    if (val === null) return defaultVal;
    if (suffix != null) {
      val += suffix;
    }
    return val;
  };
});

angular.module('gym.utilities.subArray', []).filter('subArray', function () {
  return function( arr, start, len) {
    if (! arr.splice ) {
      console.log("subArray input cannot be spliced", arr);
      return null;
    }
    if (start == null) {
      start = 0;
    }
    return arr.splice(start, len);
  };
});


angular.module('gym.utilities.keys', []).filter('keys', function () {
  return function( obj) {
    return Object.keys(obj);
  };
});

angular.module('gym.utilities.split', []).filter('split', function () {
  return function( str, separator) {
    if (!str) {
      return [];
    }
    if (!separator) {
      separator = ',';
    }
    return str.split(separator);
  };
});


angular.module('gym.utilities.onlynumber', []).directive('onlynumber', function () {
  return {
    restrict: 'EA',
    require: 'ngModel',
    link: function (scope, elem, attrs, ngModel) {

      function checknumber() {
        var et=elem.val();
        if (et==null) return;
        if (et.length === 0) return;
        if (isNaN(et)) {
          et=et.replace(",",".").replace(/[^0-9\.]/g,"").replace(".","D").replace("."," ").replace("D",".");
          if (et===".") {
            et="0.";
          }
          elem.val(et);
          ngModel.$setViewValue(et.trim());
        }
      }
      
      scope.$watch(attrs.ngModel, function(newValue, oldValue) {
        checknumber();
      });
    }
  };
}
                                                        )




angular.module('gym.utilities', [
  'gym.utilities.onlynumber',
  'gym.utilities.urldecode',
  'gym.utilities.urlencode',
  'gym.utilities.nodsr',
   'gym.utilities.txttotime',
  'gym.utilities.totime',
  'gym.utilities.ifNull',
  'gym.utilities.subArray',
  'gym.utilities.split',
]).value('version', '0.2');
