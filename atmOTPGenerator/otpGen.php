<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title> ATM - OTP Manager </title> <a href="index.php">[Home]</a><br/>
    </head>
    <body>
        <?php
        include_once 'addedFunctions.php';
        $phone = $_POST['phone'];
        $amount = $_POST['amount'];
        ?>
        <h2> ATM OTP Generator </h2>
        <form name="otpGen" method="post" action="">
        Telefono: <input type="text" name="phone" value=""><br/>
        Monto   : <input type="text" name="amount" value=""><br/>
        <div>
        <input type="submit" value="submit">
        </div>
        </form>
        <?php
        if($phone != null && $amount != null) {
            $otpRecord = newOTP($phone, $amount);
            echo '<table border=1>';
            foreach($otpRecord as $field_title => $field_value){
                echo '<tr><td>',$field_title.':','</td>';
                echo '<td>',$field_value,'</td></tr>';
            }
            echo '</table>';
        }
        ?>    
    </body>
</html>
