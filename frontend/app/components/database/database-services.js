'use strict';

angular.module('myApp.database.database-services', []).service('DatabaseService', function($http, $q) {
  var boats;
  var boatcategories;
  $http.get('data/boats.json').then(function(response) {
    boats = response.data;
    //Build indexes and lists
    angular.forEach(boats, function(boat, index) {
      this[boat.category] = boat;
    }, boatcategories);
    
  });
  
  
  
  this.getBoatCategories = function () {
    
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
   "34" : {
      "status" : "OK",
      "spaces" : 3,
      "name" : "Helge",
      "id" : "34"
   },
   "157" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "157",
      "name" : "Nemo"
   },
   "26" : {
      "name" : "Grumme",
      "id" : "26",
      "status" : "OK",
      "spaces" : 1
   },
   "119" : {
      "spaces" : 5,
      "status" : "OK",
      "id" : "119",
      "name" : "Nanna"
   },
   "222" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Regnbueørred (Privat)",
      "id" : "222"
   },
   "156" : {
      "name" : "Tangloppe",
      "id" : "156",
      "status" : "OK",
      "spaces" : 1
   },
   "120" : {
      "name" : "2-åres lånt båd",
      "id" : "120",
      "status" : "OK",
      "spaces" : 3
   },
   "99" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Muræne",
      "id" : "99"
   },
   "160" : {
      "status" : "OK",
      "spaces" : 5,
      "name" : "Skadi",
      "id" : "160"
   },
   "95" : {
      "id" : "95",
      "name" : "Alfa",
      "spaces" : 1,
      "status" : "OK"
   },
   "230" : {
      "id" : "230",
      "name" : "Møhring",
      "spaces" : 3,
      "status" : "OK"
   },
   "176" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Ising",
      "id" : "176"
   },
   "187" : {
      "id" : "187",
      "name" : "Langguster",
      "spaces" : 2,
      "status" : "OK"
   },
   "217" : {
      "name" : "Cryseis",
      "id" : "217",
      "status" : "OK",
      "spaces" : 1
   },
   "122" : {
      "status" : "OK",
      "spaces" : 2,
      "name" : "Luzern",
      "id" : "122"
   },
   "86" : {
      "status" : "OK",
      "spaces" : 5,
      "name" : "Ydun",
      "id" : "86"
   },
   "142" : {
      "id" : "142",
      "name" : "Embla",
      "spaces" : 5,
      "status" : "OK"
   },
   "55" : {
      "name" : "Olsen",
      "id" : "55",
      "status" : "OK",
      "spaces" : 5
   },
   "204" : {
      "id" : "204",
      "name" : "Totu",
      "spaces" : 1,
      "status" : "OK"
   },
   "118" : {
      "spaces" : 5,
      "status" : "OK",
      "id" : "118",
      "name" : "Ask"
   },
   "41" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Jon",
      "id" : "41"
   },
   "113" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Flyvefisk",
      "id" : "113"
   },
   "203" : {
      "id" : "203",
      "name" : "Fenris",
      "spaces" : 3,
      "status" : "OK"
   },
   "19" : {
      "id" : "19",
      "name" : "Fafner",
      "spaces" : 3,
      "status" : "OK"
   },
   "72" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Svip",
      "id" : "72"
   },
   "110" : {
      "name" : "Hornfisk",
      "id" : "110",
      "status" : "OK",
      "spaces" : 1
   },
   "239" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "239",
      "name" : "Pædagogsculler 1"
   },
   "125" : {
      "spaces" : 2,
      "status" : "OK",
      "id" : "125",
      "name" : "Lånt 2-Kajak"
   },
   "158" : {
      "id" : "158",
      "name" : "Karpe",
      "spaces" : 1,
      "status" : "OK"
   },
   "149" : {
      "name" : "Delfin",
      "id" : "149",
      "status" : "OK",
      "spaces" : 1
   },
   "144" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Ansjos",
      "id" : "144"
   },
   "228" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "228",
      "name" : "Guldmakrel"
   },
   "226" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "226",
      "name" : "Krabbe"
   },
   "145" : {
      "status" : "OK",
      "spaces" : 5,
      "name" : "Njord",
      "id" : "145"
   },
   "174" : {
      "id" : "174",
      "name" : "Blåhval",
      "spaces" : 1,
      "status" : "OK"
   },
   "49" : {
      "spaces" : 2,
      "status" : "OK",
      "id" : "49",
      "name" : "Minerva"
   },
   "207" : {
      "id" : "207",
      "name" : "Teta",
      "spaces" : 1,
      "status" : "OK"
   },
   "59" : {
      "name" : "Psi",
      "id" : "59",
      "status" : "OK",
      "spaces" : 1
   },
   "107" : {
      "name" : "Rødfisk",
      "id" : "107",
      "status" : "OK",
      "spaces" : 2
   },
   "236" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Lambda",
      "id" : "236"
   },
   "91" : {
      "name" : "Forel 2",
      "id" : "91",
      "status" : "OK",
      "spaces" : 1
   },
   "231" : {
      "spaces" : 2,
      "status" : "OK",
      "id" : "231",
      "name" : "Ål"
   },
   "232" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Havkat",
      "id" : "232"
   },
   "36" : {
      "spaces" : 3,
      "status" : "OK",
      "id" : "36",
      "name" : "Hroar"
   },
   "17" : {
      "name" : "Elektra",
      "id" : "17",
      "status" : "OK",
      "spaces" : 3
   },
   "234" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "234",
      "name" : "Stør"
   },
   "32" : {
      "id" : "32",
      "name" : "Havtaske",
      "spaces" : 2,
      "status" : "OK"
   },
   "56" : {
      "spaces" : 9,
      "status" : "OK",
      "id" : "56",
      "name" : "Ormen"
   },
   "60" : {
      "id" : "60",
      "name" : "Ran",
      "spaces" : 1,
      "status" : "OK"
   },
   "179" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Låne Kajak 3",
      "id" : "179"
   },
   "224" : {
      "spaces" : 2,
      "status" : "OK",
      "id" : "224",
      "name" : "Laks"
   },
   "198" : {
      "name" : "Pollux",
      "id" : "198",
      "status" : "OK",
      "spaces" : 5
   },
   "180" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Låne kajak 4",
      "id" : "180"
   },
   "100" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Sæl",
      "id" : "100"
   },
   "209" : {
      "id" : "209",
      "name" : "Hera",
      "spaces" : 2,
      "status" : "OK"
   },
   "175" : {
      "name" : "Gedde",
      "id" : "175",
      "status" : "OK",
      "spaces" : 1
   },
   "46" : {
      "status" : "OK",
      "spaces" : 3,
      "name" : "Loke",
      "id" : "46"
   },
   "81" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Tuto",
      "id" : "81"
   },
   "189" : {
      "id" : "189",
      "name" : "Pallas Athene",
      "spaces" : 2,
      "status" : "OK"
   },
   "188" : {
      "spaces" : 2,
      "status" : "OK",
      "id" : "188",
      "name" : "Platon"
   },
   "96" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "96",
      "name" : "Ørred"
   },
   "52" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Ny",
      "id" : "52"
   },
   "221" : {
      "name" : "Blæksprutte",
      "id" : "221",
      "status" : "OK",
      "spaces" : 1
   },
   "2" : {
      "id" : "2",
      "name" : "Alf",
      "spaces" : 1,
      "status" : "OK"
   },
   "205" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Omega",
      "id" : "205"
   },
   "139" : {
      "name" : "Papegøjefisk",
      "id" : "139",
      "status" : "OK",
      "spaces" : 1
   },
   "7" : {
      "id" : "7",
      "name" : "Barracuda",
      "spaces" : 1,
      "status" : "OK"
   },
   "153" : {
      "id" : "153",
      "name" : "Togo 8",
      "spaces" : 0,
      "status" : "OK"
   },
   "135" : {
      "spaces" : 2,
      "status" : "OK",
      "id" : "135",
      "name" : "Hugorm"
   },
   "115" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "115",
      "name" : "Pighvar"
   },
   "23" : {
      "status" : "OK",
      "spaces" : 3,
      "name" : "Frigg",
      "id" : "23"
   },
   "227" : {
      "id" : "227",
      "name" : "Grindehval",
      "spaces" : 1,
      "status" : "OK"
   },
   "13" : {
      "spaces" : 5,
      "status" : "OK",
      "id" : "13",
      "name" : "Dan"
   },
   "152" : {
      "id" : "152",
      "name" : "Togo 7",
      "spaces" : 0,
      "status" : "OK"
   },
   "233" : {
      "id" : "233",
      "name" : "Rødspætte",
      "spaces" : 1,
      "status" : "OK"
   },
   "147" : {
      "name" : "Mulle",
      "id" : "147",
      "status" : "OK",
      "spaces" : 1
   },
   "143" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "143",
      "name" : "Sardin"
   },
   "78" : {
      "name" : "Thor",
      "id" : "78",
      "status" : "OK",
      "spaces" : 5
   },
   "27" : {
      "name" : "Blåmusling",
      "id" : "27",
      "status" : "OK",
      "spaces" : 1
   },
   "131" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "131",
      "name" : "Fjæsing"
   },
   "111" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Ulk",
      "id" : "111"
   },
   "146" : {
      "id" : "146",
      "name" : "Skalle",
      "spaces" : 1,
      "status" : "OK"
   },
   "87" : {
      "id" : "87",
      "name" : "Øresvin",
      "spaces" : 1,
      "status" : "OK"
   },
   "184" : {
      "id" : "184",
      "name" : "Snapper",
      "spaces" : 1,
      "status" : "OK"
   },
   "186" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Valborg",
      "id" : "186"
   },
   "92" : {
      "name" : "4-åres lånt båd",
      "id" : "92",
      "status" : "OK",
      "spaces" : 5
   },
   "240" : {
      "id" : "240",
      "name" : "Hugin",
      "spaces" : 5,
      "status" : "OK"
   },
   "195" : {
      "id" : "195",
      "name" : "Ro",
      "spaces" : 1,
      "status" : "OK"
   },
   "35" : {
      "id" : "35",
      "name" : "Hjalte",
      "spaces" : 3,
      "status" : "OK"
   },
   "9" : {
      "id" : "9",
      "name" : "Bjarke",
      "spaces" : 5,
      "status" : "OK"
   },
   "65" : {
      "status" : "OK",
      "spaces" : 3,
      "name" : "Sif",
      "id" : "65"
   },
   "196" : {
      "name" : "002",
      "id" : "196",
      "status" : "OK",
      "spaces" : 1
   },
   "223" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "223",
      "name" : "Triton"
   },
   "1" : {
      "id" : "1",
      "name" : "Absalon",
      "spaces" : 3,
      "status" : "OK"
   },
   "181" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "181",
      "name" : "Lånt Kajak 1"
   },
   "202" : {
      "status" : "OK",
      "spaces" : 3,
      "name" : "Frederik d. IX",
      "id" : "202"
   },
   "211" : {
      "id" : "211",
      "name" : "Bifrost",
      "spaces" : 9,
      "status" : "OK"
   },
   "82" : {
      "status" : "OK",
      "spaces" : 5,
      "name" : "Vidar",
      "id" : "82"
   },
   "178" : {
      "id" : "178",
      "name" : "Låne Kajak 2",
      "spaces" : 1,
      "status" : "OK"
   },
   "10" : {
      "spaces" : 3,
      "status" : "OK",
      "id" : "10",
      "name" : "Brage"
   },
   "94" : {
      "name" : "Hu",
      "id" : "94",
      "status" : "OK",
      "spaces" : 3
   },
   "93" : {
      "id" : "93",
      "name" : "Røskva",
      "spaces" : 3,
      "status" : "OK"
   },
   "66" : {
      "name" : "Sigma",
      "id" : "66",
      "status" : "OK",
      "spaces" : 1
   },
   "22" : {
      "name" : "Freja",
      "id" : "22",
      "status" : "OK",
      "spaces" : 3
   },
   "89" : {
      "id" : "89",
      "name" : "Sild",
      "spaces" : 1,
      "status" : "OK"
   },
   "133" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "133",
      "name" : "Søløve"
   },
   "18" : {
      "name" : "Epsilon",
      "id" : "18",
      "status" : "OK",
      "spaces" : 1
   },
   "136" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Stenbider",
      "id" : "136"
   },
   "44" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "44",
      "name" : "Ksi"
   },
   "193" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Nympha",
      "id" : "193"
   },
   "199" : {
      "id" : "199",
      "name" : "Giallo",
      "spaces" : 5,
      "status" : "OK"
   },
   "197" : {
      "name" : "Aslaug",
      "id" : "197",
      "status" : "OK",
      "spaces" : 4
   },
   "206" : {
      "id" : "206",
      "name" : "Afrodite",
      "spaces" : 1,
      "status" : "OK"
   },
   "109" : {
      "name" : "Sej",
      "id" : "109",
      "status" : "OK",
      "spaces" : 1
   },
   "201" : {
      "spaces" : 3,
      "status" : "OK",
      "id" : "201",
      "name" : "Kristian"
   },
   "177" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Knurhane",
      "id" : "177"
   },
   "210" : {
      "id" : "210",
      "name" : "Sct. Cathrine",
      "spaces" : 2,
      "status" : "OK"
   },
   "200" : {
      "id" : "200",
      "name" : "Hermes",
      "spaces" : 4,
      "status" : "OK"
   },
   "98" : {
      "name" : "SøElefant",
      "id" : "98",
      "status" : "OK",
      "spaces" : 1
   },
   "54" : {
      "status" : "OK",
      "spaces" : 5,
      "name" : "Odin",
      "id" : "54"
   },
   "51" : {
      "id" : "51",
      "name" : "Munin",
      "spaces" : 5,
      "status" : "OK"
   },
   "130" : {
      "name" : "Urd",
      "id" : "130",
      "status" : "OK",
      "spaces" : 1
   },
   "70" : {
      "name" : "Svante",
      "id" : "70",
      "status" : "OK",
      "spaces" : 3
   },
   "71" : {
      "name" : "Svava",
      "id" : "71",
      "status" : "OK",
      "spaces" : 1
   },
   "103" : {
      "id" : "103",
      "name" : "Rokke",
      "spaces" : 2,
      "status" : "OK"
   },
   "190" : {
      "name" : "Supermax Lady",
      "id" : "190",
      "status" : "OK",
      "spaces" : 2
   },
   "237" : {
      "status" : "OK",
      "spaces" : 4,
      "name" : "Balder",
      "id" : "237"
   },
   "126" : {
      "name" : "1-åres lånt båd",
      "id" : "126",
      "status" : "OK",
      "spaces" : 1
   },
   "114" : {
      "name" : "Haj",
      "id" : "114",
      "status" : "OK",
      "spaces" : 1
   },
   "85" : {
      "id" : "85",
      "name" : "Vips",
      "spaces" : 1,
      "status" : "OK"
   },
   "129" : {
      "name" : "Ask (kajak)",
      "id" : "129",
      "status" : "OK",
      "spaces" : 1
   },
   "121" : {
      "status" : "OK",
      "spaces" : 1,
      "name" : "Hummer",
      "id" : "121"
   },
   "191" : {
      "id" : "191",
      "name" : "Gerda",
      "spaces" : 2,
      "status" : "OK"
   },
   "208" : {
      "name" : "Delta",
      "id" : "208",
      "status" : "OK",
      "spaces" : 1
   },
   "194" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "194",
      "name" : "My"
   },
   "90" : {
      "id" : "90",
      "name" : "Torsk",
      "spaces" : 1,
      "status" : "OK"
   },
   "50" : {
      "id" : "50",
      "name" : "Mjølner",
      "spaces" : 5,
      "status" : "OK"
   },
   "238" : {
      "status" : "OK",
      "spaces" : 5,
      "name" : "Balder",
      "id" : "238"
   },
   "33" : {
      "spaces" : 4,
      "status" : "OK",
      "id" : "33",
      "name" : "Heimdal"
   },
   "192" : {
      "name" : "Iota",
      "id" : "192",
      "status" : "OK",
      "spaces" : 1
   },
   "24" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "24",
      "name" : "Gamma"
   },
   "225" : {
      "status" : "OK",
      "spaces" : 3,
      "name" : "Ull",
      "id" : "225"
   },
   "28" : {
      "id" : "28",
      "name" : "Gudmund",
      "spaces" : 5,
      "status" : "OK"
   },
   "123" : {
      "spaces" : 9,
      "status" : "OK",
      "id" : "123",
      "name" : "Sleipner"
   },
   "79" : {
      "id" : "79",
      "name" : "Tjalfe",
      "spaces" : 3,
      "status" : "OK"
   },
   "77" : {
      "id" : "77",
      "name" : "Tau",
      "spaces" : 1,
      "status" : "OK"
   },
   "84" : {
      "spaces" : 5,
      "status" : "OK",
      "id" : "84",
      "name" : "Vile"
   },
   "45" : {
      "name" : "Kvaser",
      "id" : "45",
      "status" : "OK",
      "spaces" : 3
   },
   "69" : {
      "status" : "OK",
      "spaces" : 3,
      "name" : "Svane",
      "id" : "69"
   },
   "112" : {
      "spaces" : 1,
      "status" : "OK",
      "id" : "112",
      "name" : "Sildehaj"
   },
   "134" : {
      "status" : "OK",
      "spaces" : 2,
      "name" : "Snog",
      "id" : "134"
   }
};
    
    return boatid2boats[boat_id];
  };
  
  this.getBoatsWithCategoryId = function (category_id) {
    var category2boats = {
   "3" : [
      {
         "status" : "OK",
         "spaces" : 9,
         "name" : "Sleipner",
         "id" : "123"
      },
      {
         "spaces" : 9,
         "status" : "OK",
         "id" : "211",
         "name" : "Bifrost"
      },
      {
         "spaces" : 9,
         "status" : "OK",
         "id" : "56",
         "name" : "Ormen"
      }
   ],
   "4" : [
      {
         "name" : "Ask (kajak)",
         "id" : "129",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "name" : "Guldmakrel",
         "id" : "228",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Snapper",
         "id" : "184"
      },
      {
         "name" : "Låne Kajak 3",
         "id" : "179",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Regnbueørred (Privat)",
         "id" : "222"
      },
      {
         "name" : "Pighvar",
         "id" : "115",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "id" : "176",
         "name" : "Ising",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "112",
         "name" : "Sildehaj"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "109",
         "name" : "Sej"
      },
      {
         "id" : "156",
         "name" : "Tangloppe",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "100",
         "name" : "Sæl"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "149",
         "name" : "Delfin"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Ørred",
         "id" : "96"
      },
      {
         "name" : "Torsk",
         "id" : "90",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "234",
         "name" : "Stør"
      },
      {
         "id" : "131",
         "name" : "Fjæsing",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "name" : "Grindehval",
         "id" : "227",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Lånt Kajak 1",
         "id" : "181"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Låne Kajak 2",
         "id" : "178"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Blæksprutte",
         "id" : "221"
      },
      {
         "id" : "7",
         "name" : "Barracuda",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "id" : "114",
         "name" : "Haj",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "175",
         "name" : "Gedde"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Ulk",
         "id" : "111"
      },
      {
         "name" : "Karpe",
         "id" : "158",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "name" : "Muræne",
         "id" : "99",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Mulle",
         "id" : "147"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "144",
         "name" : "Ansjos"
      },
      {
         "id" : "139",
         "name" : "Papegøjefisk",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "89",
         "name" : "Sild"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "233",
         "name" : "Rødspætte"
      },
      {
         "id" : "27",
         "name" : "Blåmusling",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "id" : "130",
         "name" : "Urd",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Valborg",
         "id" : "186"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Krabbe",
         "id" : "226"
      },
      {
         "id" : "121",
         "name" : "Hummer",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "180",
         "name" : "Låne kajak 4"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Triton",
         "id" : "223"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Knurhane",
         "id" : "177"
      },
      {
         "name" : "Cryseis",
         "id" : "217",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "id" : "113",
         "name" : "Flyvefisk",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "id" : "174",
         "name" : "Blåhval",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "name" : "Hornfisk",
         "id" : "110",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "name" : "Nemo",
         "id" : "157",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "98",
         "name" : "SøElefant"
      },
      {
         "name" : "Skalle",
         "id" : "146",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Sardin",
         "id" : "143"
      },
      {
         "name" : "Forel 2",
         "id" : "91",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "136",
         "name" : "Stenbider"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "87",
         "name" : "Øresvin"
      },
      {
         "name" : "Søløve",
         "id" : "133",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "232",
         "name" : "Havkat"
      }
   ],
   "8" : [
      {
         "spaces" : 3,
         "status" : "OK",
         "id" : "69",
         "name" : "Svane"
      }
   ],
   "2" : [
      {
         "status" : "OK",
         "spaces" : 5,
         "name" : "Vile",
         "id" : "84"
      },
      {
         "id" : "9",
         "name" : "Bjarke",
         "spaces" : 5,
         "status" : "OK"
      },
      {
         "id" : "160",
         "name" : "Skadi",
         "spaces" : 5,
         "status" : "OK"
      },
      {
         "name" : "Munin",
         "id" : "51",
         "status" : "OK",
         "spaces" : 5
      },
      {
         "name" : "Njord",
         "id" : "145",
         "status" : "OK",
         "spaces" : 5
      },
      {
         "name" : "Embla",
         "id" : "142",
         "status" : "OK",
         "spaces" : 5
      },
      {
         "status" : "OK",
         "spaces" : 5,
         "name" : "Gudmund",
         "id" : "28"
      },
      {
         "spaces" : 5,
         "status" : "OK",
         "id" : "86",
         "name" : "Ydun"
      },
      {
         "id" : "82",
         "name" : "Vidar",
         "spaces" : 5,
         "status" : "OK"
      },
      {
         "name" : "Thor",
         "id" : "78",
         "status" : "OK",
         "spaces" : 5
      },
      {
         "spaces" : 5,
         "status" : "OK",
         "id" : "13",
         "name" : "Dan"
      },
      {
         "spaces" : 5,
         "status" : "OK",
         "id" : "119",
         "name" : "Nanna"
      },
      {
         "status" : "OK",
         "spaces" : 5,
         "name" : "Hugin",
         "id" : "240"
      },
      {
         "status" : "OK",
         "spaces" : 5,
         "name" : "4-åres lånt båd",
         "id" : "92"
      },
      {
         "name" : "Ask",
         "id" : "118",
         "status" : "OK",
         "spaces" : 5
      }
   ],
   "7" : [
      {
         "id" : "188",
         "name" : "Platon",
         "spaces" : 2,
         "status" : "OK"
      },
      {
         "spaces" : 2,
         "status" : "OK",
         "id" : "190",
         "name" : "Supermax Lady"
      },
      {
         "id" : "122",
         "name" : "Luzern",
         "spaces" : 2,
         "status" : "OK"
      },
      {
         "id" : "210",
         "name" : "Sct. Cathrine",
         "spaces" : 2,
         "status" : "OK"
      },
      {
         "spaces" : 2,
         "status" : "OK",
         "id" : "189",
         "name" : "Pallas Athene"
      },
      {
         "id" : "209",
         "name" : "Hera",
         "spaces" : 2,
         "status" : "OK"
      },
      {
         "status" : "OK",
         "spaces" : 2,
         "name" : "Minerva",
         "id" : "49"
      },
      {
         "spaces" : 2,
         "status" : "OK",
         "id" : "191",
         "name" : "Gerda"
      }
   ],
   "11" : [
      {
         "id" : "50",
         "name" : "Mjølner",
         "spaces" : 5,
         "status" : "OK"
      },
      {
         "name" : "Balder",
         "id" : "237",
         "status" : "OK",
         "spaces" : 4
      },
      {
         "id" : "197",
         "name" : "Aslaug",
         "spaces" : 4,
         "status" : "OK"
      }
   ],
   "1" : [
      {
         "id" : "22",
         "name" : "Freja",
         "spaces" : 3,
         "status" : "OK"
      },
      {
         "id" : "79",
         "name" : "Tjalfe",
         "spaces" : 3,
         "status" : "OK"
      },
      {
         "spaces" : 3,
         "status" : "OK",
         "id" : "225",
         "name" : "Ull"
      },
      {
         "name" : "Elektra",
         "id" : "17",
         "status" : "OK",
         "spaces" : 3
      },
      {
         "name" : "2-åres lånt båd",
         "id" : "120",
         "status" : "OK",
         "spaces" : 3
      },
      {
         "name" : "Absalon",
         "id" : "1",
         "status" : "OK",
         "spaces" : 3
      },
      {
         "status" : "OK",
         "spaces" : 3,
         "name" : "Frederik d. IX",
         "id" : "202"
      },
      {
         "spaces" : 3,
         "status" : "OK",
         "id" : "46",
         "name" : "Loke"
      },
      {
         "id" : "93",
         "name" : "Røskva",
         "spaces" : 3,
         "status" : "OK"
      },
      {
         "id" : "34",
         "name" : "Helge",
         "spaces" : 3,
         "status" : "OK"
      },
      {
         "name" : "Fafner",
         "id" : "19",
         "status" : "OK",
         "spaces" : 3
      },
      {
         "status" : "OK",
         "spaces" : 3,
         "name" : "Kristian",
         "id" : "201"
      },
      {
         "status" : "OK",
         "spaces" : 3,
         "name" : "Kvaser",
         "id" : "45"
      },
      {
         "status" : "OK",
         "spaces" : 3,
         "name" : "Hroar",
         "id" : "36"
      },
      {
         "status" : "OK",
         "spaces" : 3,
         "name" : "Møhring",
         "id" : "230"
      },
      {
         "spaces" : 3,
         "status" : "OK",
         "id" : "23",
         "name" : "Frigg"
      },
      {
         "spaces" : 3,
         "status" : "OK",
         "id" : "10",
         "name" : "Brage"
      },
      {
         "spaces" : 3,
         "status" : "OK",
         "id" : "70",
         "name" : "Svante"
      },
      {
         "status" : "OK",
         "spaces" : 3,
         "name" : "Sif",
         "id" : "65"
      },
      {
         "id" : "203",
         "name" : "Fenris",
         "spaces" : 3,
         "status" : "OK"
      },
      {
         "name" : "Hu",
         "id" : "94",
         "status" : "OK",
         "spaces" : 3
      },
      {
         "spaces" : 3,
         "status" : "OK",
         "id" : "35",
         "name" : "Hjalte"
      }
   ],
   "10" : [
      {
         "spaces" : 5,
         "status" : "OK",
         "id" : "55",
         "name" : "Olsen"
      },
      {
         "id" : "199",
         "name" : "Giallo",
         "spaces" : 5,
         "status" : "OK"
      },
      {
         "id" : "238",
         "name" : "Balder",
         "spaces" : 5,
         "status" : "OK"
      },
      {
         "status" : "OK",
         "spaces" : 5,
         "name" : "Odin",
         "id" : "54"
      },
      {
         "status" : "OK",
         "spaces" : 5,
         "name" : "Pollux",
         "id" : "198"
      },
      {
         "name" : "Hermes",
         "id" : "200",
         "status" : "OK",
         "spaces" : 4
      }
   ],
   "6" : [
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Grumme",
         "id" : "26"
      },
      {
         "name" : "Svip",
         "id" : "72",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "208",
         "name" : "Delta"
      },
      {
         "id" : "205",
         "name" : "Omega",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "id" : "41",
         "name" : "Jon",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "002",
         "id" : "196"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Nympha",
         "id" : "193"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "24",
         "name" : "Gamma"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "126",
         "name" : "1-åres lånt båd"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "66",
         "name" : "Sigma"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Psi",
         "id" : "59"
      },
      {
         "name" : "Teta",
         "id" : "207",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Totu",
         "id" : "204"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Alfa",
         "id" : "95"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "195",
         "name" : "Ro"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "192",
         "name" : "Iota"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Vips",
         "id" : "85"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Tuto",
         "id" : "81"
      },
      {
         "id" : "18",
         "name" : "Epsilon",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "77",
         "name" : "Tau"
      },
      {
         "name" : "Alf",
         "id" : "2",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "name" : "Afrodite",
         "id" : "206",
         "status" : "OK",
         "spaces" : 1
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Ksi",
         "id" : "44"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "194",
         "name" : "My"
      },
      {
         "spaces" : 1,
         "status" : "OK",
         "id" : "236",
         "name" : "Lambda"
      }
   ],
   "9" : [
      {
         "spaces" : 4,
         "status" : "OK",
         "id" : "33",
         "name" : "Heimdal"
      }
   ],
   "13" : [
      {
         "status" : "OK",
         "spaces" : 0,
         "name" : "Togo 8",
         "id" : "153"
      },
      {
         "spaces" : 0,
         "status" : "OK",
         "id" : "152",
         "name" : "Togo 7"
      }
   ],
   "5" : [
      {
         "spaces" : 2,
         "status" : "OK",
         "id" : "135",
         "name" : "Hugorm"
      },
      {
         "name" : "Ål",
         "id" : "231",
         "status" : "OK",
         "spaces" : 2
      },
      {
         "name" : "Langguster",
         "id" : "187",
         "status" : "OK",
         "spaces" : 2
      },
      {
         "id" : "224",
         "name" : "Laks",
         "spaces" : 2,
         "status" : "OK"
      },
      {
         "spaces" : 2,
         "status" : "OK",
         "id" : "107",
         "name" : "Rødfisk"
      },
      {
         "spaces" : 2,
         "status" : "OK",
         "id" : "134",
         "name" : "Snog"
      },
      {
         "spaces" : 2,
         "status" : "OK",
         "id" : "125",
         "name" : "Lånt 2-Kajak"
      },
      {
         "spaces" : 2,
         "status" : "OK",
         "id" : "103",
         "name" : "Rokke"
      },
      {
         "spaces" : 2,
         "status" : "OK",
         "id" : "32",
         "name" : "Havtaske"
      }
   ],
   "12" : [
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Ran",
         "id" : "60"
      },
      {
         "status" : "OK",
         "spaces" : 1,
         "name" : "Svava",
         "id" : "71"
      },
      {
         "id" : "52",
         "name" : "Ny",
         "spaces" : 1,
         "status" : "OK"
      },
      {
         "name" : "Pædagogsculler 1",
         "id" : "239",
         "status" : "OK",
         "spaces" : 1
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
