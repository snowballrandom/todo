/**
 * Created by Kyle Coots
 * User: kyle
 * Date: 7/22/18
 * Time: 12:42 AM
 */
$(document).ready(function () {
    console.log('loaded');

    var data = {
        action:"get"
    };

    $.ajax({
        url: "todo.php",
        method: "POST",
        data: data,
        dataType: "text",
        success:function(data){
            console.log(data);
            var data = $.parseJSON(data);
            for (var i = 0; i < data.length; i++) {
                $('ul').append("<li id='"+ data[i].iditems +"'><span><i class=\"fa fa-trash\"></i></span> " + data[i].item + "</li>")
            }
        }
    });

});
$('ul').on("click", "li", function(){
    $(this).toggleClass('completed');
});

$('ul').on("click", "span", function(event) {
    $(this).parent().fadeOut(500, function () {
        $(this).remove();
        var itemId = $(this).attr('id');

        var data = {
            action:"delete",
            item_id:itemId
        };

        $.ajax({
            url: "todo.php",
            method: "POST",
            data: data,
            dataType: "text",
            success:function(data){
                console.log(data);
            }
        });

    });
    event.stopPropagation();
});

$("input[type='text']").keypress(function (event) {
    console.log(this.value);
    if(event.which === 13){
        console.log('enter');
        var text = $(this).val();
        $(this).val("");

        var data = {
            action:"insert",
            item:text
        };

        $.ajax({
            url: "todo.php",
            method: "POST",
            data: data,
            dataType: "text",
            success:function(data){
                console.log(data);

                $('ul').append("<li id='"+data+"'><span><i class=\"fa fa-trash\"></i></span> "+text+"</li>");
            }
        });




    }
    event.stopPropagation();
});

$('.fa-plus').click(function () {
    $("input[type='text']").fadeToggle();
});
