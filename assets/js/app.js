require('jquery');
require('../css/app.css');
require('../../node_modules/bootstrap/dist/js/bootstrap.min.js');
require('../../node_modules/bootstrap/dist/css/bootstrap.min.css');


$('.commentButton').click(function () {
    $(this).prev().slideDown()
});