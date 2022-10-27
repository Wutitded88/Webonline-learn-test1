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
<style>

.p_gl {
    padding-top: 10px;
}
</style>
  <?php include("master/MasterPages.php"); ?>
</head>
<body data-rsssl=1 class="home blog">
  <input type="hidden" id="ip" value="<?=get_client_ip()?>">
<!--   
  <div id="topbar" class="fxx">
    <div class="tt_btn_close">
      <i class="fa fa-times"></i>
    </div>
    <div class="tt_btn_open">
      <i class="fa fa-plus"></i>
    </div>
    <a class="tt_img_fixed" target="_blank" href="<?=$_CONFIG['urlline']?>">
      <img src="images/qr-line/qrcode.png"/>
      <span class="tt_tx_line"> LINE : <?=$_CONFIG['lineid']?></span>
    </a>
  </div>

  <div id="topbar2" class="fxx">
    <div class="tt_btn_close2">
      <i class="fa fa-times"></i>
    </div>
    <div class="tt_btn_open2">
      <i class="fa fa-plus"></i>
    </div>

    <a class="tt_l mb-5 tt_img_fixed2" target="_blank" href="#">
      <img src="<?=base_url()?>/images/logoslide/pg.png"/>
    </a>
    <a class="tt_l mb-5 tt_img_fixed2" target="_blank" href="#">
      <img src="<?=base_url()?>/images/logoslide/sa.jpg"/>
    </a>
    <a class="tt_l mb-5 tt_img_fixed2" target="_blank" href="#">
      <img src="<?=base_url()?>/images/logoslide/sexy.jpg"/>
    </a>
    <a class="tt_l mb-5 tt_img_fixed2" target="_blank" href="#">
      <img src="<?=base_url()?>/images/logoslide/wm.png"/>
    </a>
  </div>
-->
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
	  <div style="padding-top:10px">
	<img src="<?php echo $row_u['logo']; ?>" width="250">
</div>
        <h1>เข้าสู่ระบบ <?=$_CONFIG['domain_name']?></h1>
      </div>
      <div class="tt_l tt_full tt_content bg">
        <div class="tt_l tt_full">
          <div class="thaitheme_read">
            <div class="tt_l tt_full fr_login fr_login_form" id="fr_login_form">
              <div class="fr_center">
                <div class="tt_l fr_input">
				<p class="textlogin"><i class="fad fa-mobile"></i> เบอร์โทรศัพท์</p>
                  <input id="username" class="tt_fr" maxlength="10" type="text" placeholder="กรอกเบอร์โทรศัพท์" onKeyPress="handle(event)" />
				 <p class="textlogin"><i class="fad fa-unlock-alt"></i> รหัสผ่าน</p>
                  <input id="password" class="tt_fr" maxlength="24" type="password" placeholder="กรอกรหัสผ่าน" onKeyPress="handle(event)" />
                </div>
                <button class="button1" id="btn_login" type="button">เข้าสู่ระบบ</button>
				<p class="textlogin" align="center">ต้องการสมัครสมาชิกใช่ไหม? <a style="color:#1472ff;" href="<?=base_url()?>/register">สมัครสมาชิก</a></p>
              </div>
            </div>
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

<script type="text/javascript">
  function handle(e)
  {
      if(e.keyCode === 13)
      {
          e.preventDefault();
          $('#btn_login').click();
      }
  }
</script>
