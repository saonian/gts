$(function(){
    if(typeof testtask == 'undefined'){
        testtask = {};
    }
    testtask.project = $('#t_project_id');
    testtask.story = $('#t_story_id');
    testtask.task = $('#t_task_id');

    //初始化动态加载
    testtask.init_select = function(pid, sid, tid){

        testtask.change_story(pid, sid);
        testtask.change_task(sid, tid);
    }

    //项目动态级联需求
    testtask.change_story = function(pid, sid)
    {
        var temp_html = "";
        var current_project_id = null;
        if(pid != null){
            current_project_id = pid;
        }else{
            current_project_id = testtask.project.children('option:selected').val();
        }
        if(current_project_id == null)
            return;

        $.ajax({
            type: "POST",
            url: "/testtask/json_story_by_pid/"+current_project_id,
            dataType:'json',
            data: "",
            success: function(msg){
                if(msg.length == 0 || !msg)
                {
                    $('#tr_story').addClass('hidden');
                    $('#tr_task').addClass('hidden');
                    return;
                }
                temp_html+="<option value='0'></option>";
                $.each(msg ,function(i, n){
                    temp_html+="<option value='"+n.id+"'"+(n.id==sid?"selected":"")+">"+n.name+"</option>";
                });
                testtask.story.html(temp_html);
                $('#tr_story').removeClass();
            }
        });

    }

    //需求动态级联任务
    testtask.change_task = function(sid, tid)
    {
        var temp_html = "";
        var current_story_id = 0;
        if(sid != null){
            current_story_id = sid;
        }else{
            current_story_id = testtask.story.children('option:selected').val();
        }
        $.ajax({
            type: "POST",
            url: "/testtask/json_task_by_tid/"+current_story_id,
            dataType:'json',
            data: "",
            success: function(msg){
                if(msg.length == 0 || !msg)
                {
                    $('#tr_task').addClass('hidden');
                    return;
                }
                temp_html+="<option value='0'></option>";
                $.each(msg ,function(i, n){
                    temp_html+="<option value='"+n.id+"'"+(n.id==tid?"selected":"")+">"+n.name+"</option>";
                });
                testtask.task.html(temp_html);
                $('#tr_task').removeClass();
            }
        });
    }

    testtask.project.change(function(){

        testtask.change_story();
    });
    testtask.story.change(function(){
        testtask.change_task();
    });
});