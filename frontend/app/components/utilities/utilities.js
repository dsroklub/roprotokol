/*jslint node: true */
'use strict';

angular.module('myApp.utilities.urldecode', []).filter('urldecode', function () {
  return function (text) {
    return window.decodeURIComponent(text);
  };
});

angular.module('myApp.utilities.urlencode', []).filter('urlencode', function () {
  return function (text) {
    return window.encodeURIComponent(text);
  };
});

angular.module('myApp.utilities.nodsr', []).filter('nodsr', function () {
  return function (text) {
    if (text === "DSR") {
      return "";
    } else {
      return text;
    }
  };
});

angular.module('myApp.utilities.mtokm', []).filter('mtokm', function () {
  return function (meters) {
    return (meters / 1000).toFixed(1);
  };
});

angular.module('myApp.utilities.totime', []).filter('totime', function () {
  return function(hours) {
    var hrs = Math.floor(hours);
    var min = Math.round(hours % 1 * 60);
    min = min < 10 ? "0"+min : min.toString();
    return hrs + ":" + min;
  };
});


angular.module('myApp.utilities.txttotime', []).filter('txttotime', function () {
  return function(txt) {
    if (!txt) return null;
    var t=txt.split(/[- :]/);
    var dd=new Date(t[0], t[1]-1, t[2], t[3]||0, t[4]||0, t[5]||0);
    return dd;
  };
});

angular.module('myApp.utilities.transformkm', []).directive('transformkm', function () {
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


angular.module('myApp.utilities', [
  'myApp.utilities.urldecode',
  'myApp.utilities.urlencode',
  'myApp.utilities.nodsr',
  'myApp.utilities.transformkm',
  'myApp.utilities.mtokm',
  'myApp.utilities.txttotime',
  'myApp.utilities.totime',
]).value('version', '0.1');
