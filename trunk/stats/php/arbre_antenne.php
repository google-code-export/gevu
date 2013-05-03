
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <title>Données des antennes</title>
    <script type="text/javascript" src="../js/d3.v2.js"></script>
    <link type="text/css" rel="stylesheet" href="../css/button.css"/>
    <style type="text/css">

.node circle {
  cursor: pointer;
  fill: #fff;
  stroke: steelblue;
  stroke-width: 1.5px;
}

.node text {
  font: 10px sans-serif;
}

path.link {
  fill: none;
  stroke: #ccc;
  stroke-width: 1.5px;
}

    </style>
  </head>
  <body>
    <div>
	<button id='BL' class='first'>ANTENNE BLÉVILLE</button><button id='CA' class='first'>ANTENNE CAUCRIAUVILLE</button><button id='CV' class='first'>ANTENNE CENTRE VILLE</button><button id='MR' class='first'>ANTENNE MARE ROUGE</button><button id='QS' class='first'>ANTENNE QS</button>
    </div>
    <div id="chart"></div>
    <script type="text/javascript">


var m = [20, 120, 20, 120],
    w = 1280 - m[1] - m[3],
    h = 600 - m[0] - m[2],
    i = 0,
    duration = 500,
    root;

var tree = d3.layout.tree()
    .size([h, w]);

var diagonal = d3.svg.diagonal()
    .projection(function(d) { return [d.y, d.x]; });

var vis = d3.select("#chart").append("svg")
    .attr("width", w + m[1] + m[3])
    .attr("height", h + m[0] + m[2])
  .append("g")
    .attr("transform", "translate(" + m[3] + "," + m[0] + ")");

function getAntenne(Antenne){	
d3.json("../data_antenne/donnees.json", function(json) {
  root = json;
  root.x0 = h / 2;
  root.y0 = 0;


  function collapse(d) {
    if (d.children) {
      d._children = d.children;
      d._children.forEach(collapse);
      d.children = null;
    }
  }

  root.children.forEach(collapse);
  update(root);
});

function update(source) {

  // Calcule la disposition de l'arbre
  var nodes = tree.nodes(root).reverse();

  // Fixe la profondeur de l'arbre
  nodes.forEach(function(d) { d.y = d.depth * 180; });

  // Met à jour les noeuds
  var node = vis.selectAll("g.node")
      .data(nodes, function(d) { return d.id || (d.id = ++i); });

  // Entre les nouveaux noeuds et la position antérieure du parent
  var nodeEnter = node.enter().append("g")
      .attr("class", "node")
      .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
      .on("click", click);

  nodeEnter.append("circle")
      .attr("r", 1e-6)
      .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });

  nodeEnter.append("text")
      .attr("x", function(d) { return d.children || d._children ? -10 : 10; })
      .attr("dy", ".35em")
      .attr("text-anchor", function(d) { return d.children || d._children ? "end" : "start"; })
      .text(function(d) { return d.name; })
      .style("fill-opacity", 1e-6);

  // Noeud de transition à la nouvelle position
  var nodeUpdate = node.transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

  nodeUpdate.select("circle")
      .attr("r", 4.5)
      .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });

  nodeUpdate.select("text")
      .style("fill-opacity", 1);

  // Transition sortante des noeuds à la nouvelle position du parent
  var nodeExit = node.exit().transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
      .remove();

  nodeExit.select("circle")
      .attr("r", 1e-6);

  nodeExit.select("text")
      .style("fill-opacity", 1e-6);

  // Met à jour les liens
  var link = vis.selectAll("path.link")
      .data(tree.links(nodes), function(d) { return d.target.id; });

  // Entre les nouveaux liens à la position antérieure du parent
  link.enter().insert("path", "g")
      .attr("class", "link")
      .attr("d", function(d) {
        var o = {x: source.x0, y: source.y0};
        return diagonal({source: o, target: o});
      });

  // Lien de transition vers les nouvelles positions
  link.transition()
      .duration(duration)
      .attr("d", diagonal);

  // Transition sortant des noeuds à la nouvelle position du parent
  link.exit().transition()
      .duration(duration)
      .attr("d", function(d) {
        var o = {x: source.x, y: source.y};
        return diagonal({source: o, target: o});
      })
      .remove();

  // Anciennes positions de transition
  nodes.forEach(function(d) {
    d.x0 = d.x;
    d.y0 = d.y;
  });
}

// Bascule les enfants selon le clic
function click(d) {
  if (d.children) {
    d._children = d.children;
    d.children = null;
  } else {
    d.children = d._children;
    d._children = null;
  }
  update(d);
}
}

				d3.select("#BL").on("click", function() {
		   			getAntenne("BL");
				d3.select("#BL").classed("active", true);
					d3.select("#CA").classed("active", false);
					d3.select("#CV").classed("active", false);
					d3.select("#MR").classed("active", false);
					d3.select("#QS").classed("active", false);
					 });
		    	
				d3.select("#CA").on("click", function() {
		   			getAntenne("CA");
				d3.select("#CA").classed("active", true);
					d3.select("#BL").classed("active", false);
					d3.select("#CV").classed("active", false);
					d3.select("#MR").classed("active", false);
					d3.select("#QS").classed("active", false);
					 });
		    	
				d3.select("#CV").on("click", function() {
		   			getAntenne("CV");
				d3.select("#CV").classed("active", true);
					d3.select("#CA").classed("active", false);
					d3.select("#BL").classed("active", false);
					d3.select("#MR").classed("active", false);
					d3.select("#QS").classed("active", false);
					 });

				d3.select("#MR").on("click", function() {
		   			getAntenne("MR");
				d3.select("#MR").classed("active", true);
					d3.select("#CA").classed("active", false);
					d3.select("#CV").classed("active", false);
					d3.select("#BL").classed("active", false);
					d3.select("#QS").classed("active", false);
					 });

				d3.select("#QS").on("click", function() {
		   			getAntenne("QS");
				d3.select("#QS").classed("active", true);
					d3.select("#CA").classed("active", false);
					d3.select("#CV").classed("active", false);
					d3.select("#MR").classed("active", false);
					d3.select("#BL").classed("active", false);
					 });


    </script>
  </body>
</html>
