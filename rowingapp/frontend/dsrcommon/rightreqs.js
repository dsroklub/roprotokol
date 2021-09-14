angular.module('row.rightreqs', []).filter('rightreqs', ['DatabaseService', rightreqs]);

function rightreqs(db_service) {
  var ss={'cox':'styrmanden','all':'alle','any':'mindst en','forbidden':'forbudt'};
  return function (rights) {
    var res="";
    angular.forEach(rights, function (r) {
      var subject=r.requirement;
      var right=r.required_right;
      if (subject) {
        if (res!="") {
          res +=", ";
        }
        if (subject=='none') {
          res+=(" ingen må "+(db_service.getRight2dkm(right)?db_service.getRight2dkm(right):right));        
        } else {
          res+=(ss[subject]+" skal "+(db_service.getRight2dkm(right)?db_service.getRight2dkm(right):right));
        }
      }
    },this);
    return res==""?"ingen krav":res;
  };
}
