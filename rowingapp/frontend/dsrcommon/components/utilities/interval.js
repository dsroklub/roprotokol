function intervalCtrl() {
  this.hmstyle={
    'white-space': "nowrap"
  };
}

angular.module('dsrcommon.utilities.dsrinterval',[]).
  component('dsrinterval',{
    replace:true,
    template:
    '<span ng-style="$ctrl.hmstyle"><dsrtime ng-model="$ctrl.dsrfrom"></dsrtime>&mdash;<dsrtime ng-model="$ctrl.dsrto"/></dsrtime> </span>',
    bindings: {
      dsrfrom: "=",
      dsrto: "=",
      onUpdate: '&'
    },
    controller:intervalCtrl
  }
);
