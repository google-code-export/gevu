var w = 800,
    h = 450,
    r = Math.min(w, h) / 2,
   color = d3.scale.category20b();
  
/* var colors = [];
colors['BL'] = ["#ccdef0", "#1e4164"];
colors['CA'] = ["#c9e1c4", "#2a4724"];
colors['CV'] = ["#f5d5ad", "#5e390b"];
colors['MR'] = ["#f1c4c5", "#621819"];
colors['QS'] = ["#dcc1df", "#412343"];	*/
	
var vis = d3.select("#chart").append("svg")
    .attr("width", w)
    .attr("height", h)
  .append("g")
    .attr("transform", "translate(" + w / 2 + "," + h / 2 + ")");

var partition = d3.layout.partition()
    .sort(null)
    .size([2 * Math.PI, r * r])
    .value(function(d) { return 1; });

var arc = d3.svg.arc()
    .startAngle(function(d) { return d.x; })
    .endAngle(function(d) { return d.x + d.dx; })
    .innerRadius(function(d) { return Math.sqrt(d.y); })
    .outerRadius(function(d) { return Math.sqrt(d.y + d.dy); });

d3.json(urlJson, function(json) {

		var arrA = json.children;
		z = [];
		for(var i=0; i < arrA.length; i++){
			if(arrA[i].ref!="")
				z[arrA[i].ref]=d3.scale.log().domain([arrA[i].min, arrA[i].nb]).range(colors[arrA[i].ref]);
		}		
  
	var path = vis.data([json]).selectAll("path")
      .data(partition.nodes)
    .enter().append("path")
      .attr("display", function(d) { return d.depth ? null : "blue"; })
      .attr("d", arc)
      .attr("fill-rule", "evenodd")
      .style("stroke", "#fff")
      .style("fill", function(d) {
   	  var c = "white";
    	  if(d.depth==1) c = d3.rgb(colors[d.ref][0]);
    	  if(d.depth==2) c = colors[d.parent.ref][0];
    	  if(d.depth==3) c = colors[d.parent.parent.ref][0];
    	  return c;  
    	  })
      .each(stash);
	  
  var titre = path.append("svg:title")
  .text(function(d) { 
  	return d.name + " : " + d.value; 
  });

  d3.select("#size").on("click", function() {
    path
        .data(partition.value(function(d) { return d.size; }))
      .transition()
        .duration(1500)
        .attrTween("d", arcTween);
		
		titre.text(function(d) { 
    	return d.name + " : " + d.value; 
    });

    d3.select("#size").classed("active", true);
    d3.select("#count").classed("active", false);
  });

  d3.select("#count").on("click", function() {
    path
        .data(partition.value(function(d) { return 1; }))
      .transition()
        .duration(1500)
        .attrTween("d", arcTween);
		
		titre.text(function(d) { 
    	return d.name + " : " + d.value; 
    });
	


    d3.select("#size").classed("active", false);
    d3.select("#count").classed("active", true);
  });
});

function stash(d) {
  d.x0 = d.x;
  d.dx0 = d.dx;
}

function arcTween(a) {
  var i = d3.interpolate({x: a.x0, dx: a.dx0}, a);
  return function(t) {
    var b = i(t);
    a.x0 = b.x;
    a.dx0 = b.dx;
    return arc(b);
  };
}