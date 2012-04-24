var w = 960,
    h = 700,
    r = Math.min(w, h) / 2,
    color = d3.scale.category20c();

	  
	  
var vis = d3.select("#chart").append("svg")
    .attr("width", w)
    .attr("height", h)
  .append("g")
    .attr("transform", "translate(" + w / 2 + "," + h / 2 + ")");

var partition = d3.layout.partition() //fct qui permet de calculer les donnees par rapport � un tableau
    .sort(null)
    .size([2 * Math.PI, r * r])
    .value(function(d) { return 1; });

var arc = d3.svg.arc() //cr�er arc qui utilise mod�le de d3.
    .startAngle(function(d) { return d.x; }) //d.x---> r�sultat du json par la partition.nodes
    .endAngle(function(d) { return d.x + d.dx; })
    .innerRadius(function(d) { return Math.sqrt(d.y); })
    .outerRadius(function(d) { return Math.sqrt(d.y + d.dy); });

d3.json("../TypesNbLogements-antennes/donnees.json", function(json) {
  var path = vis.data([json]).selectAll("path") //cr�er visualisation, ajoute des donnees, cr�er pr chaque �lement path(ligne)
      .data(partition.nodes) //ajoute nouvelle data (fonction qui permet de transformer les donnees en un mod�le de donnees partition qui n'est pas trier)
    .enter().append("path") //pr chaque valeur de data, on ajoute un path.
      .attr("display", function(d) { return d.depth ? null : "blue"; }) // ds () nb attribut, valeur attribut. d=id; d.depth=profndeur du noeud pr l'id. {}=if si d.depth=null sinn bleu
      .attr("d", arc) //.attr=attribut. Dis � d3 comment il doit construire le code du path qu'il doit faire
      .attr("fill-rule", "evenodd")
      .style("stroke", "#fff") //sroke=contour de la forme
      .style("fill", function(d) { return color((d.children ? d : d.parent).name); }) //si d.children renvoit trop alors d, sinon d.parent. fill ds sp�cification du svg = remplissage.
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
    	return d.name + " : " + d.size; 
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

    d3.select("#size").classed("active", false);
    d3.select("#count").classed("active", true);
  });
});

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