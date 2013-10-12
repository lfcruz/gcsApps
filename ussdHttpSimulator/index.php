<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            $transid = rand(20999,32000);
            $diaid = rand(1000,9999);
            
        ?>
        <form name="ussdform" method="post" action="/ussdHttpSimulator/ussdFlow.php">
        <input type="hidden" name="transactionid" value="<?php echo $transid ?>">
        <input type="hidden" name="dialogid" value="<?php echo $diaid ?>">
        <input type="hidden" name="text" value="*322#">
        <input type="hidden" name="status" value="begin">
        Phone No: <input type="text" name="number" value=""><br/>
        <div><input type="submit" value="submit"></div>
        </form>
    </body>
</html>
