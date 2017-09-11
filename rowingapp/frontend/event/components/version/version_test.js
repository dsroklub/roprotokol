'use strict';

describe('eventApp.version module', function() {
  beforeEach(module('eventApp.version'));

  describe('version service', function() {
    it('should return current version', inject(function(version) {
      expect(version).toEqual('0.2');
    }));
  });
});
