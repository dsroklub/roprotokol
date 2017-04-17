/*jslint node: true */
'use strict';

var right2dkm;

var side2dk = {
  'left':'venstre',
  'right':'højre',
  'center':'midtfor'
}

var right2dk;

var subject2dk = {
  'all':'alle',
  'cox':'styrmanden',
  'none':'ingen',
  'any':'mindst een'
}


angular.module('rowApp.utilities.urldecode', []).filter('urldecode', function () {
  return function (text) {
    return window.decodeURIComponent(text);
  };
});

angular.module('rowApp.utilities.urlencode', []).filter('urlencode', function () {
  return function (text) {
    return window.encodeURIComponent(text);
  };
});

angular.module('rowApp.utilities.nodsr', []).filter('nodsr', function () {
  return function (text) {
    if (text === "DSR") {
      return "";
    } else {
      return text;
    }
  };
});

angular.module('rowApp.utilities.mtokm', []).filter('mtokm', function () {
  return function (meters) {
    return (meters / 1000).toFixed(1);
  };
});


angular.module('rowApp.utilities.totime', []).filter('totime', function () {
  return function(hours) {
    var hrs = Math.floor(hours);
    var min = Math.round(hours % 1 * 60);
    min = min < 10 ? "0"+min : min.toString();
    return hrs + ":" + min;
  };
});


angular.module('rowApp.utilities.txttotime', []).filter('txttotime', function () {
  return function(txt) {
    if (!txt) return null;
    var t=txt.split(/[- :]/);
    var dd=new Date(t[0], t[1]-1, t[2], t[3]||0, t[4]||0, t[5]||0);
    return dd;
  };
});

angular.module('rowApp.utilities.ifNull', []).filter('ifNull', function () {
  return function( val, defaultVal, suffix) {
    if (val === null) return defaultVal;
    if (suffix != null) {
      val += suffix;
    }
    return val;
  };
});

angular.module('rowApp.utilities.subArray', []).filter('subArray', function () {
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


angular.module('rowApp.utilities.keys', []).filter('keys', function () {
  return function( obj) {
    return Object.keys(obj);
  };
});



angular.module('rowApp.utilities.onlynumber', []).directive('onlynumber', function () {
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

angular.module('rowApp.utilities.transformkm', []).directive('transformkm', function () {
  return { 
    restrict: 'A',
    require: 'ngModel',
    link: function(scope, element, attrs, ngModel) {
      if (ngModel) { // Don't do anything unless we have a model
        ngModel.$parsers.push(function (val) {
          if (val !== undefined) {
            var fval=val;
            if (typeof fval == 'string') {
              fval = val.replace(',', '.');
            }
            return fval * 1000;
          }
        });
        ngModel.$formatters.push(function (val) {
          if (val !== undefined) {
            var fval=val;
            if (typeof val == 'string') {
              fval = val.replace(',', '.');
            }
            return fval / 1000;
          }
        });
      }
    }
  };
});


angular.module('eventApp.utilities', [
  'rowApp.utilities.onlynumber',
  'rowApp.utilities.urldecode',
  'rowApp.utilities.urlencode',
  'rowApp.utilities.nodsr',
  'rowApp.utilities.transformkm',
  'rowApp.utilities.mtokm',
  'rowApp.utilities.txttotime',
  'rowApp.utilities.totime',
  'rowApp.utilities.ifNull',
  'rowApp.utilities.subArray',
]).value('version', '0.2');
