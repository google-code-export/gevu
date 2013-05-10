var colors = [], z = [];
colors['UN'] = ["#FB0000", "#007D00"];
//"#FB0000"=100;
//"#007D00"=0;

	var dataTexte = [{"id":"0","name":"Occupés - Vacants"}];
	var dataCarre = [1, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100];
	var dataCouleur = [{"id":"1","ref":"UN","name":"Occupés - Vacants","value":dataCarre}]

function getLegende(){

var wL = 500, hL = 500;
	var visLeg = d3.select("#degrade").append("svg")
	.attr("width", wL)
	.attr("height", hL)
	.attr("viewBox", "0 0 1860 1700")
	.attr("preserveAspectRatio", "xMidYMid meet")
	.append("g")
		.attr("id","gLeg")
		.attr("transform", "translate(" + wL + "," + hL + ")");
	

	for(var i=0; i < dataCouleur.length; i++){
		z[dataCouleur[i].ref]=d3.scale.linear().domain([1, 100]).range(colors[dataCouleur[i].ref]);
	}		
	
	var degradeAntenne = visLeg.selectAll("text").data(dataCouleur).enter()
	.append("text")
	   .attr("transform", "translate(0,-300)")
       .attr("class", "txtLeg")
       .attr("font-size", "130")
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


	var wh = 50, ec=0;
	var carreAntenne = visLeg.selectAll(".carrLeg").data(dataCouleur).enter() 
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
		       .attr("height", wh+90)
		       .attr("fill", function(d) {
		    	   var ref = d3.select(this);
		    	   ref = ref[0][0].parentNode.id;
		    	   var fctC = z[ref];
		    	   var c = fctC(d) 
		    	   return c;
		    	   })
		       .attr("y", "60")
		       .attr("x", function(d, i) { 
		    	   return (wh+ec)*i; 
			   })
			  .attr("transform", "translate(320,-250)");
	
	var textAntenne = visLeg.selectAll(".textLeg").data(dataTexte).enter().append("svg:g")
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
}