function iCtrl() {
  this.hmstyle={
    'white-space': "nowrap"
  };
  this.$onInit=function() {
    this.st={time:this.ngModel.start_time};
    this.et={time:this.ngModel.end_time};
  }
  this.fixdate=function() {
    var now=new Date();
    if (this.et.time && (!this.ngModel.end_time || this.ngModel.end_time!==this.et.time)) {
      this.ngModel.end_time=this.et.time;
    }
    if (this.ngModel.end_time && this.ngModel.start_time && this.ngModel.end_time<this.ngModel.start_time) {
      this.ngModel.end_time.setDate(this.ngModel.end_time.getDate()+1);
    }
    if (this.ngModel.end_time>now) {
      this.et.time.setTime(now.getTime());
    }
  }
}

angular.module('dsrcommon.utilities.dsrinterval',[]).
  component('dsrinterval',{
    replace:true,
    template:
    '<span ng-style="$ctrl.hmstyle"><dsrtime ng-model="$ctrl.st" fixdate="$ctrl.fixdate()"></dsrtime>&mdash;<dsrtime ng-model="$ctrl.et"  fixdate="$ctrl.fixdate()"></dsrtime> </span>',
    bindings: {
      ngModel: "=",
      onUpdate: '&'
    },
    controller:iCtrl
  }
);
