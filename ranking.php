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
  if($row_website_tb['status_ranking'] == "0")
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
  </div> -->
  <!-- top logo add line for mobile -->

  <!-- top-menu -->
  <?php include("partials/_top-menu.php"); ?>
  <!-- top-menu -->

  <!-- slide -->
  <?php include("partials/_slide.php"); ?>
  <!-- slide -->
<style>

.rankingpadding {
  padding:10px;
  float:left;
  width: 20%;
}
@media screen and (max-width:980px) {
  .rankingpadding {
  width: 100%;
  }

}
.rankingpadding .rankingbox {

  padding:10px;
  border-radius:10px;
  float:left;
  width: 100%;
  background: #896b00;
  border: 3px solid #ffb100;
  color: white;
}
.rankingpadding .rankingboxt {

padding:10px;
border-radius:10px;
float:left;
width: 100%;
background: #148922;
border: 3px solid #00eb3b;
color: white;
}
 
</style>
  <div class="wrapper">
    <div class="tt_l tt_full p_gl">
      <div id="ct_from" class="tt_l tt_full title_page">
        <h1>จัดอันดับผู้เล่น 20 คนถอนเงินสูงสุดประจำวันที่ <?=date("d/m/Y")?></h1>
      </div>
      <div class="tt_l tt_full tt_content bg" style="background-color: #0a0a0a !important;">
                <?php
                $_no = 1;
                $q_topup_db = dd_q('SELECT SUM(w_amount) AS w_amount, CONCAT(SUBSTRING(w_user, 1, 6), "****") AS n_user FROM withdraw_tb WHERE w_date_create = ? AND w_status = ? GROUP BY w_user ORDER BY w_amount DESC LIMIT 20', [
                  date("Y-m-d"),
                  '1'
                ]);
                while($row = $q_topup_db->fetch(PDO::FETCH_ASSOC))
                {
                ?>
                <div class="rankingpadding">
                  <div class="rankingbox">
                    <table width="100%"   style="vertical-align: top;">
                      <tr>
                        <td rowspan="2"><img src="images/ranking.png" width="50" height="50" /></td>
                        <td style="vertical-align: top;"><?=$row['n_user']?></td>
                      </tr>
                      <tr>
                        <td style="vertical-align: top;">อันดับที่ <?=$_no?></td>
                      </tr>
                      <tr>
                        <td></td>
                        <td style="vertical-align: top;">จำนวน : <?=number_format($row['w_amount'], 2, '.', ',')?></td>
                      </tr>
                    </table>
                  </div>
                </div>


                <?php
                  $_no++;
                }
                ?>
 
      </div>

      <div class="tt_l tt_full title_page">
        <h1>จัดอันดับผู้เล่น 20 คนฝากเงินสูงสุดประจำวันที่ <?=date("d/m/Y")?></h1>
      </div>
      <div class="tt_l tt_full tt_content bg" style="background-color: #0a0a0a !important;">
                <?php
                $_no = 1;
                $q_topup_dbt = dd_q('SELECT SUM(t_amount) AS t_amount, CONCAT(SUBSTRING(t_user, 1, 6), "****") AS n_user FROM topup_db WHERE t_date_create = ? AND t_status = ? GROUP BY t_user ORDER BY t_amount DESC LIMIT 20', [
                  date("Y-m-d"),
                  '1'
                ]);
                while($rowt = $q_topup_dbt->fetch(PDO::FETCH_ASSOC))
                {
                ?>
                <div class="rankingpadding">
                  <div class="rankingboxt">
                    <table width="100%"   style="vertical-align: top;">
                      <tr>
                        <td rowspan="2"><img src="images/rankingt.png" width="50" height="50" /></td>
                        <td style="vertical-align: top;"><?=$rowt['n_user']?></td>
                      </tr>
                      <tr>
                        <td style="vertical-align: top;">อันดับที่ <?=$_no?></td>
                      </tr>
                      <tr>
                        <td></td>
                        <td style="vertical-align: top;">จำนวน : <?=number_format($rowt['t_amount'], 2, '.', ',')?></td>
                      </tr>
                    </table>
                  </div>
                </div>


                <?php
                  $_no++;
                }
                ?>
 
      </div>

    </div>
  </div>

  <!-- footer -->
  <?php include("partials/_footer.php"); ?>
  <!-- footer -->

</body>
</html>