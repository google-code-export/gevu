// shim layer with setTimeout fallback
window.requestAnimFrame = window.requestAnimationFrame       ||
                          window.webkitRequestAnimationFrame ||
                          window.mozRequestAnimationFrame    ||
                          window.oRequestAnimationFrame      ||
                          window.msRequestAnimationFrame     ||
                          function( callback ){
                            window.setTimeout(callback, 2000 / 60);
                          };


var width = 1200,
    height = document.body.offsetHeight;

var m = [160, 50, 10, 20],
    w = width - m[1] - m[3],
    h = 350 - m[0] - m[2];

var xscale = d3.scale.ordinal().rangePoints([0, w], 1),
    yscale = {};

var data,
    line = d3.svg.line(),
    axis = d3.svg.axis().orient("left"),
    foreground,
    scatter,
    dimensions,
    n_dimensions,
    brush_count = 0;

d3.select("#chart")
    .style("width", (w + m[1] + m[3]) + "px")
    .style("height", (h + m[0] + m[2]) + "px")

d3.selectAll("#chart canvas")
    .attr("width", w)
    .attr("height", h)
    .style("padding", m.join("px ") + "px");

foreground = document.getElementById('foreground').getContext('2d');
scatterplot = document.getElementById('scatterplot').getContext('2d');

foreground.strokeStyle = "rgba(0,100,160,0.1)";
foreground.lineWidth = 1.3;    // avoid weird subpixel effects
//foreground.globalCompositeOperation = "lighter";
//scatterplot.globalCompositeOperation = "lighter";

foreground.fillText("Loading...",w/2,h/2);

var svg = d3.select("svg")
    .attr("width", w + m[1] + m[3])
    .attr("height", h + m[0] + m[2])
  .append("svg:g")
    .attr("transform", "translate(" + m[3] + "," + m[0] + ")");

d3.csv("../data/caracteristiques_logements.csv", function(raw) {
  //console.log(data);

  // Convert quantitative scales to floats
  data = raw.map(function(d) {
    for (var k in d) {
      d[k] = parseFloat(d[k]) || 0;
    };
    return d;
  });


  // Extract the list of dimensions and create a scale for each.
  xscale.domain(dimensions = d3.keys(data[0]).filter(function(d) {
    var scale = (yscale[d] = d3.scale.linear()
		/*.ticks(5)
		.tickSize(scale * w) */
        .domain(d3.extent(data, function(p) { return +p[d]; })));
		
    if (d == "Vmag") return scale.range([0, h]);
    return scale.range([h, 0]);
  }));



  n_dimensions = dimensions.length;

  // Render full foreground
  paths(data, foreground, brush_count);

  // Add a group element for each dimension.
  var g = svg.selectAll(".dimension")
      .data(dimensions)
    .enter().append("svg:g")
      .attr("class", "dimension")
      .attr("transform", function(d) { return "translate(" + xscale(d) + ")"; });

  // Add an axis and title.
  g.append("svg:g")
      .attr("class", "axis")
      .each(function(d) { d3.select(this).call(axis.scale(yscale[d])); })
    .append("svg:text")
      .attr("text-anchor", "left")
      .attr("y", -8)
      .attr("x", -4)
      .attr("transform", "rotate(-30)")
      .attr("class", "label")
      .text(String);

  // Add and store a brush for each axis.
  g.append("svg:g")
      .attr("class", "brush")
      .each(function(d) { d3.select(this).call(yscale[d].brush = d3.svg.brush().y(yscale[d]).on("brush", brush)); })
    .selectAll("rect")
      .attr("x", -16)
      .attr("width", 32)
      .attr("rx", 3)
      .attr("ry", 3);

  // Handles a brush event, toggling the display of foreground lines.
  function brush() {
    brush_count++;

    // Render selected lines
    paths(actives(), foreground, brush_count);
  }

  function actives() {
    var actives = dimensions.filter(function(p) { return !yscale[p].brush.empty(); }),
        extents = actives.map(function(p) { return yscale[p].brush.extent(); });

    // Get lines within extents
    var selected = [];
    data.map(function(d) {
      return actives.every(function(p, i) {
        return extents[i][0] <= d[p] && d[p] <= extents[i][1];
      }) ? selected.push(d) : null;
    });

    return selected;
  };

  // Remove all but selected from the dataset
  d3.select("#keep-data")
    .on("click", function() {
      data = actives();
      // Extract the list of dimensions and create a scale for each.
      xscale.domain(dimensions = d3.keys(data[0]).filter(function(d) {
        var scale = (yscale[d] = d3.scale.linear()
            .domain(d3.extent(data, function(p) { return +p[d]; })));
        if (d == "Vmag") return scale.range([0, h]);
        return scale.range([h, 0]);
      }));

      // update brushes
      d3.selectAll(".brush")
        .each(function(d) { d3.select(this).call(yscale[d].brush = d3.svg.brush().y(yscale[d]).on("brush", brush)); })
      brush_count++;

      // update axes
      d3.selectAll(".axis")
        .each(function(d,i) {
          var self = this;
          setTimeout(function() {
            d3.select(self)
              .transition()
              .duration(700)
              .call(axis.scale(yscale[d]));
          }, 50*i);
        });

      // Render selected data
      paths(data, foreground, brush_count);
    });

  function paths(data, ctx, count) {
    var n = data.length,
        i = 0,
        opacity = d3.min([2/Math.pow(n,0.55),1]);
    d3.select("#selected-count").text(n);
    d3.select("#opacity").text((""+opacity).slice(0,6));

    data = _.shuffle(data);

    var foodText = "";
    data.slice(0,10).forEach(function(d) {
      foodText += "<span style='background:" + color(d,0.85) + "'></span>Nr " + d.Nr+ "<br/>";
    });
    d3.select("#food-list").html(foodText);

    ctx.clearRect(0,0,w+1,h+1);
    scatterplot.clearRect(0,0,290,290);

    function render() {
      var max = d3.min([i+12, n]);
      data.slice(i,max).forEach(function(d) {
        path(d, foreground, color(d,opacity));
        //circle(d, scatterplot, color(d,0.2));
      });
      i = max;
      d3.select("#rendered-count").text(i);
    };
      // render all lines until finished or a new brush event
    (function animloop(){
      if (i >= n || count < brush_count) return;
      requestAnimFrame(animloop);
      render();
    })();
  };
});

d3.select("#hide-ticks")
    .on("click", function() {
      d3.selectAll(".axis g").style("display", "none");
      d3.selectAll(".axis path").style("display", "none");
    });

d3.select("#show-ticks")
    .on("click", function() {
      d3.selectAll(".axis g").style("display", "block");
      d3.selectAll(".axis path").style("display", "block");
    });

function path(d, ctx, color) {
  if (color) ctx.strokeStyle = color;
  ctx.beginPath();
  ctx.moveTo(xscale(0),yscale[dimensions[0]](d[dimensions[0]]));
  dimensions.map(function(p,i) {
    ctx.lineTo(xscale(p),yscale[p](d[p]));
  });
  ctx.stroke();
};

function circle(d,ctx,color) {
  if (color) ctx.fillStyle = color;
  ctx.beginPath();
  ctx.arc(290-yscale['B-V'](d['B-V']),yscale['Vmag'](d['Vmag']),1,0,2*Math.PI)
  ctx.closePath();
  ctx.fill();
};

function color(d,a) {
  return "hsla(190,60%,70%," + a + ")"};