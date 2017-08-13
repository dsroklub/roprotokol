'use strict';

describe('gym.version module', function() {
  beforeEach(module('gym.version'));

  describe('version service', function() {
    it('should return current version', inject(function(version) {
      expect(version).toEqual('0.2');
    }));
  });
});
