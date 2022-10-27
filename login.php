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


<?php include('theme-login/header.php'); ?>
<link href="<?php echo $row_u['logo']; ?>?v=1.5" rel="shortcut icon" />
<title>เข้าสู่ระบบ | <?php echo $row_u['title']; ?></title>
<meta name="description"  content="<?php echo $row_u['description']; ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo $row_u['title']; ?>" />
<meta property="og:description" content="<?php echo $row_u['description']; ?>" />
<meta property="og:url" content="<?=base_url()?>" />
<meta property="og:site_name" content="<?php echo $row_u['namesite']; ?>" />
<meta property="og:image" content="<?php echo $row_u['logo']; ?>" />
<meta property="og:image:secure_url" content="<?php echo $row_u['logo']; ?>" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?php echo $row_u['title']; ?>" />
<meta name="twitter:description" content="<?php echo $row_u['description']; ?>" />
<meta name="twitter:image" content="<?php echo $row_u['logo']; ?>" />
<meta name="keywords" content="<?php echo $row_u['keyword']; ?>" />
 
<section class="loginsection">
<input type="hidden" id="ip" value="<?=get_client_ip()?>">
   <div class="containlogin loginhi01 pb-5">
      <div class="headerlogo">
          <img src="<?php echo $row_u['logo']; ?>">
      </div>
      <!--<div class="headertext namefont">
         LOGOTEXT168
      </div>-->
      <div class="containinput px-2 boxall">
         <div class="headertaplgre">
          <table>
             <tr>
                <td>
                  <h4 class="textlogin"><i class="fad fa-sign-in-alt cutcolor"></i>&nbsp; <span>เข้าสู่ระบบ</span></h4>
                  <hr class="x-hr-border-glow-tabmain active">
               </td>
               <td>
                  <h4 class="textlogin" onclick="location.href='register.php'"><i class="fad fa-layer-plus cutcolor"></i>&nbsp; <span>สมัครสมาชิก</span></h4>
                  <hr class="x-hr-border-glow-tabmain">
               </td>
            </tr>
         </table>
      </div>
	  <?php include("partials/_top-menu.php"); ?>
	  <div id="ct_from" >
		  <div class="containinputlogin slideto" >
	  		<div  id="fr_login_form">
			 <div class="porela" style="margin-top: 30px;">
				<label for="exampleInputEmail1">เบอร์โทรศัพท์</label>
				<div class="iconlogin">
				   <i class="fad fa-user" style="font-size: 20px;"></i>
				</div>
				<input id="username" class="loginform01"   aria-describedby="emailHelp" maxlength="10" type="text" placeholder="กรอกเบอร์โทรศัพท์" onKeyPress="handle(event)">
			 </div>
			 <div class="porela" style="margin-top: 20px;">
				<label for="exampleInputEmail1">รหัสผ่าน</label>
				<div class="iconlogin">
				   <i class="fad fa-lock" style="font-size: 20px;"></i>
				</div>
				<input id="password" class="loginform01" aria-describedby="emailHelp" maxlength="24" type="password" placeholder="กรอกรหัสผ่าน" onKeyPress="handle(event)">
				<button class="btneyepass">   
				  <i class="fad fa-eye-slash" id="bteye"></i>
			   </button>
			</div>
		</div>
		</div>
        <div style="text-align: center; margin-top: 60px;">
         <button class="mcolor colorbtn01" id="btn_login" type="button">เข้าสู่ระบบ</button>
		 
      </div>
      <div  onclick="location.href='register.php'" class="needregister">ต้องการสมัครสมาชิกใช่ไหม? <span class="cutcolor">สมัครสมาชิก</span></div>
	  <?php if($row_u['sms_active'] == 1) { ?>
	  <div  onclick="location.href='forgetpassword.php'" class="needregister">ลืมรหัสผ่านใช่หรือไม่? <span class="cutcolor">ลืมรหัสผ่าน</span></div>
	  <?php } ?>
   </div>
</div>
</div>
<div class="pdhilogin01 px-1"></div>
</section>


<script type="text/javascript" src="<?=base_url()?>/themes/v2/js/jquery-1.10.2.minc619.js?v=1.0"></script>
<script type="text/javascript" src="<?=base_url()?>/themes/v2/js/owl.carousel.min2048.js?v=1.5"></script>
<script type="text/javascript" src="<?=base_url()?>/themes/v2/js/min-v3.js"></script>
<script type="text/javascript" src="<?=base_url()?>/themes/v2/js/animate.min2d4c.js?v=2.3"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.all.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.8.1/parsley.min.js" type="33c59bba0d8725c03bd010e9-text/javascript"></script>



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
<?php include('theme-login/footer.php'); ?>