<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title> ATM - OTP Manager </title>
    </head>
    <body>
        <script type="text/javascript">
            function redirectMe (sel) {
            var url = sel[sel.selectedIndex].value;
            window.location = url;
            }
        </script>
        <h2> ATM OTP Manager </h2>
        <form name="otpManager" method="post" action="">
        Operation:
        <select name="operation" onchange="redirectMe(this);">
        <option value="">Select....</option>
        <option value="otpGen.php">Generate OTP</option>
        <option value="otpQry.php">Query OTPs</option>
        <option value="otpCnl.php">Cancel OTP</option>
        </select></br>
        </form>
    </body>
</html>
