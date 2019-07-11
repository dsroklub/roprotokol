angular.module('rowApp.utilities.argrighttodk', []).filter('argrighttodk', ['DatabaseService', function (db_service) {
  return function (sb) {
    if (!sb) {
      return "U";
    }
    var r=db_service.getRight2dk(sb.member_right);
    var rr=r?r:sb;
    if (sb.arg) {
      rr=rr+" ("+sb.arg+")";
    }
    return rr;
  };
}]);
