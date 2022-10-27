<?php
require_once 'api/config.php';
if(empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './login';
        </SCRIPT>";
  exit();
}
else
{
  $q_website_tb = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
  $row_website_tb = $q_website_tb->fetch(PDO::FETCH_ASSOC);
  if($row_website_tb['status_freecredit'] == "0")
  {
    echo "<SCRIPT LANGUAGE='JavaScript'>
            window.location.href = '".base_url()."/wallet';
          </SCRIPT>";
    exit();
  }
}
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
  <?php include("master/MasterPages.php"); ?>
</head>
<body data-rsssl=1 class="home blog">
  <input type="hidden" id="username" value="<?=get_session()?>">
  <input type="hidden" id="ip" value="<?=get_client_ip()?>">

 

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

  <div class="wrapper">
    <div class="tt_l tt_full p_gl">
      <div id="ct_from" class="tt_l tt_full title_page">
        <h1>เติมเครดิตฟรี</h1>
      </div>
      <div class="tt_l tt_full tt_content bg">
        <div class="tt_l tt_full">

          <div class="now-turn">
            <a>ยอดเงินคงเหลือ&nbsp;<span class="number-h" style="display: inline-block;"><div id="credit_total_balance">Loading...</div></span> เครดิต</a>
          </div>
          <div class="now-turn">
            <a style="color: #dc3545; font-family: thaisanslite_r1; font-size: 18px;"><i class="fa fa-times" aria-hidden="true"></i> ยอดเงินคงเหลือต้องน้อยกว่า 5 เครดิต ถึงจะเติมเครดิตฟรีได้</a>
          </div>

          <?php
          $gencode_tb = dd_q("SELECT * FROM gencode_tb WHERE g_phone = '".get_session()."'");
          $freecredit_status = "โค้ดเครดิตฟรียังไม่ถูกสร้าง กรุณาติดต่อแอดมิน";
          $freecredit_color = "red";
          if($gencode_tb->rowCount() > 0)
          {
            $row_gencode = $gencode_tb->fetch(PDO::FETCH_ASSOC);
            if($row_gencode["g_use"] == "0")
            {
              $freecredit_status = "ยังไม่ใช้งาน";
              $freecredit_color = "green";
            }
            else if($row_gencode["g_use"] == "1")
            {
              $freecredit_status = "ใช้งานแล้ว";
              $freecredit_color = "red";
            }
          }
          ?>

          <form class="tt_l tt_full fr_re">
            <p class="textlogin0" style="font-size:20px;"> สถานะเครดิตฟรี</p>
 
                <input class="tt_fr" style="color: <?=$freecredit_color?>;" readonly type="text" value="<?=$freecredit_status?>" />
          
            <?php
            if($freecredit_color == "green")
            {
            ?>
            <p class="textlogin0" style="font-size:20px;"> กรอกโค้ดเครดิตฟรี</p>
                <input class="tt_fr text-uppercase" id="code" maxlength="10" type="text" onKeyUp="this.value = this.value.toUpperCase();" />
       

            <div class="now-turn">
              <a style="color: #dc3545; font-family: thaisanslite_r1; font-size: 18px;"><i class="fa fa-times" aria-hidden="true"></i> เครดิตฟรี ห้ามเล่นยอดรวมกับยอดฝากเด็ดขาด กรุณาเคลียยอดเครดิตฟรีให้หมด หรือเหลือน้อยกว่า 5 เครดิตก่อนเติมเงินเพิ่ม หากถอนเงินได้ไม่ครบ ทางทีมจะไม่รับผิดชอบใดๆทั้งสิ้น</a>
            </div>

            <div class="form-group">
              <button type="button" id="btn_freecredit" onClick="loadingPage();" class="tt_l tt_ful fr_submit_bk"><img src="./themes/v2/images/Baht-Refresh-2.png">  เติมเครดิตฟรี</button>
            </div>
            <?php
            }
            else
            {
            ?>
            <div class="tt_l tt_full fr_sec">
              <div class="input-group">
                <label class="tt_l fr_tx2x">โค้ดเครดิตฟรี</label>
                <input class="tt_bk" maxlength="10" type="text" value="<?=$row_gencode['g_code']?>" readonly />
              </div>
            </div>
            <?php
            }
            ?>
          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- footer -->
  <?php include("partials/_footer.php"); ?>
  <!-- footer -->

</body>
</html>