// tamaño de libros shelf
var shelf_tamanyo_widht         = 84;
var shelf_tamanyo_height        = 102;
var shelf_tamanyo_widht_grande  = 95;
var shelf_tamanyo_height_grande = 116;


var libro_landing = 'libro1'; // se podria hacer una funcion q lo buscase del json.js
var actual_libro = libro_landing;
var listaOrdenadaLibros = new Array("libro1","libro2","libro3");
// nombre ultimo libro
var listaOrdenadaLibros_ultimo = listaOrdenadaLibros[listaOrdenadaLibros.length-1];
// nombre primer libro
var listaOrdenadaLibros_primero = listaOrdenadaLibros[0];



$(document).ready(function() {
  // console.log("jquery funcionando");

  //////////////
  // Tooltip  //
  //////////////
  // antes estaba en index
  // $(document).ready(function() {
    $('.tooltip, .tooltip_gris').tooltipster({
        theme: 'tooltipster-punk'
    });
  // });


  // cargar los datos nada más llegar
  cargarDatosPrincipales(libro_landing);

  // TODO borrar el contenido de todas las variables json. Sino el anterior valor sobreescribe un valor vacio

  ///////////////////////////////////////////////////////////////////////////////////////////
  // Detectar cual libro del shelf ha sido clicado, cambiar el contenido de slider_content //
  ///////////////////////////////////////////////////////////////////////////////////////////
  $("#shelf .tooltip img, .tooltip_gris img").click(function() {

    // detectar cual id ha sido clicado
    var id = $(this).attr('id');

    if (actual_libro != id){  // clicando algo ya clicado
      //console.log("repitiendo");
      decrementarImagen(actual_libro);
      actual_libro = id;

      cargarDatosPrincipales(id);


    }
    console.log("El Libro del shelf clicado tiene ID=["+id+"]");
  });

  //////////////////////////////////////////////////////////
  // Las flechas para controlarsiguiente y anterior libro //
  //////////////////////////////////////////////////////////
  $(".ws_prev").click(function() {
    // console.log("boton ws_prev clicado");

    cargarDatosPrincipales(dummy_anteriorLibro());
  });
  $(".ws_next").click(function() {
    // console.log("boton ws_next clicado");

    cargarDatosPrincipales(dummy_siguinteLibro());
  });

  //////////////////////////
  // CONTROL DEL LIGHTBOX //
  //////////////////////////
  // $().click

  /////////////////////////
  // CONTROL  del FOOTER //
  /////////////////////////
  //
  //
   $("#boxes_container, .fbTimelineSection mtm pageAppTab").mouseover(function (){
    $( "#banderas_facebook" ).slideUp( "slow" );
    $( "#banderas_twitter" ).slideUp( "slow" );
    $( "#banderas_spotify" ).slideUp( "slow" );
    

   });

  //
  //
  $("#footer_icono_facebook").mouseover(function() {
    // $( "#log" ).append( "<div>Handler for .mouseover() called.</div>" );
    $( "#banderas_twitter" ).hide();
    $( "#banderas_spotify" ).hide();
    // console.log("a funcionado");
    $( "#banderas_facebook" ).slideDown( "slow" );
  });
  $("#footer_icono_twitter").mouseover(function() {
    // $( "#log" ).append( "<div>Handler for .mouseover() called.</div>" );
    $( "#banderas_facebook" ).hide();
    $( "#banderas_spotify" ).hide();
    // console.log("a funcionado");
    $( "#banderas_twitter" ).slideDown( "slow" );
  });
  $("#footer_icono_spotify").mouseover(function() {
    // $( "#log" ).append( "<div>Handler for .mouseover() called.</div>" );
    $( "#banderas_facebook" ).hide();
    $( "#banderas_twitter" ).hide();
    // console.log("a funcionado");
    $( "#banderas_spotify" ).slideDown( "slow" );
  });


  ////////////////////////////////////
  // DEBUG dentro de document.ready //
  ////////////////////////////////////

    // Para ver por consola valor del actual_libro al clicar cualquier parte d la pagina
    $("html").click(function(){
      console.log("debug 1d12d2r actual_libro  ["+actual_libro+"]");

    });
    ///////////////////////////// PARSEAR json.js

    //  alert(libros);
    // var json_obj = jQuery.parseJSON(libros);
    // console.log(json_obj);
    // var lista_libros = dummy_listaOrdenadaLibros(json_obj);
     // $.getJSON

    // jQuery.each(libros, function() {
    //   console.log("My id is " + this + ".");
    //   return (this != "four"); // will stop running to skip "five"
    // });
});

///////////////////////////////////////
// FUNCIONES fuera de document.ready //
///////////////////////////////////////

// function cargarDatosPrincipales
// carga los datos de slider_content correspondientes al id recibido leidos del json
// tb lanza fadeIn y fadeOut
function cargarDatosPrincipales(id){
  console.log("cargarDatosPrincipales id recibida=["+id+"]");

      // fade out slider_content       // cambiar el contenido (y fade in) dentro de la funcion fadeOut consigue que se haga todo en orden correcto
      // EFECTOS PARA de  shelf
      decrementarImagen(actual_libro);
      incrementarImagen(id);

      // CAJA LIGTHBOX (caja naranja)
      hacerInvisbleImgPequeniaLibgBox();
      dummyCambioLigthboxDelLibro1AlLibro2(actual_libro,id);

      $('.slider_content').fadeOut("slow", function(){
        $('#path_img').html('<a href="http://www.alfaguara.com/uploads/ficheros/libro/primeras-paginas/201401/primeras-paginas-cortazar-z.pdf" target="_blank"><img src="'+libros[id].path_img+'"></a>');
        $('#titulo').html(libros[id].titulo);
        $('#autor').html(libros[id].autor);
    $('#slogan').html(libros[id].slogan);
        $('#descripcion').html(libros[id].descripcion);
        //$('#link_ebook').html('<a href="'+libros[id].link_ebook+'"><img src="img/button.jpg" width="107" height="37" alt="Cómpralo para eBook" /></a>');//
        $('#link_comprar').html('<a class="button" href="'+libros[id].link_comprar+'">entrar en el club de lectura</a>');
        
        ///////////
        // CAJAS //
        ///////////     
        // Posiciones en json => X en numero cajas[X]
          $('#caja_roja').html(libros[id].cajas[0].caja_roja.texto+'<a class="button_box1" href="'+libros[id].cajas[0].caja_roja.link_ver_mas+'" style="">Ver más</a>');
          $('#caja_azul').html('<img style="margin:0 auto 0 auto" src='+libros[id].cajas[1].caja_azul.link_comprar+' width="87" height="110" /><br/><a class="button_box3" target="_blank" href="'+libros[id].cajas[1].caja_azul.link_ver_mas+'" style="">Comprar</a>');
          $('#caja_lima').html(libros[id].cajas[2].caja_lima.texto+'<br/><br/><iframe src="https://embed.spotify.com/?uri=spotify:album:6zFmbqx7a4MJEOPKOnOtGV" width="220" height="200" frameborder="0" allowtransparency="true"></iframe>');
          $('#caja_rosa').html(libros[id].cajas[3].caja_rosa.texto+'<a class="button_box2" href="'+libros[id].cajas[3].caja_rosa.link_ver_mas+'" style="">Ver más</a>');
          $('#caja_verde').html(libros[id].cajas[4].caja_verde.texto+'<a target="_blank" class="button_box2" href="'+libros[id].cajas[4].caja_verde.link_ver_mas+'" style="">Ver más</a>');
          // $('#caja_azul').html(libros[id].cajas[5].caja_azul.texto);   // seria la del lightbox
          
          // $('#caja_naranja').html('<div class="catalog"><ul><li><a href="img/materiales/thumb1_1024.jpg" class="html5lightbox" title="Prueba Thumbnail" data-group="set1"><img src="img/materiales/thumb1.jpg" width="48" height="49" /></a></li><li><a href="img/materiales/thumb2_1024.jpg" class="html5lightbox" title="Prueba Thumbnail" data-group="set1"><img src="img/materiales/thumb2.jpg" width="48" height="49" /></a></li><li><a href="img/materiales/thumb1_1024.jpg" class="html5lightbox" title="Prueba Thumbnail" data-group="set1"><img src="img/materiales/thumb1.jpg" width="48" height="49" /></a></li><li><a href="img/materiales/thumb1_1024.jpg" class="html5lightbox" title="Prueba Thumbnail" data-group="set1"><img src="img/materiales/thumb1.jpg" width="48" height="49" /></a></li><li><a href="img/materiales/thumb1_1024.jpg" class="html5lightbox" title="Prueba Thumbnail" data-group="set1"><img src="img/materiales/thumb1.jpg" width="48" height="49" /></a></li><li><a href="img/materiales/thumb1_1024.jpg" class="html5lightbox" title="Prueba Thumbnail" data-group="set1"><img src="img/materiales/thumb1.jpg" width="48" height="49" /></a></li><li><a href="img/materiales/thumb1_1024.jpg" class="html5lightbox" title="Prueba Thumbnail" data-group="set1"><img src="img/materiales/thumb1.jpg" width="48" height="49" /></a></li><li><a href="img/materiales/thumb1_1024.jpg" class="html5lightbox" title="Prueba Thumbnail" data-group="set1"><img src="img/materiales/thumb1.jpg" width="48" height="49" /></a></li></ul></div>');

 
      // fade in slider_content
      $(".slider_content").fadeIn();
          // var div = $("<div id='foo'>test2</div>").hide();
          // $(this).replaceWith(div);
          // $('#foo').fadeIn("slow");
  });
}

//incrementar tamaño de id
function incrementarImagen(id){
  $("#"+id).animate({ height: shelf_tamanyo_height_grande, width: shelf_tamanyo_widht_grande }, 300);
  // $("#"+id).css( "z-index" , "100" );
}
//tamaño de imagen a tamaño original
function decrementarImagen(id){
  $("#"+id).animate({ height: shelf_tamanyo_height, width: shelf_tamanyo_widht }, 150);
  // $("#"+id).css( "z-index" , "100" );
}


// Necesarias para las flechas del slider_content
function dummy_siguinteLibro(){
  nuevo_libro="";
  // es ultimo libro?
  if(actual_libro == listaOrdenadaLibros_ultimo){
    nuevo_libro = listaOrdenadaLibros_primero;
  }
  else{ // no lo es
    actual_index = listaOrdenadaLibros.indexOf(actual_libro);
    // sumar 1 a posicion actual
    actual_index = actual_index +1;
    nuevo_libro = listaOrdenadaLibros[actual_index];

  }
  decrementarImagen(actual_libro);
  actual_libro = nuevo_libro;
  return (nuevo_libro);
}

function dummy_anteriorLibro(){
  nuevo_libro="";
  // es ultimo libro de la listaOrdenadaLibros?
  if(actual_libro == listaOrdenadaLibros_primero){
    nuevo_libro = listaOrdenadaLibros_ultimo;
  }
  else{ // no lo es
    actual_index = listaOrdenadaLibros.indexOf(actual_libro);
    // restar 1 a posicion actual
    actual_index = actual_index -1;
    nuevo_libro = listaOrdenadaLibros[actual_index];

  }
  decrementarImagen(actual_libro);
  actual_libro = nuevo_libro;
  return (nuevo_libro);
}
// listaOrdenadaLibros
// queria que se cargase dinamicamente, en vez de eso usa listaOrdenadaLibros
function dummy_listaOrdenadaLibros(){  // no se usa
  for (var i in libros)
      {
        console.log("i"+i);
      }
}
// console.log(libros);

alert (libro_compra);
