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