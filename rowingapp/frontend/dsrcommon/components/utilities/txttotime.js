angular.module('dsrcommon.utilities.txttotime', []).filter('txttotime', function () {
  return function(txt) {
    if (!txt) return null;
    var t=txt.split(/[- :T]/);
    var dd=new Date(t[0], t[1]-1, t[2], t[3]||0, t[4]||0, t[5]||0);
    return dd;
  };
});
