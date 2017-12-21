angular.module('rowApp.utilities.righttodk', []).filter('righttodk', ['DatabaseService', function (db_service) {
  db_service.make_rights();
  return function (sb) {
    var r=db_service.right2dk[sb];
    return (r?r:sb);
  };
}]);
