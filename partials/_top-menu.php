<div class="tt-l tt-full">
  <div class="tt_l tt_full header_bg">
    <div class="tt_l tt_full header">
      <div class="wrapper">

        <a class="tt_l logo_pc" title="<?=$_CONFIG['title']?>" href="<?=base_url()?>/home"></a>
        <a class="tt_l logo_m" href="<?=base_url()?>/home">
          <!-- <img src="<?=base_url()?>/images/slotband-2.jpg"/> -->
        </a>

        <div class="tt_l tt_head_menu m-mc">
          <div class="wrapper">
            <div class="menu-menu-mobile-container">
              <ul id="menu-menu-mobile" class="nav">
                <li id="menu-item-66" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-77">
                  <a href="<?=base_url()?>/home" aria-current="page" onclick="loadingPage();">หน้าหลัก</a>
                </li>
                <li id="menu-item-77" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-77">
                  <a href="<?=base_url()?>/promotion" onclick="loadingPage();">โปรโมชั่น</a>
                </li>
                <li id="menu-item-70" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-70">
                  <a href="<?=$_CONFIG['urlline']?>" target="_blank">ติดต่อเรา</a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="tt_r tt_live if_login">
          <a href="<?=base_url()?>/login" title=" สมัครสมาชิก" onclick="loadingPage();">
            <img src="<?=base_url()?>/themes/v2/images/auto.gif"/>
          </a>
        </div>
      </div>
    </div>

    <div class="tt_l tt_full m_pc">
      <div class="wrapper">
        <div class="tt_l menu_l">
          <div class="tt_l tt_head_menu">
            <div class="menu-menu-left-container">
              <ul id="menu-menu-left" class="nav">
                <li id="menu-item-52" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-52">
                  <a href="<?=base_url()?>/home" aria-current="page" onclick="loadingPage();">หน้าหลัก</a>
                </li>
                <li id="menu-item-111" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-111">
                  <a href="<?=base_url()?>/promotion" onclick="loadingPage();">โปรโมชั่น</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <?php
        if(!empty(get_session()))
        {
        ?>
        <div class="tt_l menu_c">
          <a href="<?=base_url()?>/wallet" onclick="loadingPage();"> ข้อมูลส่วนตัว</a>
        </div>
        <?php
        }
        else
        {
        ?>
        <div class="tt_l menu_c">
          <a href="<?=base_url()?>/register" onclick="loadingPage();"> สมัครสมาชิก</a>
        </div>
        <?php
        }
        ?>
        <div class="tt_l menu_r">
          <div class="tt_l tt_head_menu">
            <div class="menu-menu-right-container">
              <ul id="menu-menu-right" class="nav">
                <li id="menu-item-116" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-116">
                  <a href="#">วิธีสมัคร</a>
                </li>
                <li id="menu-item-59" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-59">
                  <a href="<?=$_CONFIG['urlline']?>" target="_blank">ติดต่อเรา</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>