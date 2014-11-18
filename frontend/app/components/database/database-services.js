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
          if(this[boat.category] === undefined) {
            this[boat.category] = [];
          }
          this[boat.category].push(boat);
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
  }
  
  this.getDamagesWithBoatId = function (boat_id) {
    return boatdamages[boat_id];
  };

  this.getBoatsWithCategoryId = function (categoryname) {
    return boatcategories[categoryname];
  };
  
  this.getDestinations = function () {
    return destinations;
  };
  
  this.getTripTypes = function () {
    return triptypes;
  };
  
  this.getRowersByNameOrId = function(val) {
    return rowers.filter(function(element) {
      return element.search.indexOf(val) > -1;
    });
  };

});
