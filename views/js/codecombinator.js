$(window).load(function() {

    $(".blockreference").on("keyup", function() {
        var blockReferenceObject = $(this);
        $.ajax({
            url: url_code_combinator,
            data: {"action" : "GetSubreferencesOptions", "ajax" : "true", "blockreference" : $(this).val()}
            /*
            ,
            dataType: "json"
            */
        })
        .done(function( data, textStatus, jqXHR ) {
            if ( console && console.log ) {
                console.log( "La solicitud se ha completado correctamente." );
            }
            var id = blockReferenceObject.attr('id');
            // var id = $(this).attr('id');
            var id_arr = id.split('_');
            var language = id_arr[1]; // posicion 1 contiene identificador de lenguaje
            $("#subreference_"+languaje).html(data);
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            if ( console && console.log ) {
                console.log( "La solicitud ha fallado: " +  textStatus);
            }
        });

    })

});