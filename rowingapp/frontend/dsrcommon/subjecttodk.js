  var subject2dk = {
    'all':'alle',
    'cox':'styrmanden',
    'none':'ingen',
    'any':'mindst een'
  }

angular.module('row.subjecttodk', []).filter('subjecttodk', function () {
  return function (sb) {
    var r=subject2dk[sb];
    return r?r:sb;
  };
});
