require('jquery');
require('../css/app.css');
require('../../node_modules/bootstrap/dist/js/bootstrap.min.js');
require('../../node_modules/bootstrap/dist/css/bootstrap.min.css');

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

$('.commentInputField').keyup(function (e) {
    e.preventDefault();
    if (e.which == 13) {
        $(this).parent('.commentForm').submit();
    }
});
$(document).on('click', '.replay', function(e) {
    e.preventDefault();
    $('.commentId').val($(this).data('id'));
    if ($(this).data('status') === 'replay') {
        $(this).parent().parent().next().removeClass('d-none').addClass('d-block').find('.commentInputField').empty().text('@'+$(this).data('user') + ' ');
    } else {
        $(this).parent().next().removeClass('d-none').addClass('d-block').find('.commentInputField').empty();
    }
});
$(document).on('click', '.show-comments', function (e) {
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
// $('.show-comments').click(function (e) {
//     e.preventDefault();
//     let thisElement = $(this),
//         nextElement = $(this).next(),
//         postId = $(this).data('id'),
//         offset = 0;
//
//     if ($(this).data('flag') == 'hide') {
//         $.ajax({
//             url: "/post/comments/"+postId,
//             type: "post",
//             data: {
//                 offset: offset
//             },
//             success: function(response)
//             {
//                 $(nextElement).append(response);
//                 $(thisElement).next().next().removeClass('d-none').addClass('d-block');
//                 $(nextElement).removeClass('d-none').addClass('d-block-inline');
//                 $(thisElement).data('flag', 'show');
//             },
//             error: function()
//             {
//                 console.log('error');
//             }
//         });
//     } else {
//         $(this).data('flag', 'hide');
//         $(thisElement).next().next().removeClass('d-block').addClass('d-none');
//         $(nextElement).empty();
//         $(nextElement).addClass('d-none');
//     }
// });

$(document).on('click', '.show-more-comments', function (e) {
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

$(document).on('click', '.show-replays', function (e) {
    e.preventDefault();

    let thisElement = $(this),
        nextElement = $(this).next(),
        commentId = $(this).data('id'),
        offset = 0;

    if ($(this).data('flag') == 'hide') {
        $.ajax({
            url: "/post/comment/replays/"+commentId,
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

$(document).on('click', '.show-more-replays', function (e) {
    e.preventDefault();
    let thisElement = $(this),
        commentsDiv = $(this).prev(),
        commentId = $(this).data('id'),
        offset = parseInt($(this).attr('data-offset')) + 5,
        divCount = $(thisElement).prev().children('.replayDiv').length;

    if (divCount >= offset) {
        $.ajax({
            url: "/post/comment/replays/"+commentId,
            type: "post",
            data: {
                offset: offset
            },
            success: function(response)
            {
                $(commentsDiv).append(response);
                $(thisElement).attr('data-offset', offset );
                let divCountNew = $(thisElement).prev().children('.replayDiv').length;
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
$(document).on('keyup', '.commentInputField', function (e) {
    e.preventDefault();
    if (e.which == 13) {
        $(this).parent('.commentForm').submit();
    }
});

$("#uploadMedia").change(function() {
    for (i = 0; i < this.files.length; i++) {
        var reader = new FileReader();
        reader.onload = imageIsLoaded;
        reader.readAsDataURL(this.files[i]);

    }
    // if (this.files && this.files[0]) {
    //     var reader = new FileReader();
    //     reader.onload = imageIsLoaded;
    //     reader.readAsDataURL(this.files[0]);
    // }
});

function imageIsLoaded(e) {
    var picture = '<img style="width: 100px;border: 2px solid lightgrey;margin:2px" class="img-fluid viewImage" id="image-3" src="' + e.target.result + '">';
    $(".previewImage").append(picture);
};