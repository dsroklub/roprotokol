'use strict';

describe('myApp.admin module', function() {

  beforeEach(module('myApp.admin'));

  describe('admin controller', function(){

    it('should ....', inject(function($controller) {
      //spec body
      var adminCtrl = $controller('AdminCtrl');
      expect(adminCtrl).toBeDefined();
    }));

  });
});