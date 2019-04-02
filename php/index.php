<!--
//index.php
!-->

<?php

include('database_connection.php');

session_start();

if(!isset($_SESSION['user_id']))
{
    header("location:login.php");
}

?>

<html>
<head>
    <title>Chat Application using PHP Ajax Jquery</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
<!--    <link rel="stylesheet" href="../css/jquery-ui.css">-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.css">
    <script src="../js/jquery-3.2.1.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery-ui.js"></script>
    <script src="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.js"></script>
<!--    <script src="../js/emojionearea.min.js"></script>-->
</head>
<body>
<div class="container">

    <h3 align="center">基于WEB的网络聊天系统</a></h3><br />
    <div class="table-responsive">
        <h4 align="center">聊天室用户</h4>
        <p align="right">欢迎来到聊天室 - <?php echo $_SESSION['username'];  ?> - <a href="logout.php">注销</a></p>

        <div class="col-lg-4">
            <input type="hidden" id="is_active_group_chat_window" value="no" />
            <button type="button" name="group_chat" id="group_chat" class="btn btn-warning btn-xs">Group Chat</button>
        </div>
        <div class="col-lg-4" id="user_model_details"></div>
        <div class="col-lg-4" id="user_details"></div>

        </div>

</div>


<div id="group_chat_dialog" title="Group Chat Window">
    <div id="group_chat_history" style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;">

    </div>
    <div class="form-group">
        <textarea name="group_chat_message" id="group_chat_message" class="form-control"></textarea>
    </div>
    <div class="form-group" align="right">
        <button type="button" name="send_group_chat" id="send_group_chat" class="btn btn-info">Send</button>
    </div>
</div>

</body>
</html>




<script>
    $(document).ready(function(){

        fetch_user();

        let timerId = 1 // 模拟计时器id，唯一性
        let timerObj = {} // 计时器存储器
        // 轮询
        function start () {
            const id = timerId++
            timerObj[id] = true
            async function timerFn () {
                if (!timerObj[id]) return
                update_last_activity();
                fetch_user();
                update_chat_history_data();
                fetch_group_chat_history();
                setTimeout(timerFn, 1000)
            }
            timerFn()
        }
        start();
//        setInterval(function(){
//            update_last_activity();
//            fetch_user();
//            update_chat_history_data();
//            fetch_group_chat_history();
//        }, 5000);

        function fetch_user()
        {
            $.ajax({
                url:"fetch_user.php",
                method:"POST",
                success:function(data){
                    $('#user_details').html(data);
                }
            })
        }

        function update_last_activity()
        {
            $.ajax({
                url:"update_last_activity.php",
                success:function()
                {

                }
            })
        }

        function make_chat_dialog_box(to_user_id, to_user_name)
        {

            if(fetch_user_chat_history(to_user_id) == undefined){
                var tips = '加载中';
            }
            else {
                var tips = fetch_user_chat_history(to_user_id);
            }
            var modal_content = '<div id="user_dialog_'+to_user_id+'" class="user_dialog" title="正在与 '+to_user_name+' 聊天">';
            modal_content += '<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data-touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'">';
            modal_content +=  tips;
            modal_content += '</div>';
            modal_content += '<div class="form-group">';
            modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'" class="form-control chat_message" rows="16"></textarea>';
            modal_content += '</div><div class="form-group show_error" align="right">';
            modal_content+= '<button type="button" name="send_chat" id="'+to_user_id+'" class="btn btn-info send_chat">发送</button></div></div>';
            $('#user_model_details').html(modal_content);
        }

        $(document).on('click', '.start_chat', function(){
            var to_user_id = $(this).data('touserid');
            var to_user_name = $(this).data('tousername');
            make_chat_dialog_box(to_user_id, to_user_name);
            $("#user_dialog_"+to_user_id).dialog({
                autoOpen:false,
                width:400
            });

            $('#user_dialog_'+to_user_id).dialog('open');

            $('#chat_message_'+to_user_id).emojioneArea({
                pickerPosition:"top",
                toneStyle: "bullet"
            });
        });

        $('#group_chat_dialog').dialog({
            autoOpen:false,
            width:400
        });


        $(document).on('click', '.send_chat', function(){
                var to_user_id = $(this).attr('id');
                var chat_message = $('#chat_message_'+to_user_id).val();
                if(chat_message != ''){
                $.ajax({
                    url:"insert_chat.php",
                    method:"POST",
                    data:{to_user_id:to_user_id, chat_message:chat_message},
                    success:function(data)
                    {
                        var element = $('#chat_message_'+to_user_id).emojioneArea();
                        element[0].emojioneArea.setText('');
                        $('#chat_history_'+to_user_id).html(data);
                    }
                })
                }
        });


        function fetch_user_chat_history(to_user_id)
        {
            $.ajax({
                url:"fetch_user_chat_history.php",
                method:"POST",
                data:{to_user_id:to_user_id},
                success:function(data){
                        $('#chat_history_'+to_user_id).html(data);

                }
            })
        }

        function update_chat_history_data()
        {
            $('.chat_history').each(function(){
                var to_user_id = $(this).data('touserid');
                fetch_user_chat_history(to_user_id);
            });
        }

        $(document).on('click', '.ui-button-icon', function(){
            $('.user_dialog').dialog('destroy').remove();
        });

        $(document).on('focus', '.chat_message', function(){
            var is_type = 'yes';
            $.ajax({
                url:"update_is_type_status.php",
                method:"POST",
                data:{is_type:is_type},
                success:function()
                {

                }
            })
        });

        $(document).on('blur', '.chat_message', function(){
            var is_type = 'no';
            $.ajax({
                url:"update_is_type_status.php",
                method:"POST",
                data:{is_type:is_type},
                success:function()
                {

                }
            })
        });

        $('#group_chat').click(function(){
            $('#group_chat_dialog').dialog('open');
            $('#is_active_group_chat_window').val('yes');
            fetch_group_chat_history();
        });

        $('#send_group_chat').click(function(){
            var chat_message = $('#group_chat_message').val();
            var action = 'insert_data';
            if(chat_message != '')
            {
                $.ajax({
                    url:"group_chat.php",
                    method:"POST",
                    data:{chat_message:chat_message, action:action},
                    success:function(data){
                        $('#group_chat_message').val('');
                        $('#group_chat_history').html(data);
                    }
                })
            }
        });

        function fetch_group_chat_history()
        {
            var group_chat_dialog_active = $('#is_active_group_chat_window').val();
            var action = "fetch_data";
            if(group_chat_dialog_active == 'yes')
            {
                $.ajax({
                    url:"group_chat.php",
                    method:"POST",
                    data:{action:action},
                    success:function(data)
                    {
                        $('#group_chat_history').html(data);
                    }
                })
            }
        }

    });
</script>