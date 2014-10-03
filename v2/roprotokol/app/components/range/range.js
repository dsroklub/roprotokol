'use strict';

angular.module('myApp.range', []).filter('range', function() {
  return function (input, start, end) {
        start = parseInt(start);
        end = parseInt(end);
        for (var i = start; i < end; i++)
            input.push(i);
        return input;
    };
});

