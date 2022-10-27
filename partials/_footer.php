<?php

$q_u = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);

$row_u = $q_u->fetch(PDO::FETCH_ASSOC);

?>
<style>
#account-actions-mobile {
    position: fixed;
    left: 0;
    bottom: 0;
    z-index: 2;
    width: 100%;
}


#account-actions-mobile .-outer-wrapper {
    display: flex;
    align-items: flex-end;
}
#account-actions-mobile .-left-wrapper {
    border-top-left-radius: 10px;
    border-top-right-radius: 22px;
}
#account-actions-mobile .-center-wrapper {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: center;
    z-index: 2;
    width: 74px;
    height: 74px;
    margin: 0 8px 16px;
    color: #fff;
    background: linear-gradient(
180deg, #1f1c13, #1f1d15);
    border-radius: 50%;
    box-shadow: 0 0 10px hsl(0deg 0% 100% / 40%);
	
}
#account-actions-mobile .-fake-center-bg-wrapper {
    position: absolute;
    left: 0;
    bottom: 0;
    z-index: 1;
    width: 100%;
    height: 50px;
    transition: -webkit-filter .2s;
    transition: filter .2s;
    transition: filter .2s, -webkit-filter .2s;
    overflow: hidden;
}
#account-actions-mobile .-fake-center-bg-wrapper svg {
    position: absolute;
    left: 50%;
    bottom: 0;
    height: 108px;
    -webkit-transform: translateX(-50%);
    transform: translateX(-50%);
}
#account-actions-mobile .-fake-center-bg-wrapper svg path {
    fill: url(#rectangleGradient);
}
#account-actions-mobile .-right-wrapper {
    border-top-right-radius: 10px;
    border-top-left-radius: 22px;
}
#account-actions-mobile .-left-wrapper, #account-actions-mobile .-right-wrapper {
    display: flex;
    align-items: flex-end;
    flex: 1;
    z-index: 2;
    height: 70px;
    padding-bottom: 6px;
    background: linear-gradient(
180deg, #1f1c13, #201c14);
    border-top: 3px solid #f7d18e;
    transition: -webkit-filter .2s;
    transition: filter .2s;
    transition: filter .2s, -webkit-filter .2s;
}
#account-actions-mobile .-fully-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2;
    background: rgba(0, 0, 0, .6);
}
#account-actions-mobile .-item-wrapper {
    width: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    color: #fff;
    position: relative;
}
#account-actions-mobile .-item-wrapper {
    width: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    color: #fff;
    position: relative;
}
@media (max-width: 575.98px)
#account-actions-mobile .-item-wrapper .-text2 {
    font-size: .8rem;
}
#account-actions-mobile .-item-wrapper .-text2 {
    font-size: .999rem;
    color: #ffffff;
    transition: color .2s;
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    margin: 0 auto;
    margin-top: .25rem;
	    font-family: thaisanslite_r1;
}
.-ic-img img {
    width: 34px;
    height: auto;
    display: block;
    margin: 0 auto;
    padding-bottom: 20px;
    position: relative;
}
.overlay {
    display: none;
    position: fixed;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.7);
    z-index: 998;
    opacity: 0;
    transition: all 0.5s ease-in-out;
}
.x-button-actions {
	color:#FFFFFF;
}
a {
	color:#FFFFFF;
}
.imgloading {
  animation-name: move;
  animation-iteration-count: infinite;
  animation-duration: 1s;
  position: relative;
}
@keyframes move {
  0%   { top: 0px; }
  50%  { top: 10px; }
  100% { top: 0px; }
}
</style>
<div class="tt_l tt_full footer">

  <div class="tt_l tt_full footer_tx"><?=$_CONFIG['domain_name']?> สุดยอดเว็บพนันออนไลน์ เปิดตลอด 24ชั่วโมง</div>

  <div class="tt_l tt_full footer_cx" style="padding: 10px 0px 0px 0px;">

    <div class="wrapper">

      <div class="tt_l footer_logo">

        <a href="<?=base_url()?>/home">

          <img style="width: 150px;" src="<?php echo $row_u['logo']; ?>"/>

        </a>

      </div>

      <div class="tt_l c_footer">

        <div class="tt_l tt_full">พนันออนไลน์ที่ดีที่สุด ในประเทศไทย</div>

        <div class="tt_l tt_full">Best of Gambling Online in Thailand</div>

      </div>

      <div class="tt_r copyright_footer">

        <div class="tt_l tt_full mb-10">

          <a href="<?=$_CONFIG['urlline']?>" target="_blank">

            <img class="img_line" src="<?=base_url()?>/themes/v2/images/line-add.png"/>

            <span> ID LINE I <strong class="tx-li"><?=$_CONFIG['lineid']?></strong></span>

          </a>

        </div>

        <?php echo $row_u['copyright']; ?>

      </div>

    </div>

  </div>

  <div class="tt_l tt_full footer_tx">

    <img style="width: 25px;" src="<?=base_url()?>/themes/v2/images/google-chrome.png"> Google Chrome / <img style="width: 25px;" src="<?=base_url()?>/themes/v2/images/edge.png"> Microsoft Edge 

    <p class="head_mc" style="padding-bottom: 20px;"></p>

  </div>

</div>



<script type='text/javascript' src='<?=base_url()?>/js/wp-embed.min7661.js?ver=5.4.2'></script>
 
 <div class="x-button-actions" id="account-actions-mobile">
   <div class="-outer-wrapper">
      <div class="-left-wrapper">

         <span class="-item-wrapper"><span class="-ic-img"><span class="-text2 d-block"><a href="<?=base_url()?>/login" onclick="loadingPage();">หน้าแรก</a></span><a href="<?=base_url()?>/login" onclick="loadingPage();"><img width="150" height="150" src="<?=base_url()?>/images/menufooter-1.png" class="image wp-image-435  attachment-thumbnail size-thumbnail" alt="" loading="lazy" style="max-width: 100%; height: auto;" srcset="<?=base_url()?>/images/menufooter-1.png 150w, <?=base_url()?>/images/menufooter-1.png 300w, <?=base_url()?>/images/menufooter-1.png 512w" sizes="(max-width: 150px) 100vw, 150px">
 
		 </a></span></span><span class="-item-wrapper"><span class="-ic-img"><span class="-text2 d-block"><a href="<?=base_url()?>/deposit" onclick="loadingPage();">ฝาก</a></span><a href="<?=base_url()?>/deposit" onclick="loadingPage();"><img width="300" height="300" src="<?=base_url()?>/images/menufooter-2.png" class="image wp-image-436  attachment-medium size-medium" alt="" loading="lazy" style="max-width: 100%; height: auto;" srcset="<?=base_url()?>/images/menufooter-2.png 300w, <?=base_url()?>/images/menufooter-2.png 150w, <?=base_url()?>/images/menufooter-2.png 512w" sizes="(max-width: 300px) 100vw, 300px"></a></span></span>
      </div>
 
      <span class="-center-wrapper js-footer-lobby-selector js-menu-mobile-container">

          <div class="-selected"><a href="<?=base_url()?>/gambling"  onclick="loadingPage();"><img width="150" height="150" src="<?=base_url()?>/images/playgame.png" class="image wp-image-557  attachment-thumbnail size-thumbnail" alt="" loading="lazy" style="max-width: 100%; height: auto;" srcset="<?=base_url()?>/images/playgame.png 150w, <?=base_url()?>/images/playgame.png 300w, <?=base_url()?>/images/playgame.png 500w" sizes="(max-width: 150px) 100vw, 150px"></a></div>            
      </span>
      <div class="-fake-center-bg-wrapper">
         <svg viewBox="-10 -1 30 12">
            <defs>
               <linearGradient id="rectangleGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                  <stop offset="0%" stop-color="#272727d9"></stop>
                  <stop offset="100%" stop-color="#1d190e"></stop>
               </linearGradient>
            </defs>
            <path d="M-10 -1 H30 V12 H-10z M 5 5 m -5, 0 a 5,5 0 1,0 10,0 a 5,5 0 1,0 -10,0z"></path>
         </svg>
      </div>
      <div class="-right-wrapper">
         
         <span class="-item-wrapper"><span class="-ic-img"><span class="-text2 d-block"><a href="<?=base_url()?>/withdrawal" onclick="loadingPage();">ถอน</a></span><a href="<?=base_url()?>/withdrawal" onclick="loadingPage();"><img width="150" height="150" src="<?=base_url()?>/images/menufooter-3.png" class="image wp-image-437  attachment-thumbnail size-thumbnail" alt="" loading="lazy" style="max-width: 100%; height: auto;" srcset="<?=base_url()?>/images/menufooter-3.png 150w, <?=base_url()?>/images/menufooter-3.png 300w, <?=base_url()?>/images/menufooter-3.png 512w" sizes="(max-width: 150px) 100vw, 150px"></a></span></span><span class="-item-wrapper"><span class="-ic-img"><span class="-text2 d-block"><a target="_blank" href="<?=$_CONFIG['urlline']?>">ติดต่อเรา</a></span><a target="_blank" href="<?=$_CONFIG['urlline']?>"><img width="150" height="150" src="<?=base_url()?>/images/menufooter-4.png" class="image wp-image-431  attachment-full size-full" alt="" loading="lazy" style="max-width: 100%; height: auto;"></a></span></span>

      </div>
      <div class="-fully-overlay js-footer-lobby-overlay"></div>
   </div>
</div>

<div class="overlay"></div>

<div id="toTop">

  <i class="fa fa-chevron-up"></i>

</div>
  <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
  <script  src="<?=base_url()?>/themes/story/script.js"></script>
<script type="text/javascript" src="<?=base_url()?>/themes/v2/js/jquery-1.10.2.minc619.js?v=2"></script>

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

         <!-- <div class="sk-rect sk-rect1"></div>

         <div class="sk-rect sk-rect2"></div>

         <div class="sk-rect sk-rect3"></div>

         <div class="sk-rect sk-rect4"></div>

         <div class="sk-rect sk-rect5"></div> -->
		 <img class="imgloading" style="width: 200px;" src="<?php echo $row_u['logo']; ?>"/>

      </div>

   </div>

</div>



<script src="<?=base_url()?>/js/clipboard.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>/js/script.inc.51yua5qtehpwryzsgxoh.js?ver=1" type="text/javascript"></script>
<script src="<?=base_url()?>/js/script.inc.25fsyrjghnf896dhjsrtujg.js?ver=1" type="text/javascript"></script>
<script src="<?=base_url()?>/js/cs.js" type="text/javascript"></script>
