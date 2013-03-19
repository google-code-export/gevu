var text, click;
//légende
var colors = []; //On créer une variable colors qui est un tableau
colors['UN'] = ["#A2FF00", "#00FF22"]; // La variable colors prend la valeur UN avec la couleur verte
colors['DEUX'] = ["#EEFF00", "#FCEB00"]; // La variable colors prend la valeur DEUX avec la couleur jaune
colors['TROIS'] = ["#FCD200", "#FFB300"];// La variable colors prend la valeur TROIS avec la couleur orange
colors['QUATRE'] = ["#F14C40", "#FF0000"];// La variable colors prend la valeur QUATRE avec la couleur rouge

	var dataTexte = [{"id":"0","name":"Logements jeunes"},{"id":"1","name":"Logements moins jeunes"},{"id":"2","name":"Logements anciens"},{"id":"3","name":"Logements très anciens"}];
	var dataCarre = [1,33,66,100];
	var dataCouleur = [{"id":"1","ref":"UN","name":"Logements jeunes","value":dataCarre},{"id":"2","ref":"DEUX","name":"Logements moins jeunes","value":dataCarre},{"id":"3","ref":"TROIS","name":"Logements anciens","value":dataCarre},{"id":"4","ref":"QUATRE","name":"Logements très anciens","value":dataCarre}];
	  
function getTypeLog(typeLog){

		z = []; //On créer un tableau z
	for(var i=0; i < dataCouleur.length; i++){ //Pour chaque valeur allant de i=0 à la totalité des valeurs de dataCouleur, on prend la couleur de la référence sité précédemment
		z[dataCouleur[i].ref]=d3.scale.log().domain([1, 100]).range(colors[dataCouleur[i].ref]);
	}	

	var vis = d3.select("#map_canvas").append("svg")
		.attr("width", 800)
		.attr("height", 600)
		.append("g")
		.attr("id","gVis");
		//.attr("transform", "translate(" + w / 2 + "," + h / 2 + ")");
	
	d3.json("../donnees_tables/gevulieux.json", function(json) {
		//suprime l'ancien graphique
		if(path)path.remove();
		if(text)text.remove();
				
	var	path = vis.selectAll("path")
		      .attr("fill-rule", "evenodd")
		      .style("stroke", "#fff") //sroke=contour de la forme
		      .on("click", click)
		      .attr("id", function(d, i) { 
		    	  return "path-" + i; 
		    	  })
		      .style("fill", function(d) {
		    	  var i = (d.children ? d : d.parent);
		    	  if(i.valeur){
		    		  var color = z[i.valeur]; 
		    		  return color(i.valeur);		    		  
		    	  }else 
		    		  return "white";
		    	  }) //si d.children renvoit trop alors d, sinon d.parent. fill ds sp�cification du svg = remplissage.
		      ;
		    	//  .each(stash);
			  

		/*var titre = path.append("svg:title")
		  .text(function(d) { 
		  	return d.lat + d.lng + " : " + d.value; 
		  });
		  
	//	var text = vis.selectAll("g").data(nodes);
		var text = vis.selectAll("g").data(Logements);
	
		/*var textEnter = text.enter().append("svg:g")
	    .attr("fill", "navy")
	    .append("text")
	    	.attr("class", "txtLeg")
	    	.attr("font-size", "10")
	    	.style("fill", "white")
	    	.append("textPath")
	         	.attr("xlink:href", function(d, i) { 
				  	return "#" + "path-" + i; 
	         	})
			  	.text(function(d) { 
				  	return d.valeur; 
			  	});*/
	
	
			function click(d) {
			    path.transition()
			      .duration(duration);
			    //  .attrTween("d", arcTween(d));
			  }
	});
}  
	  
var wL = 1200, hL = 1200; //On créer un div legendecarto qui prend comme hauteur et largeur 1200.
		
function getLegende(){ //On créer une fonction GetLegende

	var visLeg = d3.select("#legendeCarto").append("svg") //On créer une variable visLeg dans laquel on ajoute un svg à legendeCarto. A ce svg on lui met la hauteur et la largeur, un encadré.
	.attr("width", wL)
	.attr("height", hL)
	.attr("viewBox", "0 0 2000 1500")
	.attr("preserveAspectRatio", "xMidYMid meet")
	.append("g") //On créer une balise g qui prend comme id:gleg avec un cadre de 1200 * 1200
		.attr("id","gLeg")
		.attr("transform", "translate(" + wL + "," + hL + ")");
	

	z = []; //On créer un tableau z
	for(var i=0; i < dataCouleur.length; i++){ //Pour chaque valeur allant de i=0 à la totalité des valeurs de dataCouleur, on prend la couleur de la référence sité précédemment
		z[dataCouleur[i].ref]=d3.scale.log().domain([1, 100]).range(colors[dataCouleur[i].ref]);
	}		
	
	var antenneLeg = visLeg.selectAll("text").data(dataCouleur).enter() //On créer une variable antenneLeg dans laquelle on prend la couleur de chaque id
	.append("text")
	   .attr("transform", "translate(0,-300)")
       .attr("class", "txtLeg") //On ajoute une balise g qui s'appelle txtLeg 
       .attr("font-size", "30") //où la taille est de 30
       .attr("font-weight", "bold")//où l'écriture est bold
       .attr("fill", function(d) { 
    	   return colors[d.ref][1];  //Pour chaque ref, on retourne sa couleur
    	   })
       .attr("x", "320") 
       .attr("y", function(d) { 
    	   return 100*d.id; 
	   })
       .text(function(d) { 
    	   return d.name; //On écrit le name de chaque ref
	   });


	var wh = 40, ec=10; //On créer une variable wh qui prend comme valeur 40. wh est la largeur du carré. On créer ensuite une variable ec qui prend 10. C'est l'écart entre les carrés
	var carreLeg = visLeg.selectAll(".carrLeg").data(dataCouleur).enter() //On créer une variable carreLeg qui dans le svg visLeg ajoute un carreLeg qui prend les variable couleurs 
		.append("g") //On lui ajoute une balise g qui prend comme class: carrLeg
			.attr("class","carrLeg")
			.attr("transform", function(d,i) { 
				return "translate(0,"+100*i+")"; 
			   })
			.attr("id", function(d) { 
		    	   return d.ref; 
			   })
			.selectAll("rect").data(dataCarre).enter() //On ajoute une variable rect dans les balises g où l'on prend pour chaque carré wh
			.append("rect")	
		       .attr("width", wh)
		       .attr("height", wh+30)
		       .attr("fill", function(d) {
		    	   var ref = d3.select(this);
		    	   ref = ref[0][0].parentNode.id;
		    	   return z[ref](d);  //Pour chaque rectangle, on retourne la ref et sa couleur
		    	   })
		       .attr("y", "60")
		       .attr("x", function(d, i) { 
		    	   return (wh+ec)*i; 
			   })
			  .attr("transform", "translate(320,-250)"); //et on la place à 320 ; -250
	
	var textLeg = visLeg.selectAll(".textLeg").data(dataTexte).enter().append("svg:g") //POur chaque textLeg, on lui met une variable textLeg auquel onlui ajoute une balise text qui prend comme valeur name de dataTexte.
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
	
	