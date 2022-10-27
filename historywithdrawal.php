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
        <h1>ประวัติการถอนเงิน</h1>
      </div>
      <div class="tt_l tt_full tt_content bg" style="background-color: #090909 !important;">
        <div class="tt_l tt_full">
          <div class="tt_l tt_full tx_hd">
            ประวัติการถอนเงิน
          </div>

          <div class="containloophiswd">
                <?php
                $q_withdraw = dd_q('SELECT * FROM withdraw_tb WHERE w_user = ? ORDER BY w_id DESC LIMIT 10', [get_session()]);
                while($row = $q_withdraw->fetch(PDO::FETCH_ASSOC))
                {
                ?>
                <div class="historyofwd">
                <table width="100%">
                        <tbody><tr>
                           <td width="50%" style="padding-top: 7px;">
                              <table>
                                 <tbody><tr>
                                    <td style="text-align: left; line-height: 20px;">
                                       <span class="spanofbankhis"><?=$row['w_bank_name']?></span>
                                       <br>
                                       <span class="spanofbankhis"><?=$row['w_bank_number']?></span>
                                       <br>
                                       <span class="spanofbankhis"><?=$row['w_fname']?></span>
                                    </td>
                                 </tr>
                              </tbody></table>
                           </td>
                           <td width="50%" style="text-align: right; line-height: 20px;">
                              <div class="statushistory"> <span style="background: #000; border-radius:10px; padding:5px">
                              <?php
                    if($row['w_status'] == "1")
                    {
                      echo "<span style='color:#00ff5a;'>สำเร็จ</span>";
                    }
                    else if($row['w_status'] == "2")
                    {
                      echo "<span style='color:#ff0000;'>ยกเลิก</span>";
                    }
                    else if($row['w_status'] == "3")
                    {
                      echo "<span style='color:#ffeb00;'>คืนเครดิต</span>";
                    }
                    else if($row['w_status'] == "0")
                    {
                      echo "<span style='color:#8c8c8c;'>รอดำเนินการ</span>";
                    }
                    else if($row['w_status'] == "99")
                    {
                      echo "<span style='color:#8c8c8c;'>รอแอดมินตรวจสอบ</span>";
                    }
                    ?></span></div>
                              <span class="moneyhisdps"> <i class="fal fa-minus-circle minushis"></i> <?=$row['w_amount']?> บาท</span>
                              <br>
                              <span class="spanofbankhis"><?=thai_date_and_time(strtotime($row['w_date_create']));?> <?=$row['w_time_create']?></span>
                           </td>
                        </tr>
                  </tbody>
                </table>
                </div>
                <?php
                }
                ?>

          </div>
        </div>
        <div class="tt_l tt_full">
          <div class="tt_l tt_full tx_hd">
            ประวัติการถอนเงินค่าแนะนำเพื่อน
          </div>
          <div class="containloophiswd">
                <?php
                $q_receive = dd_q('SELECT * FROM aff_receive WHERE aff_u_user = ? ORDER BY aff_id DESC LIMIT 10', [get_session()]);
                while($row = $q_receive->fetch(PDO::FETCH_ASSOC))
                {
                ?>
 
                <div class="historyofwd" style="background:#ab7a00;box-shadow: 0 0 0 2px #ffbe1d;">
                <table width="100%">
                        <tbody><tr>
                           <td width="50%" style="padding-top: 7px;">
                              <table>
                                 <tbody><tr>
                                    <td style="text-align: left; line-height: 20px;">
                                       <span class="spanofbankhis"><?=$row['aff_action']?></span>
                                    </td>
                                 </tr>
                              </tbody></table>
                           </td>
                           <td width="50%" style="text-align: right; line-height: 20px;">
                              <div class="statushistory"> <span style="background: #000; border-radius:10px; padding:5px">
                              <span style='color:#00ff5a;'>สำเร็จ</span></span></div>
                              <span class="moneyhisdps"> <i class="fal fa-minus-circle minushis" style="color: #ffd56c;"></i> <?=$row['aff_amount']?> บาท</span>
                           </td>
                        </tr>
                  </tbody>
                </table>
                </div>
                <?php
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- footer -->
  <?php include("partials/_footer.php"); ?>
  <!-- footer -->

</body>
</html>
