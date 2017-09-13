$(function () {
    $('#removeOrganization').click(function () {
        var organizationId = $('#organization').val();
        $.get( "/organizations/" + organizationId + "/remove", function( data ) {
            $( ".result" ).html( data );
            window.location = "/organizations";
        });
    });
    $('.js-datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });
});