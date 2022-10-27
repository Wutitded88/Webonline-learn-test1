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
<link href="https://fonts.googleapis.com/css?family=Eater" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
<link rel="stylesheet" href="halloween/halloween.css">
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
  </div>-->
  <!-- top logo add line for mobile -->

  <!-- top-menu -->
  <?php include("partials/_top-menu.php"); ?>
  <!-- top-menu -->
  <!-- Halloween -->
<div class='spider_0'>
  <div class='eye left'></div>
  <div class='eye right'></div>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
</div>
<div class='spider_1'>
  <div class='eye left'></div>
  <div class='eye right'></div>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
</div>
<div class='spider_2'>
  <div class='eye left'></div>
  <div class='eye right'></div>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
</div>
<div class='spider_3'>
  <div class='eye left'></div>
  <div class='eye right'></div>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
</div>
<div class='spider_4'>
  <div class='eye left'></div>
  <div class='eye right'></div>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
</div>
<div class='spider_5'>
  <div class='eye left'></div>
  <div class='eye right'></div>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg left'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
  <span class='leg right'></span>
</div>

<img class='web-right' src='http://www.scandiafun.com/images/spiderweb-corner-right.png'>
<img class='web-left' src='http://www.scandiafun.com/images/spiderweb-corner-right.png'>
<div id="jquery-script-menu">
<div class="jquery-script-center">
<ul>
</ul><div id="carbon-block"></div>
<div class="jquery-script-ads">
<script type="text/javascript"
src="https://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>
<div class="jquery-script-clear"></div>
</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="halloween/halloween-bats.js"></script>
	<script type="text/javascript">
		window.halloweenBats = $.halloweenBats({
			amount: 5
		});
	</script>
	<script>
try {
  fetch(new Request("https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js", { method: 'HEAD', mode: 'no-cors' })).then(function(response) {
    return true;
  }).catch(function(e) {
    var carbonScript = document.createElement("script");
    carbonScript.src = "//cdn.carbonads.com/carbon.js?serve=CK7DKKQU&placement=wwwjqueryscriptnet";
    carbonScript.id = "_carbonads_js";
    document.getElementById("carbon-block").appendChild(carbonScript);
  });
} catch (error) {
  console.log(error);
}
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<!-- end Halloween -->
<!-- partial -->
  <div class="wrapper">
  
    <div class="tt_l tt_full p_gl">
	
      <div id="ct_from" class="tt_l tt_full title_page">
	  <div style="padding-top:10px">
	<img src="<?php echo $row_u['logo']; ?>" width="250">
</div>
        <h1>เข้าสู่ระบบ <span style="font-family: 'Eater', cursive;"><?=$_CONFIG['domain_name']?></span></h1>
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
<!--
 <script type="text/javascript">
   var news = '!! ลูกค้าท่านใดที่มีเครดิตค้างอยู่ ก่อนอัพระบบ ให้แจ้งเบอร์โทร พร้อมแจ้งว่าต้องการย้ายเครดิต ทางไลน์ !!';
   var news_imgs = 'https://sv1.picz.in.th/images/2021/10/26/u5XhOl.png';

   news = news.split('|');
   var wrapper = document.createElement('div');
   wrapper.innerHTML = news[1];

   setTimeout(function(){
     swal.fire({
       title: news[0],
       text: news[1],
       imageUrl: news_imgs,
       icon: news_imgs,
       content: wrapper,
       animation: true,
     });
     $('.swal2-content').html(news[1]);
     $('.swal2-title').css('color','red');
   },500);
 </script> 
 -->