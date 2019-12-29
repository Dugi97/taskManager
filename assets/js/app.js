require('jquery');
require('../css/app.css');
require('../../node_modules/bootstrap/dist/js/bootstrap.min.js');
require('../../node_modules/bootstrap/dist/css/bootstrap.min.css');


$('.commentButton').click(function () {
    $(this).prev().slideDown()
});
// $('.images').change(function (e) {
//     e.preventDefault();
//     alert($(this).val());
//     $('.newPostArea').val('<p>Aaaa</p>');
// });
// $('.postClass').submit(function (e) {
//     e.preventDefault();
//
//     let userId = $('.userId').val(),
//         data = new FormData(this);
//
//     $.ajax({
//         url: "/post/new",
//         type: "GET",
//         data:  {
//             data: data,
//             userId: userId
//         },
//         contentType: false,
//         cache: false,
//         processData:false,
//         success: function(response)
//         {
//             $('#newPost').append('<img src="https://images.unsplash.com/photo-1503023345310-bd7c1de61c7d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&w=1000&q=80" class="img-fluid">')
//         },
//         error: function(e)
//         {
//             console.log('error');
//         }
//     });
// });

// Image slider
jQuery(document).ready(function ($) {

    $('#checkbox').change(function(){
        setInterval(function () {
            moveRight();
        }, 3000);
    });

    var slideCount = $('#slider ul li').length;
    var slideWidth = $('#slider ul li').width();
    var slideHeight = $('#slider ul li').height();
    var sliderUlWidth = slideCount * slideWidth;

    $('#slider').css({ width: slideWidth, height: slideHeight });

    $('#slider ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });

    $('#slider ul li:last-child').prependTo('#slider ul');

    function moveLeft() {
        $('#slider ul').animate({
            left: + slideWidth
        }, 200, function () {
            $('#slider ul li:last-child').prependTo('#slider ul');
            $('#slider ul').css('left', '');
        });
    };

    function moveRight() {
        $('#slider ul').animate({
            left: - slideWidth
        }, 200, function () {
            $('#slider ul li:first-child').appendTo('#slider ul');
            $('#slider ul').css('left', '');
        });
    };

    $('a.control_prev').click(function () {
        moveLeft();
    });

    $('a.control_next').click(function () {
        moveRight();
    });

});
