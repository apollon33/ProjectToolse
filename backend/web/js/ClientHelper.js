$(document).ready(function () {
    $('button.client').on('click', function () {
        var client_id = $('#assing-modal-client').find('select').val();
        var local = location.href.split('/');
        var process_id;
        $.each(local, function (index, value) {
            if ($.isNumeric(value)) {
                process_id = value;
            }
        })
        $.ajax({
            url: '/buildingprocess/deal/client',
            type: 'post',
            data: {client_id: client_id, process_id: process_id},
            success: function (succcess) {
                location.reload();
            }
        });
    })
})