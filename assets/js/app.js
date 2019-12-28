require('jquery');
require('../css/app.css');
require('../../node_modules/bootstrap/dist/js/bootstrap.min.js');
require('../../node_modules/bootstrap/dist/css/bootstrap.min.css');


$('.commentButton').click(function () {
    $(this).prev().slideDown()
});

$('.uploadImageForm').submit(function (e) {
    e.preventDefault();

    let userId = $('.userId').val(),
        data = new FormData(this);

    $.ajax({
        url: "/upload/image/"+userId,
        type: "POST",
        data:  {
            data: data
        },
        contentType: false,
        cache: false,
        processData:false,
        success: function(response)
        {
            alert(response);
            $('#newPost').append('<img src="response" class="img-fluid">')
        },
        error: function(e)
        {
            console.log('error');
        }
    });
});
