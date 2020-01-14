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
$('.commentInputField').on('keyup keydown change', function() {
    while(($(this).outerHeight() + 2) < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
        $(this).height($(this).height()+1);
    }
});

$('.selectedImage').click(function (e) {
    e.preventDefault();

    $('.selectedImage').css('border', 'none');
    $(this).css('border', '3px solid lightblue');

    let imageId = $(this).data('id');
    $('.selectedImageId').val(imageId);
});
// Image gallery
$(function () {
    $("#mdb-lightbox-ui").load("mdb-addons/mdb-lightbox-ui.html");
});

$('img, .viewImage').click(function () {
    let imageUrl = $(this).data('url');

    $('#viewPictureModal').show(function () {
        $('.modalImage').attr({
            src: imageUrl,
            alt:'Preview'
        });
    });
});
// Chat
$('.chatInputField').keyup(function (e) {
    e.preventDefault();
    if (e.which == 13) {
        let message = $('.chatInputField').val();
        $.ajax({
            url: "/send/message",
            type: "post",
            data:  {
                message: message,
            },
            success: function(response)
            {
            },
            error: function(e)
            {
                console.log('error');
            }
        });
    }
});
$('.commentInputField').keyup(function (e) {
    e.preventDefault();
    if (e.which == 13) {
        $(this).parent('.commentForm').submit();
    }
});
$('.replay').click(function (e) {
    e.preventDefault();
    $('.commentId').val($(this).data('id'));
    if ($(this).data('status') === 'replay') {
        $(this).parent().parent().next().removeClass('d-none').addClass('d-block').find('.commentInputField').text('@'+$(this).data('user') + ' ');

    } else {
        $(this).parent().next().removeClass('d-none').addClass('d-block');
    }
});

$('.show-comments').click(function (e) {
    e.preventDefault();
    let thisElement = $(this),
        nextElement = $(this).next(),
        postId = $(this).data('id'),
        offset = 0;

    if ($(this).data('flag') == 'hide') {
        $.ajax({
            url: "/post/comments/"+postId,
            type: "post",
            data: {
                offset: offset
            },
            success: function(response)
            {
                $(nextElement).append(response);
                $(thisElement).next().next().removeClass('d-none').addClass('d-block');
                $(nextElement).removeClass('d-none').addClass('d-block-inline');
                $(thisElement).data('flag', 'show');
            },
            error: function()
            {
                console.log('error');
            }
        });
    } else {
        $(this).data('flag', 'hide');
        $(thisElement).next().next().removeClass('d-block').addClass('d-none');
        $(nextElement).empty();
        $(nextElement).addClass('d-none');
    }
});

$('.show-more-comments').click(function (e) {
    e.preventDefault();

    let thisElement = $(this),
        commentsDiv = $(this).prev(),
        postId = $(this).data('id'),
        offset = parseInt($(this).attr('data-offset')) + 5,
        divCount = $(thisElement).prev().children('.commentDiv').length;

    if (divCount >= offset) {
        $.ajax({
            url: "/post/comments/"+postId,
            type: "post",
            data: {
                offset: offset
            },
            success: function(response)
            {
                $(commentsDiv).append(response);
                $(thisElement).attr('data-offset', offset );
                let divCountNew = $(thisElement).prev().children('.commentDiv').length;
                if (divCountNew % 5 != 0) {
                   $(thisElement).removeClass('d-block').addClass('d-none');
                }
            },
            error: function()
            {
                console.log('error');
            }
        });
    }
});