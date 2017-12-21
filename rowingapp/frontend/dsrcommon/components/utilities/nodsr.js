angular.module('dsrcommon.utilities.nodsr', []).filter('nodsr', function () {
  return function (text) {
    if (text === "DSR") {
      return "";
    } else {
      return text;
    }
  };
});
