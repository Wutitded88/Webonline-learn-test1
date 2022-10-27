<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $row_u['title']; ?></title>
<link href="<?php echo $row_u['logo']; ?>?v=1.5" rel="shortcut icon" />
<link href="<?=base_url()?>/assets/plugins/fontawesome/css/all.min.css?v=2.5" rel="stylesheet" />
<link href="<?=base_url()?>/assets/dist/css/adminlte.min.css" rel="stylesheet" />
<link href="<?=base_url()?>/themes/v2/css/css-v2c0a9.css?v=3.9" rel="stylesheet" type="text/css" />
<link href="<?=base_url()?>/themes/v2/css/framework.css?v=2.5" rel="stylesheet" type="text/css" />
<link href="<?=base_url()?>/themes/v2/css/animatef195.css?v=2.5" rel="stylesheet" type="text/css" />
<link href="<?=base_url()?>/themes/v2/css/imagehover.min6da2.css?v=2.3" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?=base_url()?>/plugins/sweetalert2/dist/sweetalert2.min.css">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

<meta name="description"  content="<?php echo $row_u['description']; ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo $row_u['title']; ?>" />
<meta property="og:description" content="<?php echo $row_u['description']; ?>" />
<meta property="og:url" content="<?=base_url()?>" />
<meta property="og:site_name" content="<?php echo $row_u['namesite']; ?>" />
<meta property="og:image" content="<?php echo $row_u['logo']; ?>" />
<meta property="og:image:secure_url" content="<?php echo $row_u['logo']; ?>" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?php echo $row_u['title']; ?>" />
<meta name="twitter:description" content="<?php echo $row_u['description']; ?>" />
<meta name="twitter:image" content="<?php echo $row_u['logo']; ?>" />
<meta name="keywords" content="<?php echo $row_u['keyword']; ?>" />

<link rel='stylesheet' id='wp-customer-reviews-3-frontend-css' href='<?=base_url()?>/plugins/wp-customer-reviews/css/wp-customer-reviews-generatede248.css?ver=3.5.4' type='text/css' media='all' />
<link rel='stylesheet' id='wp-block-library-css' href='<?=base_url()?>/css/dist/block-library/style.min7661.css?ver=5.4.2' type='text/css' media='all' />
<link rel='stylesheet' id='addtoany-css' href='<?=base_url()?>/plugins/add-to-any/addtoany.min9be6.css?ver=1.15' type='text/css' media='all' />
<script type='text/javascript' src='<?=base_url()?>/js/jquery/jquery4a5f.js?ver=1.12.4-wp'></script>
<script type='text/javascript' src='<?=base_url()?>/js/jquery/jquery-migrate.min330a.js?ver=1.4.1'></script>
<script type='text/javascript' src='<?=base_url()?>/plugins/wp-customer-reviews/js/wp-customer-reviewse248.js?ver=3.5.4'></script>
<script type='text/javascript' src='<?=base_url()?>/plugins/add-to-any/addtoany.min4963.js?ver=1.1'></script>
<script type='text/javascript' src="<?=base_url()?>/plugins/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script src="https://kit.fontawesome.com/adb36dfc51.js" crossorigin="anonymous"></script>

<style>
	body {
		background-color:#000;
		background-repeat:repeat;
		background-size:cover;
		background-attachment:fixed;
		background-position:center center;
		background-image:linear-gradient(rgb(0 0 0 / 0%), rgb(0 0 0 / 0%)),url('<?php echo $row_u['bg']; ?>');
	}

    .login {
      padding: 15px;
      border-radius: 7px;
      background: #272727;
      /*box-shadow: 0px 5px 15px 0px #0000003d;*/
      box-shadow: inset 0 0 7px #f1d58d, 0 0 10px #f1d58d;
      border: 1px solid #f1d58d;
    }
    .input-group>.input-group-append:last-child>.btn:not(:last-child):not(.dropdown-toggle), .input-group>.input-group-append:last-child>.input-group-text:not(:last-child), .input-group>.input-group-append:not(:last-child)>.btn, .input-group>.input-group-append:not(:last-child)>.input-group-text, .input-group>.input-group-prepend>.btn, .input-group>.input-group-prepend>.input-group-text {
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
    }
    .input-group-text {
      color: #3c2701;
      border-color: #bda157;
      /* box-shadow: 2px 5px 8px rgba(0, 0, 0, 0); */
      background-image: linear-gradient(to bottom, #decea6, #fdf0bc, #ae8e3f);
    }
    .input-group-text {
      display: -ms-flexbox;
      display: flex;
      -ms-flex-align: center;
      align-items: center;
      padding: .375rem .75rem;
      margin-bottom: 0;
      font-size: 1rem;
      font-weight: 400;
      line-height: 1.5;
      color: #495057;
      text-align: center;
      white-space: nowrap;
      background-color: #e9ecef;
      border: 1px solid #ced4da;
      border-radius: .25rem;
    }
    .form-control {
      color: #d4d4d4;
      border: 1px solid #717171;
      display: block;
      width: 100%;
      padding: .375rem .75rem;
      font-size: 1rem;
      font-weight: 400;
      line-height: 1.5;
      /* color: #495057; */
      background-color: #353535;
      background-clip: padding-box;
      border-radius: .25rem;
      transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    .btn-primary {
      border-color: #a77c3a73;
      color: #443319 !important;
      background: linear-gradient(135deg, rgba(167,124,58,1) 0%, rgba(167,124,58,1) 25%, rgba(242,214,142,1) 50%, rgba(167,124,58,1) 78%, rgba(167,124,58,1) 100%);
    }

  	header {
  		width: 100%;
    	/*height: 85px;*/
    	color: #fff;
    	/*background-color: rgba(0, 0, 0, .5);*/
    	/*background: #382a00!important;*/
    	/*border-bottom: 1px solid rgba(0, 0, 0, .2);*/
	}
	.navigation {
	    position: fixed;
	    display: flex;
	    z-index: 9999;
	    bottom: 0;
	    width: 100%;
	}
	.nav-footer {
    	background: #2b2b2b !important;
    }
    .navigation-nav {
	    display: flex;
	}
	.navigation-nav .list-inline-item {
	    display: flex;
	    flex: 1;
	    font-size: .8rem;
	    font-weight: 400;
	    justify-content: center;
	}
	.navigation a {
	    color: #fff;
	    margin-bottom: 5px;
	}
	.exchange {
	    border-radius: 50%;
	    display: block;
	    width: 50px;
	    height: 50px;
	    position: absolute;
	    border: 2px solid #ccc;
	    bottom: 20px;
	    color: #033!important;
	    background: #2b2b2b !important;
	}
	.exchange i {
	    color: #fff;
	    margin-top: 8px;
	}
	.navigation i {
	    font-size: 1.2rem;
	    margin-bottom: 5px;
	    color: #fff;
	    padding: 5px;
	}
	.list-inline-item p {
	    margin: 0;
	    position: absolute;
	    left: 0;
	    right: 0;
	    bottom: -22px;
	    color: #fff;
	    text-shadow: 1px 1px 2px rgba(0,0,0,.1);
	}
    .nav-top {
    	overflow: hidden;
	    position: fixed;
	    display: flex;
	    z-index: 9999;
	    top: 0;
	    width: 100%;
	    background-color: #2b2b2b !important;
	}
	.img-top {
	    width: 70px;
	    margin-top: 10px;
	    position: fixed;
	    margin-left: auto;
	    margin-right: auto;
	    left: 0;
	    right: 0;
	}
    .bg-login {
	    color: #fff;
	    height: 100vh !important;
	    font-size: 14px;
	    overflow-y: hidden!important;
	    overflow-x: hidden!important;
	    background-size: contain;
	    background-repeat: no-repeat;
	}
	.my-login-page .brand {
	    overflow: hidden;
	    border-radius: 1rem;
	    margin: 20px 0;
	    box-shadow: 0 4px 8px rgba(0, 0, 0, .05);
	    z-index: 1;
	}
	.card {
	    border-radius: 1rem;
	    background-color: rgba(0, 0, 0, .5)!important;
	    text-shadow: 1px 1px 4px rgba(0, 0, 0, .68);
	}

	.card-trans {
		background: linear-gradient(45deg,#926f00 10%,#ab820f 90%)!important;
		box-shadow: 2px 2px 5px 1px rgba(0,0,0,.4)!important;
	}

	form .input-group-text {
	    width: 40px;
	    text-align: center;
	    clear: both;
	    justify-content: center;
	}
	.input-group-prepend {
	    margin-right: -1px;
	}
	.input-group-prepend span {
	    width: 50px;
	    background-color: rgba(0, 0, 0, .3);
	    color: #fff;
	    border: 0!important;
	}
	.login_btn {
	    background-color: #b79525!important;
	}
	.content-heading {
	    color: #fff;
	    border-bottom: 1px solid rgba(255,255,255,.2);
	    font-size: 1.3rem;
	    padding-bottom: .5rem;
	    margin-bottom: 1rem;
	}
	.transfer-slide-img {
	    margin-bottom: 5px;
	    border: 1px solid rgba(255,255,255,.3);
	}
	.cut-text {
	    width: 100%;
	    overflow: hidden;
	    display: inline-block;
	    text-overflow: ellipsis;
	    white-space: nowrap;
	}
	.wallet-heading {
	    font-size: 1rem;
	    letter-spacing: 1px;
	    padding-bottom: 0.5rem;
	}
	.wallet-money {
	    font-size: 2rem;
	    color: #fff;
	}
	.wallet-balance {
	    font-size: 1.5rem;
	}
	.main-menu-item {
	    padding-top: 1rem;
	    padding-bottom: 1rem;
	}
	.main-menu-item a {
	    color: rgba(255,255,255,.8);
	}
	.zoom {
	  transition: transform .2s;
	  margin: 0 auto;
	}

	.zoom:hover {
	  transform: scale(1.5);
	}
	.notice {
	    font-size: 13px;
	    color: white;
	    display: flex;
	    flex-direction: row;
	    padding: 10px 15px;
	    background: #2b2b2b !important;
	    margin-bottom: 15px;
	    font-family: sans-serif;
	}
	.intro-text {
		background: -webkit-linear-gradient(top, #ffffeb 0%, #fffe54 31%, #fffe64 31%, #ffd500 56%, #ffad00 82%, #ab7e1e 82%, #f1d64a 100%);
		background: linear-gradient(top, #ffffeb 0%, #fffe54 31%, #fffe64 31%, #ffd500 56%, #ffad00 82%, #ab7e1e 82%, #f1d64a 100%);
		-webkit-text-fill-color: transparent;
		-webkit-background-clip: text;
	}
	.manage-txt {
	    text-align: center;
	    font-size: 12px;
	    color: #DDD;
	    text-shadow: 0px 0px 4px #333333;
	}


	.owl-expansion-panel {
	    display: block;
	    box-sizing: content-box;
	    margin: 0;
	    transition: margin 225ms cubic-bezier(.4,0,.2,1);
	    color: rgba(0,0,0,.85);
	    box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);
	}
	.owl-expansion-panel {
	    background: #e4e4e4!important;
	    border-radius: 5px;
	}
	.owl-expansion-panel-header:not([aria-disabled=true]) {
	    cursor: pointer;
	}
	.owl-expansion-panel-header {
	    display: block;
	}
	.owl-expansion-panel-header .owl-expansion-panel-header-wrapper {
	    display: flex;
	    align-items: center;
	    height: 100%;
	    padding: 0 1.5em;
	}
	.owl-expansion-panel-header-wrapper {
	    padding: 0 .5em!important;
	    vertical-align: middle!important;
	    color: #fff!important;
	}
	.owl-expansion-panel-header-content {
	    display: flex;
	    flex-grow: 1;
	    font-size: .9em;
	    font-weight: 400;
	    overflow: hidden;
	}
	.owl-expansion-panel-header-title {
	    color: rgba(0,0,0,.85);
	}
	.owl-expansion-panel-header-description {
	    color: rgba(0,0,0,.55);
	}
	.owl-expansion-panel-header-description, .owl-expansion-panel-header-title {
	    /*display: flex;*/
	    text-align: right;
	    flex-grow: 1;
	    margin-right: 1em;
	}
	.owl-expansion-indicator {
	    display: flex;
	    justify-content: center;
	    align-items: center;
	    width: .7em;
	    height: .7em;
	}
	.owl-expansion-indicator svg {
	    width: 100%;
	    height: 100%;
	    fill: rgba(0,0,0,.55);
	}
	.owl-expansion-panel-body {
	    padding: 0 .5em .5em!important;
	    vertical-align: middle!important;
	}
	.fontsize10 {
		font-size: 10px !important;
	}
	.fontsize11 {
		font-size: 11px !important;
	}
	.fontsize12 {
		font-size: 12px !important;
	}
	.fontsize13 {
		font-size: 13px !important;
	}
	.fontsize14 {
		font-size: 14px !important;
	}
	.fontsize15 {
		font-size: 15px !important;
	}
	.fontsize16 {
		font-size: 16px !important;
	}
	.fontsize17 {
		font-size: 17px !important;
	}
	.fontsize18 {
		font-size: 18px !important;
	}
	.fontsize19 {
		font-size: 19px !important;
	}
	.fontsize20 {
		font-size: 20px !important;
	}
	.modal .modal-content {
	    box-shadow: 0px 0px 23px black;
	    border: 2px solid #353535;
	}
	#transferModal .modal-content {
	    color: #8a8a8a;
	    background: -webkit-linear-gradient(right, #464646, #232323);
	    font-weight: 500;
	}
	.noticemodel h5 {
	    border-left: 5px solid #fcdc76;
	    padding-left: 5px;
	}
	.breadcrumb-item a {
	    color: #adadad !important;
	}
	.list-group-item {
		background-color: rgba(0, 0, 0, 0)!important;
	}
	.alert-info {
	    color: #0c5460 !important;
	    background-color: #d1ecf1 !important;
	    border-color: #bee5eb !important;
	}
	.alert-danger {
	    color: #5f0000 !important;
	    background-color: #f1d1d1 !important;
	    border-color: #f1d1d1 !important;
	}
	.text-shadow-none {
		text-shadow: none !important;
	}
	.nav-pills .nav-link.active, .nav-pills .show>.nav-link {
	    color: #fff;
	    background-color: #3e3e3e;
	}
	.nav-pills .nav-link {
	    color: #fff;
	}
	.text-debug {
	    background-color: #3e3e3e;
	    padding: 3px 15px;
	    border-radius: 13px;
	    color: #fff !important;
	    font-size: 12px;
	    box-shadow: 2px 4px 5px rgba(0, 0, 0, 0.25);
	}
	#loading {
	    height: 100%;
	    width: 100%;
	    position: fixed;
	    z-index: 99999;
	    left: 0;
	    top: 0;
	    color: white;
	    background-color: rgb(0,0,0);
	    background-color: rgba(0,0,0, 0.9);
	    overflow-x: hidden;
	    transition: 0.5s;
	}
	#loading .loading-content {
	    position: relative;
	    top: 30%;
	    width: 100%;
	    text-align: center;
	}
	.loadingelement {
  animation: 
    nudge 1s linear infinite alternate;
	}
	@keyframes nudge {
	0% {	
		transform: translate(0, 0);
	}
	
	50% {
		transform: translate(0px, 20px);
	}
	
	100% {
		transform: translate(0px, 0);
	}
	}
	.sk-wave {
	    margin: 40px auto;
	    /*width: 50px;
	    height: 40px;*/
	    text-align: center;
	    font-size: 10px;
	}
	.sk-wave .sk-rect {
	    background-color: #fff;
	    height: 100%;
	    width: 6px;
		border-radius:20px;
	    display: inline-block;
	    -webkit-animation: sk-waveStretchDelay 1.2s infinite ease-in-out;
	    animation: sk-waveStretchDelay 1.2s infinite ease-in-out;
	}
	.sk-wave .sk-rect1 {
	    -webkit-animation-delay: -1.2s;
	    animation-delay: -1.2s;
	}
	.sk-wave .sk-rect2 {
	    -webkit-animation-delay: -1.1s;
	    animation-delay: -1.1s;
	}
	.sk-wave .sk-rect3 {
	    -webkit-animation-delay: -1s;
	    animation-delay: -1s;
	}
	.sk-wave .sk-rect4 {
	    -webkit-animation-delay: -0.9s;
	    animation-delay: -0.9s;
	}
	.sk-wave .sk-rect5 {
	    -webkit-animation-delay: -0.8s;
	    animation-delay: -0.8s;
	}
	@-webkit-keyframes sk-waveStretchDelay {
		0%,
		40%,
		100% {
			-webkit-transform: scaleY(0.4);
			transform: scaleY(0.4);
		}
		20% {
			-webkit-transform: scaleY(1);
			transform: scaleY(1);
		}
	}

	@keyframes sk-waveStretchDelay {
		0%,
		40%,
		100% {
			-webkit-transform: scaleY(0.4);
			transform: scaleY(0.4);
		}
		20% {
			-webkit-transform: scaleY(1);
			transform: scaleY(1);
		}
	}

	.g-recaptcha div {
		margin-left: auto;
  		margin-right: auto;
	}

	.swal2-popup {
	    font-family: thaisanslite_r1 !important;
	}

	.text-uppercase {
		text-transform: uppercase!important
	}

	.hx_slot {
	    font-size: 36px;
	    line-height: 36px;
	    text-align: center;
	    color: #fff;
	    font-weight: bold;
	    margin: 18px 0px 10px 0px;
	    background: #f5f6f6;
	    background: -moz-linear-gradient(top, #f5f6f6 0%, #dbdce2 21%, #b8bac6 49%, #dddfe3 80%, #f5f6f6 100%);
	    background: -webkit-linear-gradient(top, #f5f6f6 0%,#dbdce2 21%,#b8bac6 49%,#dddfe3 80%,#f5f6f6 100%);
	    background: linear-gradient(to bottom, #f5f6f6 0%,#dbdce2 21%,#b8bac6 49%,#dddfe3 80%,#f5f6f6 100%);
	    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f5f6f6', endColorstr='#f5f6f6',GradientType=0 );
	    -webkit-background-clip: text;
	    -webkit-text-fill-color: transparent;
	}

	.ct_home {
	    padding: 0px 15px 10px 15px;
	    margin-bottom: 15px;
	    background-color: #111;
	    border: 1px solid #222;
	    -webkit-box-shadow: inset 0px 0px 7px 0px rgba(0, 0, 0, 0.54);
	    -moz-box-shadow: inset 0px 0px 7px 0px rgba(0, 0, 0, 0.54);
	    box-shadow: inset 0px 0px 7px 0px rgba(0, 0, 0, 0.54);
	}

	.ar_gx {
	    margin: 10px 0px 10px 0px;
	    height: 2px;
	    background-image: url(<?=base_url()?>/images/animate.gif);
	    background-position: center center;
	}
</style>
  <!-- Histats.com  START  (aync)-->
<script type="text/javascript">var _Hasync= _Hasync|| [];
_Hasync.push(['Histats.start', '1,4594843,4,0,0,0,00010000']);
_Hasync.push(['Histats.fasi', '1']);
_Hasync.push(['Histats.track_hits', '']);
(function() {
var hs = document.createElement('script'); hs.type = 'text/javascript'; hs.async = true;
hs.src = ('//s10.histats.com/js15_as.js');
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
})();</script>
<noscript><a href="/" target="_blank"><img  src="//sstatic1.histats.com/0.gif?4594843&101" alt="" border="0"></a></noscript>
<!-- Histats.com  END  -->