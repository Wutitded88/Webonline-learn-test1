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
        
           <div class="containmoney warp-up">
         <table width="100%">
            <tbody>
              
              <tr>
                <td style="padding-left: 20px;">&nbsp;</td>
                <td style="text-align: right;">&nbsp;</td>
              </tr>
              <tr>
               <td width="50%" style="padding-left: 20px;">
                  <i onClick="opentab(event, 'section01')" id="opensection01" style="cursor: pointer;" class="fal fa-wallet"></i>               </td>
               <td width="50%" style="text-align: right;">
                  <span style="font-size: 17px; color: #fff;"><i class="fad fa-coins"></i> ยอดเครดิตของคุณ </span>
                  <br>
                  <span id="credit_total_balance" style="color:#ecc568; font-size: 35px;">Loading...</span>  </td>
            </tr>
         </tbody></table>
         <hr style="margin: 10px; border-top:1px solid #68635c;">
         <div style="padding-left: 20px;margin-top: 10px; margin-bottom: 15px;">
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
              if($row_transfer["t_promotion_id"] == "3" && strpos($row_transfer["t_promotion_title"], 'ฟรีเครดิต') !== false)
              {
                $wd_max = true;
              }
              $t_over = $row_transfer["t_turnover"];
            }
          }
          ?>
            <i class="fad fa-gift" style="color:#fad275;"></i><a style="color: #f8f8f8;"> โปรโมชั่นล่าสุดที่รับ<span style="color: #cead5e;">  <?=$last_pro?></span></a>
         </div>
 
        </div>
      </div>

      <div class="tt_l tt_full tt_content bg">
        <div class="tt_l tt_full">
          <div class="tt_l tt_full gr_btn" style="border-bottom: 2px solid rgb(253 162 49);padding-bottom: 20px;">
            <div class="grup_b grup_b1 blobgame bluegame active">
              <img src="<?=base_url()?>/images/g_menu/g_menu1.png"/><span>ทั้งหมด</span>
            </div>
            <div class="grup_b grup_b2 blobgame bluegame">
              <img src="<?=base_url()?>/images/g_menu/g_menu2.png"/><span>คาสิโน ออนไลน์</span>
            </div>
            <div class="grup_b grup_b3 blobgame bluegame">
              <img src="<?=base_url()?>/images/g_menu/g_menu3.png"/><span>สล๊อต ออนไลน์</span>
            </div>
            <div class="grup_b grup_b4 blobgame bluegame">
              <img src="<?=base_url()?>/images/g_menu/g_menu4.png"/><span>เกมยิงปลา ออนไลน์</span>
            </div>
            <div class="grup_b grup_b5 blobgame bluegame">
              <img src="<?=base_url()?>/images/g_menu/g_menu5.png"/><span>เกมไพ่</span>
            </div>
            <div class="grup_b grup_b6 blobgame bluegame">
              <img src="<?=base_url()?>/images/g_menu/g_menu6.png"/><span>หวย/กีฬา</span>
            </div>
          </div>

          <div class="tt_l tt_full gp1 active">
            <div class="row">
              <?php
              $q_gp1 = dd_q('SELECT * FROM game_tb WHERE g_type LIKE ? AND g_status = ? ORDER BY g_seq', ['%1%', '1']);
              while($row = $q_gp1->fetch(PDO::FETCH_ASSOC))
              {
              ?>
              <div class="tt_l tt_col_4x post-gslot warp-up" style="border: 0px;">
                <div class="filter_grid grid-item show_g_grid" style="margin-top: 10px;">
                  <div class="bg-gradient-game_grid">
                    <?php
                    if($row['g_gamelist'] == "")
                    {
                    ?>
                    <a href="javascript:void(0)" onClick="popitup('<?=$row['g_provider']?>', '<?=$row['g_api_category']?>')">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
                    <?php
                    }
                    else
                    {
                    ?>
                    <a href="<?=base_url()?>/gambling/<?=$row['g_gamelist']?>">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
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

          <div class="tt_l tt_full gp2">
            <div class="row">
              <?php
              $q_gp2 = dd_q('SELECT * FROM game_tb WHERE g_type LIKE ? AND g_status = ? ORDER BY g_seq', ['%2%', '1']);
              while($row = $q_gp2->fetch(PDO::FETCH_ASSOC))
              {
              ?>
              <div class="tt_l tt_col_4x post-gslot warp-up" style="border: 0px;">
                <div class="filter_grid grid-item show_g_grid" style="margin-top: 10px;">
                  <div class="bg-gradient-game_grid">
                    <?php
                    if($row['g_gamelist'] == "")
                    {
                    ?>
                    <a href="javascript:void(0)" onClick="popitup('<?=$row['g_provider']?>', '<?=$row['g_api_category']?>')">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                        <div class=""></div>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
                    <?php
                    }
                    else
                    {
                    ?>
                    <a href="<?=base_url()?>/gambling/<?=$row['g_gamelist']?>">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
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

          <div class="tt_l tt_full gp3">
            <div class="row">
              <?php
              $q_gp3 = dd_q('SELECT * FROM game_tb WHERE g_type LIKE ? AND g_status = ? ORDER BY g_seq', ['%3%', '1']);
              while($row = $q_gp3->fetch(PDO::FETCH_ASSOC))
              {
              ?>
              <div class="tt_l tt_col_4x post-gslot warp-up" style="border: 0px;">
                <div class="filter_grid grid-item show_g_grid" style="margin-top: 10px;">
                  <div class="bg-gradient-game_grid">
                    <?php
                    if($row['g_gamelist'] == "")
                    {
                    ?>
                    <a href="javascript:void(0)" onClick="popitup('<?=$row['g_provider']?>', '<?=$row['g_api_category']?>')">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
                    <?php
                    }
                    else
                    {
                    ?>
                    <a href="<?=base_url()?>/gambling/<?=$row['g_gamelist']?>">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
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

          <div class="tt_l tt_full gp4">
            <div class="row">
              <?php
              $q_gp4 = dd_q('SELECT * FROM game_tb WHERE g_type LIKE ? AND g_status = ? ORDER BY g_seq', ['%4%', '1']);
              while($row = $q_gp4->fetch(PDO::FETCH_ASSOC))
              {
              ?>
              <div class="tt_l tt_col_4x post-gslot warp-up" style="border: 0px;">
                <div class="filter_grid grid-item show_g_grid" style="margin-top: 10px;">
                  <div class="bg-gradient-game_grid">
                    <?php
                    if($row['g_gamelist'] == "")
                    {
                    ?>
                    <a href="javascript:void(0)" onClick="popitup('<?=$row['g_provider']?>', '<?=$row['g_api_category']?>')">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
                    <?php
                    }
                    else
                    {
                    ?>
                    <a href="<?=base_url()?>/gambling/<?=$row['g_gamelist']?>">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
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

          <div class="tt_l tt_full gp5">
            <div class="row">
              <?php
              $q_gp4 = dd_q('SELECT * FROM game_tb WHERE g_type LIKE ? AND g_status = ? ORDER BY g_seq', ['%5%', '1']);
              while($row = $q_gp4->fetch(PDO::FETCH_ASSOC))
              {
              ?>
              <div class="tt_l tt_col_4x post-gslot warp-up" style="border: 0px;">
                <div class="filter_grid grid-item show_g_grid" style="margin-top: 10px;">
                  <div class="bg-gradient-game_grid">
                    <?php
                    if($row['g_gamelist'] == "")
                    {
                    ?>
                    <a href="javascript:void(0)" onClick="popitup('<?=$row['g_provider']?>', '<?=$row['g_api_category']?>')">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
                    <?php
                    }
                    else
                    {
                    ?>
                    <a href="<?=base_url()?>/gambling/<?=$row['g_gamelist']?>">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
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

          <div class="tt_l tt_full gp6">
            <div class="row">
              <?php
              $q_gp4 = dd_q('SELECT * FROM game_tb WHERE g_type LIKE ? AND g_status = ? ORDER BY g_seq', ['%6%', '1']);
              while($row = $q_gp4->fetch(PDO::FETCH_ASSOC))
              {
              ?>
              <div class="tt_l tt_col_4x post-gslot warp-up" style="border: 0px;">
                <div class="filter_grid grid-item show_g_grid" style="margin-top: 10px;">
                  <div class="bg-gradient-game_grid">
                    <?php
                    if($row['g_gamelist'] == "")
                    {
                    ?>
                    <a href="javascript:void(0)" onClick="popitup('<?=$row['g_provider']?>', '<?=$row['g_api_category']?>')">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
                    <?php
                    }
                    else
                    {
                    ?>
                    <a href="<?=base_url()?>/gambling/<?=$row['g_gamelist']?>">
                      <div class="image shadow">
                        <?php
                        if($row['g_new'] == "1")
                        {
                        ?>
                        <img src="<?=base_url()?>/images/new.png" style="position: absolute; width: 60px; left: -9px; top: -10px; z-index: 999;">
                        <?php
                        }
                        ?>
                        <img src="<?=base_url()?>/<?=$row['g_img']?>" class="img-fluid logoGame <?php if($row['g_closegame'] == 0) { echo "closegameimg"; } ?>">
                        <?php if($row['g_closegame'] == 0) { echo "<div class='closegame'><i class='fad fa-cogs'></i> ปิดปรับปรุง</div>"; } ?>
                      </div>
                      <div class="text-center">
                        <span class="grup_black"><?=$row['g_name']?></span>
                      </div>
                    </a>
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

  <!-- footer -->
  <?php include("partials/_footer.php"); ?>
  <!-- footer -->

  <script type="text/javascript">
    function popitup(provider, gamemode, gameId = '', w=1024, h=600)
    {
      $('#loading').show();
      var left = (screen.width / 2) - (w / 2);
      var top = (screen.height / 2) - (h / 2);

      var formData = new FormData();
      formData.append('provider', provider);
      formData.append('gamemode', gamemode);
      if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4)))
      { 
        formData.append('isMobile', true);
      }
      else
      {
        formData.append('isMobile', false);
      }
      if (/iPad|iPhone|iPod|ipad|iphone|ipod/.test(navigator.userAgent))
      {
        formData.append('osMobile', 'ios');
      }
      else
      {
        formData.append('osMobile', 'android');
      }
      formData.append('gameId', gameId);
      $.ajax({
        type: 'POST',
        url: 'system/gettoken',
        data:formData,
        contentType: false,
        processData: false,
      }).done(function(res) {
        window.location.assign(res.message, provider, 'fullscreen=yes, toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left, true);
      }).fail(function(jqXHR) {
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
        });
        console.clear();
      });
    }

    document.addEventListener('touchmove', function (event) {
        event = event.originalEvent || event;
        if (event.scale !== 1) {
            event.preventDefault();
        }
    }, false);

    if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
        window.document.addEventListener('touchmove', e => {
            if (e.scale !== 1) {
                e.preventDefault();
            }
        }, {
            passive: false
        });
    }
  </script>

</body>
</html>