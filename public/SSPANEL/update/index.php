<?php 
header('Cache-Control: no-cache'); 
require_once '../includes/ssp_helper.php'; // Include LicenseBox external/client API helper file
$api = new LicenseBoxAPI(); // Initialize a new LicenseBoxAPI object
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>MyScript - Updater</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css"/>
    <style type="text/css">
      body, html {
        background: #F7F7F7;
      }
    </style>
  </head>
  <body>
    <?php
      $update_data = $api->check_update(); // 首先让我们检查是否有可用的更新
    ?>
    <div class="container"> 
      <div class="section">
        <div class="column is-6 is-offset-3">
          <center>
            <h1 class="title" style="padding-top: 20px;">更新程序</h1><br>
          </center>
          <div class="box">
            <?php if($update_data['status']){ ?>
              <article class="message is-success">
                <div class="message-body">
                  请在升级之前备份数据库和脚本文件。
                </div>
              </article>
            <?php } ?>
            <p class="subtitle is-5" style="margin-bottom: 0px">
              <?php 
                echo $update_data['message']; // 您也可以在此处显示更新通知/摘要。
              ?>
            </p>
            <div class='content'>
              <?php if($update_data['status']){ ?>
                <p><?php echo $update_data['changelog']; ?></p><?php 
                $update_id = null;
                $has_sql = null;
                $version = null;
                if(!empty($_POST['update_id'])){
                  $update_id = strip_tags(trim($_POST["update_id"]));
                  $has_sql = strip_tags(trim($_POST["has_sql"]));
                  $version = strip_tags(trim($_POST["version"]));
                  echo '<progress id="prog" value="0" max="100.0" class="progress is-success" style="margin-bottom: 10px;"></progress>';
                  // 一旦我们有了更新id，我们就可以使用LicenseBoxAPI的download_update（）函数来下载和安装更新。
                  $api->download_update($_POST['update_id'], $_POST['has_sql'], $_POST['version']);
                }else{ ?>
                  <form action="index.php" method="POST">
                    <input type="hidden" class="form-control" value="<?php echo $update_data['update_id']; ?>" name="update_id">
                    <input type="hidden" class="form-control" value="<?php echo $update_data['has_sql']; ?>" name="has_sql">
                    <input type="hidden" class="form-control" value="<?php echo $update_data['version']; ?>" name="version">
                    <center>
                      <button type="submit" class="button is-link">下载并安装更新</button>
                    </center>
                  </form><?php 
                }
              } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="content has-text-centered">
      <p>Copyright <?php echo date('Y'); ?> Company, All rights reserved.</p><br>
    </div>
  </body>
</html>