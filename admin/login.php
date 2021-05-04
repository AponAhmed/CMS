<?php
ob_start();
define("LOGIN", true);
require_once('../loader.php' );
if (isset($_POST['login'])) {
    $userName = $_POST['user'];
    $passWord = md5($_POST['password']);
    $u = $DB->select("user", "ID", "(user_login='$userName' or user_email='$userName') and user_pass='$passWord'");
    if (!empty($u)) {
        $_SESSION[SESS_KEY]['login'] = $u[0]['ID'];
        $_SESSION['login'] = true;
    } else {
        $_SESSION['msg'] = "Incorrect Login !!";
        ob_get_clean();
        header("location:login.php");
        exit;
    }
}
if (isset($_SESSION[SESS_KEY]['login'])) {
    ob_get_clean();
    if (isset($_SESSION[SESS_KEY]['redir'])) {
        $red = $_SESSION[SESS_KEY]['redir'];
        header("location:$red");
        exit;
    } else {
        header("location:index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>User Login</title>
        <?php login_script(true); ?>
    </head>
    <body>
        <form method="post">
            <div class='login-container'>
                <?php
                if (isset($_SESSION['msg'])) {
                    echo "<p class=\"alert\">$_SESSION[msg]</p>";
                    unset($_SESSION['msg']);
                }
                ?>
                <div class="cmsLogo">
                    <img class="" src="images/logo.png"> 
                </div>
                <div class='loginBody' style='display:none'>
                    <div class="loginField">
                        <label>Username or Email Address</label>
                        <input type='text' name="user">
                    </div>
                    <div class="loginField">
                        <label>Password</label>
                        <input type="password" name="password">
                    </div>
                    <div class='loginfooter'>
                        <input type="submit" name='login' value="Login">&nbsp;&nbsp;<a href="" class="frgt">Forget Password ?</a>
                    </div>
                </div>
                <p id='serverIp' style="color:#888;text-align: center;display: none">Server IP: <?php echo gethostbyname($_SERVER['HTTP_HOST']) ?></p>
            </div>
        </form>
        <script src="<?php echo get_protocol() ?>code.jquery.com/jquery-3.3.1.min.js"></script>
        <script>
            $(document).ready(function() {
                $(".loginBody").show(300, function() {
                    $("#serverIp").show();
                });

            })
        </script>
    </body>
</html> 
