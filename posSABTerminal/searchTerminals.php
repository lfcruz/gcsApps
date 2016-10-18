<?php
session_start();
if(date("H:i") > date($_SESSION['expiretime'])){
    session_destroy();
    header("Location: index.php");
}
$_SESSION['expiretime'] = date("H:i", strtotime('+20 minutes'));
?>
<html>  
    <head>  
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="icon" href="img/tPago.ico" type="image/x-icon" />
        <title>posSwtich Terminal Loader - Login</title>
        <script src="js/jquery-1.10.2.min.js"></script>  
        <script href="js/bootstrap.min.js"></script>
        <link href="css/bootstrap.css" rel="stylesheet" />
        <!-- 
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
        <title>posSwtich Terminal Loader - Terminals</title>
        <script src="js/jquery-1.10.2.min.js"></script>  
        <script src="js/bootstrap.min.js"></script>  
        <link href="css/bootstrap.css" rel="stylesheet" />
        -->
        <style type="text/css">
        h2{text-align: left; margin-left: 3%}
        img{margin-left: 3%}
        button{margin-right: 18%}
        th,tbody {text-align:center}
        body{margin-top: 15px;}
        </style>
    </head>  
    <body>
        <div class="container">  
            <br />
            <img src="img/tPago.png" alt="logo"/>
            <h2>switchPOS Terminal Loader</h2><br />  
            <div class="form-group">  
                <div class="input-group">  
                    <span class="input-group-addon">Search</span>  
                    <input type="text" name="search_text" id="search_text" placeholder="Search by Agency Name or Terminal ID" class="form-control" />  
                </div>  
            </div>  
            <br />  
            <div id="result"></div>  
        </div>  
    </body>  
 </html>  
 <script>  
 $(document).ready(function(){  
        $('#search_text').keyup(function(){  
            var txt = $(this).val();  
            if(txt != '') {  
                $.ajax({  
                    url:"lib/getTableResult.php",  
                    method:"post",  
                    data:{search:txt},  
                    dataType:"text",  
                    success:function(data) {  
                        $('#result').html(data);  
                    }  
                });  
            } else {  
                $('#result').html('');                 
            }  
        });
});
</script>
<script type="text/javascript">
function buttonClicked(tid,mid,name,street,city,region,country){
    alert("Terminal number "+tid+", "+mid+", "+name);
    $.ajax({
        url:"lib/saveTerminal.php",
        method:"post",
        data:{tid:tid,
            mid:mid,
            name:name,
            street:street,
            city:city,
            region:region,
            country:country},
        dateType:"text",
        success:function(data){
            $('#result').html('');
        },
        error:function(data){
            alert(data);
        }
    });    
};
 </script>