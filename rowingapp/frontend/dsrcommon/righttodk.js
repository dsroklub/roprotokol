angular.module('row.righttodk', []).filter('righttodk', ['DatabaseService', function (db_service) {
  return function (sb) {
    var r=db_service.getRight2dk(sb);
    return (r?r:sb);
  };
}]);
