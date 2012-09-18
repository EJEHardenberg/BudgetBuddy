
function dropClicked(id){
	//This isn't very pretty but thats ok it works!
	if( document.getElementById(id).style.display == ""){
		document.getElementById(id).style.display = 'none';
	}
	if(document.getElementById(id).style.display == 'none' ){
		document.getElementById(id).style.display = 'inline';
	}else{
		document.getElementById(id).style.display = 'none';
	}
}