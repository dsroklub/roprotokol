angular.module('rowApp.utilities.year2tool', []).filter('year2tool', year2tool);

function year2tool () {
  var years = {
    2016: 'wrench',
    2017: 'hammer',
    2018: 'saw',
    2019: 'screwdriver',
    2020: 'knibtang',
    2021: 'saw',
    2022: 'hammer',
    2023: 'screwdriver',
    2023: 'knibtang'
  };
   return function(str) {
    if (str && years[str]) {
      return years[str];
    }
    return 'wrench';
  };
}
