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
 

 


  <!-- slide -->
  <?php include("partials/_slide.php"); ?>


  <div class="wrapper">
    <div class="tt_l tt_full p_gl">
      <div id="ct_from" class="tt_l tt_full title_page">
        <h1>ถอนเงิน</h1>
      </div>
      <div class="tt_l tt_full tt_content bg" style="background-color: #0a0a0a !important;">
        <div class="tt_l tt_full">
          <?php
          $t_over = "0.00";
          $max_wd = "ไม่จำกัด";
          $last_pro = "ไม่รับโบนัส";
          $wd_max = false;
          $q_transfer = dd_q('SELECT * FROM transfergame_tb WHERE t_user = ? ORDER BY t_id DESC LIMIT 1', [get_session()]);
          if ($q_transfer->rowCount() > 0)
          {
            $row_transfer = $q_transfer->fetch(PDO::FETCH_ASSOC);
            if($row_transfer["t_active"] == "Y")
            {
              if($row_transfer["t_withdraw_max"] > 0)
              {
                $max_wd = $row_transfer["t_withdraw_max"];
              }
              else
              {
                $max_wd = "ไม่จำกัด";
              }
              $last_pro = $row_transfer["t_promotion_title"];
              // if($row_transfer["t_promotion_id"] == "3" && strpos($row_transfer["t_promotion_title"], 'ฟรีเครดิต') !== false)
              // {
                $wd_max = true;
              // }
              $t_over = $row_transfer["t_turnover"];
            }
          }
          ?>

          <div class="mount-credit">
            <a class="textcolor_w">ยอดเงินคงเหลือ&nbsp;<span class="number-h" style="display: inline-block;"><div id="credit_total_balance">Loading...</div></span> เครดิต</a>
          </div>
          <div class="must-turn">
            <a>ต้องมียอดเทรินโอเวอร์<span class="number-h">  <?=$t_over?></span></a>
          </div>
          <div class="now-turn">
            <a>ถอนได้สูงสุด<span class="number-h">  <?=$max_wd?></span> เครดิต</a>
          </div>
          <div class="now-turn">
            <a>โปรโมชั่นล่าสุดที่รับ<span class="number-h">  <?=$last_pro?></span></a>
          </div>
          <div class="now-turn">
            <a style="color: #dc3545; font-family: thaisanslite_r1; font-size: 18px;"><i class="fa fa-times" aria-hidden="true"></i> โปรโมชั่นที่จำกัดยอดถอน จะไม่สามารถรีโปรได้ จนกว่าจะเล่นหมด หรือถอนเงินทั้งหมด</a>
          </div>
          <div class="now-turn">
            <a style="color: #caa41b; font-family: thaisanslite_r1; font-size: 18px;"><i class="fad fa-engine-warning"></i> หากพบการทุจริต หรือเติมเงินเพื่อรีโปรโดยไม่เล่นแล้วถอนเงิน ทางทีมงานจะยกเลิกถอนและยึดเครดิตทั้งหมด</a>
          </div>

          <?php
          $q_1 = dd_q('SELECT * FROM user_tb WHERE (u_user = ? AND u_block_agent = 0)', [get_session()]);
          if ($q_1->rowCount() >= 1)
          {
            $row_u = $q_1->fetch(PDO::FETCH_ASSOC);
            ?>
            <form class="tt_l tt_full fr_re">
              <p class="textlogin0" style="font-size:24px;"> ชื่อบัญชี</p>
                <input class="tt_fr" readonly type="text" value="<?=$row_u["u_fname"]?> <?=$row_u["u_lname"]?>" />
        
              <p class="textlogin0" style="font-size:24px;"> โอนเข้าธนาคาร</p>
                <input class="tt_fr" readonly type="text" value="<?=$row_u["u_bank_name"]?> - <?=$row_u["u_bank_number"]?>" />
 
              <?php
              if($wd_max)
              {
              ?>
                <p class="textlogin0" style="font-size:24px;"> กรอกจำนวนเงิน</p>
                  <input class="tt_fr" readonly type="text" value="ถอนทั้งหมด" />
              <?php
              }
              else
              {
              ?>
                <p class="textlogin0" style="font-size:24px;"> กรอกจำนวนเงิน</p>
                  <input class="tt_fr" id="amount" placeholder="300 หรือ 5000 ไม่ต้องใส่ , (ลูกน้ำ)" maxlength="9" max="100000" type="text" data-parsley-pattern="/^[0-9.]+$/" data-parsley-errors-container="#checkbox-errors-amount"/>
              <?php
              }
              $q_w = dd_q('SELECT * FROM withdraw_tb WHERE w_user = ? AND (w_status = ? OR w_status = ?)', [get_session(), "0", "99"]);
              if ($q_w->rowCount() > 0)
              {
              ?>
                <div class="form-group">
                  <center>
                    <p style="font-weight: bold; color: #e81404;">คุณมีรายการถอนค้างอยู่ กรุณารอแอดมินดำเนินการ</p>
                  </center>
                </div>
              <?php
              }
              else
              {
              ?>
                <div class="form-group">
                  <button type="button" style="background:linear-gradient(to right, #007c04, #84c700); box-shadow: 1px 1px 20px rgb(98 179 1);" id="btn_withdraw" onClick="loadingPage();" class="tt_l tt_ful fr_submit_bk"><i class="fas fa-hand-holding-usd"></i> แจ้งถอนเงิน</button>
                </div>
              <?php
              }
              ?>
            </form>
            <?php
          }
          ?>

          <div class="containloophiswd">
          <?php
          $q_withdraw = dd_q('SELECT * FROM withdraw_tb WHERE w_user = ? ORDER BY w_id DESC LIMIT 10', [get_session()]);
          while($row = $q_withdraw->fetch(PDO::FETCH_ASSOC))
          {
            ?>
            <div class="historyofwd">
              <table width="100%">
                <tbody>
                  <tr>
                    <td width="50%" style="padding-top: 7px;">
                      <table>
                        <tbody>
                          <tr>
                            <td style="text-align: left; line-height: 20px;">
                              <span class="spanofbankhis"><?=$row['w_bank_name']?></span>
                              <br>
                              <span class="spanofbankhis"><?=$row['w_bank_number']?></span>
                              <br>
                              <span class="spanofbankhis"><?=$row['w_fname']?></span>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                    <td width="50%" style="text-align: right; line-height: 20px;">
                      <div class="statushistory">
                        <span style="background: #000; border-radius:10px; padding:5px">
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
                          ?>
                        </span>
                      </div>
                      <span class="moneyhisdps">
                        <i class="fal fa-minus-circle minushis"></i> <?=$row['w_amount']?> บาท
                      </span>
                      <br>
                      <span class="spanofbankhis">
                        <?=thai_date_and_time(strtotime($row['w_date_create']));?> <?=$row['w_time_create']?>
                      </span>
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
      </div>
    </div>
  </div>

  <!-- footer -->
  <?php include("partials/_footer.php"); ?>
  <!-- footer -->

</body>
</html>