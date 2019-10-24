function timeCtrl() {
  this.hmstyle={
    'white-space': "nowrap"
  };

  this.$onInit=function() {
    if (this.ngModel && this.ngModel.time) {
      this.hours=(this.ngModel.time.getHours().length<2?"0":"")+this.ngModel.time.getHours();
      this.minutes=(this.ngModel.time.getMinutes().length<2?"0":"")+this.ngModel.time.getMinutes();
    } else {
      this.hours="";
    }
  }
  this.$doCheck=function() {
    if (this.ngModel && this.ngModel.time) {
      var h=this.ngModel.time.getHours();
      var m=this.ngModel.time.getMinutes();
      // console.log("XXchan "+ 1*this.hours+":"+1*this.minutes+" => " + h+":"+m);
      if ((1*this.hours!=h || 1*this.minutes!=m)) {
        console.log("chan "+ 1*this.hours+":"+1*this.minutes+" => " + h+":"+m);
        this.hours=(this.ngModel.time.getHours()<10?"0":"")+this.ngModel.time.getHours();
        this.minutes=(this.ngModel.time.getMinutes()<10?"0":"")+this.ngModel.time.getMinutes();
      }
    } else {
      this.hours="";
    }
  }
  this.ddstyle={
    maxwidth: "2em"
  };
  this.updateHours = function() {
    if (!this.ngModel.time) {
      console.log("DATE");
      this.ngModel["time"]=new Date();
    }
    if (isNaN(this.hours) || this.hours.length>2) {
      this.hours="";
      //this.ngModel.time=null;
    }
    if (this.hours<0) {
      this.hours="00";
      this.ngModel.time.setHours(0);
    } else if (this.hours>23) {
      this.hours=23;
      this.ngModel.time.setHours(23);
    } else {
      this.ngModel.time.setHours(this.hours);
    }
    this.onUpdate();
  };

  this.setMinutes = function() {
    if (!this.ngModel.time) {
      console.log("DATE");
      this.ngModel["time"]=new Date();
    }
    if (!this.minutes) {
      this.minutes="00";
    } else if (this.minutes.length==1) {
      this.minutes="0"+this.minutes;
    } else {
      this.ngModel.time.setMinutes(this.minutes);
    }
    this.ngModel.time.setMinutes(this.minutes);
    this.onUpdate();
  }

  this.updateMinutes = function() {
    if (!this.ngModel.time) {
      console.log("dDATE");
      this.ngModel["time"]=new Date();
    }
    if (isNaN(this.minutes) || !this.minutes || this.minutes.length>2) {
      this.minutes="";
      //this.ngModel.time=null;
    }
    if (this.minutes<0) {
      this.minutes="00";
      this.ngModel.time.setMinutes(0);
    } else if (this.minutes>59) {
      this.minutes="59";
      this.ngModel.time.setMinutes(59);
    } else {
      this.ngModel.time.setMinutes(this.minutes);
    }
    this.onUpdate();
  }
}

angular.module('dsrcommon.utilities.dsrtime',[]).
  component('dsrtime',{
    replace:true,
    template:
    '<span ng-style="$ctrl.hmstyle"><input  type="text" style="max-width:2em;" pattern="(1[0-3])|([0-1][0-9])?" size="2" ng-model="$ctrl.hours" ng-change="$ctrl.updateHours()">:<input type="text" style="max-width:2em;" size="2" pattern="[0-5][0-9]?" ng-model="$ctrl.minutes" ng-change="$ctrl.updateMinutes()" ng-blur="$ctrl.setMinutes()"></span>',
    bindings: {
      ngModel: "=",
      onUpdate: '&',
    },
    controller: timeCtrl
  }
);
