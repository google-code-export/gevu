// shim layer with setTimeout fallback
window.requestAnimFrame = window.requestAnimationFrame       ||
                          window.webkitRequestAnimationFrame ||
                          window.mozRequestAnimationFrame    ||
                          window.oRequestAnimationFrame      ||
                          window.msRequestAnimationFrame     ||
                          function( callback ){
                            window.setTimeout(callback, 1000 / 60);
                          };

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

d3.select("#dark-theme")
    .on("click", function() {
      d3.select("body").attr("class", "dark");
    });

d3.select("#light-theme")
    .on("click", function() {
      d3.select("body").attr("class", null);
    });

var width = document.body.offsetWidth,
    height = document.body.offsetHeight;

var m = [60, 0, 10, 0],
    w = width - m[1] - m[3],
    h = 290 - m[0] - m[2];

var xscale = d3.scale.ordinal().rangePoints([0, w], 1),
    yscale = {};

var line = d3.svg.line(),
    axis = d3.svg.axis().orient("left"),
    foreground,
    dimensions,                           
    n_dimensions,
    brush_count = 0;

var colors = {
  "Antenne BL": [28,100,52],
  "Antenne CA": [214,55,79],
  "Antenne CV": [185,56,73],
  "Antenne MR": [30,100,73],
  "Antenne QS": [359,69,49]
};

d3.select("#chart")
    .style("width", (w + m[1] + m[3]) + "px")
    .style("height", (h + m[0] + m[2]) + "px")

d3.selectAll("canvas")
    .attr("width", w)
    .attr("height", h)
    .style("padding", m.join("px ") + "px");

foreground = document.getElementById('foreground').getContext('2d');

foreground.strokeStyle = "rgba(0,100,160,0.1)";
foreground.lineWidth = 1.3;    // avoid weird subpixel effects

foreground.fillText("Loading...",w/2,h/2);

var svg = d3.select("svg")
    .attr("width", w + m[1] + m[3])
    .attr("height", h + m[0] + m[2])
  .append("svg:g")
    .attr("transform", "translate(" + m[3] + "," + m[0] + ")");

d3.csv("../data/gevu_stats.csv", function(data) {

  // Convert quantitative scales to floats
  data = data.map(function(d) {
    for (var k in d) {
      if (k != "name" && k != "group" && k != "id")
        d[k] = parseFloat(d[k]) || 0;
    };
    return d;
  });

  // Extract the list of dimensions and create a scale for each.
  xscale.domain(dimensions = d3.keys(data[0]).filter(function(d) {
    return d != "name" && d != "group" && d != "id" &&(yscale[d] = d3.scale.linear()
        .domain(d3.extent(data, function(p) { return +p[d]; }))
        .range([h, 0]));
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
      .attr("transform", "rotate(-19)")
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
    var actives = dimensions.filter(function(p) { return !yscale[p].brush.empty(); }),
        extents = actives.map(function(p) { return yscale[p].brush.extent(); });

    // Get lines within extents
    var selected = [];
    data.map(function(d) {
      return actives.every(function(p, i) {
        return extents[i][0] <= d[p] && d[p] <= extents[i][1];
      }) ? selected.push(d) : null;
    });

    // Render selected lines
    paths(selected, foreground, brush_count);
  }

  function paths(data, ctx, count) {
    var n = data.length,
        i = 0,
        opacity = d3.min([2/Math.pow(n,0.37),1]);
    d3.select("#selected-count").text(n);
    d3.select("#opacity").text((""+opacity).slice(0,6));

    data = _.shuffle(data);

    // data table
    var foodText = "";
    data.slice(0,10).forEach(function(d) {
      foodText += "<span style='background:" + color(d.group,0.85) + "'></span>" + d.name + "<br/>";
    });
    d3.select("#food-list").html(foodText);

    ctx.clearRect(0,0,w+1,h+1);
    function render() {
      var max = d3.min([i+12, n]);
      data.slice(i,max).forEach(function(d) {
        path(d, foreground, color(d.group,opacity));
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


function path(d, ctx, color) {
  if (color) ctx.strokeStyle = color;
  ctx.beginPath();
  var x0 = -xscale(1)/2,
      y0 = (yscale[dimensions[n_dimensions-1]](d[dimensions[n_dimensions-1]]) +
            yscale[dimensions[0]](d[dimensions[0]]))/2;          // begin cycle
  ctx.moveTo(x0,y0);
  dimensions.map(function(p,i) {
    var x = xscale(p),
        y = yscale[p](d[p]);
    var cp1x = x - 0.85*(x-x0);
    var cp1y = y0;
    var cp2x = x - 0.15*(x-x0);
    var cp2y = y;
    ctx.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, x, y);
    x0 = x;
    y0 = y;
  });
  var y = yscale[dimensions[0]](d[dimensions[0]]);
  var dx = w-x0;
  var cp1x = (w+dx) - 0.85*((w+dx)-x0);
  var cp1y = y0;
  var cp2x = (w+dx) - 0.15*((w+dx)-x0);
  var cp2y = y;
  ctx.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, (w+dx), y); // end cycle

  ctx.stroke();
};

function color(d,a) {
  var c = colors[d];
  return ["hsla(",c[0],",",c[1],"%,",c[2],"%,",a,")"].join("");
};