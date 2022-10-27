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

  <?php
  $q_topup = dd_q('SELECT SUM(t_amount) AS s_topup FROM topup_db WHERE t_user = ? AND t_status = ?', [get_session(), '1']);
  $sum_topup = 0;
  if($q_topup->rowCount() > 0)
  {
    $row_topup = $q_topup->fetch(PDO::FETCH_ASSOC);
    $sum_topup = $row_topup['s_topup'];
  }
  if ($sum_topup >= 300)
  {
  ?>
  <div class="wrapper">
    <div class="tt_l tt_full p_gl">
      <div id="ct_from" class="tt_l tt_full title_page">
        <h1><i class="fad fa-calendar-alt" aria-hidden="true"></i> ฝากประจำ</h1>
      </div>
      <div class="tt_l tt_full tt_content bg">
        <div class="tt_l tt_full">
          <div class="thaitheme_read">
            <!-- <img src="images/sd1.gif" style="border-radius:20px; width:100%;"> --> 
            <h2 align="center" class="textcolor_w entry-title">
              <span class="icon-bar"></span>ฝากประจำ รับเครดิตฟรี
            </h2>

            <div class="row">
              <?php
              $q_fixed_deposit_tb = dd_q("SELECT * FROM promotion_fixed_deposit_tb WHERE p_status = 1");
              while($row = $q_fixed_deposit_tb->fetch(PDO::FETCH_ASSOC))
              {
                $isAllow = false;
                for ($i=0; $i < $row['p_deposit_day']; $i++)
                {
                  $q_f = dd_q('SELECT f_id FROM fixed_deposit_receive_tb WHERE f_action_date = ? AND f_u_user = ?', [
                    date('Y-m-d', strtotime(date('Y-m-d'). ' - '.$i.' days')), 
                    get_session()
                  ]);
                  if ($q_f->rowCount() == 0)
                  {
                    $q_t = dd_q('SELECT SUM(t_amount) AS t_amount FROM topup_db WHERE t_system_create_date = ? AND t_user = ? AND t_status = ?', [
                      date('Y-m-d', strtotime(date('Y-m-d'). ' - '.$i.' days')), 
                      get_session(),
                      "1"
                    ]);
                    if ($q_t->rowCount() > 0)
                    {
                      $row_t = $q_t->fetch(PDO::FETCH_ASSOC);
                      if($row_t['t_amount'] >= $row["p_transfer_min"])
                      {
                        $isAllow = true;
                      }
                      else
                      {
                        $isAllow = false;
                      }
                    }
                  }
                  else
                  {
                    $isAllow = false;
                  }
                  if(!$isAllow)
                  {
                    break;
                  }
                }
              ?>
              <div class="col-sm-12">
                <div style="border-bottom:1px solid #252525!important; margin-bottom: 20px;">
                  <img style="width: 100%;" src="<?=$row['p_img']?>">
                  <h2 style="font-weight: bold; margin-top: 5px; color: #e5af4b;"><?=$row['p_title']?></h2>
                  <p style="color:#FFFFFF;">
                    <?=$row['p_detail']?> ทำยอดเทิร์น <b><?php echo number_format($row['p_turnover']);?> <?php if($row['p_turnover_type'] == "c"){echo "เท่าของเครดิตคงเหลือ";}elseif($row['p_turnover_type'] == "p"){echo "%";}elseif($row['p_turnover_type'] == "w"){echo "เท่าของเครดิตเครดิต Win Loss";}?></b> เพื่อทำการถอน<br>
                    <span> * รับเครดิตฟรี <?php echo number_format($row['p_reward']);?> บาท</span>
                    <br>
                    <span> * ถอนได้สูงสุด <?php if(number_format($row['p_withdraw_max']) == 0){echo "ไม่จำกัด";}else{echo number_format($row['p_withdraw_max'])." บาท";}?> </span>
                  </p>
                  <div class=" text-center">
                    <p style="font-size: 16px; font-weight: bold; padding: 10px; color: #e81404;">
                      <i class="fad fa-exclamation"></i> หากพบเห็นการตั้งใจใช้ข้อผิดพลาดในการหาผลประโยชน์ ทีมงานจะระงับการจ่ายเงิน ทุกกรณี
                    </p>
                  </div>
                  <?php
                  if($isAllow)
                  {
                  ?>
                  <button style="background-color: #e4c068; margin-bottom: 10px;" onClick="loadingPage(); get_fixed_deposit(<?=$row['p_id']?>);" class="btn btn-lg btn-dark-green hvr-buzz ml-0">
                    <i class="fas fa-hand-holding-usd"></i> รับเครดิตฟรี
                  </button>
                  <?php
                  }
                  ?>
                </div>
              </div>
              <?php
              }
              ?>
            </div>

            <div class="tt_l tt_full title_page">
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
                    <tbody>
                      <tr>
                        <td width="50%" style="padding-top: 7px;">
                          <table>
                            <tbody>
                              <tr>
                                <td style="padding-right: 5px;">
                                  <img class="backlogohis" style="border-radius: 20%; width: 50px;" src="theme-login/images/bank/<?=$row['t_sys_bank_code']?>.svg">
                                </td>
                                <td style="text-align: left; line-height: 20px;">
                                  <span class="spanofbankhis"><?=$row['t_sys_bank_name']?></span>
                                  <br>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                        <td width="50%" style="text-align: right; line-height: 20px;">
                          <div class="statushistory">
                            <span style="background: #000; border-radius:10px; padding:5px">
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
                            </span>
                          </div>
                          <span class="moneyhisdps">
                            <i class="fal fa-plus-circle plushis"></i> <?=$row['t_amount']?> บาท
                          </span>
                          <br>
                          <span class="spanofbankhis">
                            <?=thai_date_and_time(strtotime($row['t_date_create'])); ?> <?=$row['t_time_create']?>
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
    </div>
  </div>
  <?php
  }
  ?>
  
  <!-- footer -->
  <?php include("partials/_footer.php"); ?>
  <!-- footer -->

  <?php
  if ($sum_topup < 300)
  {
  ?>
  <script type="text/javascript">
    Swal.fire({
      type: 'error',
      title: 'ผิดพลาด',
      text: 'ต้องมียอดเติมเงินขั้นต่ำ 300 บาท เพื่อเปิดใช้งานฟังชั่นนี้',
    }).then((result) => {
      window.location = './wallet';
    });
  </script>
  <?php
  }
  ?>
</body>
</html>
<script type="text/javascript">
  function copyToClipboard(element)
  {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text().replaceAll("-", "")).select();
    document.execCommand("copy");
    $temp.remove();
	Swal.fire(
	  'Link แนะนำเพื่อน',
	  'คัดลอก Link แนะนำ สำเร็จแล้ว',
	  'success'
	)
  }
</script>