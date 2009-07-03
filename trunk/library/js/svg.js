var cstHeight = 150;
var cstWidth = 150;

function EnlargeSvg(id,large,haut){
	try {
		var svg = document.getElementById(id);
		//pour vérifier si on grandit ou par defaut
		if(svg.getAttribute("width")==large){
			svg.setAttribute("width", cstWidth);	
			svg.setAttribute("height", cstHeight);	
		}else{
			svg.setAttribute("width", large);
			svg.setAttribute("height", haut);	
		}
  } catch(ex2){ alert("svg:EnlargeSvg:"+ex2); } 
	
}