'use strict';
app.controller('RowerCtrl', ['$scope', '$routeParams', 'DatabaseService', '$interval', 'ngDialog', 'ngTableParams', '$filter',
			     function ($scope, $routeParams, DatabaseService, $interval, ngDialog, ngTableParams, $filter) {
			       DatabaseService.init().then(function () {
			//	 alert("rower init");
			       }
							  );
			     }
			    ]
	      );
