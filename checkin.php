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
<style>
  .fr_link_1x a:last-child {
    border-radius: 10px;
}
</style>
<body data-rsssl=1 class="home blog">
  <input type="hidden" id="username" value="<?=get_session()?>">
  <input type="hidden" id="ip" value="<?=get_client_ip()?>">

 
 
  <!-- slide -->
  <?php include("partials/_slide.php"); ?>
  <!-- slide -->

  <?php
  $topup_min = 1000;
  $q_topup = dd_q('SELECT SUM(t_amount) AS s_topup FROM topup_db WHERE t_user = ? AND t_status = ?', [get_session(), '1']);
  $sum_topup = 0;
  if($q_topup->rowCount() > 0)
  {
    $row_topup = $q_topup->fetch(PDO::FETCH_ASSOC);
    $sum_topup = $row_topup['s_topup'];
  }
  if ($sum_topup >= $topup_min)
  {
  ?>
  <div class="wrapper">
    <div class="tt_l tt_full p_gl">
      <div id="ct_from" class="tt_l tt_full title_page">
 
        <h1>เช็คอินรายวัน</h1>
	  </div>
      <div class="tt_l tt_full tt_content bg">
        <div class="tt_l tt_full">

          <div class="tt_l tt_full">
            <div class="row">
              <?php
              $q_checkin_tb = dd_q('SELECT * FROM checkin_tb');
              while($row_checkin_tb = $q_checkin_tb->fetch(PDO::FETCH_ASSOC))
              {
              ?>
              <div class="tt_l tt_col_4x post-gslot" style="border: 0px;">
                <div class="filter_grid grid-item show_g_grid" style="margin-top: 10px;">
                  <div class="bg-gradient-game_grid">
                    <?php
                    $q_checkin_reward_tb = dd_q('SELECT * FROM checkin_reward_tb WHERE c_user = ? AND c_reward_day = ? AND c_reward_month = ? AND c_reward_year = ?',[
                      get_session(),
                      $row_checkin_tb['c_day'],
                      date('m'),
                      date('Y')
                    ]);
                    if ($q_checkin_reward_tb->rowCount() == 0)
                    {
                      if($row_checkin_tb['c_status'] == "1" && $row_checkin_tb['c_day'] == date('j'))
                      {
                        if($row_checkin_tb['c_reward'] > 0)
                        {
                        ?>
                        <div class="tt_l tt_full fr_link_1x">
                          <a href="javascript:void(0)" id="a_checkin" onClick="fn_checkin()" class="blobgame bluegame active">
                            <div class="text-center">
                            <h2><img src="images/checkin.webp" style="width: 50px;"></h2>
                              <h2><i class="fad fa-sack-dollar"></i> วันที่ <?=$row_checkin_tb['c_day']?> <i class="fad fa-sack-dollar"></i></h2>
                              <p style="color:#FFCC00;"><i class="fad fa-coins"></i> รับฟรี <?=$row_checkin_tb['c_reward']?> เครดิต</p>
                            </div>
                            <input type="hidden" id="c_text" value="รับฟรี <?=$row_checkin_tb['c_reward']?> เครดิต ทำเทิร์น <?=$row_checkin_tb['c_turnover']?> เท่า ถอนได้สูงสุด <?php if($row_checkin_tb['c_withdraw_max'] > 0){echo number_format($row_checkin_tb['c_withdraw_max'], 2, '.', ',').' บาท';}else{echo 'ไม่จำกัด';}?> เครดิตที่เหลือจะถูกตัดทิ้ง">
                          </a>
                        </div>
                        <script type="text/javascript">
                          function fn_checkin() {
                            Swal.fire({
                              title: 'เช็คอินวันที่ <?=date('j')?>',
                              text: $('#c_text').val(),
                              icon: 'warning',
                              confirmButtonColor: '#3085d6',
                              confirmButtonText: 'เช็คอิน',
                              showCancelButton: true,
                              cancelButtonColor: '#d33',
                              cancelButtonText: 'ยกเลิก'
                            }).then((result) => {
                              if (result.value) {
                                var formData = new FormData();
                                formData.append('ip', $('#ip').val());
                                $('#a_checkin').attr('disabled', 'disabled');

                                $.ajax({
                                  type: 'POST',
                                  url: '<?=base_url()?>/system/checkin',
                                  data:formData,
                                  contentType: false,
                                  processData: false,
                                }).done(function(res) {
                                  Swal.fire({
                                    type: 'success',
                                    title: 'สำเร็จ',
                                    text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
                                  }).then((result) => {
                                    window.location = '<?=base_url()?>/checkin';
                                  });
                                  console.clear();
                                  $('#a_checkin').removeAttr('disabled');
                                  $('#loading').hide();
                                }).fail(function(jqXHR) {
                                  res = jqXHR.responseJSON;
                                  Swal.fire({
                                    type: 'error',
                                    title: 'ผิดพลาด',
                                    text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
                                  });
                                  console.clear();
                                  $('#a_checkin').removeAttr('disabled');
                                  $('#loading').hide();
                                });
                              }
                            });
                          }
                        </script>
                        <?php
                        }
                        else
                        {
                        ?>
                        <div class="tt_l tt_full fr_link_1x">
                          <a disabled style="cursor: no-drop;" class="blobgame bluegame active">
                            <div class="text-center">
                              <h2><i class="fad fa-sack-dollar"></i> วันที่ <?=$row_checkin_tb['c_day']?> <i class="fad fa-sack-dollar"></i></h2>
                              # 
                            </div>
                          </a>
                        </div>
                        <?php
                        }
                      }
                      else
                      {
                      ?>
                      <div class="tt_l tt_full fr_link_1x">
                        <a disabled style="cursor: no-drop;">
                          <div class="text-center">
                            <h2><i class="fad fa-sack-dollar"></i> วันที่ <?=$row_checkin_tb['c_day']?> <i class="fad fa-sack-dollar"></i></h2>
                            <span style="font-size: 14px;">
                              <?php
                              if($row_checkin_tb['c_reward'] > 0)
                              {
                                echo "<i class='fad fa-coins'></i> รับฟรี ".$row_checkin_tb['c_reward']." เครดิต";
                              }
                              else
                              {
                                echo "<i class='fad fa-times-circle'></i>";
                              }
                              ?>
                            </span>
                          </div>
                        </a>
                      </div>
                      <?php
                      }
                    }
                    else
                    {
                    ?>
                    <div class="tt_l tt_full fr_link_1x">
                      <a disabled style="cursor: no-drop;" class='<?php if($row_checkin_tb['c_status'] == "1" && $row_checkin_tb['c_day'] == date('j')){ echo "blobgame bluegame active";}?>'>
                        <div class="text-center">
                          <h2><i class="fad fa-sack-dollar"></i> วันที่ <?=$row_checkin_tb['c_day']?> <i class="fad fa-sack-dollar"></i></h2>
                          <span style="font-size: 14px;"><i class="fad fa-shield-check"></i> รับรางวัลแล้ว</span>
                        </div>
                      </a>
                    </div>
                    <?php
                    }
                    ?>
                  </div>
                </div>
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
  <?php
  }
  ?>

  <!-- footer -->
  <?php include("partials/_footer.php"); ?>
  <!-- footer -->

  <?php
  if ($sum_topup < $topup_min)
  {
  ?>
  <script type="text/javascript">
    Swal.fire({
      type: 'error',
      title: 'ผิดพลาด',
      text: 'ต้องมียอดเติมเงินขั้นต่ำ <?=$topup_min?> บาท เพื่อเปิดใช้งานฟังชั่นนี้',
    }).then((result) => {
      window.location = './wallet';
    });
  </script>
  <?php
  }
  ?>
</body>
<script type="text/javascript">


 var news = '<i class="fad fa-map-marker-question"></i> คำเตือน <i class="fad fa-map-marker-question"></i>|<p style="color:#ffffff;">ควรมีเครดิตน้อยกว่า 5 ค่อยกดเช็คอิน เพื่อผลประโยชน์ของลูกค้าค่ะ และอ่านเงื่อนไขก่อนกด เช็คอิน หากไม่อ่านเงื่อนไข ทางเราไม่รับผิดชอบใดๆทั้งสิ้น</p></a>';
  var news_imgs = '';

  news = news.split('|');
  var wrapper = document.createElement('div');
  wrapper.innerHTML = news[1];

  setTimeout(function(){
    swal.fire({
      title: news[0],
      text: news[1],
	  background: '#fff url(https://wallpaperaccess.com/full/136534.jpg)',
      imageUrl: news_imgs,
      icon: news_imgs,
      content: wrapper,
      animation: true,
    });
    $('.swal2-content').html(news[1]);
    $('.swal2-title').css('color','red');
  },500);
</script>
</html>

  