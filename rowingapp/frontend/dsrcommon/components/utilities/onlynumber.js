angular.module('dsrcommon.utilities.onlynumber', []).directive('onlynumber',onlynumber);

function onlynumber () {
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
