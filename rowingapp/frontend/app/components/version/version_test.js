'use strict';

describe('rowApp.version module', function() {
  beforeEach(module('rowApp.version'));

  describe('version service', function() {
    it('should return current version', inject(function(version) {
      expect(version).toEqual('0.2');
    }));
  });
});
