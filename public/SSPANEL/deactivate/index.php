<?php
require_once '../includes/lb_helper.php'; // Include LicenseBox external/client api helper file
$api = new LicenseBoxAPI(); // Initialize a new LicenseBoxAPI object
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>MyScript - Deactivator</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css"/>
    <style type="text/css">
      body, html {
        background: #F7F7F7;
      }
    </style>
  </head>
  <body>
    <div class="container"> 
      <div class="section" >
        <div class="column is-6 is-offset-3">
          <center>
            <h1 class="title" style="padding-top: 20px">停用激活</h1><br>
          </center>
          <div class="box">
            <article class="message is-success">
              <div class="message-body">
                单机按钮将会停用激活码！此功能谨慎使用！停用后不可以再次使用原激活码激活！
				如需激活码转让请联系：管理员
              </div>
            </article>
            <?php
              if(!empty($_POST)){
                /* We can use LicenseBoxAPI's deactivate_license() function for deactivating the installed license, the local license file will be also deleted.*/
                $deactivate_response = $api->deactivate_license();
                if(empty($deactivate_response)){
                  $msg='Server is unavailable.';
                }else{
                  $msg=$deactivate_response['message'];
                }
                if($deactivate_response['status'] != true){ ?>
                  <form action="index.php" method="POST">
                    <div class="notification is-danger"><?php echo ucfirst($msg); ?></div>
                    <input type="hidden" name="something">
                    <center>
                      <button type="submit" class="button is-warning">停用 激活状态</button>
                    </center>
                  </form><?php
                }else{ ?>
                  <div class="notification is-success"><?php echo ucfirst($msg); ?></div><?php 
                }
              }else{ ?>
                <form action="index.php" method="POST">
                  <input type="hidden" name="something">
                  <center>
                    <button type="submit" class="button is-warning">停用 激活状态</button>
                  </center>
                </form><?php 
              } ?>
          </div>
        </div>
      </div>
    </div>
    <div class="content has-text-centered">
      <p>Copyright <?php echo date('Y'); ?> Company, All rights reserved.</p><br>
    </div>
  </body>
</html>