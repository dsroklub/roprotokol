'use strict';
angular.module('eventApp').controller(
  'clubCtrl',
  ['$scope','$routeParams','$route','DatabaseService','LoginService','$filter','ngDialog','orderByFilter','$log','$location','$anchorScroll','$timeout',
   clubCtrl
  ]);

function clubCtrl ($scope, $routeParams,$route,DatabaseService, LoginService, $filter, ngDialog, orderBy, $log, $location,$anchorScroll,$timeout) {
  var dberr=function(err) {
    $log.debug("db init err "+err);
    if (err['error']) {
      alert('DB fejl '+err['error']);
    }
  }

  DatabaseService.getDataNow('event/stats/sumgraphforce',null, function (res) {
    $scope.graph=res.data;    
    var width = 2200, height = 1400;
    var dcola = cola.d3adaptor(d3).linkDistance(20).symmetricDiffLinkLengths(5). size([width, height]);
    var svg = d3.select("#clubgraph").append("svg")
        .attr("width", width)
        .attr("height", height);
    $scope.triptypecolors={"blandet":"blue","Inriggerkaproning":"red","Motionsroning":"green","Puls og program":"brown","Langtur":"pink","Racerkanin":"grey","Costalroning" : "cyan3","Instruktion":"bisque","Kajakmotionsroning":"darkorange"};
    dcola
      .nodes($scope.graph.nodes)
      .links($scope.graph.links)
      .linkDistance(90)
      .avoidOverlaps(true)
      .start(8,10,20);        
    var link = svg.selectAll(".link")
        .data($scope.graph.links)
        .enter().append("line")
    //    .style("line-color", function (d) { return d.color; })
        .style("stroke", function (d) {
          if ($scope.triptypecolors[d.tooltip]) {
            return($scope.triptypecolors[d.tooltip]);
          }
          return $scope.triptypecolors["blandet"];
        }
              )
        .attr("class", "link");
    
    var label = svg.selectAll(".label")
        .data($scope.graph.nodes)
        .enter().append("text")
        .attr("class", "label")
        .style("stroke",function (d) { return d.me?"red":"brown"; })
        .style('opacity',0.7).style("stroke-width",0.5)
        .text(function (d) { return d.name; })
        .call(dcola.drag)
    ;
    var me=[];
    for (var i=0; i<$scope.graph.nodes.length;i++) {
      if ($scope.graph.nodes[i].me) {
        me.push($scope.graph.nodes[i]);
        break;
      }
    }
    var node=svg.selectAll(".node").data(me).enter().append("rect").attr("width", 80).attr("height", 30)  .attr("class", "node").style("fill","gold").call(dcola.drag) ;

    dcola.on("tick", function () {
      link.attr("x1", function (d) { return d.source.x; })
        .attr("y1", function (d) { return d.source.y; })
        .attr("x2", function (d) { return d.target.x; })
        .attr("y2", function (d) { return d.target.y; });
      
      label.attr("x", function (d) { return d.x; })
        .attr("y", function (d) {
          var h = this.getBBox().height;
          return d.y + h/4;
        });
      
      node.attr("x", function (d) {
        return d.x - this.getBBox().width/2;
      }) .attr("y", function (d) {return d.y- this.getBBox().height/2;});
    });        
  },dberr)
}
