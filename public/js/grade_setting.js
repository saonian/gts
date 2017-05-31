$(function(){
    $.fn.insert_content = function() {
        var html = $('.story_grade_table').html();
        $('#story_grade').append(html);
    };

    $.fn.insert_desc = function() {
        // Our plugin implementation code goes here.
    };

    $('#add_story_content').click(function(){
        //$('#story_grade').insert_content();
        get_story_data();
    });

    function get_story_data()
    {
        var cont = $('.story_content').children('.content');
        console.log(cont.length);
    }

});