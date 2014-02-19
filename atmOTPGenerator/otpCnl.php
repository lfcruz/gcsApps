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
        $ref = $_POST['ref'];
        ?>
        <h2> ATM OTP Cancellation </h2>
        <form name="otpGen" method="post" action="">
        Phone: <input type="text" name="phone" value=""><br/>
        Reference: <input type="text" name="ref" value=""><br/>
        <div>
        <input type="submit" value="submit">
        </div>
        </form>
        <?php
        if($phone != null && $ref != null) {
            $otpRecord = cnlOTP($phone, $ref);
            if($otpRecord === null){
                echo "OTP Ref #$ref was cancelled successfuly.";
            }
            else {
                var_dump($otpRecord);
            }
        }
        ?>   
    </body>
</html>
