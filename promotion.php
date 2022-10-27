<?php
require_once 'api/config.php';
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
  <?php include("master/MasterPages.php"); ?>
</head>
<style>
.row img {
    border-radius: 20px;
}
.boxpro {
   width:50%;
   float: left; 
   padding: 20px;
}
@media screen and (max-width: 992px) {
.boxpro {
   width:100%;
}
}
</style>
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
        <h1>โปรโมชั่น</h1>
      </div>
      <div class="tt_l tt_full tt_content bg">
        <div class="tt_l tt_full">
          <div class="thaitheme_read">
 
            <?php
            $promotion_tb1 = dd_q("SELECT * FROM promotion_tb WHERE p_transfer_type != '4' AND p_transfer_type != '5' AND p_status = 1");
            while($row = $promotion_tb1->fetch(PDO::FETCH_ASSOC))
            {
              $q_1 = dd_q('SELECT * FROM transfergame_tb WHERE (t_user = ? AND t_promotion_id = ?) ORDER BY t_create_date DESC', [get_session(), $row["p_id"]]);
              if ($q_1->rowCount() > 0 && $row["p_transfer_type"] != "3") //เคยรับโปรนี้ไปแล้ว
              {
                $row_transfer = $q_1->fetch(PDO::FETCH_ASSOC);
                if($row["p_transfer_type"] == "1") //โปรย้ายเงินครั้งแรก
                {
                  //เคยรับแล้วตัดออก
                }
                else if($row["p_transfer_type"] == "2") //โปรครั้งแรกของวัน
                {
                  if($row_transfer['t_date_create'] != date("Y-m-d"))
                  {
                  ?>
                  <div class="row boxpro">
                    <div class=" ">
                      <img style="width: 100%;" src="<?=$row['p_img']?>">
                    </div>
                    <div class="col-sm-12" style="border-bottom:1px solid #252525!important; margin-bottom: 20px;">
                      <h2 style="font-weight: bold; margin-top: 5px; color: #e5af4b;"><?=$row['p_title']?></h2>
                      <p style="color:#FFFFFF;">
                        ต้องมีเครดิตคงเหลือขั้นต่ำ <b><?php echo number_format($row['p_transfer_min']);?> บาท ถึงกดรับได้</b> ทำยอดเทิร์น <b><?php echo number_format($row['p_turnover']);?> <?php if($row['p_turnover_type'] == "c"){echo "เท่าของเครดิตคงเหลือ";}elseif($row['p_turnover_type'] == "p"){echo "%";}elseif($row['p_turnover_type'] == "w"){echo "เท่าของครดิต Win Loss";}?></b> เพื่อทำการถอน<br>
                        <span> * รับโบนัสสูงสุดไม่เกิน <?php echo number_format($row['p_reward_max']);?> บาท</span>
                        <br>
                        <span> * ถอนได้สูงสุด <?php if(number_format($row['p_withdraw_max']) == 0){echo "ไม่จำกัด";}else{echo number_format($row['p_withdraw_max'])." บาท";}?> </span>
                      </p>
                      <div class=" text-center">
                        <p style="font-size: 16px; font-weight: bold; padding: 10px; color: #e81404;"><i class="fad fa-exclamation"></i> หากพบเห็นการตั้งใจใช้ข้อผิดพลาดในการหาผลประโยชน์ ทีมงานจะระงับการจ่ายเงิน ทุกกรณี</p>
 
                      </div>
                      <?php
                      if(!empty(get_session()))
                      {
                        $q_w = dd_q('SELECT * FROM withdraw_tb WHERE w_user = ? AND (w_status = ? OR w_status = ?)', [get_session(), "0", "99"]);
                        if ($q_w->rowCount() > 0)
                        {
                        ?>
                        <div class=" text-center">
                          <p style="font-size: 18px; font-weight: bold; padding: 10px; color: #ffc800;"><i class="fad fa-engine-warning color_o" aria-hidden="true"></i> คุณมีรายการถอนค้างอยู่ กรุณารอแอดมินดำเนินการ</p>
                        </div>
                        <?php
                        }
                        else
                        {
                        ?>
                        <button style="background-color: #4caf50; margin-bottom: 10px; color: aliceblue; width: 100%;" onClick="loadingPage(); get_bonus(<?=$row['p_id']?>);" class="btn btn-lg btn-dark-green hvr-buzz ml-0">
                          <i class="fas fa-hand-holding-usd"></i> รับโบนัส
                        </button>
                        <?php
                        }
                      }
                      ?>
                    </div>
                  </div>
                  <?php
                  }
                }
              }
              else //ยังไม่เคยรับโปรนี้
              {
              ?>
               <div class="row boxpro">
                
                <div class="col-sm-12" style="border-bottom:1px solid #252525!important; margin-bottom: 20px;"><img style="width: 100%;" src="<?=$row['p_img']?>">
                  <h2 style="font-weight: bold; margin-top: 5px; color: #e5af4b;"><?=$row['p_title']?></h2>
                  <p style="color:#FFFFFF;">
                    ต้องมีเครดิตคงเหลือขั้นต่ำ <b><?php echo number_format($row['p_transfer_min']);?> บาท ถึงกดรับได้</b> ทำยอดเทิร์น <b><?php echo number_format($row['p_turnover']);?> <?php if($row['p_turnover_type'] == "c"){echo "เท่าของเครดิตคงเหลือ";}elseif($row['p_turnover_type'] == "p"){echo "%";}elseif($row['p_turnover_type'] == "w"){echo "เท่าของเครดิตเครดิต Win Loss";}?></b> เพื่อทำการถอน<br>
                    <span> * รับโบนัสสูงสุดไม่เกิน <?php echo number_format($row['p_reward_max']);?> บาท</span>
                    <br>
                    <span> * ถอนได้สูงสุด <?php if(number_format($row['p_withdraw_max']) == 0){echo "ไม่จำกัด";}else{echo number_format($row['p_withdraw_max'])." บาท";}?> </span>
                  </p>
                  <div class=" text-center">
                    <p style="font-size: 16px; font-weight: bold; padding: 10px; color: #e81404;"><i class="fad fa-exclamation"></i> หากพบเห็นการตั้งใจใช้ข้อผิดพลาดในการหาผลประโยชน์ ทีมงานจะระงับการจ่ายเงิน ทุกกรณี</p>
 
                  </div>
                  <?php
                  if(!empty(get_session()))
                  {
                    $q_w = dd_q('SELECT * FROM withdraw_tb WHERE w_user = ? AND (w_status = ? OR w_status = ?)', [get_session(), "0", "99"]);
                    if ($q_w->rowCount() > 0)
                    {
                    ?>
                    <div class="text-center">
                      <p style="font-size: 18px; font-weight: bold; padding: 10px; color: #ffc800;"><i class="fad fa-engine-warning color_o" aria-hidden="true"></i> คุณมีรายการถอนค้างอยู่ กรุณารอแอดมินดำเนินการ</p>
                    </div>
                    <?php
                    }
                    else
                    {
                    ?>
                    <button style="background-color: #4caf50; margin-bottom: 10px; color: aliceblue; width: 100%;" onClick="loadingPage(); get_bonus(<?=$row['p_id']?>);" class="btn btn-lg btn-dark-green hvr-buzz ml-0">
                      <i class="fas fa-hand-holding-usd"></i> รับโบนัส
                    </button>
                    <?php
                    }
                  }
                  ?>
                </div>
              </div>
              <?php
              }
            }
            ?>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- footer -->
  <?php include("partials/_footer.php"); ?>
  <!-- footer -->

</body>
<style type="text/css">
  .swal-wide
  {
    width:850px !important;
  }
</style>
</html>
