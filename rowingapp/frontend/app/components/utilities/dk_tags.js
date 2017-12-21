var dktags={
  'intime': 'ind',
  'outtime': 'ud',
  'destination': 'destination',
  'triptype': 'turtype',
  'rowers': 'roere',
  'boat': 'båd'
}


angular.module('rowApp.utilities.dk_tags', []).filter('dk_tags', function () {
  return function (tag) {
    var r=dktags[tag];
    return r?r:tag;
  };
});
