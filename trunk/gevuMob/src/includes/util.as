private function formatFunction(val:Number):String 
{ 
	return val.toString().replace(".",","); 
}   
private function parseFunction(val:String):Number 
{ 
	var stringVal:String = val.replace(",","."); 
	return Number(stringVal); 
}
private function dateToString(date:Date):String
{
	var dateString:String = date.fullYear+"-"+(date.month+1)+"-"+date.date+" "+date.hours+":"+date.minutes+":"+date.seconds;    
	return dateString;
}
private function stringToDate(dateString:String):Date {
	if ( dateString == null ) {
		return null;
	}
	
	var year:int = int(dateString.substr(0,4));
	var month:int = int(dateString.substr(5,2))-1;
	var day:int = int(dateString.substr(8,2));
	
	if ( year == 0 && month == 0 && day == 0 ) {
		return null;
	}
	
	if ( dateString.length == 10 ) {
		return new Date(year, month, day);
	}
	
	var hour:int = int(dateString.substr(11,2));
	var minute:int = int(dateString.substr(14,2));
	var second:int = int(dateString.substr(17,2));
	
	return new Date(year, month, day, hour, minute, second);
}			
