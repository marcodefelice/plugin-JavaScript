// JavaScript Document
// palette colori in jquery
// scritto da Marco De Felice www.mglabfactory.it info@mglabfactory.it / news.mglabfactory.it
/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////               /////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////

// variabile where è il tag dove dovra essere inserito

function colorPicker(colorASCI) {
	// creiamo array per la variabile dei colori in codice asci
	var initialite = 1;
	var lengthArray = colorASCI.length; 
	while(initialite < lengthArray) {
		$("#colorPlick").append('<div style="margin-left:3px; margin-top:3px;height:20px; width:20px; float:right; border:thin solid #000; background-color:'+colorASCI[initialite]+';" onClick="colorGet('+ initialite +')" ></div>');	
		initialite++;	
	};
	
};

