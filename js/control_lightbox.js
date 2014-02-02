

// Dos funciones complementarias, Input: nombre del id. Uno lo hace invisible otro le pone el css block
function InvisibleEsteId(id){
        //document.getElementById(id).style.display = "none";
        var obj = document.getElementById(id);
	InvisibleEsteObj(obj);
}
function VisibleEsteId(id){
	var obj = document.getElementById(id);
	VisibleEsteObj(obj);
//        document.getElementById(id).style.display = "block";
}


function InvisibleEsteObj(obj){
	obj.style.display= "none";
}

function VisibleEsteObj(obj){
	obj.style.display = "block";

}

function AlternarVisibilidadId(id){
	obj = document.getElementById(id);
	AlternarVisibilidadObj(obj);
}

function AlternarVisibilidadObj(obj){
        if (obj.style.display == "none"){ //Estaba Invisble, hay que hacerlo VISIBLE
                VisibleEsteObj(obj);
        }
        else{
                InvisibleEsteObj(obj);
        }
}
function dummyCambioLigthboxDelLibro1AlLibro2(actual_libro,nuevo_libro){
        console.log("XXXXXXXXXXXXX dummyCambioLigthboxDelLibro1AlLibro2 actual_libro=["+actual_libro+"] nuevo_libro["+nuevo_libro+"]");
        InvisibleEsteId("lb"+actual_libro);
        VisibleEsteId("lb"+nuevo_libro);
        // VisibleEsteId("lilibro2");
}

function hacerInvisbleImgPequeniaLibgBox(){
        // recorro el array de libros y hago invistble todos los id = ·lblibro*
        for (index = 0; index < listaOrdenadaLibros.length; ++index) {
                console.log("lb"+listaOrdenadaLibros[index]);
                InvisibleEsteId("lb"+listaOrdenadaLibros[index]);


        }
              

}