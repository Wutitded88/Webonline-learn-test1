<?php
require_once 'api/config.php';
if(empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './login';
        </SCRIPT>";
  exit();
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
  </div> -->
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
        <h1>ประวัติการรับเครดิต</h1>
      </div>
      <div class="tt_l tt_full tt_content bg" style="background-color: #0a0a0a !important;">
        <div class="tt_l tt_full">
          <div class="tt_l tt_full tx_hd">
            ประวัติการเติมเครดิต
          </div>
 
                
          <div class="containloophisdps">
                <?php
                $q_transfer = dd_q('SELECT * FROM topup_db WHERE t_user = ? ORDER BY t_id DESC LIMIT 10', [get_session()]);
                while($row = $q_transfer->fetch(PDO::FETCH_ASSOC))
                {
                ?>
                <div class="historyofdps">
                <table width="100%">
                        <tbody><tr>
                           <td width="50%" style="padding-top: 7px;">
                              <table>
                                 <tbody><tr>
                                    <td style="padding-right: 5px;"> <img class="backlogohis" style="border-radius: 20%; width: 50px;" src="theme-login/images/bank/<?=$row['t_sys_bank_code']?>.svg"></td>
                                    <td style="text-align: left; line-height: 20px;">
                                      <span class="spanofbankhis"><?=$row['t_sys_bank_name']?></span>
                                      <br>
                                  </td>
                                 </tr>
                                   
                              </tbody></table>
                           </td>
                           <td width="50%" style="text-align: right; line-height: 20px;">
                              <div class="statushistory"> <span style="background: #000; border-radius:10px; padding:5px">
                              <?php
                    if($row['t_status'] == "1")
                    {
                      echo "<span style='color:#00ff5a;'>สำเร็จ</span>";
                    }
                    else if($row['t_status'] == "2")
                    {
                      echo "<span style='color:#ffeb00;'>ผิดพลาด</span>";
                    }
                    else if($row['t_status'] == "3")
                    {
                      echo "<span style='color:#ff0000;'>ยกเลิก</span>";
                    }
                    ?>
                    </span></div>
                              <span class="moneyhisdps"> <i class="fal fa-plus-circle plushis"></i> <?=$row['t_amount']?> บาท</span>
                              <br>
                              <span class="spanofbankhis"><?=thai_date_and_time(strtotime($row['t_date_create'])); ?> <?=$row['t_time_create']?></span>
                           </td>
                        </tr>
                  </tbody>
                </table>
                </div>
                <?php
                }
                ?>
          </div>


          <div class="tt_l tt_full tx_hd">
          ประวัติการรับโบนัส
          </div>
          <div class="containloophisdps">
                <?php
                $q_transfergame = dd_q('SELECT * FROM transfergame_tb WHERE t_user = ? ORDER BY t_id DESC LIMIT 10', [get_session()]);
                while($row = $q_transfergame->fetch(PDO::FETCH_ASSOC))
                {
                ?>
                <div class="historyofdps">
                <table width="100%">
                        <tbody><tr>
                           <td width="50%" style="padding-top: 7px;">
                              <table>
                                 <tbody><tr>
                                    <td style="text-align: left; line-height: 20px;">
                                      <span class="spanofbankhis"><?=$row['t_promotion_title']?></span>
                                      <br>
                                  </td>
                                 </tr>
                                   
                              </tbody></table>
                           </td>
                           <td width="50%" style="text-align: right; line-height: 20px;">
 
                              <span class="moneyhisdps"> <i class="fal fa-plus-circle plushis"></i> <?=$row['t_bonus']?> บาท</span>
                              <br>
                              <span class="spanofbankhis"><?=thai_date_and_time(strtotime($row['t_date_create'])); ?> <?=$row['t_time_create']?></span>
                           </td>
                        </tr>
                  </tbody>
                </table>
                </div>
                <?php
                }
                ?>
          </div>
          <!-- <div class="tt_l tt_full tx_hd">
            ประวัติการรับเครดิตแนะนำเพื่อน
          </div>
          <div class="tt_l tt_full tb_hd">
            <table class="table">
              <tbody class="table_h">
                <tr>
                  <td td="col"><center>หมายเลข</center></td>
                  <td scope="col"><center>วันที่/ชื่อผู้ใช้</center></td>
                  <td scope="col"><center>ธนาคาร</center></td>
                  <td scope="col"><center>จำนวน</center></td>
                  <td scope="col"><center>สถานะ</center></td>
                </tr>
              </tbody>
              <tbody>
              </tbody>
            </table>
          </div> -->
        </div>
      </div>
    </div>
  </div>

  <!-- footer -->
  <?php include("partials/_footer.php"); ?>
  <!-- footer -->

</body>
</html>