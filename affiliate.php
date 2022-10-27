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
  if($row_website_tb['status_aff'] == "0")
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
        <h1><i class="fad fa-sitemap" aria-hidden="true"></i> แนะนำเพื่อน</h1>
      </div>
      <div class="tt_l tt_full tt_content bg">
        <div class="tt_l tt_full">
          <div class="thaitheme_read">
            <!-- <img src="images/sd1.gif" style="border-radius:20px; width:100%;"> --> 
            <h2 align="center" class="textcolor_w entry-title">
              <span class="icon-bar"></span>แนะนำเพื่อน รับค่าคอม
              <?php
              if($row_website_tb['aff_type'] == "1")
              {
                echo "เฉพาะยอดฝากแรกของเพื่อน";
              }
              else if($row_website_tb['aff_type'] == "2")
              {
                echo "ทุกยอดฝากของเพื่อน (สูงสุด ".number_format($row_website_tb['aff_maxofday'], 0, '.', ',')."/วัน)";
              }
              else if($row_website_tb['aff_type'] == "3")
              {
                echo "ของยอดเสียของเพื่อนในวันที่ ".date('d/m/Y', strtotime(date('Y-m-d'). ' - 1 days'))." (สูงสุด ".number_format($row_website_tb['aff_maxofday'], 0, '.', ',')."/วัน)";
              }
              ?>
            </h2>


            <div class="moneycontainaf">
                  <table width="100%">
                     <tbody>
                        <tr>
                           <td style="vertical-align: middle;">
                              <i class="fas fa-users-crown"></i>
                           </td>
                           <?php
                            $countPercent = 0;
                            $q_percent_tb = dd_q('SELECT SUM(aff_amount) AS oAmount FROM aff_percent_tb WHERE aff_u_user_ref = ? AND aff_status = ?', [get_wallet("u_user"), "0"]);
                            if ($q_percent_tb->rowCount() > 0)
                            {
                              $row_percent_tb = $q_percent_tb->fetch(PDO::FETCH_ASSOC);
                              $countPercent = $row_percent_tb["oAmount"];
                            }
                            ?>
 
               
              <!-- โบนัสที่ได้รับ -->
                           <td style="text-align: right; font-size: clamp(20px, 2.5vw, 30px);">
                              รายได้ปัจจุบัน<br>
                              <span style="color: #fddc8c;     font-size: clamp(28px, 2.5vw, 30px);"><?php if($countPercent < 1) { echo "0.00"; } else { echo $countPercent; }?></span>
                              <input type="hidden" id="amount" value="<?=$countPercent?>" />
                              <br>
                              <div style="position: relative;">
                                 <span class="countearnmoney" style="">
                                 <i class="fad fa-money-bill"></i>
                                 อัตราส่วนแบ่งรายได้ (<?=$row_website_tb['aff_step']?> ชั้น)</span>
                                 <!-- ปุ่มถอนรายได้ form>....... -->
                                 <br> 
                                 <div id="withdrawfriend" style="display: none;">
                                    <button class="fr_submit_bk col-12" style="background: linear-gradient(to right, #f88900, #e1b102);box-shadow: 1px 1px 20px rgb(228 173 2);" type="submit"><i class="fas fa-hand-holding-usd"></i> แจ้งถอนรายได้</button>
                                 </div>
                                 <!-- </from>................ -->
                              </div>
                           </td>
                        </tr>
                     </tbody>
                  </table>
                  <div class="pcfriendback" style="">
                      <?php
                      for ($i=1; $i <= $row_website_tb['aff_step']; $i++)
                      {
                        if($i==1){
                          $_percent = $row_website_tb['affpersen']*100;
                        }else{
                          $_percent = $row_website_tb['affpersen'.$i]*100;
                      }
                      ?>
                     <table style="width: 100%; font-size: clamp(20px, 2.5vw, 28px);">
                        <tbody>
                           <tr>
                              <td width="80%" class="text-left">
                                 <i class="fad fa-coins" style=" "></i> <span>ส่วนแบ่งรายได้ชั้นที่ <?=$i?></span>
                              </td>
                              <td class="text-right">
                                 <span style=" "><?=$_percent?>%</span>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     <?php
                      }
                     ?>
                    <?php
                    $countClick = 0;
                    $q_aff_count = dd_q('SELECT * FROM aff_count WHERE aff_u_user = ?', [get_wallet("u_user")]);
                    if ($q_aff_count->rowCount() > 0)
                    {
                      $row_aff_count = $q_aff_count->fetch(PDO::FETCH_ASSOC);
                      $countClick = $row_aff_count["aff_count"];
                    }
                    ?> 
                    <!-- Link แนะนำเพื่อน -->
                     <div class="containlinkcopy">
                        ลิงค์แนะนำ (Click ลิงค์ <?=$countClick?> ครั้ง)
                        <table style="width: 100%;">
                           <tbody>
                              <tr>
                                 <td width="80%" style="text-align: center;">
                                    <p style="display: none;" id="linkaff_">
                                      <?=base_url()?>/register?aff=<?=get_wallet("u_id")?>
                                    </p>
                                    <input style=" cursor: pointer;" class="tt_fr" readonly value="<?=base_url()?>/register?aff=<?=get_wallet("u_id")?>" onClick="this.select();execCommand('copy');" />
                                    <a style=" cursor: pointer; color: #ffc800; background: #0e0e0e; padding: 10px; border-radius: 10px;"onClick="copyToClipboard('#linkaff_')"><i class="far fa-copy"></i> คัดลอก link</a>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <!-- Link แนะนำเพื่อน -->

                  </div>
               </div>

 
            
          <form class="" id="form" method="post">

            <div class="moneycontainaf">


              <!-- จำนวนเพื่อนที่่ แนะนำขั้นที่ 1 -->
              <?php
              $countAff1 = 0;
              $q_count = dd_q('SELECT COUNT(u_id) AS oCountAff FROM user_tb WHERE u_aff = ?', [get_wallet("u_user")]);
              if ($q_count->rowCount() > 0)
              {
                $row_count = $q_count->fetch(PDO::FETCH_ASSOC);
                $countAff1 = $row_count["oCountAff"];
              }
              ?>
              <p class="textlogin0"> จำนวนเพื่อนที่่แนะนำ ขั้นที่ 1</p>
              <input class="tt_fr" readonly value="<?=$countAff1?> คน" />
              <!-- จำนวนเพื่อนที่่ แนะนำขั้นที่ 1 -->


              <!-- จำนวนเพื่อนที่่ แนะนำขั้นที่ 2 -->
              <?php
              if($row_website_tb['aff_step'] >= 2)
              {
                $countAff2 = 0;
                $q_aff1 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [get_wallet("u_user")]);
                while($row_aff1 = $q_aff1->fetch(PDO::FETCH_ASSOC))
                {
                  $q_count2 = dd_q('SELECT COUNT(u_id) AS oCountAff2 FROM user_tb WHERE u_aff = ?', [$row_aff1['u_user']]);
                  if ($q_count2->rowCount() > 0)
                  {
                    $row_count2 = $q_count2->fetch(PDO::FETCH_ASSOC);
                    $countAff2 = $countAff2 + $row_count2["oCountAff2"];
                  }
                }
              ?>
              <p class="textlogin0"> จำนวนเพื่อนที่่แนะนำ ขั้นที่ 2</p>
              <input class="tt_fr" readonly value="<?=$countAff2?> คน" />
              <?php
              }
              ?>
              <!-- จำนวนเพื่อนที่่ แนะนำขั้นที่ 2 -->


              <!-- จำนวนเพื่อนที่่ แนะนำขั้นที่ 3 -->
              <?php
              if($row_website_tb['aff_step'] == 3)
              {
                $countAff3 = 0;
                $q_aff1 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [get_wallet("u_user")]);
                while($row_aff1 = $q_aff1->fetch(PDO::FETCH_ASSOC))
                {
                  $q_aff2 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [$row_aff1['u_user']]);
                  while($row_aff2 = $q_aff2->fetch(PDO::FETCH_ASSOC))
                  {
                    $q_count3 = dd_q('SELECT COUNT(u_id) AS oCountAff3 FROM user_tb WHERE u_aff = ?', [$row_aff2['u_user']]);
                    if ($q_count3->rowCount() > 0)
                    {
                      $row_count3 = $q_count3->fetch(PDO::FETCH_ASSOC);
                      $countAff3 = $countAff3 + $row_count3["oCountAff3"];
                    }
                  }
                }
              ?>
              <p class="textlogin0"> จำนวนเพื่อนที่่แนะนำ ขั้นที่ 3</p>
              <input class="tt_fr" readonly value="<?=$countAff3?> คน" />
              <?php
              }
              ?>
              <!-- จำนวนเพื่อนที่่ แนะนำขั้นที่ 3 -->
 

              <div class="now-turn">
                <a style="color: #dc3545; font-family: thaisanslite_r1; font-size: 18px;">
                  <i class="fad fa-bullhorn color_aqua" aria-hidden="true"></i> ก่อนถอนค่าแนะนำ กรุณาเคลียยอดเทิร์นให้หมดก่อน หากถอนเงินได้ไม่ครบ ทางทีมงานจะไม่รับผิดชอบใดๆทั้งสิ้น
                </a>
              </div>
              <br>
              <div class="now-turn">
                <a style="color: #ffc800; font-family: thaisanslite_r1; font-size: 18px;"><i class="fad fa-engine-warning color_o" aria-hidden="true">
                  </i> เพื่อนต้องฝากจาก ธนาคารเท่านั้นถึงจะได้รับ % จากเพื่อน
                </a>
              </div>
              <br>
              <?php
              if($countPercent > 0)
              {
              ?>
              <button type="button" id="btn_withdraw_aff" onClick="loadingPage();" class="tt_l tt_ful fr_submit_bk">
                <i class="fas fa-hand-holding-usd"></i> ถอนค่าแนะนำเพื่อน
              </button>
              <?php
              }
              ?>
           
          </form>

            <div style="font-size: 20px; text-align:center;">
              <span style="color: red; font-weight: bold;font-size: 20px">"หากพบเห็นตั้งใจปั้ม แนะนำเพื่อนไม่ว่าจะกรณีใดก็ตามทาง ทางระบบจะยึดทันที และไม่สามารถร้องเรียนได้!!"</span>
              <br>
            </div>
            <div class="containloophisdps">

                  <?php
                  $_no = 0;
                  $q_user_tb1 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [get_session()]);
                  while($row = $q_user_tb1->fetch(PDO::FETCH_ASSOC))
                  {
                    $aff_amount = 0.00;
                    $q_aff_percent_tb = dd_q('SELECT SUM(aff_amount) AS aff_amount FROM aff_percent_tb WHERE aff_u_user_ref = ? AND aff_user  =?', [get_session(), $row['u_user']]);
                    if ($q_aff_percent_tb->rowCount() > 0)
                    {
                      $row_aff_percent = $q_aff_percent_tb->fetch(PDO::FETCH_ASSOC);
                      $aff_amount = number_format($row_aff_percent['aff_amount'], 2, '.', ',');
                    }
                    $_no++;
                  ?>
                  <div class="historyofdps">
                    <table width="100%">
                        <tbody><tr>
                           <td width="50%" style="padding-top: 7px;">
                              <table>
                                 <tbody><tr>
                                    <td style="text-align: left; line-height: 20px;">
                                      <span class="spanofbankhis">ยูสที่แนะนำ : <?=$row['u_user']?></span>
                                      <br>
                                      <span class="spanofbankhis">ชื่อ : <?=$row['u_fname']?></span>
                                  </td>
                                 </tr>
                                   
                              </tbody></table>
                           </td>
                           <td width="50%" style="text-align: right; line-height: 20px;">
                              <div class="statushistory"> <span style="background: #000; border-radius:10px; padding:5px">ขั้นที่ 1</span></div>
                              <span class="moneyhisdps"> <i class="fal fa-plus-circle plushis"></i> <?=$aff_amount?> บาท</span>
                              <br>
                              <span class="spanofbankhis">วันที่สมัคร : <?=$row['u_create_on']?></span>

                           </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <?php
                  }

                  if($row_website_tb['aff_step'] >= 2)
                  {
                    $q_user_tb1 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [get_session()]);
                    while($row1 = $q_user_tb1->fetch(PDO::FETCH_ASSOC))
                    {
                      $q_user_tb2 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [$row1['u_user']]);
                      while($row2 = $q_user_tb2->fetch(PDO::FETCH_ASSOC))
                      {
                        $aff_amount = 0.00;
                        $q_aff_percent_tb = dd_q('SELECT SUM(aff_amount) AS aff_amount FROM aff_percent_tb WHERE aff_u_user_ref = ? AND aff_user=?', [
                          get_session(), 
                          $row2['u_user']
                        ]);
                        if ($q_aff_percent_tb->rowCount() > 0)
                        {
                          $row_aff_percent = $q_aff_percent_tb->fetch(PDO::FETCH_ASSOC);
                          $aff_amount = number_format($row_aff_percent['aff_amount'], 2, '.', ',');
                        }
                        $_no++;
                        ?>
                        <div class="historyofdps">
                          <table width="100%">
                              <tbody><tr>
                                <td width="50%" style="padding-top: 7px;">
                                    <table>
                                      <tbody><tr>
                                          <td style="text-align: left; line-height: 20px;">
                                            <span class="spanofbankhis">ยูสที่แนะนำ : <?=$row2['u_user']?></span>
                                            <br>
                                            <span class="spanofbankhis">ชื่อ : <?=$row2['u_fname']?></span>
                                        </td>
                                      </tr>
                                        
                                    </tbody></table>
                                </td>
                                <td width="50%" style="text-align: right; line-height: 20px;">
                                    <div class="statushistory"> <span style="background: #000; border-radius:10px; padding:5px">ขั้นที่ 2</span></div>
                                    <span class="moneyhisdps"> <i class="fal fa-plus-circle plushis"></i> <?=$aff_amount?> บาท</span>
                                    <br>
                                    <span class="spanofbankhis">วันที่สมัคร : <?=$row2['u_create_on']?></span>

                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <?php
                      }
                    }
                  }

                  if($row_website_tb['aff_step'] == 3)
                  {
                    $q_user_tb1 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [get_session()]);
                    while($row1 = $q_user_tb1->fetch(PDO::FETCH_ASSOC))
                    {
                      $q_user_tb2 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [$row1['u_user']]);
                      while($row2 = $q_user_tb2->fetch(PDO::FETCH_ASSOC))
                      {
                        $q_user_tb3 = dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [$row2['u_user']]);
                        while($row3 = $q_user_tb3->fetch(PDO::FETCH_ASSOC))
                        {
                          $aff_amount = 0.00;
                          $q_aff_percent_tb = dd_q('SELECT SUM(aff_amount) AS aff_amount FROM aff_percent_tb WHERE aff_u_user_ref = ? AND aff_user=?', [
                            get_session(), 
                            $row3['u_user']
                          ]);
                          if ($q_aff_percent_tb->rowCount() > 0)
                          {
                            $row_aff_percent = $q_aff_percent_tb->fetch(PDO::FETCH_ASSOC);
                            $aff_amount = number_format($row_aff_percent['aff_amount'], 2, '.', ',');
                          }
                          $_no++;
                          ?>
                          <div class="historyofdps">
                            <table width="100%">
                                <tbody><tr>
                                  <td width="50%" style="padding-top: 7px;">
                                      <table>
                                        <tbody><tr>
                                            <td style="text-align: left; line-height: 20px;">
                                              <span class="spanofbankhis">ยูสที่แนะนำ : <?=$row3['u_user']?></span>
                                              <br>
                                              <span class="spanofbankhis">ชื่อ : <?=$row3['u_fname']?></span>
                                          </td>
                                        </tr>
                                          
                                      </tbody></table>
                                  </td>
                                  <td width="50%" style="text-align: right; line-height: 20px;">
                                      <div class="statushistory"> <span style="background: #000; border-radius:10px; padding:5px">ขั้นที่ 3</span></div>
                                      <span class="moneyhisdps"> <i class="fal fa-plus-circle plushis"></i> <?=$aff_amount?> บาท</span>
                                      <br>
                                      <span class="spanofbankhis">วันที่สมัคร : <?=$row3['u_create_on']?></span>

                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                          <?php
                        }
                      }
                    }
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