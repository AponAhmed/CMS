<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
$currentStep = 1;

function siteURL() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'] . '/';
    return $protocol . $domainName;
}

$Next = "";
$exception = "";
if (isset($_POST['currentStep']) && !empty($_POST['currentStep'])) {
    $currentStep = $_POST['currentStep'];
    if ($currentStep == 1) {
        $currentStep = 2;
    } elseif ($currentStep == 2) {
        $mysqli = mysqli_connect($_POST["db-host"], $_POST['db-user'], $_POST["db-password"], $_POST['db-name']);
        //var_dump($mysqli);
        if (!$mysqli) {
            $exception = "Connect Error (" . mysqli_connect_errno() . ") " . mysqli_connect_error();
            $currentStep = 2;
        } else {
            $install = new InstallCms($mysqli);
            $install->createTable();
            $currentStep = 3;
            $_SESSION['install-info'] = array(
                "db-name" => $_POST['db-name'],
                "db-user" => $_POST['db-user'],
                "db-password" => $_POST["db-password"],
                "db-host" => $_POST["db-host"],
                "sub" => $_POST["subFolder"],
                "app" => isset($_POST['webApp']) ? true : false,
            );
        }
    } elseif ($currentStep == 3) {
        //Creating  config.php file
        $uniqueKey = md5(mt_rand() . "SiATEXCMS");
        $configFile = '<?php
            defined("ABSPATH") OR exit("No direct script access allowed");
			//MySQL Database Name
			define("DB","' . $_SESSION['install-info']['db-name'] . '");
			
			//MySQL Database User
			define("DB_USER","' . $_SESSION['install-info']['db-user'] . '");
			
			//MySQL Database Password
			define("DB_PASS","' . $_SESSION['install-info']['db-password'] . '");
			
			//Database Host
			define("DB_HOST","' . $_SESSION['install-info']['db-host'] . '");
			//------------------------------------------------
            //Sub folder of site
            define("SUB_ROOT","' . $_SESSION['install-info']['sub'] . '");
			define("DEBUG", false);
			define("SESS_KEY","' . $uniqueKey . '");
			';
        if ($_SESSION['install-info']['app']) {
            $configFile.="
            define('WEB_APP', true);
                    ";
        }
        $configFileCreate = fopen(ABSPATH . "config.php", "w");
        if ($configFileCreate) {
            fwrite($configFileCreate, $configFile);
        } else {
            echo $configFile;
        }
        //---------Robots text Create
        $robotTxt = "User-agent: *
Disallow: /admin/
Allow: /*.js
Allow: /*.css
Allow: /*.png
Allow: /*.jpg
Allow: /*.gif
SITEMAP: " . siteURL() . "sitemap.xml";
        $robotTxtCreate = fopen(ABSPATH . "robots.txt", "w");
        if ($robotTxtCreate) {
            fwrite($robotTxtCreate, trim($robotTxt));
        } else {
            echo "can't create 'robots.txt' file";
        }

        //.htaccess file create 
        $htaccessFileStr = "<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /" . $_SESSION['install-info']['sub'] . "
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /" . $_SESSION['install-info']['sub'] . "index.php [L]
</IfModule>";
        $htaccessFileCreate = fopen(ABSPATH . ".htaccess", "w");
        if ($htaccessFileCreate) {
            fwrite($htaccessFileCreate, $htaccessFileStr);
        } else {
            echo "can't create '.htaccess' file, its required so create manualy<br>";
            echo "<code>$htaccessFileStr</code>";
        }

        $mysqli = mysqli_connect($_SESSION['install-info']["db-host"], $_SESSION['install-info']['db-user'], $_SESSION['install-info']["db-password"], $_SESSION['install-info']['db-name']);
        $install = new InstallCms($mysqli);
        $sucUser = $install->initUser($_POST);
        $install->initOption($_POST);

        $SubRot = $_SESSION['install-info']['sub'];
//        $RqSch = $_SERVER['REQUEST_SCHEME'];
//        $re = '/((https)|(http)).*/';
//        preg_match_all($re, $_SERVER['SCRIPT_URI'], $matches, PREG_SET_ORDER, 0);
//        $DifSch = $matches[0][1];
//        if (empty($RqSch)) {
//            $RqSch = $DifSch;
//        }
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";


        $SiteUrl = $protocol . $_SERVER['HTTP_HOST'];
        if ($SubRot != "") {
            $SiteUrl.="/" . $SubRot;
        }
        $install->initSiteUrl($SiteUrl);
        if ($sucUser) {
            header("location:admin");
            exit;
        }
        //var_dump($_POST);
    }
} else {
    unset($_SESSION['install-info']);
}
//var_dump($_SESSION['install-info']);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Install </title>
        <style>
            body {
                font-family:arial;
                background: #f6f6f6;
                color:#666;
                font-size:14px;
            }
            .installer-container{
                max-width: 60%;
                margin: auto;
                position: relative;
                margin-top: 90px;
            }

            .installerBody {
                background: #fff;
                border-radius: 5px;
                padding: 20px 30px 30px 30px;
                border: 1px solid #e9e9e9;
            }

            .cmsLogo {
                max-width: 65px;
                margin: auto;
                padding: 15px;
                border-radius: 50%;
                margin-top: -38px;
                background: #fff;
                position: absolute;
                left: -38px;
                top: 0;
                overflow: hidden;
                line-height: 0;
                z-index: 999999;
            }

            .cmsLogo::before {
                content: "";
                position: absolute;
                width: calc(100% - 2px);
                height: calc(100% - 2px);
                left: 0;
                top: 0;
                border: 1px solid #e9e9e9;
                border-radius: 50%;
            }

            .cmsLogo img {
                max-width: 50px;
            }

            .installerFooter {
                overflow: hidden;
                padding-top: 10px;
            }

            .NextButton {
                text-decoration: none;
                color: #fff;
                background: #054d7f;
                padding: 7px 20px;
                border-radius: 2px;
                /* margin-top: 25px; */
                display: inline-block;
                float: right;
                border: 0;
            }

            .step{display:none;}
            .step.in{display:block;}

            .install-input-text {
                border: 1px solid #ededed;
                margin: 2px 10px;
                padding: 4px;
                width: 40%;
            }
            .install-comment {
                font-size: 11px;
            }
            .exception {
                color: #e16262;
            }
        </style>
    </head>
    <body>
        <div class='installer-container'>
            <div class="cmsLogo">
                <img class="" src="admin/images/logo.png"> 
            </div>
            <div class='installerBody' style='display:none'>
                <h3 class='stepTitle'>Install </h3>
                <form method="post">
                    <?php echo!empty($exception) ? "<p class='exception'>" . $exception . "</p>" : "" ?>
                    <?php if ($currentStep == 1) { ?>
                        <div id='step1' class='step <?php echo $currentStep == 1 ? "in" : "" ?>'>
                            <p>Welcome to SiATEX PHP framework.<br> 
                                Before getting started, we need some information on the database. You will need to know the following items before proceeding.</p>
                            <ol>
                                <li>Database name</li><li>Database username</li><li>Database password</li><li>Database host</li>
                            </ol>
                            <p>We’re going to use this information to create a "config.php" file.
                                If you’re all ready than go next..</p> 
                        </div>
                        <?php
                    } elseif ($currentStep == 2) {
                        ?>
                        <div id='step2' class='step <?php echo $currentStep == 2 ? "in" : "" ?>'>
                            <table width="100%">
                                <tr>
                                    <td>Database Name</td>
                                    <td><input type="text" name="db-name" class="install-input-text" required>  	<span class='install-comment'>The name of the database</span></td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td><input type="text" name="db-user" class="install-input-text" required> <span class='install-comment'>Your database username.</span></td>
                                </tr>
                                <tr>
                                    <td>Password</td>
                                    <td><input type="password" name="db-password"  class="install-input-text"> <span class='install-comment'>Your database password.</span></td>
                                </tr>
                                <tr>
                                    <td>Database Host</td>
                                    <td><input type="text" name="db-host" class="install-input-text" value="localhost" required><span class='install-comment'>You should be able to get this info from your web host</span></td>
                                </tr>
                                <tr>
                                    <td>Sub-Folder</td>
                                    <td><input type="text" name="subFolder" class="install-input-text"><span class='install-comment'>Site Sub Root</span></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td> &nbsp;&nbsp;<input type="checkbox" name="webApp" value="true" id="webAppSwt"><label for="webAppSwt">Web Application</label></td>
                                </tr>
                            </table>
                        </div>
                        <?php
                    } elseif ($currentStep == 3) {
                        ?>
                        <div id='step3' class='step <?php echo $currentStep == 3 ? "in" : "" ?>'>
                            <table width="100%">
                                <tr>
                                    <td>Site Name</td>
                                    <td><input type="text" name="site_name" class="install-input-text" required>  	<span class='install-comment'>The name of  Site</span></td>
                                </tr>
                                <tr>
                                    <td>User Email</td>
                                    <td><input type="text" name="user_email" class="install-input-text" required> <span class='install-comment'>Enter user's email address</span></td>
                                </tr>
                                <tr>
                                    <td>User Name</td>
                                    <td><input type="text" name="site_user" class="install-input-text" required> <span class='install-comment'>Set new user name</span></td>
                                </tr>
                                <tr>
                                    <td>User Display Name</td>
                                    <td><input type="text" name="site_user_d_name" class="install-input-text"> <span class='install-comment'>Set new user name</span></td>
                                </tr>
                                <tr>
                                    <td>Password</td>
                                    <td><input id="sitePassword" onkeyUp="match(this.value, sitePasswordRe)" type="password" name="site_password"  class="install-input-text" required> <span class='install-comment'>Set new password</span></td>
                                </tr>
                                <tr>
                                    <td>Re-Enter Password</td>
                                    <td><input id="sitePasswordRe" onkeyUp="match(sitePassword.value, this)" type="password" class="install-input-text"> <span class='install-comment'>Enter again your  new password</span></td>
                                </tr>
                            </table>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="installerFooter">
                        <input type="hidden" value="<?php echo!empty($Next) ? $next : $currentStep ?>" name="currentStep">
                        <button type='submit' class="NextButton">Next</button>
                    </div>
                </form>
            </div>
            <script
                src="https://code.jquery.com/jquery-3.3.1.min.js"
                integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
            <script>
                                        function match(n, rn) {
                                            var rePass = $(rn).val();
                                            if (n !== rePass) {
                                                $(rn).css("border-color", "#f00");
                                                $(".NextButton").hide();
                                            } else {
                                                $(rn).removeAttr("style");
                                                $(".NextButton").show();
                                            }
                                        }
                                        $(document).ready(function() {
                                            $(".installerBody").show(300);
                                        })

            </script>
        </div>
    </body>
</html> 				