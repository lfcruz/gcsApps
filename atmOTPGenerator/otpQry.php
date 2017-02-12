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
        ?>
        <h2> ATM OTP Query </h2>
        <form name="otpGen" method="post" action="">
        Telefono: <input type="text" name="phone" value=""><br/>
        <div>
        <input type="submit" value="submit">
        </div>
        </form>
        <?php
        if($phone != null) {
            $otpRecord = qryOTP($phone);
            echo '<table border=1>';
            foreach($otpRecord as $field_title => $field_value){
                echo '<tr><td>',$field_title.':','</td>';
                echo '<td>',$field_value,'</td></tr>';
                echo '<table border=1>';
                foreach($otpRecord[$field_title] as $subfield_title => $subfield_value){
                    echo '<tr><td>',$subfield_title.':','</td>';
                    echo '<td>',$subfield_value,'</td></tr>';
                }
                echo'</table>';
            }
            echo '</table>';
        }
        ?>    
    </body>
</html>
