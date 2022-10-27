<?php
require_once 'api/config.php';
require_once 'api/Service/amb.php';
$api = new AMBAPI();
  
  // 0962036415
  // 0644349888
  $sum_winloass = 0;
  $q_winloss_receive_tb = dd_q('SELECT * FROM winloss_receive_tb WHERE wl_user = ? AND wl_date = ?', [
    '0644349888',
    date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'))
  ]);
  if ($q_winloss_receive_tb->rowCount() == 0)
  {
    $q_user_tb = dd_q('SELECT * FROM user_tb WHERE u_user = ? AND u_block_agent = 0', ['0644349888']);
    if ($q_user_tb->rowCount() > 0)
    {
      $row_user_tb = $q_user_tb->fetch(PDO::FETCH_ASSOC);
        $q_topup_tb = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_system_create_date = ? AND t_transaction_id != ? AND t_status = ?', [
          $row_user_tb['u_user'],
          date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')),
          "",
          "1"
        ]);
        if ($q_topup_tb->rowCount() > 0)
        {
          $amount_winloss = 0;
          while($row_topup_tb = $q_topup_tb->fetch(PDO::FETCH_ASSOC))
          {
            if($row_topup_tb['t_transaction_id'] != "")
            {
              $winloss = $api->GetWinLose($row_user_tb['u_agent_id'], $row_topup_tb['t_transaction_id']);
              if ($winloss->success == true)
              {
                $winloss = json_decode(json_encode($winloss->data), true);
                $_check_sum = 0;
                foreach ($winloss["data"] as $val)
                {
                  if(array_key_exists('game', $val) && $val['game'] == "FOOTBALL")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                  else if(array_key_exists('game', $val) && $val['game'] == "STEP")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                  else if(array_key_exists('game', $val) && $val['game'] == "PARLAY")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                  else if(array_key_exists('game', $val) && $val['game'] == "GAME")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                  else if(array_key_exists('game', $val) && $val['game'] == "CASINO")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                  else if(array_key_exists('game', $val) && $val['game'] == "LOTTO")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                  else if(array_key_exists('game', $val) && $val['game'] == "M2")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                  else if(array_key_exists('game', $val) && $val['game'] == "MULTI_PLAYER")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                  else if(array_key_exists('game', $val) && $val['game'] == "POKER")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                  else if(array_key_exists('game', $val) && $val['game'] == "E_SPORT")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                  else if(array_key_exists('game', $val) && $val['game'] == "KENO")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                  else if(array_key_exists('game', $val) && $val['game'] == "TRADING")
                  {
                    // หมวดไหนที่จะไม่เอามาคิด winloss ให้ comment ออก
                    $_check_sum = $_check_sum + ($val['wlTurnAmount']);
                  }
                }

                if($_check_sum > $row_topup_tb['t_amount'])
                {
                  $amount_winloss = $amount_winloss + $row_topup_tb['t_amount'];
                }

              }
            }
          }
          $q_withdraw_tb = dd_q('SELECT SUM(w_amount) AS w_amount FROM withdraw_tb WHERE w_user = ? AND w_date_create = ? AND w_status = ?', [
            $row_user_tb['u_user'],
            date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')),
            '1'
          ]);
          if ($q_withdraw_tb->rowCount() > 0)
          {
            $row_withdraw_tb = $q_withdraw_tb->fetch(PDO::FETCH_ASSOC);
            $sum_winloass = $row_withdraw_tb['w_amount'] - $amount_winloss;
          }
          else
          {
            $sum_winloass = $amount_winloss;
          }
        }
        else
        {
          echo "ไม่พบข้อมูล topup_db ".date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'));
        }
    }
    else
    {
      echo "ไม่พบข้อมูล user_tb";
    }
  }
  else
  {
    echo "มีข้อมูล winloss_receive_tb แล้ว ".date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'));
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
        <h1><i class="fas fa-chart-bar" aria-hidden="true"></i> คืนยอดเสีย</h1>
      </div>
      <div class="tt_l tt_full tt_content bg">
        <div class="tt_l tt_full">
          <div class="thaitheme_read">
            <?php
            $q_website_tb = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
            $row_website_tb = $q_website_tb->fetch(PDO::FETCH_ASSOC);

            $receive_winloss = 0;
            if (strpos($sum_winloass, '-') !== false)
            {
              $receive_winloss = str_replace(",", "", str_replace("-", "", $sum_winloass)) * $row_website_tb['affwinloss'];
            }
            ?>
            <h2 align="center" class="textcolor_w entry-title">
              คืนยอดเสีย <?php echo $row_website_tb['affwinloss']*100;?>% ทุกวัน
            </h2>

            <?php
            $check_sum_winloass = str_replace(",", "", str_replace("-", "", $sum_winloass));
            if($check_sum_winloass >= $row_website_tb['minwinloss'])
            {
            ?>
            <button type="button" id="btn_winloss" onClick="loadingPage();" class="tt_l tt_ful fr_submit_bk" style="background: linear-gradient(to right, #6900ff, #009aff);">
              <i class="fas fa-hand-holding-usd"></i> รับยอดเสียคืน
            </button>
            <?php
            }
            else
            {
            ?>
            <button type="button" class="tt_l tt_ful fr_submit_bk" disabled>
              ยอดเสียไม่เพียงพอที่จะกดรับได้
            </button>
            <?php
            }
            ?>

            <div style="font-size: 20px; text-align:center;">
              <span style="color: #fadb0a; font-weight: bold;font-size: 18px">เมื่อมียอดเสียมากกว่า <?=number_format($row_website_tb['minwinloss'], 2, '.', ',')?> บาท ขึ้นไป</span>
              <br>
            </div>

            <div class="tt_l tt_full tb_hd" style="margin-top: 40px; margin-bottom: 40px;">
              <table class="tablea" style="width:100%; font-size: 20px; text-align: center;">
                <tbody class=" ">
                  <tr class="tr-table">
                    <td td="col">
                      วันที่
                    </td>
                    <td scope="col">
                      แพ้ชนะ
                    </td>
                    <td scope="col">
                      ยอดเสียที่ได้คืน
                    </td>
                  </tr>
                </tbody>
                <tbody>
                  <tr class="tdd" style="font-size: 20px;">
                    <td scope="col">
                      <?=date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'))?>
                    </td>
                    <td scope="col">
                      <?php
                      if (strpos($sum_winloass, '-') !== false)
                      {
                      ?>
                      <span style="color: #ef1127;"><?=number_format($sum_winloass, 2, '.', ',')?></span>
                      <?php
                      }
                      else
                      {
                        if($sum_winloass == 0)
                        {
                        ?>
                        <?=number_format($sum_winloass, 2, '.', ',')?>
                        <?php
                        }
                        else
                        {
                        ?>
                        <span style="color: #06f73d;">+<?=number_format($sum_winloass, 2, '.', ',')?></span>
                        <?php
                        }
                      }
                      ?>
                    </td>
                    <td scope="col">
                      <?=number_format($receive_winloss, 2, '.', ',')?>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div style="font-size: 20px; text-align:center;">
              <span style="color: #fff; font-weight: bold;font-size: 18px">
                ยอด <span style="color: #fadb0a;">+</span> และมี <span style="color: #06f73d;">สีเขียว</span> คือ ผู้เล่นได้กำไร
              </span>
            </div>
            <div style="font-size: 20px; text-align:center;">
              <span style="color: #fff; font-weight: bold;font-size: 18px">
                ยอด <span style="color: #fadb0a;">-</span> และมี <span style="color: #ef1127;">สีแดง</span> คือ ผู้เล่นขาดทุน
              </span>
            </div>

            <div style="font-size: 20px; text-align:center;">
              <span style="color: #fadb0a; font-weight: bold;font-size: 18px">
                ผู้เล่นที่มีสิทธิได้รับการคืนยอดเสีย ยอดที่ได้รับจะต้องเป็น - (ลบ) และมีสีแดง และยอดเสียรวมจะต้องมากกว่า <?=number_format($row_website_tb['minwinloss'], 2, '.', ',')?> บาท
              </span>
            </div>
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