var w = 960,
    h = 600,
    r = Math.min(w, h) / 2,
	z,
	path, titre;

//merci Ã 
//http://www.colorhexa.com 
//http://vis.stanford.edu/color-names/analyzer/ number-6
var cBL = ["#ccdef0", "#1e4164"];
var cCA = ["#c9e1c4", "#2a4724"];
var cCV = ["#f5d5ad", "#5e390b"];
var cMR = ["#f1c4c5", "#621819"];
var cQS = ["#dcc1df", "#412343"];

	  
var vis = d3.select("#chart").append("svg")
    .attr("width", w)
    .attr("height", h)
  .append("g")
  	.attr("id","gVis")
    .attr("transform", "translate(" + w / 2 + "," + h / 2 + ")");

var partition = d3.layout.partition() //fct qui permet de calculer les donnees par rapport Ã  un tableau
    .sort(null)
    .size([2 * Math.PI, r * r])
    .value(function(d) { 
    	return d.nb; 
    	});

var arc = d3.svg.arc() //crï¿½er arc qui utilise modï¿½le de d3.
    .startAngle(function(d) { return d.x; }) //d.x---> rï¿½sultat du json par la partition.nodes
    .endAngle(function(d) { return d.x + d.dx; })
    .innerRadius(function(d) { return Math.sqrt(d.y); })
    .outerRadius(function(d) { return Math.sqrt(d.y + d.dy); });

var wL = 300, hL = 300;
var visLeg = d3.select("#legende").append("svg")
	.attr("width", wL)
	.attr("height", hL)
	.attr("viewBox", "0 0 800 800")
	.attr("preserveAspectRatio", "xMidYMid meet")
	.append("g")
		.attr("id","gLeg")
		.attr("transform", "translate(" + wL + "," + hL + ")");


function getTypeLog(typeLog){
	
d3.json("http://www.gevu.org/public/stat/antenne?type=ArbreTypeLog&typeLog="+typeLog, function(json) {
		//suprime l'ancien graphique
		if(path)path.remove();
		
		//construction des Ã©chelles de couleurs
		var arrA = json.children;
		z = [];
		for(var i=0; i < arrA.length; i++){
			if(arrA[i].ref=="BL")z["BL"]=d3.scale.log().domain([arrA[i].min, arrA[i].nb]).range(cBL);
			if(arrA[i].ref=="CA")z["CA"]=d3.scale.log().domain([arrA[i].min, arrA[i].nb]).range(cCA);
			if(arrA[i].ref=="CV")z["CV"]=d3.scale.linear().domain([arrA[i].min, arrA[i].nb]).range(cCV);
			if(arrA[i].ref=="MR")z["MR"]=d3.scale.log().domain([arrA[i].min, arrA[i].nb]).range(cMR);
			if(arrA[i].ref=="QS")z["QS"]=d3.scale.log().domain([arrA[i].min, arrA[i].nb]).range(cQS);
		}		
		
		path = vis.data([json]).selectAll("path") //crï¿½er visualisation, ajoute des donnees, crï¿½er pr chaque ï¿½lement path(ligne)
		      .data(partition.nodes) //ajoute nouvelle data (fonction qui permet de transformer les donnees en un modï¿½le de donnees partition qui n'est pas trier)
		    .enter().append("path") //pr chaque valeur de data, on ajoute un path.
		      .attr("display", function(d) { return d.depth ? null : "blue"; }) // ds () nb attribut, valeur attribut. d=id; d.depth=profndeur du noeud pr l'id. {}=if si d.depth=null sinn bleu
		      .attr("d", arc) //.attr=attribut. Dis ï¿½ d3 comment il doit construire le code du path qu'il doit faire
		      .attr("fill-rule", "evenodd")
		      .style("stroke", "#fff") //sroke=contour de la forme
		      .style("fill", function(d) {
		    	  var i = (d.children ? d : d.parent);
		    	  if(i.ref){
		    		  var color = z[i.ref]; 
		    		  return color(i.nb);		    		  
		    	  }else 
		    		  return "white";
		    	  }) //si d.children renvoit trop alors d, sinon d.parent. fill ds spï¿½cification du svg = remplissage.
		      .each(stash);
			  

		  titre = path.append("svg:title")
		  .text(function(d) { 
		  	return d.name + " : " + d.value; 
		  });

		});
	
}


function getLegende(){
	
	d3.json("../data/legende.js", function(json) {
		
			var dataCircle = {"id":"0","name":"Alc\u00e9ane","children":[{"id":"1","name":"Antenne","nb":1,"children":[{"id":"2","name":"Groupe","nb":1,"children":[{"id":"3","name":"Batiment","nb":1,"children":[{"id":"4","name":"Type logement","nb":"1"}]}]}],"max":"1","min":"1"}],"nb":5};

		
			//construction des Ã©chelles de couleurs
			var arrA = json.children;
			z = [];
			for(var i=0; i < arrA.length; i++){
				if(arrA[i].ref=="BL")z["BL"]=d3.scale.log().domain([arrA[i].min, arrA[i].nb]).range(cBL);
				if(arrA[i].ref=="CA")z["CA"]=d3.scale.log().domain([arrA[i].min, arrA[i].nb]).range(cCA);
				if(arrA[i].ref=="CV")z["CV"]=d3.scale.linear().domain([arrA[i].min, arrA[i].nb]).range(cCV);
				if(arrA[i].ref=="MR")z["MR"]=d3.scale.log().domain([arrA[i].min, arrA[i].nb]).range(cMR);
				if(arrA[i].ref=="QS")z["QS"]=d3.scale.log().domain([arrA[i].min, arrA[i].nb]).range(cQS);
				visLeg.append("text")
			       .attr("class", "txtLeg")
			       .attr("font-size", "20")
			       .attr("x", "300")
			       .attr("y", 100*i)
			       .text(arrA[i].name);
				
			}		
			
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

			
			visLeg.data([dataCircle]).selectAll("svg:g").append("svg:g")
		    .attr("fill", "navy")
		    .append("text")
		       .attr("class", "txtLeg")
		       .attr("font-size", "20")
		       .append("textPath")
		         .attr("xlink:href", function(d) { 
					  	return "#" + d.id; 
				  })
		         .text(function(d) { 
					  	return d.name + " : " + d.value; 
				  });
			
			  titre = pathLeg.append("svg:title")
			  .text(function(d) { 
			  	return d.name + " : " + d.value; 
			  });

			});
		
	}

// Stash the old values for transition.
function stash(d) {
  d.x0 = d.x;
  d.dx0 = d.dx;
}

// Interpolate the arcs in data space.
function arcTween(a) {
  var i = d3.interpolate({x: a.x0, dx: a.dx0}, a);
  return function(t) {
    var b = i(t);
    a.x0 = b.x;
    a.dx0 = b.dx;
    return arc(b);
  };
}