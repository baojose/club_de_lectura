$(document).ready(function() {
  // cargar la imagen landing
  cargarDatosPrincipales('libro1');

  console.log("jquery funcionando");
  // TODO borrar el contenido de todas las variables json. Sino el anterior valor sobreescribe un valor vacio
  
  // Detectar cual libro pequeño ha sido clicado, cambiar el contenido de slider_content
  $(".change_libros_peq img").click(function() {
    // fade out slider_content
    $(".slider_content").fadeOut();    
    
    // detectar cual id ha sido clicado
    var id = $(this).attr('id');
    cargarDatosPrincipales(id);

    // fade in slider_content
    $(".slider_content").fadeIn();    
    
    console.log("dentro de Libro pequeño clicado has clickado ID=["+id+"]");
  });
});

// function cargarDatosPrincipales
// carga los datos de slider_content correspondientes al id recibido leidos del json 
function cargarDatosPrincipales(id){
  console.log("cargarDatosPrincipales libro=["+id+"]");
  $('#path_img').html('<img src="'+libros[id].path_img+'">');
  $('#titulo').html(libros[id].titulo);
  $('#descripcion').html(libros[id].descripcion);
  $('#link_ebook').html('<a href="'+libros[id].link_ebook+'">link_ebook</a>');
  $('#link_comprar').html('<a href="'+libros[id].link_comprar+'">link_comprar</a>');

}

