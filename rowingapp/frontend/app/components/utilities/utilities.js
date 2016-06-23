/*jslint node: true */
'use strict';

var right2dkm = {
  'rowright':'have roret',
  'cox':'være styrmand',
  'coxtheory':'have styrmandsteori',
  'competition':'være kaproer',
  '8':'have otterret',
  '8cox':'have otter styrmandsret',
  'instructor':'være instruktør',
  'skærgård':'have skærgårdsret',
  'longdistancetheory':'have langdistanceteori',
  'longdistance':'være langtursstyrmand',
  'kajak':'have kajakret A',
  'svava':'have svavaret',
  'kajak_b':'have kajak-ret B',
  'swim400':'kunne svømme 400m',
  'motorboat':'have motorbådsret',
  'sculler':'have scullerret',
  'langturøresund':'have øresund langtursret',
  'outrigger_instructor':'være outriggerinstruktør',
  'wrench':'have rød svensknøgle',
  'kanin':'være kanin'
};

var side2dk = {
  'left':'venstre',
  'right':'højre',
  'center':'midtfor'
}



var right2dk = {
  'kanin':'kanin',
  'rowright':'roret',
  'cox':'styrmandsret',
  'coxtheory':'styrmandsteori',
  'competition':'kaproer',
  '8':'otterret',
  '8cox':'otter styrmandsret',
  'instructor':'instruktørret',
  'skærgård':'skærgårdsret',
  'longdistancetheory':'langdistanceteori',
  'longdistance':'langtursstyrmandsret',
  'kajak':'kajakret A',
  'svava':'svavaret',
  'kajak_b':'kajakret B',
  'swim400':'svømme 400m',
  'motorboat':'motorbådsret',
  'sculler':'scullerret',
  'langturøresund':'øresund langtursret',
  'outrigger_instructor':'outriggerinstruktørret',
  'wrench':'rød svensknøgle'
};

var subject2dk = {
  'all':'alle',
  'cox':'styrmanden',
  'none':'ingen',
  'any':'mindst een'
}

angular.module('myApp.utilities.urldecode', []).filter('urldecode', function () {
  return function (text) {
    return window.decodeURIComponent(text);
  };
});

angular.module('myApp.utilities.urlencode', []).filter('urlencode', function () {
  return function (text) {
    return window.encodeURIComponent(text);
  };
});

angular.module('myApp.utilities.nodsr', []).filter('nodsr', function () {
  return function (text) {
    if (text === "DSR") {
      return "";
    } else {
      return text;
    }
  };
});

angular.module('myApp.utilities.mtokm', []).filter('mtokm', function () {
  return function (meters) {
    return (meters / 1000).toFixed(1);
  };
});

angular.module('myApp.utilities.rightreqs', []).filter('rightreqs', function () {
  var ss={'cox':'styrmanden','all':'alle','any':'mindst en','forbidden':'forbudt'};
  return function (rights) {
    var res="";
    angular.forEach(rights, function (subject,right) {
        if (res!="") {
          res +=", ";
        }
      if (subject=='none') {
        res+=(" ingen må "+(right2dkm[right]?right2dkm[right]:right));        
      } else {
        res+=(ss[subject]+" skal "+(right2dkm[right]?right2dkm[right]:right));
      }
    },this);
    return res==""?"ingen krav":res;
  };
});

angular.module('myApp.utilities.subjecttodk', []).filter('subjecttodk', function () {
  return function (sb) {
    var r=subject2dk[sb];
    return r?r:sb;
  };
});

var damage_degrees={
  //  0: '\u2713', // until we make it work on windows
  0: ' ',
  1: 'Let skadet',
  2: 'Middel skadet',
  3: 'Svært skadet',
  4: 'Vedligehold'
}

var dktags={
  'intime': 'ind',
  'outtime': 'ud',
  'destination': 'destination',
  'triptype': 'turtype',
  'rowers': 'roere',
  'boat': 'båd'
}


angular.module('myApp.utilities.damagedegreedk', []).filter('damagedegreedk', function () {
  return function (dd) {
    var r=damage_degrees[dd];
    return r?r:dd;
  };
});

angular.module('myApp.utilities.dk_tags', []).filter('dk_tags', function () {
  return function (tag) {
    var r=dktags[tag];
    return r?r:tag;
  };
});

angular.module('myApp.utilities.righttodk', []).filter('righttodk', function () {
  return function (sb) {
    var r=right2dk[sb];
    return (r?r:sb);
  };
});

angular.module('myApp.utilities.argrighttodk', []).filter('argrighttodk', function () {
  return function (sb) {
    var r=right2dk[sb.member_right];
    var rr=r?r:sb;
    if (sb.arg) {
      rr=rr+" ("+sb.arg+")";
    }
    return rr;
  };
});

angular.module('myApp.utilities.sidetodk', []).filter('sidetodk', function () {
  return function (sd) {
    var r=side2dk[sd];
    return (r?r:sd);
  };
});

angular.module('myApp.utilities.leveltodk', []).filter('leveltodk', function () {
  return function (lvl) {
    return (lvl?"hylde "+lvl:"gulv");
  };
});

angular.module('myApp.utilities.rowtodk', []).filter('rowtodk', function () {
  return function (rw) {
    if (!rw) return("");
    if (rw==1) return ("mod porten");
    if (rw==2) return ("inderst");
    return (rw);
  };
});


angular.module('myApp.utilities.totime', []).filter('totime', function () {
  return function(hours) {
    var hrs = Math.floor(hours);
    var min = Math.round(hours % 1 * 60);
    min = min < 10 ? "0"+min : min.toString();
    return hrs + ":" + min;
  };
});


angular.module('myApp.utilities.txttotime', []).filter('txttotime', function () {
  return function(txt) {
    if (!txt) return null;
    var t=txt.split(/[- :]/);
    var dd=new Date(t[0], t[1]-1, t[2], t[3]||0, t[4]||0, t[5]||0);
    return dd;
  };
});

angular.module('myApp.utilities.onlynumber', []).directive('onlynumber', function () {
  return {
    restrict: 'EA',
    require: 'ngModel',
    link: function (scope, elem, attrs, ngModel) {

      function checknumber() {
        var et=elem.val();
        if (et==null) return;
        if (et.length === 0) return;
        if (isNaN(et)) {
          et=et.replace(",",".").replace(/[^0-9\.]/g,"").replace(".","D").replace("."," ").replace("D",".");
          if (et===".") {
            et="0.";
          }
          elem.val(et);
          ngModel.$setViewValue(et.trim());
        }
      }
      
      scope.$watch(attrs.ngModel, function(newValue, oldValue) {
        checknumber();

                              
      });
    }
  };
}
                                                          )

angular.module('myApp.utilities.transformkm', []).directive('transformkm', function () {
  return { 
    restrict: 'A',
    require: 'ngModel',
    link: function(scope, element, attrs, ngModel) {
      if (ngModel) { // Don't do anything unless we have a model
        ngModel.$parsers.push(function (val) {
          if (val !== undefined) {
            var fval=val;
            if (typeof fval == 'string') {
              fval = val.replace(',', '.');
            }
            return fval * 1000;
          }
        });
        ngModel.$formatters.push(function (val) {
          if (val !== undefined) {
            var fval=val;
            if (typeof val == 'string') {
              fval = val.replace(',', '.');
            }
            return fval / 1000;
          }
        });
      }
    }
  };
});


angular.module('myApp.utilities', [
  'myApp.utilities.onlynumber',
  'myApp.utilities.urldecode',
  'myApp.utilities.urlencode',
  'myApp.utilities.nodsr',
  'myApp.utilities.sidetodk',
  'myApp.utilities.leveltodk',
  'myApp.utilities.rowtodk',
  'myApp.utilities.transformkm',
  'myApp.utilities.mtokm',
  'myApp.utilities.rightreqs',
  'myApp.utilities.subjecttodk',
  'myApp.utilities.righttodk',
  'myApp.utilities.argrighttodk',
  'myApp.utilities.dk_tags',
  'myApp.utilities.damagedegreedk',
  'myApp.utilities.txttotime',
  'myApp.utilities.totime',
]).value('version', '0.1');
