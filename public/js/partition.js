var w = 960,
    h = 600,
    r = Math.min(w, h) / 2,
	z,
	path, titre;

//merci à
//http://www.colorhexa.com 
//http://vis.stanford.edu/color-names/analyzer/ number-6
var colors = [];
colors['BL'] = ["#ccdef0", "#1e4164"];
colors['CA'] = ["#c9e1c4", "#2a4724"];
colors['CV'] = ["#f5d5ad", "#5e390b"];
colors['MR'] = ["#f1c4c5", "#621819"];
colors['QS'] = ["#dcc1df", "#412343"];

	  
var vis = d3.select("#chart").append("svg")
    .attr("width", w)
    .attr("height", h)
  .append("g")
  	.attr("id","gVis")
    .attr("transform", "translate(" + w / 2 + "," + h / 2 + ")");

var partition = d3.layout.partition() //fct qui permet de calculer les donnees par rapport à un tableau
    .sort(null)
    .size([2 * Math.PI, r * r])
    .value(function(d) { 
    	return d.nb; 
    	});

var arc = d3.svg.arc() //cr�er arc qui utilise mod�le de d3.
    .startAngle(function(d) { return d.x; }) //d.x---> r�sultat du json par la partition.nodes
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
		
		//construction des échelles de couleurs
		var arrA = json.children;
		z = [];
		for(var i=0; i < arrA.length; i++){
			z[arrA[i].ref]=d3.scale.log().domain([arrA[i].min, arrA[i].nb]).range(colors[arrA[i].ref]);
		}		
		
		path = vis.data([json]).selectAll("path") //cr�er visualisation, ajoute des donnees, cr�er pr chaque �lement path(ligne)
		      .data(partition.nodes) //ajoute nouvelle data (fonction qui permet de transformer les donnees en un mod�le de donnees partition qui n'est pas trier)
		    .enter().append("path") //pr chaque valeur de data, on ajoute un path.
		      .attr("display", function(d) { return d.depth ? null : "blue"; }) // ds () nb attribut, valeur attribut. d=id; d.depth=profndeur du noeud pr l'id. {}=if si d.depth=null sinn bleu
		      .attr("d", arc) //.attr=attribut. Dis � d3 comment il doit construire le code du path qu'il doit faire
		      .attr("fill-rule", "evenodd")
		      .style("stroke", "#fff") //sroke=contour de la forme
		      .style("fill", function(d) {
		    	  var i = (d.children ? d : d.parent);
		    	  if(i.ref){
		    		  var color = z[i.ref]; 
		    		  return color(i.nb);		    		  
		    	  }else 
		    		  return "white";
		    	  }) //si d.children renvoit trop alors d, sinon d.parent. fill ds sp�cification du svg = remplissage.
		      .each(stash);
			  

		  titre = path.append("svg:title")
		  .text(function(d) { 
		  	return d.name + " : " + d.value; 
		  });

		});
	
}


function getLegende(){
	
	var dataCircle = {"id":"0","name":"Alc\u00e9ane","children":[{"id":"1","name":"Antenne","nb":1,"children":[{"id":"2","name":"Groupe","nb":1,"children":[{"id":"3","name":"Batiment","nb":1,"children":[{"id":"4","name":"Type logement","nb":"1"}]}]}],"max":"1","min":"1"}],"nb":1};
	var dataTexte = [{"id":"0","name":"Alc\u00e9ane"},{"id":"1","name":"Antennes"},{"id":"2","name":"Groupes"},{"id":"3","name":"Bâtiments"},{"id":"4","name":"Type logement"}];
	var dataAntenne = [{"id":"1","ref":"BL","name":"Antenne - BL"},{"id":"2","ref":"CA","name":"Antenne - CA"},{"id":"3","ref":"CV","name":"Antenne - CV"},{"id":"4","ref":"MR","name":"Antenne - MR"},{"id":"5","ref":"QS","name":"Antenne - QS"}];
			
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

	var textLeg = visLeg.selectAll("g").data(dataTexte).enter().append("svg:g")
	    .attr("fill", "navy")
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