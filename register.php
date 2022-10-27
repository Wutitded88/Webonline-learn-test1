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
                     <h4 class="textlogin" onclick="location.href='login.php'"><i class="fad fa-sign-in-alt cutcolor"></i>&nbsp; <span>เข้าสู่ระบบ</span></h4>
                     <hr class="x-hr-border-glow-tabmain ">
                  </td>
                  <td>
                     <h4 class="textlogin" ><i class="fad fa-layer-plus cutcolor"></i>&nbsp; <span>สมัครสมาชิก</span></h4>
                     <hr class="x-hr-border-glow-tabmain active">
                  </td>
               </tr>
            </table>
         </div>
         <hr hidden class="x-hr-border-glow-register stepline">




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
            <input type="hidden" id="s2_account_aff" value="<?=$aff?>">













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





<style>
.bkk {
position: absolute;
    bottom: -15px;
	width:100px;
    font-size: 10px;
}

.rc-anchor-li ght {
	background: #8f7c4f!important;
	color: #fff!important;
}

</style>










<!-- Step2 -->
<div class="stepre02 slideto">
            <label for="exampleInputEmail1">เลือกธนาคาร</label>
            <div class="x-bank-choices-type mt-1 mb-2">
               <div class="-outer-wrapper">
                  <input type="radio" class="-input-radio" id="bank-acc-51620499644" name="bank_code" value="014">
                  <label class="-label" for="bank-acc-51620499644">
                  <img class="-logo" src="theme-login/images/bank/scb.svg?v=1" alt="">
                  <i class="fas fa-check"></i>
				  <span class="bkk">ไทยพาณิชย์</span>
                  </label>
                  <input type="radio" class="-input-radio" id="bank-acc-61620499644" name="bank_code" value="004">
                  <label class="-label" for="bank-acc-61620499644">
                  <img class="-logo" src="theme-login/images/bank/kbank.svg?v=1" alt="">
                  <i class="fas fa-check"></i>
				   <span class="bkk">กสิกร</span>
                  </label>
                  <input type="radio" class="-input-radio" id="bank-acc-71620499644" name="bank_code" value="002">
                  <label class="-label" for="bank-acc-71620499644">
                  <img class="-logo" src="theme-login/images/bank/bbl.svg?v=1" alt="">
                  <i class="fas fa-check"></i>
				   <span class="bkk">กรุงเทพ</span>
                  </label>
                  <input type="radio" class="-input-radio" id="bank-acc-81620499644" name="bank_code" value="025">
                  <label class="-label" for="bank-acc-81620499644">
                  <img class="-logo" src="theme-login/images/bank/bay.svg?v=1" alt="">
                  <i class="fas fa-check"></i>
				   <span class="bkk">กรุงศรี</span>
                  </label>
                  <input type="radio" class="-input-radio" id="bank-acc-91620499644" name="bank_code" value="069">
                  <label class="-label" for="bank-acc-91620499644">
                  <img class="-logo" src="theme-login/images/bank/kiatnakin.svg?v=1" alt="">
                  <i class="fas fa-check"></i>
				  <span class="bkk">เกียรตินาคินภัทร</span>
                  </label>
                  <input type="radio" class="-input-radio" id="bank-acc-101620499644" name="bank_code" value="022">
                  <label class="-label" for="bank-acc-101620499644">
                  <img class="-logo" src="theme-login/images/bank/cimb.svg?v=1" alt="">
                  <i class="fas fa-check"></i>
				  <span class="bkk">ซีไอเอ็มบี</span>
                  </label>
                  <input type="radio" class="-input-radio" id="bank-acc-131620499644" name="bank_code" value="024">
                  <label class="-label" for="bank-acc-131620499644">
                  <img class="-logo" src="theme-login/images/bank/uob.svg?v=1" alt="">
                  <i class="fas fa-check"></i>
				  <span class="bkk">ยูโอบี</span>
                  </label>
                  <input type="radio" class="-input-radio" id="bank-acc-141620499644" name="bank_code" value="006">
                  <label class="-label" for="bank-acc-141620499644">
                  <img class="-logo" src="theme-login/images/bank/ktb.svg?v=1" alt="">
                  <i class="fas fa-check"></i>
				  <span class="bkk">กรุงไทย</span>
                  </label>
                  <input type="radio" class="-input-radio" id="bank-acc-151620499644" name="bank_code" value="030">
                  <label class="-label" for="bank-acc-151620499644">
                  <img class="-logo" src="theme-login/images/bank/gsb.svg?v=1" alt="">
                  <i class="fas fa-check"></i>
				  <span class="bkk">ออมสิน</span>
                  </label>
                  <input type="radio" class="-input-radio" id="bank-acc-161620499644" name="bank_code" value="034">
                  <label class="-label" for="bank-acc-161620499644">
                  <img class="-logo" src="theme-login/images/bank/baac.svg?v=1" alt="">
                  <i class="fas fa-check"></i>
				  <span class="bkk">ธ.ก.ส</span>
                  </label>
				  <input type="radio" class="-input-radio" id="bank-acc-145615798516" name="bank_code" value="074">
                  <label class="-label" for="bank-acc-145615798516">
                  <img class="-logo" src="theme-login/images/bank/ttb.png?v=1" alt="">
                  <i class="fas fa-check"></i>
				  <span class="bkk">ทหารไทยธนชาติ</span>
                  </label>
               </div>
            </div>
            <div class="porela" style="margin-top: 20px;">
               <label for="exampleInputEmail1">หมายเลขบัญชี</label>
               <div class="iconlogin">
                  <i class="fad fa-sort-numeric-up-alt" style="font-size: 20px;"></i>
               </div>
               <input type="text" class=" loginform01" id="s2_account_number" aria-describedby="emailHelp" placeholder="กรอกหมายเลขบัญชี">
            </div>
            <div class="porela" style="margin-top: 20px;">
               <label for="exampleInputEmail1">ชื่อบัญชี</label>
               <div class="iconlogin">
                  <i class="fad fa-user" style="font-size: 20px;"></i>
               </div>
               <input type="text"  class=" loginform01" id="s2_account_name" aria-describedby="emailHelp" placeholder="กรอกชื่อบัญชี">
            </div>
            <div class="porela" style="margin-top: 20px;">
               <label for="exampleInputEmail1">คุณรู้จักเราจากไหน</label>
               <div class="iconlogin">
                  <i class="fad fa-bullhorn" style="font-size: 20px;"></i>
               </div>
               <select class="loginform01" id="s2_register_channel_id" required="">
                  <option value="">เลือกช่องทาง</option>
                  <option value="1">FaceBook</option>
                  <option value="2">Tiktok</option>
                  <option value="3">Line</option>
                  <option value="4">Google</option>
                  <option value="5">Instargram</option>
                  <option value="6">Youtube</option>
                  <option value="7">SMS</option>
                  <option value="8">เพื่อนแนะนำ</option>
               </select>
            </div>
            <div class="porela" style="margin-top: 20px;">
               <label for="exampleInputEmail1">ID Line</label>
               <div class="iconlogin">
                  <i class="fab fa-line" style="font-size: 20px;"></i>
               </div>
               <input type="text"  class=" loginform01" id="s2_line" aria-describedby="emailHelp" placeholder="กรอกไอดีไลน์">
            </div>
            <?php
            if($row_website_tb['sms_active'] == "1")
            {
            ?>
            <div class="porela" style="margin-top: 20px;">
               <label for="exampleInputEmail1">OTP (เลข 6 หลักจะส่งไปในข้อความ)</label>
               <div class="iconlogin">
                  <i class="fad fa-user" style="font-size: 20px;"></i>
               </div>
               <input type="number" class=" loginform01" id="s2_otp" aria-describedby="emailHelp" placeholder="กรอกเลข OTP 6 หลัก">
            </div>
            <?php
            }
            else
            {
            ?>
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
            </div>
            <div  onclick="location.href='login.php'" class="needregister">ต้องการเข้าสู่ระบบใช่ไหม? <span class="cutcolor">เข้าสู่ระบบ</span></div>
         </div>
<!-- Step2 -->
















<!-- Step3 -->
         <div class="stepre03 slideto" style="padding-bottom: 140px;">
            <div class="porela" style="margin-top: 30px;">
               <label for="exampleInputEmail1">รหัสผ่าน</label>
               <div class="iconlogin">
                  <i class="fad fa-user" style="font-size: 20px;"></i>
               </div>
               <input type="text"  class=" loginform01"  id="s3_password" aria-describedby="emailHelp" placeholder="รหัสผ่าน">
            </div>
            <div class="porela" style="margin-top: 10px;">
               <label for="exampleInputEmail1">ยืนยันรหัสผ่าน</label>
               <div class="iconlogin">
                  <i class="fad fa-user" style="font-size: 20px;"></i>
               </div>
               <input type="text"   class=" loginform01" id="s3_c_password" aria-describedby="emailHelp" placeholder="ยืนยันรหัสผ่าน">
            </div>
            
            <?php
            if($row_website_tb['sms_active'] != "1")
            {
            ?>
            <div class="porela" style="margin-top: 10px;     text-align: -webkit-center;">
               <div class="g-recaptcha" data-sitekey="<?=$_CONFIG['sitekey']?>"></div>
            </div>
            <?php
            }
            ?>

            <div class="btnofregister">
               <table style="width: 100%">
                  <tr>
                     <td class="tdbtnregister">
                        <button type="button" id="btn-back2" class="btnbackregister bkcolor">ก่อนหน้า</button>
                     </td>
                     <td class="tdbtnregister">
                        <button type="button" id="btn-step3" class="btnnextregister mcolor">ถัดไป </button>
                     </td>
                  </tr>
               </table>

               <input type="hidden" id="s3_phone">
               <input type="hidden" id="s3_ref">
               <input type="hidden" id="s3_otp">
               <input type="hidden" id="s3_u_bank_code">
               <input type="hidden" id="s3_u_bank_number">
               <input type="hidden" id="s3_u_fname">
               <input type="hidden" id="s3_u_refer_id">
               <input type="hidden" id="s3_u_aff">
               <input type="hidden" id="s3_u_line">
               <input type="hidden" id="ip" value="<?=get_client_ip()?>">
            </div>
            <div  onclick="location.href='login.php'" class="needregister">ต้องการเข้าสู่ระบบใช่ไหม? <span class="cutcolor">เข้าสู่ระบบ</span></div>
         </div>
<!-- Step3 -->









<!-- Step04 Finish -->
         <div class="stepre04 slideto pb-5">
            <div style="text-align: center;margin-top: 20px;
               margin-bottom: 10px;">
               <i class="fad fa-shield-check checkfinish"></i>
            </div>
            <div class="finishregister">
               <span>ยินดีต้อนรับ <br> คุณ <span id="nameuser"></span></span> <br>
            </div>
         </div>
<!-- Step04 Finish -->





      </div>
   </div>
   <div class="pdhiregis01 p-1"></div>
</section>






<?php include('theme-login/footer.php'); ?>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
   $('.selectbank').click(function(){
      $('#bank_code').val($(this).attr('alt'));
      $('.open_select_bank').val($(this).attr('name'));
      $('.inbankselectpopup').addClass("closeanimationselectbank");
      $('.bankselectpopup').hide();
   });
   $('.btnclosebankselect').click(function(){
      $('.inbankselectpopup').addClass("closeanimationselectbank");
      $('.bankselectpopup').hide();
      
   });
   
   
   $('.open_select_bank').click(function(){
      $('.inbankselectpopup').removeClass("closeanimationselectbank");
      $('.bankselectpopup').show();
   });
   
   
   
   
   
   
   $(".stepre02").hide();
   $(".stepre03").hide();
   $(".stepre04").hide();
   
   
   // go step01 to step2
   document.getElementById("btn-step1").onclick = function(){
      var formData = new FormData();
      formData.append('s1_phone', $("#s1_phone").val());
      formData.append('s', 's1');
      $.ajax({
         type: 'POST',
         url: 'system/register',
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
      formData.append('s2_bank_code', $("input:radio[name='bank_code']:checked").val());
      formData.append('s2_account_number', $("#s2_account_number").val());
      formData.append('s2_account_name', $("#s2_account_name").val());
      formData.append('s2_line', $("#s2_line").val());
      formData.append('s2_register_channel_id', $("#s2_register_channel_id").val());
      formData.append('s2_account_aff', $("#s2_account_aff").val());
      formData.append('s2_otp', $("#s2_otp").val());
      formData.append('s2_phone', $("#s2_phone").val());
      formData.append('s2_ref', $("#s2_ref").val());
      formData.append('s', 's2');
      $.ajax({
         type: 'POST',
         url: 'system/register',
         data:formData,
         contentType: false,
         processData: false,
      }).done(function(res){
         $(".stepre02").hide();
         $(".stepre03").show();
         $(".headstep2").removeClass("active");
         $(".headstep3").addClass("active");
         $("#s3_phone").val(res.message.phone);
         $("#s3_ref").val(res.message.ref);
         $("#s3_otp").val(res.message.otp);
         $("#s3_u_bank_code").val(res.message.u_bank_code);
         $("#s3_u_bank_number").val(res.message.u_bank_number);
         $("#s3_u_fname").val(res.message.u_fname);
         $("#s3_u_refer_id").val(res.message.u_refer_id);
         $("#s3_u_aff").val(res.message.u_aff);
         $("#s3_u_line").val(res.message.u_line);
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
   // back step3 to step2
   document.getElementById("btn-back2").onclick = function(){
      $(".stepre03").hide();
      $(".stepre02").show();
      $(".headstep3").removeClass("active");
      $(".headstep2").addClass("active");
      $("#s3_phone").val('');
      $("#s3_ref").val('');
      $("#s3_otp").val('');
      $("#s3_u_bank_code").val('');
      $("#s3_u_bank_number").val('');
      $("#s3_u_fname").val('');
      $("#s3_u_refer_id").val('');
      $("#s3_u_aff").val('');
      $("#s3_u_line").val('');
   };
   
    // go step03 to step4
    document.getElementById("btn-step3").onclick = function(){
      var formData = new FormData();
      formData.append('s3_password', $("#s3_password").val());
      formData.append('s3_c_password', $("#s3_c_password").val());
      formData.append('s3_phone', $("#s3_phone").val());
      formData.append('s3_ref', $("#s3_ref").val());
      formData.append('s3_otp', $("#s3_otp").val());
      formData.append('s3_u_bank_code', $("#s3_u_bank_code").val());
      formData.append('s3_u_bank_number', $("#s3_u_bank_number").val());
      formData.append('s3_u_fname', $("#s3_u_fname").val());
      formData.append('s3_u_refer_id', $("#s3_u_refer_id").val());
      formData.append('s3_u_aff', $("#s3_u_aff").val());
      formData.append('s3_u_line', $("#s3_u_line").val());
      formData.append('ip', $("#ip").val());
      formData.append('s', 's3');

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
         url: 'system/register',
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
         $(".stepre03").hide();
         $(".needregister").hide();
         $(".stepre04").show();
         $(".headstep3").removeClass("active");
         $(".headstep4").addClass("active");
         var nameuservalue = $("#s3_u_fname").val();
         document.getElementById("nameuser").innerHTML = nameuservalue;
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
   
</script>