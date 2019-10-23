function timeCtrl() {
  this.hmstyle={
    'white-space': "nowrap"
  };
  this.ddstyle={
    maxwidth: "2em"
  };
  this.hours=12;
  this.minutes=0;

  this.updateHours = function() {
    if (isNaN(this.hours) || this.hours.length>2) {
      this.hours="";
      //this.ngModel=null;
    }
    if (this.hours<0) {
      this.hours="00";
      this.ngModel.setHours(0);
    } else if (this.hours>23) {
      this.hours=23;
      this.ngModel.setHours(23);
    } else {
      this.ngModel.setHours(this.hours);
    }
  };

  this.setMinutes = function() {
    if (!this.minutes) {
      this.minutes="00";
    } else if (this.minutes.length==1) {
      this.minutes="0"+this.minutes;
    } else {
      this.ngModel.setMinutes(this.minutes);
    }
    this.ngModel.setMinutes(this.minutes);
  }

  this.updateMinutes = function() {
    if (isNaN(this.minutes) || !this.minutes || this.minutes.length>2) {
      this.minutes="";
      //this.ngModel=null;
    }
    if (this.minutes<0) {
      this.minutes="0";
      this.ngModel.setMinutes(0);
    }
    if (this.minutes>59) {
      this.minutes="59";
      this.ngModel.setMinutes(59);
    }
  }
}

angular.module('dsrcommon.utilities.dsrtime',[]).
  component('dsrtime',{
    replace:true,
    template:
    '<span ng-style="$ctrl.hmstyle"><input  type="text" style="max-width:2em;" pattern="(1[0-3])|([0-1][0-9])?" size="2" ng-model="$ctrl.hours" ng-change="$ctrl.updateHours()">:<input type="text" style="max-width:2em;" size="2" pattern="[0-5][0-9]?" ng-model="$ctrl.minutes" ng-change="$ctrl.updateMinutes()" ng-blur="$ctrl.setMinutes()"></span>',
    bindings: {
      ngModel: "=",
      onUpdate: '&'
    },
    controller: timeCtrl
  }
);
