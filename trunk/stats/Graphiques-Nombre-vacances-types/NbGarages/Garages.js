var w = 960,
    h = 500,
    x = d3.scale.linear().range([0, w]),
    y = d3.scale.linear().range([0, h - 40]);

// An SVG element with a bottom-right origin.
var svg = d3.select("#chart").append("svg")
    .attr("width", w)
    .attr("height", h)
    .style("padding-right", "30px")
  .append("g")
    .attr("transform", "translate(" + x(1) + "," + (h - 20) + ")scale(-1,-1)");

// A sliding container to hold the bars.
var body = svg.append("g")
    .attr("transform", "translate(0,0)");

// A container to hold the y-axis rules.
var rules = svg.append("g");

// A label for the current year.
var title = svg.append("text")
    .attr("class", "title")
    .attr("dy", ".71em")
    .attr("transform", "translate(" + x(1) + "," + y(1) + ")scale(-1,-1)")
    .text(2000);

d3.csv("Garages.csv", function(data) {

  // Convert strings to numbers.
  data.forEach(function(d) {
    d.nb = +d.nb;
    d.lib = +d.lib;
    d.type = +d.type;
  });

  // Compute the extent of the data set in age and years.
  var type0 = 0,
      type1 = d3.max(data, function(d) { return d.type; }),
      lib0 = d3.min(data, function(d) { return d.lib; }),
      lib1 = d3.max(data, function(d) { return d.lib; }),
      lib = lib1;

  // Update the scale domains.
  x.domain([0, type1 + 5]);
  y.domain([0, d3.max(data, function(d) { return d.nb; })]);

  // Add rules to show the population values.
  rules = rules.selectAll(".rule")
      .data(y.ticks(10))
    .enter().append("g")
      .attr("class", "rule")
      .attr("transform", function(d) { return "translate(0," + y(d) + ")"; });

  rules.append("line")
      .attr("x2", w);

  rules.append("text")
      .attr("x", 6)
      .attr("dy", ".35em")
      .attr("transform", "rotate(180)")
      .text(function(d) { return Math.round(d / 1e6) + "M"; });

  // Add labeled rects for each birthyear.
  var lib = body.selectAll("g")
      .data(d3.range(lib0 - type1, lib1 + 5, 5))
    .enter().append("g")
      .attr("transform", function(d) { return "translate(" + x(year1 - d) + ",0)"; });

  lib.selectAll("rect")
      .data(d3.range(2))
    .enter().append("rect")
      .attr("x", 1)
      .attr("width", x(5) - 2)
      .attr("height", 1e-6);

  lib.append("text")
      .attr("y", -6)
      .attr("x", -x(5) / 2)
      .attr("transform", "rotate(180)")
      .attr("text-anchor", "middle")
      .style("fill", "#fff")
      .text(String);

  // Add labels to show the age.
  svg.append("g").selectAll("text")
      .data(d3.range(0, type1 + 5, 5))
    .enter().append("text")
      .attr("text-anchor", "middle")
      .attr("transform", function(d) { return "translate(" + (x(d) + x(5) / 2) + ",-4)scale(-1,-1)"; })
      .attr("dy", ".71em")
      .text(String);

  // Nest by year then birthyear.
  data = d3.nest()
      .key(function(d) { return d.lib; })
      .key(function(d) { return d.lib - d.type; })
      .rollup(function(v) { return v.map(function(d) { return d.nb; }); })
      .map(data);

  // Allow the arrow keys to change the displayed year.
  d3.select(window).on("keydown", function() {
    switch (d3.event.keyCode) {
      case 37: lib = Math.max(lib0, lib - 10); break;
      case 39: lib = Math.min(lib1, lib + 10); break;
    }
    redraw();
  });

  redraw();

  function redraw() {
    if (!(lib in data)) return;
    title.text(lib);

    body.transition()
        .duration(750)
        .attr("transform", function(d) { return "translate(" + x(lib - lib1) + ",0)"; });

    years.selectAll("rect")
        .data(function(d) { return data[lib][d] || [0, 0]; })
      .transition()
        .duration(750)
        .attr("height", y);
  }
});
