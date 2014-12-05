'use strict';

angular.module('myApp.database.database-services', []).service('DatabaseService', function($http, $q) {
  var boats;
  var boatcategories;
  var boatdamages;
  var destinations;
  var triptypes;
  var rowers;

  this.init = function () {
    var boatsloaded = $q.defer();
    var boatdamagesloaded = $q.defer();
    var destinationsloaded = $q.defer();
    var triptypesloaded = $q.defer();
    var rowersloaded = $q.defer();

    if(boats === undefined) {
      //Build indexes and lists for use by API
      $http.get('data/boats.json').then(function(response) {
        boats = {};
        angular.forEach(response.data, function(boat, index) {
          this[boat.id] = boat;
        }, boats);
        boatcategories = {};
        angular.forEach(response.data, function(boat, index) {
          var category = boat.category;
          if(this[category] === undefined) {
            this[category] = [];
          }
          this[category].push(boat);
        }, boatcategories);

       boatsloaded.resolve(true);
      });

    } else {
      boatsloaded.resolve(true);
    }
    
    if(boatdamages === undefined) {
      $http.get('data/boatdamages.json').then(function(response) {
        boatdamages = {};
        angular.forEach(response.data, function(boatdamage, index) {
           if(this[boatdamage.boat_id] === undefined) {
            this[boatdamage.boat_id] = [];
          }
          this[boatdamage.boat_id].push(boatdamage);
        }, boatdamages);
        boatdamagesloaded.resolve(true);
      });

    } else {
      boatdamagesloaded.resolve(true);
    }

    if(destinations === undefined) {
      $http.get('data/destinations.json').then(function(response) {
        destinations = response.data;
        destinationsloaded.resolve(true);
      });

    } else {
      destinationsloaded.resolve(true);
    }

    if(triptypes === undefined) {
      $http.get('data/triptypes.json').then(function(response) {
        triptypes = response.data;
        triptypesloaded.resolve(true);
      });

    } else {
      triptypesloaded.resolve(true);
    }

    if(rowers === undefined) {
      $http.get('data/rowers.json').then(function(response) {
        rowers = [];
        angular.forEach(response.data, function(rower, index) {
          rower.search = rower.id + " " + rower.name;
          this.push(rower);
        }, rowers);

        rowersloaded.resolve(true);
      });

    } else {
      rowersloaded.resolve(true);
    }

    return $q.all([boatsloaded.promise,boatdamagesloaded.promise, destinationsloaded.promise, 
      triptypesloaded.promise, rowersloaded.promise]);
  };
  
  this.getBoatCategories = function () {
    return Object.keys(boatcategories).sort();
  };

  this.getBoatWithId = function (boat_id) {
    return boats[boat_id];
  };
  
  this.getBoatStatuses = function (boat_id) {
    // On the water(Checkouted), Being booked(Locked until), Reserved, Has damage(Severe, Medium, Light) = ?
  };
  
  this.lockBoatWithId = function (boat_id, date) {
    var timestamp = date.toISOString();
    // TODO: Send timestamp to server
    console.log("Lock "+ boat_id + " : " + timestamp);
  };
  
  this.getDamagesWithBoatId = function (boat_id) {
    return boatdamages[boat_id];
  };

  this.getBoatsWithCategoryName = function (categoryname) {
    var boats = boatcategories[categoryname];
    if (boats) {
      return boats.sort(function (a, b) {
        return a.name.localeCompare(b.name);
      });
    } else {
      return null;
    }
  };
  
  this.getDestinations = function () {
    return destinations;
  };
  
  this.getTripTypes = function () {
    return triptypes;
  };
  
  this.getRowersByNameOrId = function(val, preselectedids) {
    return rowers.filter(function(element) {
      return val.length > 2  
        && (preselectedids === undefined || !(element.id in preselectedids))
        && element.search.indexOf(val) > -1;
    });
  };
  
  this.createRowerByName = function(name) {
    // TODO: implement
    return {
        "id": "K1",
        "name": name
      };
  };
  
  this.createTrip = function(data) {
    $http.post('../../backend/createtrip.php', data).success(function() {
      // TODO: make sure we block until the trip is created    
    });
    return;
  };
  

});
