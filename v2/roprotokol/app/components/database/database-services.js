'use strict';

angular.module('myApp.database.database-services', []).service('DatabaseService', function() {
  this.getBoatCategories = function () {
    return [
      {
        'id': 1,
        'name': 'Inrigger 2+'
      },
      {
        'id': 2,
        'name': 'Inrigger 4+'
      },
      {
        'id': 3,
        'name': 'Gig 2x'
      },
      {
        'id': 4,
        'name': 'Gig 3x'
      },
      {
        'id': 5,
        'name': 'Git/Outrig 4+ og 4-'
      },
      {
        'id': 6,
        'name': 'Git/Outrig 4x'
      },
      {
        'id': 7,
        'name': 'Git/Outrig 8+'
      },
      {
        'id': 8,
        'name': 'Sculler 1x'
      },
      {
        'id': 9,
        'name': 'Sculler 2x'
      },
      {
        'id': 10,
        'name': 'Svava 1x'
      },
      {
        'id': 11,
        'name': 'Kajak 1'
      },
      {
        'id': 12,
        'name': 'Kajak 2'
      },
      {
        'id': 13,
        'name': 'Motorbåde'
      }
    ];
  };
  
  this.getDamagesWithBoatId = function (boat_id) {
    var boatid2damages = {
      '1': [{
        'id': 1,
        'title': 'Let lak skade',
        'comment': 'blevet lakeret sidste gang 31/12',
        'level': 1,
      }],
      '2': [{
        'id': 1,
        'title': 'Mangler ror',
        'comment': '',
        'level': 4,
      }]
    };
    
    return boatid2damages[boat_id];
  };
  
  
  this.getBoatWithId = function (boat_id) {
    var boatid2boats = {
      '1': {
        'id': 1,
        'name': 'Ask',
        'status': 'Ok',
        'spaces': 3,
      },
      '2': {
        'id': 2,
        'name': 'Bjarke',
        'status': 'OK',
        'spaces': 3
      }
    };
    
    return boatid2boats[boat_id];
  };
  
  this.getBoatsWithCategoryId = function (category_id) {
    var category2boats = {
      '1': [
        {
          'id': 1,
          'name': 'Ask',
          'status': 'Ok',
          'spaces': 3
        },
        {
          'id': 2,
          'name': 'Bjarke',
          'status': 'OK',
          'spaces': 3
        }
      ]
    };
    
    return category2boats[category_id];
  };
  
  this.getDestinations = function () {
    return [
      {
        'id': 0,
        'name': 'Ukendt',
        'distance': 0
      },
      {
        'id': 1,
        'name': 'Bellevue',
        'distance': 15
      },
      {
        'id': 2,
        'name': 'Charlottenlund',
        'distance': 7
      },
      {
        'id': 3,
        'name': 'Flakfortet',
        'distance': 22
      }
    ];
  };
  
  this.getTripTypes = function () {
    return [
      {
        'id': 1,
        'name': 'Lokaltur'
      }
    ];
  };
  
  this.getRowersByNameOrId = function(val) {
    var data = [
      {
        'title': '8183 - Troels Liebe Bentsen',
        'id': 8183,
        'name': 'Troels Liebe Bentsen'
      },
      {
        'title': '1234 - Test Testsen',
        'id': 1234,
        'name': 'Test Testsen'
      }
    ];
    
    var results = data.filter(function(element) {
      return element.title.indexOf(val) > -1;
    });
    
    return results;
  };

});