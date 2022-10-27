<?php
require_once 'api/config.php';
if(empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './login';
        </SCRIPT>";
  exit();
}

$q_website_tb = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
$row_website_tb = $q_website_tb->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
  <?php include("master/MasterPages.php"); ?>
   <style>
 .textlogin0 {
	padding-bottom: 8px;
    font-size: 24px;
}
 </style>
 <link href="https://fonts.googleapis.com/css?family=Eater" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
 
</head>
<body data-rsssl=1 class="home blog">
  <input type="hidden" id="username" value="<?=get_session()?>">
  <input type="hidden" id="ip" value="<?=get_client_ip()?>">

 
 <div class=hero>

  <div class=hero_bg></div>
  <div class=hero_fg></div>


</div>

  <!-- top logo add line for mobile 
  <div class="tt-l tt-full head_mc">
    <div class="tt_l h-linem">
      <a href="<?=$_CONFIG['urlline']?>" target="_blank">
        <img src="<?=base_url()?>/images/qr-line/button-lineadd.jpg"/>
      </a>
    </div>
  </div>-->
  <!-- top logo add line for mobile -->

  <!-- top-menu -->
  <?php include("partials/_top-menu.php"); ?>
  <!-- top-menu -->

  <!-- slide -->
  <?php include("partials/_slide.php"); ?>
  <!-- slide -->
  
<div class="x-ranking-entrance">
    <a href="/ranking" class="-link-wrapper">
        <div class="-image-wrapper">
            <img class="img-fluid -ic-right-star-rank" src="https://ezslot.bet/build/web/ez-bet/img/ez-slot-ic-star-rank-animation.png" alt="Aqua">
        </div>
        <div class="-text-wrapper">อันดับ <br> ผู้ชนะ</div>
    </a>
</div>

  <div class="wrapper">
 
    <div class="tt_l tt_full p_gl">
      <div id="ct_from" class="tt_l tt_full title_page">
        <h1><span><?=$_CONFIG['domain_name']?> ยืนดีต้อนรับ</span></h1>
      </div>
      <div class="tt_l tt_full tt_content bg">
        <div class="tt_l tt_full fr_login center" id="fr_login_user_form">
          <div class="fr_ficenter">

            <div class="tt_l tt_full user-box" style="animation: transitionIn 0.75s;">
              <h3 style="float: center; width: 100%;"><?=get_wallet("u_user")?> (<?=$_CONFIG['start_prefix']?><?=get_wallet("u_agent_id")?>)</h3>
            </div>

            <div class="tt_l fr2x">
               
<!--
              <?php
              if(get_wallet("u_truewallet_id") == "")
              {
              ?>
              <form class="tt_l tt_full fr_re">
                <div class="tt_l tt_full fr_sec">
                  <div class="input-group">
                    <label  class="tt_l fr_tx2x">Wallet ID</label>
                    <input class="tt_bk" type="text" maxlength="20" placeholder="กรุณาระบุ Wallet ID (สำหรับคนที่ตั้งค่า Wallet ID ใน App True Wallet เท่านั้น)" id="txt_truewallet_id" />
                  </div>
                </div>

                <div class="form-group">
                  <button type="button" id="btn_updatewallet_id" onClick="loadingPage();" class="btn btn-lg btn-block btn-dark-green ml-0"><i class="fas fa-wallet"></i> อัพเดท Wallet ID</button>
                </div>
              </form>
              <?php
              }
              ?>
-->

      <div class="containmoney warp-up" style="animation: transitionIn 0.80s;">
         <table width="100%">
            <tbody>
              <tr>
                <td colspan="2" style="padding-left: 20px;"><div class="tt_l tt_full fr_name">
                <span>สวัสดี, <span id="user"><?=get_wallet("u_fname")?></span>
              </div></td>
                </tr>
              <tr>
                <td style="padding-left: 20px;">&nbsp;</td>
                <td style="text-align: right;">&nbsp;</td>
              </tr>
              <tr>
               <td width="50%" style="padding-left: 20px;">
                  <i onClick="opentab(event, 'section01')" id="opensection01" style="cursor: pointer;" class="fal fa-wallet"></i>               </td>
               <td width="50%" style="text-align: right;">
                  <span style="font-size: 17px; color: #fff;"><i class="fad fa-coins"></i> ยอดเครดิตของคุณ </span>
                  <br>
                  <span id="credit_total_balance" style="color:#ecc568; font-size: 35px;">Loading...</span>  </td>
            </tr>
         </tbody></table>
         <hr style="margin: 10px; border-top:1px solid #68635c;">
         <div style="padding-left: 20px;margin-top: 10px; margin-bottom: 15px;">
		 <?php
          $t_over = "0.00";
          $max_wd = "ไม่จำกัด";
          $last_pro = "ไม่รับโบนัส";
          $wd_max = false;
          $q_transfer = dd_q('SELECT * FROM transfergame_tb WHERE t_user = ? ORDER BY t_id DESC LIMIT 1', [get_session()]);
          if ($q_transfer->rowCount() > 0)
          {
            $row_transfer = $q_transfer->fetch(PDO::FETCH_ASSOC);
            if($row_transfer["t_active"] == "Y")
            {
              if($row_transfer["t_withdraw_max"] > 0)
              {
                $max_wd = $row_transfer["t_withdraw_max"];
              }
              else
              {
                $max_wd = "ไม่จำกัด";
              }
              $last_pro = $row_transfer["t_promotion_title"];
              if($row_transfer["t_promotion_id"] == "3" && strpos($row_transfer["t_promotion_title"], 'ฟรีเครดิต') !== false)
              {
                $wd_max = true;
              }
              $t_over = $row_transfer["t_turnover"];
            }
          }
          ?>
            <i class="fad fa-gift" style="color:#fad275;"></i><a style="color: #f8f8f8;"> โปรโมชั่นล่าสุดที่รับ<span style="color: #cead5e;">  <?=$last_pro?></span></a>
         </div>
		 <table width="100%">
            <tbody><tr>
               <td width="50%" style="text-align: center;">
 
                  <a href="./affiliate" onClick="loadingPage();"><button class="btnfriend mcolor active" href="./affiliate" onClick="loadingPage();"><i class="fal fa-users-medical"></i> แนะนำเพื่อน</button></a>
               </td>
               <td width="50%" style="text-align: center;">
                  <a href="./winloss" onClick="loadingPage();"><button class="btncommis bkcolor active" onClick="opentab(event, 'comis')"><i class="fal fa-hands-usd"></i> คืนยอดเสีย</button></a>
               </td>
            </tr>
         </tbody></table>
              </div>
              <div class="tt_l tt_full fr_bkb" style="animation: transitionIn 0.90s;"><?=get_wallet("u_bank_name")?> | เลขบัญชี: <?=get_wallet("u_bank_number")?></div>

              <a class="u-btn btn-play btn-block hvr-buzz"  style="cursor: pointer;" href="<?=base_url()?>/gambling" onClick="loadingPage();">
                <div class="tt_l tt_full b_link_game" style="background: linear-gradient(to bottom, #dbc500 0%, #d49500 44%, #c46300 100%); box-shadow: 1px 1px 20px rgb(209 142 0);">
 
                  <i class="fad fa-play-circle"></i> เข้าเล่นเกมส์
                </div>
              </a>
<!--
              <button type="button" id="btn_withdraw_ag" onClick="loadingPage();" class="tt_l tt_ful fr_submit_bk" style="margin-top:10px;">
                <i class="fas fa-hand-holding-usd"></i> ดึงเงินออกจาก AG
              </button>
-->
              <div class="tt_l tt_full fr_link_lx fr_link_4x" style="animation: transitionIn 1.10s;">
                <a href="./deposit" onClick="loadingPage();"><i class="fad fa-arrow-down"> </i> <span>ฝากเงิน</span></a>
                <a href="./withdrawal" onClick="loadingPage();"><i class="fad fa-hand-holding-usd"></i><span>ถอนเงิน</span></a>
                <?php
                if($row_website_tb['status_winloss'] == "0")
                {
                ?>
                <a onClick="alertMSG('ฟังชั่นนี้ยังไม่เปิดใช้งาน', 'error', 'ผิดพลาด');" ><i class="fas fa-chart-bar"></i> <span>คืนยอดเสีย</span></a>
                <?php
                }
                else
                {
                ?>
                <a href="./winloss" onClick="loadingPage();"><i class="fas fa-chart-bar"></i> <span>คืนยอดเสีย</span></a>
                <?php
                }
                ?>
                <a href="./promotion" onClick="loadingPage();"><i class="fad fa-gift"></i> <span>รับโปรโมชั่น</span></a>
              </div>
              <div class="tt_l tt_full fr_link_2xx" style="animation: transitionIn 1.20s;">
                <?php
                if($row_website_tb['status_checkin'] == "0")
                {
                ?>
                <a onClick="alertMSG('ฟังชั่นนี้ยังไม่เปิดใช้งาน', 'error', 'ผิดพลาด');"><i class="fas fa-calendar-alt"></i> เช็คอินรายวัน</a>
                <?php
                }
                else
                {
                ?>
                <a href="./checkin" onClick="loadingPage();"><i class="fas fa-calendar-alt"></i> เช็คอินรายวัน</a>
                <?php
                }
                ?>
                <a href="./fixed-deposit" onClick="loadingPage();"><i class="fas fa-calendar-alt"></i> ฝากประจำ</a>
              </div>
              <div class="tt_l tt_full fr_link_2xx" style="animation: transitionIn 1.30s;">
                <?php
                if($row_website_tb['status_aff'] == "0")
                {
                ?>
                <a onClick="alertMSG('ฟังชั่นนี้ยังไม่เปิดใช้งาน', 'error', 'ผิดพลาด');"><i class="fad fa-users"></i> แนะนำเพื่อน</a>
                <?php
                }
                else
                {
                ?>
                <a href="./affiliate" onClick="loadingPage();"><i class="fad fa-users"></i> แนะนำเพื่อน</a>
                <?php
                }
                ?>

                <?php
                if($row_website_tb['status_freecredit'] == "0")
                {
                ?>
                <a onClick="alertMSG('ฟังชั่นนี้ยังไม่เปิดใช้งาน', 'error', 'ผิดพลาด');"><img src="./themes/v2/images/Baht-Refresh-2.png" style="opacity: 0.1;">  เติมเครดิตฟรี</a>
                <?php
                }
                else
                {
                ?>
                <a href="./freecredit" onClick="loadingPage();"><img src="./themes/v2/images/Baht-Refresh-2.png">  เติมเครดิตฟรี</a>
                <?php
                }
                ?>
              </div>
              <div class="tt_l tt_full fr_link_2x" style="animation: transitionIn 1.40s;">
                <a href="./historydeposit" onClick="loadingPage();"><i class="fad fa-indent"></i> ประวัติรับเงิน</a>
                <a href="./historywithdrawal" onClick="loadingPage();"><i class="fad fa-outdent"></i> ประวัติถอนเงิน</a>
              </div>
		 

              <!-- <a href="./affiliate" class="u-btn btn-play btn-block hvr-buzz" onclick="loadingPage();">
                <div class="tt_l tt_full b_logout_game">
                  <i class="fas fa-users"></i> <span> แนะนำเพื่อน รับค่าคอมทันที 20% ของยอดฝากแรกของเพื่อน</span>
                </div>
              </a> -->
              <a onClick="loadingPage();" href="system/logout" class="u-btn btn-play btn-block hvr-buzz">
                <div class="tt_l tt_full b_logout_game">
                  <i class="fad fa-sign-out-alt" onClick="loadingPage();"> </i><span> ออกจากระบบ</span>
                </div>
              </a>
              <form class="tt_l tt_full fr_re">
                
                   <hr>
                   <p align="center" class="textlogin0"><i class="fad fa-exchange"></i> เปลี่ยนรหัสผ่าน</p>
                   <p align="left" class="textlogin0">รหัสผ่านเก่า</p>
                   <input class="tt_fr" type="password" maxlength="24" placeholder="รหัสผ่านเก่า" id="oldpass" />
             

 
                    <p align="left" class="textlogin0">รหัสผ่านใหม่</p>
                    <input class="tt_fr" type="password" maxlength="24" placeholder="ตั้งรหัสผ่าน 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)" id="newpass" />
 
                	<p align="left" class="textlogin0">ยืนยันรหัสผ่านใหม่</p>
                    <input class="tt_fr" type="password" maxlength="24" placeholder="ตั้งรหัสผ่าน 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)" id="newpass1" />
             

                <div class="form-group">
                  <button type="button" id="send-mail" onClick="loadingPage();" class="btn btn-lg btn-block btn-dark-green ml-0"><i class="fas fa-key"></i> แก้ไขรหัสผ่าน</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- footer -->
  <?php include("partials/_footer.php"); ?>
  <!-- footer -->

</body>
</html>
