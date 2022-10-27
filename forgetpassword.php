<?php 
require_once 'api/config.php';
include('theme-login/header.php'); 
$q_website_tb = dd_q('SELECT * FROM website_tb WHERE id = ?', [1]);
$row_website_tb = $q_website_tb->fetch(PDO::FETCH_ASSOC);
?>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<link href="<?php echo $row_u['logo']; ?>?v=1.5" rel="shortcut icon" />
<title>สมัครสมาชิก | <?php echo $row_u['title']; ?></title>
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
   <div class="containlogin regishi01">
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
                     <h4 class="textlogin" ><i class="fad fa-layer-plus cutcolor"></i>&nbsp; <span>ลืมรหัสผ่าน</span></h4>
                     <hr class="x-hr-border-glow-tabmain active">
                  </td>
               </tr>
            </table>
         </div>
         <hr hidden class="x-hr-border-glow-register stepline">













<!-- Step1 -->
         <div class="stepre01 slideto" style="padding-bottom: 140px;">
            <div class="porela" style="margin-top: 30px;">
               <label for="s1_phone">เบอร์โทรศัพท์</label>
               <div class="iconlogin">
                  <i class="fad fa-user" style="font-size: 20px;"></i>
               </div>
               <input type="email" class=" loginform01" id="s1_phone" aria-describedby="emailHelp" placeholder="เบอร์โทรศัพท์">
            </div>
            <div class="btnofregister">
               <table style="width: 100%">
                  <tr>
                     <td class="tdbtnregister" style="width: 100%;">
                        <button type="button" id="btn-step1" class="btnnextregister mcolor">ถัดไป </button>
                     </td>
                  </tr>
               </table>
            </div>
            <div  onclick="location.href='login.php'" class="needregister">ต้องการเข้าสู่ระบบใช่ไหม? <span class="cutcolor">เข้าสู่ระบบ</span></div>
         </div>
<!-- Step1 -->















<!-- Step2 -->
         <div class="stepre02 slideto">
            <div class="porela" style="margin-top: 30px;">
               <label for="exampleInputEmail1">รหัสผ่านใหม่</label>
               <div class="iconlogin">
                  <i class="fad fa-user" style="font-size: 20px;"></i>
               </div>
               <input type="text"  class=" loginform01"  id="s2_password" aria-describedby="emailHelp" placeholder="รหัสผ่าน">
            </div>
            <div class="porela" style="margin-top: 10px;">
               <label for="exampleInputEmail1">ยืนยันรหัสผ่านใหม่</label>
               <div class="iconlogin">
                  <i class="fad fa-user" style="font-size: 20px;"></i>
               </div>
               <input type="text"   class=" loginform01" id="s2_c_password" aria-describedby="emailHelp" placeholder="ยืนยันรหัสผ่าน">
            </div>
            
            <?php
            if($row_website_tb['sms_active'] == "1")
            {
            ?>
            <div class="porela" style="margin-top: 20px;">
               <label for="exampleInputEmail1">OTP</label>
               <div class="iconlogin">
                  <i class="fad fa-user" style="font-size: 20px;"></i>
               </div>
               <input type="number" class=" loginform01" id="s2_otp" aria-describedby="emailHelp" placeholder="กรอก OTP">
            </div>
            <?php
            }
            else
            {
            ?>
            <div class="porela" style="margin-top: 10px;">
               <div class="g-recaptcha" data-sitekey="<?=$_CONFIG['sitekey']?>"></div>
            </div>
            <input type="hidden" id="s2_otp" value="">
            <?php
            }
            ?>
            <div class="btnofregister">
               <table style="width: 100%">
                  <tr>
                     <td class="tdbtnregister">
                        <button type="button" id="btn-back1" class="btnbackregister bkcolor">ก่อนหน้า</button>
                     </td>
                     <td class="tdbtnregister">
                        <button type="button" id="btn-step2" class="btnnextregister mcolor">ถัดไป </button>
                     </td>
                  </tr>
               </table>
               <input type="hidden" id="s2_phone">
               <input type="hidden" id="s2_ref">
               <input type="hidden" id="ip" value="<?=get_client_ip()?>">
            </div>
            <div  onclick="location.href='login.php'" class="needregister">ต้องการเข้าสู่ระบบใช่ไหม? <span class="cutcolor">เข้าสู่ระบบ</span></div>
         </div>
<!-- Step2 -->

<!-- Step03 Finish -->
         <div class="stepre04 slideto pb-5">
            <div style="text-align: center;margin-top: 20px;
               margin-bottom: 10px;">
               <i class="fad fa-shield-check checkfinish"></i>
            </div>
            <div class="finishregister">
               <span>ยินดีต้อนรับ</span>
            </div>
         </div>
<!-- Step03 Finish -->





      </div>
   </div>
   <div class="pdhiregis01 p-1"></div>
</section>






<?php include('theme-login/footer.php'); ?>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
   $(".stepre02").hide();
   $(".stepre04").hide();

   // go step01 to step2
   document.getElementById("btn-step1").onclick = function(){
      var formData = new FormData();
      formData.append('s1_phone', $("#s1_phone").val());
      formData.append('s', 's1');
      $.ajax({
         type: 'POST',
         url: 'system/forgetpassword',
         data:formData,
         contentType: false,
         processData: false,
      }).done(function(res){
         $(".stepre01").hide();
         $(".stepre02").show();
         $(".headstep1").removeClass("active");
         $(".headstep2").addClass("active");
         $("#s2_phone").val(res.message.phone);
         $("#s2_ref").val(res.message.ref);
      }).fail(function(jqXHR){
         res = jqXHR.responseJSON;
         Swal.fire({
            icon: 'error',
            title: 'ผิดพลาด',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
         });
         console.clear();
      });
   };
   // back step2 to step1
   document.getElementById("btn-back1").onclick = function(){
      $(".stepre02").hide();
      $(".stepre01").show();
      $(".headstep2").removeClass("active");
      $(".headstep1").addClass("active");
      $("#s2_phone").val('');
      $("#s2_ref").val('');
   };
   
   
    // go step02 to step3
    document.getElementById("btn-step2").onclick = function(){
      var formData = new FormData();
      formData.append('s2_password', $("#s2_password").val());
      formData.append('s2_c_password', $("#s2_c_password").val());
      formData.append('s2_otp', $("#s2_otp").val());
      formData.append('s2_phone', $("#s2_phone").val());
      formData.append('s2_ref', $("#s2_ref").val());
      formData.append('ip', $("#ip").val());
      formData.append('s', 's2');

      <?php
      if($row_website_tb['sms_active'] != "1")
      {
      ?>
      captcha = grecaptcha.getResponse();
      formData.append('captcha', captcha);
      <?php
      }
      ?>

      $.ajax({
         type: 'POST',
         url: 'system/forgetpassword',
         data:formData,
         contentType: false,
         processData: false,
      }).done(function(res){
         <?php
         if($row_website_tb['sms_active'] != "1")
         {
         ?>
         grecaptcha.reset();
         <?php
         }
         ?>
         $(".stepre02").hide();
         $(".needregister").hide();
         $(".stepre04").show();
         $(".headstep2").removeClass("active");
         $(".headstep4").addClass("active");
         setTimeout(function(){ location.href='login.php'; }, 2000);
      }).fail(function(jqXHR){
         res = jqXHR.responseJSON;
         <?php
         if($row_website_tb['sms_active'] != "1")
         {
         ?>
         grecaptcha.reset();
         <?php
         }
         ?>
         Swal.fire({
            icon: 'error',
            title: 'ผิดพลาด',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
         });
         console.clear();
      });
   };
   // back step3 to step2
   document.getElementById("btn-back2").onclick = function(){
      $(".stepre03").hide();
      $(".stepre02").show();
      $(".headstep3").removeClass("active");
      $(".headstep2").addClass("active");
   };
   
   $("#s2_register_channel_id").on('keyup change', function(){
      if ($('#s2_register_channel_id').val() == '7'){ 
        $('#friendinput').show();
     } else{
        $('#friendinput').hide();
     }  
   });
   
</script>