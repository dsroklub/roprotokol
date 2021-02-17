function timeCtrl() {
  this.hmstyle={
    'white-space': "nowrap"
  };
  function pad(n) {
    if ((""+n).length==1) return "0"+n;
    return ""+n;
  }

  this.$onInit=function() {
    console.log("dsrtimestring AAA");
    if (!this.ngModel) {
      this.ngModel="12:00";
    }
    if (typeof(this.ngModel)=="string") {
      var ta=this.ngModel.split(':');
      if (ta.length>1) {
        this.ngModel={"hour":ta[0],"minute":ta[1]};
      }
    }
    if (this.ngModel && this.ngModel.hour!=null) {
      if (this.ngModel.hour<10) {
        this.ngModel.hour="0"+1*this.ngModel.hour;
        }
      if (this.ngModel.minute<10) {
        this.ngModel.minute="0"+1*this.ngModel.minute;
      }
    }
    this.setTimeString();
  }
  this.ddstyle={
    maxwidth: "2em"
  };
  this.setTimeString = function() {
    this.ngModel.timestring=(this.ngModel.hour?this.ngModel.hour:"0")+":"+(this.ngModel.minute?this.ngModel.minute:"00");
  }

  this.updateHours = function() {
    if (isNaN(this.ngModel.hour) || this.ngModel.hour.length>2) {
      this.ngModel.hour="";
    }
    if (this.ngModel.hour<0) {
      this.ngModel.hour="00";
    } else if (this.ngModel.hour>23) {
      this.ngModel.hour=23;
    }
    this.setTimeString();
    this.onUpdate();
    if (this.ngModel.hour) {
      this.ngChange();
    }
  };

  this.setHours = function() {
    this.setTimeString();
  }

  this.setMinutes = function() {
    if (!this.ngModel.minute) {
      this.ngModel.minute="00";
    } else if (this.ngModel.minute.length==1) {
      this.ngModel.minute="0"+this.ngModel.minute;
    }
    this.onUpdate();
    this.setTimeString();
  }

  this.initEnd = function() {
    if (this.ngModel.hour!=null) {
      return;
    }
    now = new Date();
    this.ngModel.year=now.getFullYear();
    this.ngModel.month=pad(now.getMonth()+1);
    this.ngModel.day=pad(now.getDate());
    this.ngModel.hour=pad(now.getHours());
    this.ngModel.minute=pad(now.getMinutes());
  }

  this.updateMinutes = function() {
    if (isNaN(this.ngModel.minute) || !this.ngModel.minute || this.ngModel.minute.length>2) {
      this.ngModel.minute="";
      //this.ngModel.time=null;
    }
    if (this.ngModel.minute<0) {
      this.ngModel.minute="00";
    } else if (this.ngModel.minute>59) {
      this.ngModel.minute="59";
    }
    this.setTimeString();
    this.onUpdate();
    if (this.ngModel.minute) {
      this.ngChange();
    }
  }
}

angular.module('dsrcommon.utilities.dsrtimestring',[]).
  component('dsrtimestring',{
    replace:true,
    template:
    '<span ng-style="$ctrl.hmstyle"><input  size="2" type="text"  pattern="(1[0-3])|([0-1][0-9])?" ng-focus="$ctrl.initEnd()" ng-model="$ctrl.ngModel.hour" ng-blur="$ctrl.setHours()" ng-change="$ctrl.updateHours()">:<input type="text" size="2" pattern="[0-9][0-9]?" ng-model="$ctrl.ngModel.minute" ng-change="$ctrl.updateMinutes()" ng-blur="$ctrl.setMinutes()"></span>',
    bindings: {
      ngModel: "=",
      onUpdate: '&',
      ngChange: '&'
    },
    controller: timeCtrl
  }
);
