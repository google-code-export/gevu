//légende
var colors = [];
colors['UN'] = ["#A2FF00", "#00FF22"];
colors['DEUX'] = ["#EEFF00", "#FCEB00"];
colors['TROIS'] = ["#FCD200", "#FFB300"];
colors['QUATRE'] = ["#F14C40", "#FF0000"];
	  
var wL = 1200, hL = 1200;
		
function getLegende(){

	var visLeg = d3.select("#legendeCarto").append("svg")
	.attr("width", wL)
	.attr("height", hL)
	.attr("viewBox", "0 0 2000 1500")
	.attr("preserveAspectRatio", "xMidYMid meet")
	.append("g")
		.attr("id","gLeg")
		.attr("transform", "translate(" + wL + "," + hL + ")");
	
	var dataTexte = [{"id":"0","name":"Logements jeunes"},{"id":"1","name":"Logements moins jeunes"},{"id":"2","name":"Logements anciens"},{"id":"3","name":"Logements très anciens"}];
	var dataCarre = [1,33,66,100];
	var dataCouleur = [{"id":"1","ref":"UN","name":"Logements jeunes","value":dataCarre},{"id":"2","ref":"DEUX","name":"Logements moins jeunes","value":dataCarre},{"id":"3","ref":"TROIS","name":"Logements anciens","value":dataCarre},{"id":"4","ref":"QUATRE","name":"Logements très anciens","value":dataCarre}];
	
	z = [];
	for(var i=0; i < dataCouleur.length; i++){
		z[dataCouleur[i].ref]=d3.scale.log().domain([1, 100]).range(colors[dataCouleur[i].ref]);
	}		
	
	var antenneLeg = visLeg.selectAll("text").data(dataCouleur).enter()
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


	var wh = 40, ec=10; 
	var carreLeg = visLeg.selectAll(".carrLeg").data(dataCouleur).enter()
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
}
	
	