<?php
require_once 'api/config.php';
if(!empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './wallet';
        </SCRIPT>";
  exit();
}
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
</head>
<body data-rsssl=1 class="home blog">
 

  <!-- top logo add line for mobile
  <div class="tt-l tt-full head_mc">
    <div class="tt_l h-linem">
      <a href="<?=$_CONFIG['urlline']?>" target="_blank">
        <img src="<?=base_url()?>/images/qr-line/button-lineadd.jpg"/>
      </a>
    </div>
  </div> -->
  <!-- top logo add line for mobile -->

  <!-- top-menu -->
  <?php include("partials/_top-menu.php"); ?>
  <!-- top-menu -->
 

  <div class="wrapper">
    <div class="tt_l tt_full p_gl">
      <div id="ct_from" class="tt_l tt_full title_page">
	  <div style="padding-top:50px">
	<img src="<?php echo $row_u['logo']; ?>" width="300">
</div>
        <h1>สมัครสมาชิก</h1>
      </div>
 

        <div class="tt_l fr_login" style="background:#000000e6;">
          <div class="fr_center">
            <div class="tt_l tt_full fr_h1" style="color:#FFFFFF;">สมัครสมาชิกเพื่อเข้าเล่น <?=$_CONFIG['domain_name']?></div>
            <form class="tt_l tt_full fr_re">
              <?php
              if(isset($_GET["aff"]) && $_GET["aff"] != "")
              {
              ?>
            
           		<p class="textlogin0"><i class="fad fa-mobile"></i> รหัสเพื่อน</p>
                <input class="tt_fr"  type="text" readonly placeholder="รหัสเพื่อน" value="<?=$_GET["aff"]?>" />
           
              <?php
              }
              else
              {
              ?>
              
                <!-- <p class="textlogin0"> รหัสเพื่อน</p>
                <input class="tt_fr" style="color:#00CC00;"  type="text" readonly placeholder="รหัสเพื่อน" value="สมัครโดยไม่มีคนแนะนำ" /> -->
             
              <?php
              }
              ?>
 				<p class="textlogin0"><i class="fad fa-mobile"></i> เบอร์โทรศัพท์</p>
                <input required="required" class="tt_fr" minlength="10" maxlength="10" id="tel" type="text" placeholder="เบอร์โทรศัพท์" size="10" />
     
 				<p class="textlogin0"><i class="fad fa-key"></i> รหัสผ่าน (ตั้งรหัสผ่าน 8 ตัวขึ้นไป เช่น Aa123456)</p>
 
				<p class="badge badge-warning textlogin2"><i class="fad fa-exclamation-triangle"></i> ตัวอักษรภาษาอังกฤษพิมพ์ใหญ่อย่างน้อย 1 ตัวเพื่อความปลอดภัย</p>
                <input required="required" class="tt_fr" id="password" type="password" maxlength="24" placeholder="ตั้งรหัสผ่าน 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)" />
 
 				<p class="textlogin0"><i class="fad fa-file-signature"></i> ชื่อ นามสกุล</p>
				<p class="badge badge-warning textlogin2"><i class="fad fa-exclamation-triangle"></i> ชื่อบัญชีฝาก ต้องตรงกับบัญชีถอน ต้องภาษาไทยเท่านั้น ไม่เช่นนั้นจะถอนเงินไม่ได้ <br> เว้นวรรคระหว่างชื่อและนามสกุลให้ถูกต้อง เช่น ธงชัย ใจรัก</p>

 
                <input required="required" class="tt_fr" id="firstname" maxlength="255" placeholder="ชื่อ นามสกุล" />
             
      
 				<p class="textlogin0"><i class="fad fa-university"></i> บัญชีธนาคารของท่าน</p>
                <select required="required" class="fr_sc" id="bank">
                  <?php
                  $bank_tb = dd_q("SELECT * FROM bank_tb");
                  while($row = $bank_tb->fetch(PDO::FETCH_ASSOC))
                  {
                  ?>
                  <option value="<?=$row['b_short_name']?>:<?=$row['b_official_name_th']?>"><?=$row['b_official_name_th']?></option>
                  <?php
                  }
                  ?>
                </select>
          
 				<p class="textlogin0"><i class="fad fa-money-check-alt"></i> เลขบัญชีของท่าน</p>
                <input  class="tt_fr" required="required" id="acc_no" type="text" maxlength="15" placeholder="เลขบัญชีธนาคารของท่าน" />
 				<p class="textlogin0"><i class="fab fa-line"></i> LINE ID</p>
                <input  class="tt_fr" required="required" id="lineid" type="text" maxlength="50" placeholder="LINE ID (ถ้าไม่มีใส่เบอร์โทร)" />
 				<p class="textlogin0"><i class="fad fa-question-circle"></i> รู้จักเราจากช่องทางไหน</p>
                <select class="fr_sc" required="required" id="refer">
                  <option value="">รู้จักเราจากที่ไหน</option>
                  <option value="1:Google">Google</option>
                  <option value="2:Facebook">Facebook</option>
				  <option value="3:Tiktok">Tiktok</option>
                  <option value="4:SMS">SMS</option>
                  <option value="5:เพื่อนแนะนำมา">เพื่อนแนะนำมา</option>
                  <option value="6:พนักงานชวน">พนักงานชวน</option>
                  <option value="7:อื่นๆ">อื่นๆ</option>
                </select>

                <input type="hidden" id="ip" value="<?=get_client_ip()?>">
                <input type="hidden" id="url" value="<?=urlencode((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")?>">
                <?php
                if(isset($_GET["aff"]) && $_GET["aff"] != "")
                {
                  $aff = $_GET["aff"];
                  $q_u = dd_q('SELECT * FROM user_tb WHERE u_id = ?', [$aff]);
                  if ($q_u->rowCount() > 0)
                  {
                    $row_u = $q_u->fetch(PDO::FETCH_ASSOC);
                    $aff = $row_u['u_user'];

                    $q_aff_count = dd_q('SELECT * FROM aff_count WHERE aff_u_user = ?', [$row_u['u_user']]);
                    if ($q_aff_count->rowCount() > 0)
                    {
                      $row_aff_count = $q_aff_count->fetch(PDO::FETCH_ASSOC);
                      $count = $row_aff_count["aff_count"] + 1;
                      dd_q('UPDATE aff_count SET aff_count=? WHERE aff_u_user=?', [
                        $count,
                        $row_u['u_user']
                      ]);
                    }
                    else
                    {
                      dd_q('INSERT INTO aff_count (aff_u_user, aff_count) VALUES (?, ?)', [
                        $row_u['u_user'],
                        1
                      ]);
                    }
                  }
                  else
                  {
                    $aff = "";
                  ?>
                  <div id="banner-12345">
                    <h3>ไม่พบชื่อเพื่อน!</h3>
                    <span style="font-size: 20px;">
                      กรุณาติดต่อแอดมิน เนื่องจากไม่พบข้อมูลเพื่อนที่แนะนำ
                    </span>
                    <br>
                    <span style="font-size: 20px;">
                      หรือ <a href="<?=base_url()?>/register" target="_blank"><span style="color: red;">>>คลิกที่นี่<<</span></a> เพื่อสมัครแบบไม่มีคนแนะนำ
                    </span>
                  </div>
                  <style>
                    #banner-12345 {
                      display: none;
                      position: fixed; /* Stay in place */
                      z-index: 999999999; /* Sit on top */
                      left: 0;
                      right: 0;
                      top: 0;
                      bottom: 0;
                      padding-top: 150px;
                      width: 100%; /* Full width */
                      height: 100%; /* Full height */
                      overflow: auto; /* Enable scroll if needed */
                      opacity: 1;
                      background: #000;
                      color: #fff;
                      text-align: center;
                      font-weight: bold;
                    }
                  </style>
                  <script type="text/javascript">
                    document.getElementById('banner-12345').style.display='block';
                  </script>
                  <?php
                  }
                }
                else if(isset($_GET["aff"]) && $_GET["aff"] == "")
                {
                  $aff = "";
                  ?>
                  <div id="banner-12345">
                    <h3>ไม่พบชื่อเพื่อน!</h3>
                    <span style="font-size: 20px;">
                      กรุณาติดต่อแอดมิน เนื่องจากไม่พบข้อมูลเพื่อนที่แนะนำ
                    </span>
                    <br>
                    <span style="font-size: 20px;">
                      หรือ <a href="<?=base_url()?>/register" target="_blank"><span style="color: red;">>>คลิกที่นี่<<</span></a> เพื่อสมัครแบบไม่มีคนแนะนำ
                    </span>
                  </div>
                  <style>
                    #banner-12345 {
                      display: none;
                      position: fixed; /* Stay in place */
                      z-index: 999999999; /* Sit on top */
                      left: 0;
                      right: 0;
                      top: 0;
                      bottom: 0;
                      padding-top: 150px;
                      width: 100%; /* Full width */
                      height: 100%; /* Full height */
                      overflow: auto; /* Enable scroll if needed */
                      opacity: 1;
                      background: #000;
                      color: #fff;
                      text-align: center;
                      font-weight: bold;
                    }
                  </style>
                  <script type="text/javascript">
                    document.getElementById('banner-12345').style.display='block';
                  </script>
                  <?php
                }
                else
                {
                  $aff = "";
                }
                ?>
                <input type="hidden" id="aff" value="<?=$aff?>">
        

              <div class="tt_l tt_full" style="margin-bottom: 5px;">
                <div class="g-recaptcha" data-sitekey="<?=$_CONFIG['sitekey']?>"></div>
              </div>
              <button class="button1" id="btn_register" type="button">สมัครสมาชิก</button>
			  <p class="textlogin0" align="center">ต้องการเข้าสู่ระบบใช่ไหม? <a style="color:#1472ff;" href="<?=base_url()?>/login">ข้าสู่ระบบ</a></p>
            </form>
			
          </div>
        </div>

      </div>
    </div>
  </div>

 <script type="text/javascript" src="<?=base_url()?>/themes/v2/js/jquery-1.10.2.minc619.js?v=1.0"></script>
<script type="text/javascript" src="<?=base_url()?>/themes/v2/js/owl.carousel.min2048.js?v=1.5"></script>
<script type="text/javascript" src="<?=base_url()?>/themes/v2/js/min-v3.js"></script>
<script type="text/javascript" src="<?=base_url()?>/themes/v2/js/animate.min2d4c.js?v=2.3"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.all.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.8.1/parsley.min.js" type="33c59bba0d8725c03bd010e9-text/javascript"></script>

<div class="modal fade" id="copyClipboard" tabindex="-1" role="dialog" style="display: none; margin-top: 60px;" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <i class="fas fa-check-square text-success fa-fw"></i> คัดลอกสำเร็จ 
      </div>
    </div>
  </div>
</div>

<div id="loading" style="display: none">
   <div class="loading-content">
      <div class="sk-wave">
         <div class="sk-rect sk-rect1"></div>
         <div class="sk-rect sk-rect2"></div>
         <div class="sk-rect sk-rect3"></div>
         <div class="sk-rect sk-rect4"></div>
         <div class="sk-rect sk-rect5"></div>
      </div>
   </div>
</div>

<script src="<?=base_url()?>/js/clipboard.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>/js/script.inc.51yua5qtehpwryzsgxoh.js" type="text/javascript"></script>
<script src="<?=base_url()?>/js/script.inc.25fsyrjghnf896dhjsrtujg.js" type="text/javascript"></script>
<script src="<?=base_url()?>/js/cs.js" type="text/javascript"></script>

</body>
</html>
