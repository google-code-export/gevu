function LatLng(degLat, degLong) {
     this.lat = LatLng.llToRad(degLat);
     this.lon = LatLng.llToRad(degLong);
   }    
   
   LatLng.llToRad = function(brng) {
     if (!isNaN(brng)) return brng * Math.PI / 180; 
   
     brng = brng.replace(/[\s]*$/,'');               
     var dir = brng.slice(-1).toUpperCase();         
     if (!/[NSEW]/.test(dir)) return NaN;           
     brng = brng.slice(0,-1);                       
     var dms = brng.split(/[\s:,°º′\'″\"]/);         
     switch (dms.length) {                           
       case 3:                                       
         var deg = dms[0]/1 + dms[1]/60 + dms[2]/3600; break;
       case 2:                                       
         var deg = dms[0]/1 + dms[1]/60; break;
       case 1:                                       
         if (/[NS]/.test(dir)) brng = '0' + brng;   
         var deg = brng.slice(0,3)/1 + brng.slice(3,5)/60 + brng.slice(5)/3600; break;
       default: return NaN;
     }
     if (/[WS]/.test(dir)) deg = -deg;               
     return deg * Math.PI / 180;                     
   }
   
   LatLng.distHaversine = function(p1, p2) {
     var R = 6371;
     var dLat  = p2.lat - p1.lat;
     var dLng = p2.lng - p1.lng;

     var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
             Math.cos(p1.lat) * Math.cos(p2.lat) * Math.sin(dLng/2) * Math.sin(dLng/2);
     var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
     var d = R * c;
   
     return d;
   }