// coverter da pixel in cm
function converterToPixel(x,y, valuex, valuey) {
	// recuperiamo z da un input e w
	
	
	var w = $("#originHeight").val();
	var z = $("#orininWidht").val();
	
	z = parseFloat(z); // covertiamo la stringa in numeri
	w = parseFloat(w); // covertiamo la stringa in numeri
	var pixelWidht = 290 / w;  
	var pixelHeight = 240 / z; 
	
	var cm = 0.0264583333333334; // = 1px
	
	if (x.length == 5) {
	x = x.substring(0,3);
	} else if (x.length == 4) {
	x = x.substring(0,2);
	}
	x = parseFloat(x); // covertiamo la stringa in numeri
	
	
	
	if (y.length == 5) {
	y = y.substring(0,3);
	} else if (y.length == 4) {
	y = y.substring(0,2);
	}
	y = parseFloat(y); // covertiamo la stringa in numeri
	
	var pixelx = x / cm;
	var pixely = y / cm;
	// altezza	
	var cmx = x / pixelWidht; 
	// larghezza
	var cmy = y / pixelHeight; 
	
	cmx = cmx;
	cmy = cmy;
	
	var maxHeight = 320 / y;
	maxHeight = maxHeight * valuex;
	
	var maxWidht = 320 / x;
	maxWidht = maxWidht * valuey;
	
	// calcoliamo teorema di pitagora
	var cmxx = cmx * cmx;
	var cmyy = cmy * cmy;
	var radice = cmxx + cmyy;
	var radice = Math.sqrt(radice);
	
	
	var myArray = new Array(2);
	myArray['x'] = cmx;
	myArray['y'] = cmy;
	myArray['z'] = radice;
	
	myArray['maxHeight'] = maxHeight;
	myArray['maxWidht'] = maxWidht
	
	return myArray;
}