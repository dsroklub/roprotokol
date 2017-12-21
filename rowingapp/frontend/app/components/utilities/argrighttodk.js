angular.module('rowApp.utilities.argrighttodk', []).filter('argrighttodk', ['DatabaseService', function (db_service) {
  db_service.make_rights();

  return function (sb) {
    var r=db_service.right2dk[sb.member_right];
    var rr=r?r:sb;
    if (sb.arg) {
      rr=rr+" ("+sb.arg+")";
    }
    return rr;
  };
}]);
