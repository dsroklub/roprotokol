angular.module('dsrcommon.utilities.safefilename', []).directive('safefilename', safefilename);

function safefilename () {
  return {
    restrict: 'EA',
    require: 'ngModel',
    link: function (scope, elem, attrs, ngModel) {
      function checkname() {
        var et=elem.val();
        if (et==null) return;
        if (et.length === 0) return;
        et=et.replace(/[^\.a-z0-9æøå#=:+\-@_]/gi, '').replace(/[.](?=.*[.])/g, "");
        elem.val(et);
        ngModel.$setViewValue(et.trim());
      }      
      scope.$watch(attrs.ngModel, function(newValue, oldValue) {
        checkname();                              
      });
    }
  };
}
