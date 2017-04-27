$(window).load(function() {
    $("#combination_file").on("change", function() {
        $("#uploadFile").val($(this).val());
    });
})