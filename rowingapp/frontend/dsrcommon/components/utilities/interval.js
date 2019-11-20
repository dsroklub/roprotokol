
function pad(n) {
  if ((""+n).length==1) return "0"+n;
  return ""+n;
}

function todate(dd) {
  if (!dd) {
    return null;
  }
  return new Date(dd.year,dd.month-1,dd.day,dd.hour,dd.minute);
}
function iCtrl() {
  this.hmstyle={
    'white-space': "nowrap"
  };
  this.$onInit=function() {
    this.st=this.ngModel.start_time;
    this.et=this.ngModel.end_time;
    this.sdate=""+this.ngModel.start_time.year+"-"+pad(this.ngModel.start_time.month)+"-"+pad(this.ngModel.start_time.day);
  }

  this.updateStartDate=function() {
    if (this.sdate) {
      fd=this.sdate.split("-");
      if (fd.length==3) {
        this.ngModel.start_time.year=fd[0];
        this.ngModel.start_time.month=fd[1];
        this.ngModel.start_time.day=fd[2];
      }
      this.onUpdate();
      this.ngChange();
    }
  }

  this.fixdate=function() {
    var now=new Date();
    if (!(this.ngModel.end_time.hour &&this.ngModel.start_time)) {
      return;
    }
    var et=todate(this.ngModel.end_time);
    var st=todate(this.ngModel.start_time);
    this.ngModel.end_time.year=this.ngModel.start_time.year;
    this.ngModel.end_time.month=this.ngModel.start_time.month;
    this.ngModel.end_time.day=this.ngModel.start_time.day;
    if (st>et) {
      this.ngModel.end_time.day=1*this.ngModel.end_time.day+1;
      tt=todate(this.ngModel.end_time);
      this.ngModel.end_time.year=tt.getFullYear();
      this.ngModel.end_time.month=tt.getMonth()+1;
      this.ngModel.end_time.day=tt.getDate();
      this.onUpdate();
    }
    if (todate(this.ngModel.end_time.year)>now) {
      this.ngModel.end_time.year=(now.getHour());
      this.ngModel.end_time.month=now.getMonth()+1;
      this.ngModel.end_time.day=(now.getDate());
      this.ngModel.end_time.hour=(now.getHours());
      this.ngModel.end_time.minute=(now.getMinutes());
      this.onUpdate();
    }
    if (this.ngModel.hours) {
      this.ngModel.hours=Math.round((et-st)/360000)/10;
    }
    this.ngChange();
  }
}

angular.module('dsrcommon.utilities.dsrinterval',[]).
  component('dsrinterval',{
    replace:true,
    template:
    '<span ng-style="$ctrl.hmstyle"><input ng-if="$ctrl.usestartdate" "type=date" ng-model="$ctrl.sdate" ng-blur="$ctrl.updateStartDate()" placeholder="YYYY-MM-DD" size="10" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"><span ng-if="!$ctrl.usestarttime">&nbsp;{{$ctrl.st.hour}}:{{$ctrl.st.minute}}</span><dsrtime ng-if="$ctrl.usestarttime" ng-model="$ctrl.st" fixdate="$ctrl.fixdate()"></dsrtime>&mdash;<dsrtime ng-model="$ctrl.et"  fixdate="$ctrl.fixdate()"></dsrtime> </span>',
    bindings: {
      ngModel: "=",
      usestartdate: "<",
      onUpdate: '&',
      ngChange: '&'
    },
    controller:iCtrl
  }
);
