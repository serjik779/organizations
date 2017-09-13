$(function () {
    $('#removeOrganization').click(function () {
        var organizationId = $('#organization').val();
        $.get( "/organizations/" + organizationId + "/remove", function( data ) {
            $( ".result" ).html( data );
            window.location = "/organizations";
        });
    });
});