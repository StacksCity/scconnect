<?php
//require_once '../includes/lb_helper.php'; // Include LicenseBox external/client api helper file
//$api = new LicenseBoxAPI(); // Initialize a new LicenseBoxAPI object
//?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>MyScript - Installer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"/>
    <style type="text/css">
      body, html {
          background: #F7F7F7;
      }
    </style>
  </head>
  <body>
    <?php
      $errors = false;
      $today = date('Y-m-d');
      $csrf_token_rand = substr(str_shuffle(MD5(microtime())), 0, 10);
      $csrf_cookie_rand = substr(str_shuffle(MD5(microtime())), 0, 10);
      $session_rand = substr(str_shuffle(MD5(microtime())), 0, 10);
      $encrypt_rand = substr(str_shuffle(MD5(microtime())), 0, 20);
      $db_file = '../application/config/database.php';
      $db_file_sample = 'database-sample.php';
      $database_dump_file = 'database.sql';
      $config_file = '../application/config/config.php';
      $config_file_sample = 'config-sample.php';
      $htaccess_file = '../.htaccess';

      @chmod($installFile,0777);
      @chmod($config_file,0777);
      @chmod($config_file_sample,0777);
      $step = isset($_GET['step']) ? $_GET['step'] : '';

    function check_dir_iswritable($dir_path){
        $is_writale=1;
        if(!is_dir($dir_path)){
            $is_writale=0;
            return $is_writale;
        }else{
            $file_hd=@fopen($dir_path.'/test.txt','w');
        if(!$file_hd){
            @fclose($file_hd);
            @unlink($dir_path.'/test.txt');
            $is_writale=0;
            return $is_writale;
        }
        $dir_hd=opendir($dir_path);
        while(false!==($file=readdir($dir_hd))){
            if ($file != "." && $file != "..") {
                if(is_file($dir_path.'/'.$file)){
                     //文件不可写，直接返回
                    if(!is_writable($dir_path.'/'.$file)){
                        return 0;
                    }
                }else{
                    $file_hd2=@fopen($dir_path.'/'.$file.'/test.txt','w');
                    if(!$file_hd2){
                        @fclose($file_hd2);
                        @unlink($dir_path.'/'.$file.'/test.txt');
                        $is_writale=0;
                        return $is_writale;
                    }
                    $is_writale=check_dir_iswritable($dir_path.'/'.$file);
                }
            }
        }
    }
    return $is_writale;
  } 

    ?>
    <div class="container"> 
      <div class="section">
        <div class="column is-6 is-offset-3">
          <center>
            <h1 class="title" style="padding-top: 20px">LicenseBox Installer</h1><br>
          </center>
          <div class="box">
            <?php
            switch ($step) {
              default: ?>
                <div class="tabs is-fullwidth">
                  <ul>
                    <li class="is-active">
                      <a>
                        <span><b>Requirements</b></span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Verify</span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Database</span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Finish</span>
                      </a>
                    </li>
                  </ul>
                </div>
                <?php
                //监测PHP版本
                $need_php_version = '5.6.0';
                if(version_compare(PHP_VERSION,$need_php_version ) < 0){
                    $errors = true;
                    echo "<div class='notification is-danger' style='padding:12px;'>
                             <i class='fa fa-times'></i> 您正在运行PHP版本 ".phpversion()."!至少需要.".$need_php_version." </div>";
                }else{
                    echo "<div class='notification is-success' style='padding:12px;'>
                            <i class='fa fa-check'></i> 您正在运行PHP版本 ".phpversion()." </div>";
                }

                //监测扩展是否打开
                $checkExtension = [
                     'pdo','curl'
                ];
                foreach ($checkExtension as $key =>$value){
                    if(!extension_loaded($value)){
                        $errors = true;
                        echo "<div class='notification is-danger' style='padding:12px;'>
                            <i class='fa fa-times'></i> ".$value."扩展未打开!</div>";
                    }else{
                        echo "<div class='notification is-success' style='padding:12px;'>
                            <i class='fa fa-check'></i>".$value."扩展可以使用!</div>";
                    }
                }
                  //监测函数是否开启
                  $checkFunction = [
                      'htmlspecialchars','strtoupper'
                  ];
                  foreach ($checkFunction as $key =>$value){
                      if(!function_exists($value)){
                          $errors = true;
                          echo "<div class='notification is-danger' style='padding:12px;'>
                            <i class='fa fa-times'></i> ".$value."函数未打开!</div>";
                      }else{
                          echo "<div class='notification is-success' style='padding:12px;'>
                            <i class='fa fa-check'></i>".$value."函数可以使用!</div>";
                      }
                  }

                  //监测文件是否存在
                  $checkFile = [
                      './database.sql'
                  ];
                  foreach ($checkFile as $key =>$value){
                      if(!is_readable($value)){
                          $errors = true;
                          echo "<div class='notification is-danger' style='padding:12px;'>
                            <i class='fa fa-times'></i> ".$value."文件不存在!</div>";
                      }else{
                          echo "<div class='notification is-success' style='padding:12px;'>
                            <i class='fa fa-check'></i>".$value."文件正常!</div>";
                      }
                  }
                  //监测目录是否存在
                  $checkFile = [
                      '../includes','../licensebox-test-wordpress-plugin/includes'
                  ];
                  foreach ($checkFile as $key =>$value){
                      if(!file_exists($value)){
                          $errors = true;
                          echo "<div class='notification is-danger' style='padding:12px;'>
                            <i class='fa fa-times'></i> ".$value."目录不存在!</div>";
                      }else{
                          echo "<div class='notification is-success' style='padding:12px;'>
                            <i class='fa fa-check'></i>".$value."目录正常!</div>";
                      }
                  }
                  //监测文件目录读写权限
                  $checkIsWritable = [
                      '../update','../includes'
                  ];
                  foreach ($checkIsWritable as $key =>$value){
                      if(!check_dir_iswritable($value)){
                          $errors = true;
                          echo "<div class='notification is-danger' style='padding:12px;'>
                            <i class='fa fa-times'></i> ".$value."目录没有读写权限</div>";
                      }else{
                          echo "<div class='notification is-success' style='padding:12px;'>
                            <i class='fa fa-check'></i>".$value."目录正常!</div>";
                      }
                  }
            ?>
                <div style='text-align: right;'>
                  <?php if($errors==true){ ?>
                  <a href="#" class="button is-link" disabled>Next</a>
                  <?php }else{ ?>
                  <a href="index.php?step=0" class="button is-link">Next</a>
                  <?php } ?>
                </div>
                  <?php
                break;
              case "0": ?>
                <div class="tabs is-fullwidth">
                  <ul>
                    <li>
                      <a>
                        <span><i class="fa fa-check-circle"></i> Requirements</span>
                      </a>
                    </li>
                    <li class="is-active">
                      <a>
                        <span><b>Verify</b></span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Database</span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Finish</span>
                      </a>
                    </li>
                  </ul>
                </div>
                <?php
                  $license_code = null;
                  $client_name = null;
                  if(!empty($_POST['license'])&&!empty($_POST['client'])){
                    $license_code = strip_tags(trim($_POST["license"]));
                    $client_name = strip_tags(trim($_POST["client"]));
                    /* Once we have the license code and client's name we can use LicenseBoxAPI's activate_license() function for activating/installing the license, if the third parameter is empty a local license file will be created which can be used for background license checks. */
//                    $activate_response = $api->activate_license($license_code,$client_name);
                      $activate_response ['msg'] = '验证成功';
                      $activate_response ['status'] = true;

                      if(empty($activate_response)){
                      $msg='Server is unavailable.';
                    }else{
                      $msg=$activate_response['message'];
                    }
                    if($activate_response['status'] != true){ ?>
                      <form action="index.php?step=0" method="POST">
                        <div class="notification is-danger"><?php echo ucfirst($msg); ?></div>
                        <div class="field">
                          <label class="label">License code</label>
                          <div class="control">
                            <input class="input" type="text" placeholder="enter your purchase/license code" name="license" required>
                          </div>
                        </div>
                        <div class="field">
                          <label class="label">Your name</label>
                          <div class="control">
                            <input class="input" type="text" placeholder="enter your name/envato username" name="client" required>
                          </div>
                        </div>
                        <div style='text-align: right;'>
                          <button type="submit" class="button is-link">Verify</button>
                        </div>
                      </form><?php
                    }else{ ?>
                      <form action="index.php?step=1" method="POST">
                        <div class="notification is-success"><?php echo ucfirst($msg); ?></div>
                        <input type="hidden" name="lcscs" id="lcscs" value="<?php echo ucfirst($activate_response['status']); ?>">
                        <div style='text-align: right;'>
                          <button type="submit" class="button is-link">Next</button>
                        </div>
                      </form><?php
                    }
                  }else{ ?>
                    <form action="index.php?step=0" method="POST">
                      <div class="field">
                        <label class="label">License code</label>
                        <div class="control">
                          <input class="input" type="text" placeholder="enter your purchase/license code" name="license" required>
                        </div>
                      </div>
                      <div class="field">
                        <label class="label">Your name</label>
                        <div class="control">
                          <input class="input" type="text" placeholder="enter your name/envato username" name="client" required>
                        </div>
                      </div>
                      <div style='text-align: right;'>
                        <button type="submit" class="button is-link">Verify</button>
                      </div>
                    </form>
                  <?php } 
                break;
              case "1": ?>
                <div class="tabs is-fullwidth">
                  <ul>
                    <li>
                      <a>
                        <span><i class="fa fa-check-circle"></i> Requirements</span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span><i class="fa fa-check-circle"></i> Verify</span>
                      </a>
                    </li>
                    <li class="is-active">
                      <a>
                        <span><b>Database</b></span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Finish</span>
                      </a>
                    </li>
                  </ul>
                </div>
                <?php
                  if($_POST && isset($_POST["lcscs"])){
                    $valid = strip_tags(trim($_POST["lcscs"]));
                    $db_host = strip_tags(trim($_POST["host"]));
                    $db_user = strip_tags(trim($_POST["user"]));
                    $db_pass = strip_tags(trim($_POST["pass"]));
                    $db_name = strip_tags(trim($_POST["name"]));
                    // Let's import the sql file into the given database
                    if(!empty($db_host)){
                      $con = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
                      if(mysqli_connect_errno()){ ?>
                        <form action="index.php?step=1" method="POST">
                          <div class='notification is-danger'>Failed to connect to MySQL: <?php echo mysqli_connect_error(); ?></div>
                          <input type="hidden" name="lcscs" id="lcscs" value="<?php echo $valid; ?>">
                          <div class="field">
                            <label class="label">Database Host</label>
                            <div class="control">
                              <input class="input" type="text" id="host" placeholder="enter your database host" name="host" required>
                            </div>
                          </div>
                          <div class="field">
                            <label class="label">Database Username</label>
                            <div class="control">
                              <input class="input" type="text" id="user" placeholder="enter your database username" name="user" required>
                            </div>
                          </div>
                          <div class="field">
                            <label class="label">Database Password</label>
                            <div class="control">
                              <input class="input" type="text" id="pass" placeholder="enter your database password" name="pass">
                            </div>
                          </div>
                          <div class="field">
                            <label class="label">Database Name</label>
                            <div class="control">
                              <input class="input" type="text" id="name" placeholder="enter your database name" name="name" required>
                            </div>
                          </div>
                          <div style='text-align: right;'>
                            <button type="submit" class="button is-link">Import</button>
                          </div>
                        </form><?php
                        exit;
                      }
                      $templine = '';
                      $lines = file($database_dump_file);
                      foreach($lines as $line){
                        if(substr($line, 0, 2) == '--' || $line == '')
                          continue;
                        $templine .= $line;
                        $query = false;
                        if(substr(trim($line), -1, 1) == ';'){
                          $query = mysqli_query($con, $templine);
                          $templine = '';
                        }
                      } ?>
                    <form action="index.php?step=2" method="POST">
                      <div class='notification is-success'>Database was successfully imported.</div>
                      <input type="hidden" name="dbscs" id="dbscs" value="true">
                      <div style='text-align: right;'>
                        <button type="submit" class="button is-link">Next</button>
                      </div>
                    </form><?php
                  }else{ ?>
                    <form action="index.php?step=1" method="POST">
                      <input type="hidden" name="lcscs" id="lcscs" value="<?php echo $valid; ?>">
                      <div class="field">
                        <label class="label">Database Host</label>
                        <div class="control">
                          <input class="input" type="text" id="host" placeholder="enter your database host" name="host" required>
                        </div>
                      </div>
                      <div class="field">
                        <label class="label">Database Username</label>
                        <div class="control">
                          <input class="input" type="text" id="user" placeholder="enter your database username" name="user" required>
                        </div>
                      </div>
                      <div class="field">
                        <label class="label">Database Password</label>
                        <div class="control">
                          <input class="input" type="text" id="pass" placeholder="enter your database password" name="pass">
                        </div>
                      </div>
                      <div class="field">
                        <label class="label">Database Name</label>
                        <div class="control">
                          <input class="input" type="text" id="name" placeholder="enter your database name" name="name" required>
                        </div>
                      </div>
                      <div style='text-align: right;'>
                        <button type="submit" class="button is-link">Import</button>
                      </div>
                    </form><?php
                } 
              }else{ ?>
                <div class='notification is-danger'>Sorry, something went wrong.</div><?php
              }
              break;
            case "2": ?>
              <div class="tabs is-fullwidth">
                <ul>
                  <li>
                    <a>
                      <span><i class="fa fa-check-circle"></i> Requirements</span>
                    </a>
                  </li>
                  <li>
                    <a>
                      <span><i class="fa fa-check-circle"></i> Verify</span>
                    </a>
                  </li>
                  <li>
                    <a>
                      <span><i class="fa fa-check-circle"></i> Database</span>
                    </a>
                  </li>
                  <li class="is-active">
                    <a>
                      <span><b>Finish</b></span>
                    </a>
                  </li>
                </ul>
              </div>
              <?php
              if($_POST && isset($_POST["dbscs"])){
                $valid = $_POST["dbscs"];
                ?>
                <center>
                  <p><strong>LicenseBox is successfully installed.</strong></p><br>
                  <p>You can now login using your email or username: <strong>admin</strong> and default password: <strong>admin1234</strong></p><br><strong>
                  <p><a class='button is-link' href='../'>Login</a></p></strong>
                  <br>
                  <p class='help has-text-grey'>The first thing you should do is change your account details.</p>
                </center>
                <?php
              }else{ ?>
                <div class='notification is-danger'>Sorry, something went wrong.</div><?php
              } 
            break;
          } ?>
        </div>
      </div>
    </div>
  </div>
  <div class="content has-text-centered">
    <p>Copyright <?php echo date('Y'); ?> CCCP, All rights reserved.</p><br>
  </div>
</body>
</html>