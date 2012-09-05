function DropDownColorChange(){
	var dropText = document.getElementById("DropDownH5");
	if(dropText.style.color == "white"){
		dropText.style.color = "black";
		dropText.style.textDecoration = "underline";
	}else{
		dropText.style.color = "white";
		dropText.style.textDecoration = "none";
	}
}

function dropClicked(){
	//This isn't very pretty but thats ok it works!
	if( document.getElementById("DropDownUL").style.display == ""){
		 document.getElementById("DropDownUL").style.display = 'none';
	}
	if(document.getElementById("DropDownUL").style.display == 'none' ){
		document.getElementById("DropDownH5").innerHTML = "Close Add Form";
		document.getElementById("DropDownUL").style.display = 'inline';
	}else{
		document.getElementById("DropDownH5").innerHTML = "+ Add Transaction";
		document.getElementById("DropDownUL").style.display = 'none';
	}

}