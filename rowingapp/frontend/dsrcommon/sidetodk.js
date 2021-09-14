var side2dk = {
  'left':'venstre',
  'right':'højre',
  'center':'midtfor'
}

angular.module('row.sidetodk', []).filter('sidetodk', function () {
  return function (sd) {
    var r=side2dk[sd];
    return (r?r:sd);
  };
});
