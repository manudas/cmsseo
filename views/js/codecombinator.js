$(window).load(function() {
    
    $(".blockreference").on("change", function() {
        // alert("entra reference");
        if (typeof reference_timer == 'undefined') {
            reference_timer = 0;
        }
        var blockReferenceObject = $(this);
        reference_timer = addDelay(reference_timer, getSubreferences, blockReferenceObject);

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

function getSubreferences(subblockReferenceObject){
        /*
        var copy_subreferenceObject = subblockReferenceObject;
        var id = subblockReferenceObject.attr('id');
*/
        var related_block_reference = $("#blockreference");

        $.ajax({
            url: url_code_combinator,

            data: {"action" : "GetSubreferencesOptions", "ajax" : "true", 
                    // "subreference" : subblockReferenceObject.val(),
                    "blockreference": related_block_reference.val()}        
            /*
            ,
            dataType: "json"
            */
        })
        .done(function( data, textStatus, jqXHR ) {
            if ( console && console.log ) {
                console.log( "La solicitud de getSubreferencesSuggestions se ha completado correctamente." );
            }
            $("#subreference").html(data);
            /*
            var id = copy_subreferenceObject.attr('id');
            $("#datalist_"+id).html(data);
            */
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            if ( console && console.log ) {
                console.log( "La solicitud de getSubreferencesSuggestions ha fallado: " +  textStatus);
            }
        });
}
