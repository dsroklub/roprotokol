'use strict';
angular.module('myApp.database.database-services', []).service('DatabaseService', function($http, $q) {
  var boats;
  var boatcategories;
  var boatdamages;
  var destinations;
  var triptypes;
  var rowers;
  var dbdone=0;
  var rodb;
  var rowerstatistics={'rowboat':undefined,'kayak':undefined,'any':undefined};
  var boatstatistics={};
  var databasesource=dbmode;

  function multifield(fld) {
    var res=[];
    fld.split('££').forEach(function(fl) {
      var ris=fl.split(":§§:");
      if (ris.length == 2) {
	var kv={};
	kv[ris[0]]=ris[1];
	res.push(kv);
      } else {
	console.log("Unparseable multifield: "+fld);
      }
    }
			   )
    return res;
  }
  
  function onError(err){
    console.log(err);
  }
  
  function onSuccess(tx, results ){
    console.log( 'SQL Done' )
  }

  function onReadyTransaction() {
    // console.log( 'Transaction completed' )
  }
  
  this.init = function () {
    if (typeof(openDatabase) === typeof(Function)) {
      rodb = openDatabase('roprotokol4', '1.0', 'DSR devel roprotokol', 30 * 1024 * 1024);
//      alert('got rodb '+rodb);
      //console.log('got rodb '+rodb);
      rodb.transaction(function (tx) {
//	tx.executeSql("DROP TABLE Boat;");
      }
		      );
//      alert('got made '+rodb);
    }
    var dbloaded = $q.defer();
    var boatsloaded = $q.defer();
    var boatdamagesloaded = $q.defer();
    var destinationsloaded = $q.defer();
    var triptypesloaded = $q.defer();
    var rowersloaded = $q.defer();
    var boatstatisticsloaded = {'any':$q.defer(),'rowboat':$q.defer(),'kayak':$q.defer()};
    var rowerstatisticsloaded = {'any':$q.defer(),'rowboat':$q.defer(),'kayak':$q.defer()};
    var boattypes = ['kayak','any','rowboat'];

    var iter=0;
    rodb.readTransaction(function (tx) {
      tx.executeSql("SELECT name FROM sqlite_master WHERE type='table'",[],
		    function(xa, rs) {
		      if (rs.rows.length>4) {
			dbdone=1;
		      }
		    }
		    , onError);	    
    }
			);
			   
    if(dbdone ==0) {
      // console.log("do db load");
      $http.get('data/db.sql').then(function(response) {
	rodb.transaction(function (tx) {
          angular.forEach(response.data.split("\n"), function(sqlline, index) {
	    // console.log("i "+iter+" "+sqlline);
	    tx.executeSql(sqlline,[], onSuccess, onError);
          }, null);	    
//	  console.log('RODB DONE DONE '+rodb);	  
	},onError,onReadyTransaction);
	dbloaded.resolve(true);
	dbdone=1;
      }
				   )
    } else {
      dbloaded.resolve(true);
    }
    
    if(boats === undefined) {
      //Build indexes and lists for use by API
      boats = {};
      boatcategories = {};
      rodb.readTransaction(function (tx) {
      var sq="SELECT Boat.id,\
           Boat.Name as name,\
           BoatType.Seatcount as spaces,\
           Boat.Description as description,\
           BoatType.Name as category,\
           BoatCategory.Name as boattype,\
           Boat.Location as location,\
           Boat.Placement as placement,\
           COALESCE(MAX(Damage.Degree),0) as damage,\
           MAX(Trip.TripID) as trip,\
           MAX(Trip.OutTime) as outtime,\
           MAX(Trip.ExpectedIn) as expected_in\
    FROM Boat\
         INNER JOIN BoatType ON (BoatType.id=BoatType)\
         INNER JOIN BoatCategory ON (BoatCategory.id = BoatType.Category)\
         LEFT OUTER JOIN Damage ON (Damage.Boat=Boat.id AND Damage.Repaired IS NULL)\
         LEFT OUTER JOIN Trip ON (Trip.BoatID = Boat.id AND Trip.Intime IS NULL)\
    WHERE \
         Boat.Decommissioned IS NULL\
    GROUP BY\
       Boat.id,\
       Boat.Name,\
       BoatType.Seatcount,\
       Boat.Description,\
       BoatType.Name,\
       Boat.Location,\
       Boat.Placement";
	iter++;
	// console.log("i "+iter+" "+sqlline);
	tx.executeSql(sq,[],
		      function(xa, rs) {
			for(var i=0;i<rs.rows.length;i++){
			  var boat=rs.rows.item(i)
			  // alert(boat.name);
			  boats[boat.id] = boat;
			  var category = boat.category;
			  if (boatcategories[category] === undefined) {
			    boatcategories[category] = [];
			  }
			  boatcategories[category].push(boat);
			};
   		      },
		      onError);
      },onError,onReadyTransaction);
      // console.log('RODB DONE DONE '+rodb);	  
      boatsloaded.resolve(true);      
    } else {
      boatsloaded.resolve(true);
    }
    
    if(boatdamages === "undefined") {
      $http.get('boatdamages.php').then(function(response) {
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
      rodb.readTransaction(function (tx) {
	  tx.executeSql("SELECT id, Name as name, Meter as distance, ExpectedDurationNormal AS duration,  ExpectedDurationInstruction AS duration_instruction FROM Destination ORDER BY name",[],
			function(xact, rs) {
			  destinations=[];
			  for(var i=0;i<rs.rows.length;i++) {
			    destinations.push(rs.rows.item(i));
			  }
			  destinationsloaded.resolve(true);
   			},
			onError);
      },onError,onReadyTransaction);
    } else {
      destinationsloaded.resolve(true);
    }
    
    if(triptypes === undefined) {
      rodb.readTransaction(function (tx) {
	  tx.executeSql("SELECT Name AS name,Description AS description, GROUP_CONCAT(required_right||':§§:'||requirement, '££') as rights from TripType, TripRights WHERE active AND trip_type=Name GROUP BY TripType.Name",[],
			function(xact, rs) {
			  triptypes=[];
			  for(var i=0;i<rs.rows.length;i++) {
			    var row=rs.rows.item(i);
			    var crow = {};
			    for (var key in row) {
			      crow[key] = row[key];
			    }
			    crow['rights']=multifield(row['rights']);
			    triptypes.push(crow);
			  }
			  triptypesloaded.resolve(true);
   			},
			onError);
      },onError,onReadyTransaction);      
    } else {
      triptypesloaded.resolve(true);
    }

    if(rowers === undefined) {
      rodb.readTransaction(function (tx) {
	tx.executeSql("SELECT Member.MemberID as id,FirstName||' '||LastName AS name,Initials AS initials, GROUP_CONCAT(MemberRight||':§§:'||argument, '££') as rights" +
    "  FROM Member,MemberRights WHERE MemberRights.MemberID=Member.MemberID GROUP BY Member.MemberID",[],
		      function(xact, rs) {
			rowers=[];
			for(var i=0;i<rs.rows.length;i++) {
			  var row=rs.rows.item(i);
			  var crow = {};
			  for (var key in row) {
			    crow[key] = row[key];
			  }
			  crow['rights']=multifield(row['rights']);
			  rowers.push(crow);
			}
			rowersloaded.resolve(true);
   		      },
		      onError);
      },onError,onReadyTransaction);      
    } else {
      rowersloaded.resolve(true);
    }
      
    if(rowerstatistics['any'] === "undefined") {
      var bx;
      for (bx in boattypes) {
	(function(boattype) {
	var farg="";
	  if (boattype != "any") {
	    farg='?boattype='+boattype;
	    	   // farg='Qboattype'+boattype;
	  }
	  $http.get('rower_statistics.php'+farg).then(function(response) {
            rowerstatistics[boattype] = [];
            angular.forEach(response.data, function(stat, index) {
              //stat.search = stat.id + " " + stat.firstname + " " + stat.lastname;
              this.push(stat);
            }, rowerstatistics[boattype])
	    rowerstatisticsloaded[boattype].resolve(true);
	  }							    );
	})(boattypes[bx]);
      }
    } else {
      rowerstatisticsloaded['any'].resolve(true);
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
    // console.log("Lock "+ boat_id + " : " + timestamp);
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

  this.getRowerStatistics = function (boattype) {
    return rowerstatistics[boattype];
  };
  this.getBoatStatistics = function (boattype) {
    return boatstatistics[boattype];
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
