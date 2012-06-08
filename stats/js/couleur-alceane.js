var colors = [];
colors['BL'] = ["#ccdef0", "#1e4164"];
colors['CA'] = ["#c9e1c4", "#2a4724"];
colors['CV'] = ["#f5d5ad", "#5e390b"];
colors['MR'] = ["#f1c4c5", "#621819"];
colors['QS'] = ["#dcc1df", "#412343"];

var wL = 300, hL = 300;
var visLeg = d3.select("#legende").append("svg")
	.attr("width", wL)
	.attr("height", hL)
	.attr("viewBox", "0 0 800 900")
	.attr("preserveAspectRatio", "xMidYMid meet")
	.append("g")
		.attr("id","gLeg")
		.attr("transform", "translate(" + wL + "," + hL + ")");
		
function getLegende(){

	var dataAntenne = [{"id":"1","ref":"BL","name":"Antenne - BL","value":dataCarre},{"id":"2","ref":"CA","name":"Antenne - CA","value":dataCarre},{"id":"3","ref":"CV","name":"Antenne - CV","value":dataCarre},{"id":"4","ref":"MR","name":"Antenne - MR","value":dataCarre},{"id":"5","ref":"QS","name":"Antenne - QS","value":dataCarre}];
	z = [];
	for(var i=0; i < dataAntenne.length; i++){
		z[dataAntenne[i].ref]=d3.scale.log().domain([1, 100]).range(colors[dataAntenne[i].ref]);
	}		
	
	var antenneLeg = visLeg.selectAll("text").data(dataAntenne).enter()
	.append("text")
	   .attr("transform", "translate(0,-300)")
       .attr("class", "txtLeg")
       .attr("font-size", "30")
       .attr("font-weight", "bold")
       .attr("fill", function(d) { 
    	   return colors[d.ref][1]; 
    	   })
       .attr("x", "320")
       .attr("y", function(d) { 
    	   return 100*d.id; 
	   })
       .text(function(d) { 
    	   return d.name; 
	   });

	var wh = 30, ec=10; 
	var carreLeg = visLeg.selectAll(".carrLeg").data(dataAntenne).enter()
		.append("g")
			.attr("class","carrLeg")
			.attr("transform", function(d,i) { 
				return "translate(0,"+100*i+")"; 
			   })
			.attr("id", function(d) { 
		    	   return d.ref; 
			   })
			.selectAll("rect").data(dataCarre).enter()
			.append("rect")	
		       .attr("width", wh)
		       .attr("height", wh+30)
		       .attr("fill", function(d) {
		    	   var ref = d3.select(this);
		    	   ref = ref[0][0].parentNode.id;
		    	   return z[ref](d); 
		    	   })
		       .attr("y", "60")
		       .attr("x", function(d, i) { 
		    	   return (wh+ec)*i; 
			   })
			  .attr("transform", "translate(320,-250)");
	
	var pathLeg = visLeg.data([dataCircle]).selectAll("path") 
	      .data(partition.nodes) 
	    .enter().append("path") 
	      .attr("display", function(d) { return d.depth ? null : "blue"; }) 
	      .attr("d", arc)
	      .attr("id", function(d) { return d.id; })
	      .attr("fill-rule", "evenodd")
	      .style("stroke", "#fff") 
	      .style("fill", "#CCCCCC")
	      .each(stash)
	      ;

	var textLeg = visLeg.selectAll(".textLeg").data(dataTexte).enter().append("svg:g")
	    .attr("class", "textLeg")
	    .attr("transform", "rotate(164)")
	    .append("text")
	    	.attr("transform", "translate(0,-30)")
	    	.attr("class", "txtLeg")
	    	.attr("font-size", "30")
	    	.append("textPath")
	         	.attr("xlink:href", function(d) { 
				  	return "#" + d.id; 
	         	})
			  	.text(function(d) { 
				  	return d.name; 
			  	});	