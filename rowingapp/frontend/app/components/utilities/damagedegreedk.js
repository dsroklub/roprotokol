var damage_degrees={
  //  0: '\u2713', // until we make it work on windows
  0: ' ',
  1: 'Let skadet',
  2: 'Middel skadet',
  3: 'Svært skadet',
  4: 'Vedligehold'
}

angular.module('rowApp.utilities.damagedegreedk', []).filter('damagedegreedk', function () {
  return function (dd) {
    var r=damage_degrees[dd];
    return r?r:dd;
  };
});
