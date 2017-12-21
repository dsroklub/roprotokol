angular.module('rowApp.utilities.rightreqs', []).filter('rightreqs', ['DatabaseService', rightreqs]);

function rightreqs(db_service) {
  db_service.make_rights();
  var ss={'cox':'styrmanden','all':'alle','any':'mindst en','forbidden':'forbudt'};
  return function (rights) {
    var res="";
    angular.forEach(rights, function (subject,right) {
        if (res!="") {
          res +=", ";
        }
      if (subject=='none') {
        res+=(" ingen må "+(db_service.right2dkm[right]?right2dkm[right]:right));        
      } else {
        res+=(ss[subject]+" skal "+(db_service.right2dkm[right]?right2dkm[right]:right));
      }
    },this);
    return res==""?"ingen krav":res;
  };
}
