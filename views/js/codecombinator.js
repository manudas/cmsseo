$(window).load(function() {

    $(".subreference").on("keyup", function() {
        // alert("entra subreference");
        if (typeof subreference_timer == 'undefined') {
            subreference_timer = 0;
        }
        var subblockReferenceObject = $(this);
        subreference_timer = addDelay(subreference_timer, getSubreferencesSuggestions, subblockReferenceObject);

    });

    
    $(".blockreference").on("keyup", function() {
        // alert("entra reference");
        if (typeof reference_timer == 'undefined') {
            reference_timer = 0;
        }
        var blockReferenceObject = $(this);
        reference_timer = addDelay(reference_timer, getReferencesSuggestions, blockReferenceObject);

    });

});


function addDelay(timer, funcion, function_arguments, delay){

    if (typeof delay == 'undefined') {
        var delay = 200;
    }
    clearTimeout(timer);
    // console.log("se cancelo timer: "+timer);
    new_timer=setTimeout(function() {
        // console.log("timer es: "+timer);
        funcion(function_arguments);
    }, delay);
    return new_timer;
}

function getSubreferencesSuggestions(subblockReferenceObject){
        
        var copy_subreferenceObject = subblockReferenceObject;
        var id = subblockReferenceObject.attr('id');
        var id_arr = id.split('_');
        var language_id = id_arr[1]; // posicion 1 contiene identificador de lenguaje
        var related_block_reference = $("#blockreference_"+language_id);

        $.ajax({
            url: url_code_combinator,
            data: {"action" : "GetSubreferencesOptions", "ajax" : "true", 
                    "subreference" : subblockReferenceObject.val(),
                    "blockreference": related_block_reference.val(),
                    "language" : language_id}
            /*
            ,
            dataType: "json"
            */
        })
        .done(function( data, textStatus, jqXHR ) {
            if ( console && console.log ) {
                console.log( "La solicitud de getSubreferencesSuggestions se ha completado correctamente." );
            }
            var id = copy_subreferenceObject.attr('id');
            $("#datalist_"+id).html(data);
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            if ( console && console.log ) {
                console.log( "La solicitud de getSubreferencesSuggestions ha fallado: " +  textStatus);
            }
        });
}



function getReferencesSuggestions(blockReferenceObject){
        var copy_referenceObject = blockReferenceObject;

        var id = blockReferenceObject.attr('id');
        var id_arr = id.split('_');
        var language_id = id_arr[1]; // posicion 1 contiene identificador de lenguaje

        $.ajax({
            url: url_code_combinator,
            data: {"action" : "GetReferencesOptions", "ajax" : "true", 
                    "blockreference": blockReferenceObject.val(), "language": language_id}
            /*
            ,
            dataType: "json"
            */
        })
        .done(function( data, textStatus, jqXHR ) {
            if ( console && console.log ) {
                console.log( "La solicitud de getReferencesSuggestions se ha completado correctamente." );
            }
            var id = copy_referenceObject.attr('id');
            $("#datalist_"+id).html(data);
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            if ( console && console.log ) {
                console.log( "La solicitud de getReferencesSuggestions ha fallado: " +  textStatus);
            }
        });
}