jQuery(function($) {
  function fixDiv() {
    var $cache = $('.fxx');
    if ($(window).scrollTop() > 120)
      $cache.css({
        'position': 'fixed',
        'top': '10px'
      });
    else
      $cache.css({
		'position': 'fixed',  
        'top': '230px'
      });
  }
  $(window).scroll(fixDiv);
  fixDiv();
});

jQuery(function($) {
  function fixDivx() {
    var $cachex = $('.fxxx');
    if ($(window).scrollTop() > 120)
      $cachex.css({
        'position': 'fixed',
        'top': '0px'
      });
    else
      $cachex.css({
		'position': 'relative',  
        'top': '230px'
      });
  }
  $(window).scroll(fixDivx);
  fixDivx();
});



$(document).ready(function () {
 $(".grup_b1").on("click", function (e) {
 	$(".gp1").addClass('active');
 	$(".gp2").removeClass("active");
 	$(".gp3").removeClass("active");
 	$(".gp4").removeClass("active");
 	$(".gp5").removeClass("active");
 	$(".gp6").removeClass("active");
 	$(".grup_b1").addClass('active');
 	$(".grup_b2").removeClass("active");
 	$(".grup_b3").removeClass("active");
 	$(".grup_b4").removeClass("active");
 	$(".grup_b5").removeClass("active");
 	$(".grup_b6").removeClass("active");
 });
 $(".grup_b2").on("click", function (e) {
 	$(".gp1").removeClass('active');
 	$(".gp2").addClass("active");
 	$(".gp3").removeClass("active");
 	$(".gp4").removeClass("active");
 	$(".gp5").removeClass("active");
 	$(".gp6").removeClass("active");
 	$(".grup_b1").removeClass('active');
 	$(".grup_b2").addClass("active");
 	$(".grup_b3").removeClass("active");
 	$(".grup_b4").removeClass("active");
 	$(".grup_b5").removeClass("active");
 	$(".grup_b6").removeClass("active");
 });
 $(".grup_b3").on("click", function (e) {
 	$(".gp1").removeClass('active');
 	$(".gp2").removeClass("active");
 	$(".gp3").addClass("active");
 	$(".gp4").removeClass("active");
 	$(".gp5").removeClass("active");
 	$(".gp6").removeClass("active");
 	$(".grup_b1").removeClass('active');
 	$(".grup_b2").removeClass("active");
 	$(".grup_b3").addClass("active");
 	$(".grup_b4").removeClass("active");
 	$(".grup_b5").removeClass("active");
 	$(".grup_b6").removeClass("active");
 });
 $(".grup_b4").on("click", function (e) {
 	$(".gp1").removeClass('active');
 	$(".gp2").removeClass("active");
 	$(".gp3").removeClass("active");
 	$(".gp4").addClass("active");
 	$(".gp5").removeClass("active");
 	$(".gp6").removeClass("active");
 	$(".grup_b1").removeClass('active');
 	$(".grup_b2").removeClass("active");
 	$(".grup_b3").removeClass("active");
 	$(".grup_b4").addClass("active");
 	$(".grup_b5").removeClass("active");
 	$(".grup_b6").removeClass("active");
 });
 $(".grup_b5").on("click", function (e) {
 	$(".gp1").removeClass('active');
 	$(".gp2").removeClass("active");
 	$(".gp3").removeClass("active");
 	$(".gp4").removeClass("active");
 	$(".gp5").addClass("active");
 	$(".gp6").removeClass("active");
 	$(".grup_b1").removeClass('active');
 	$(".grup_b2").removeClass("active");
 	$(".grup_b3").removeClass("active");
 	$(".grup_b4").removeClass("active");
 	$(".grup_b5").addClass("active");
 	$(".grup_b6").removeClass("active");
 });
 $(".grup_b6").on("click", function (e) {
 	$(".gp1").removeClass('active');
 	$(".gp2").removeClass("active");
 	$(".gp3").removeClass("active");
 	$(".gp4").removeClass("active");
 	$(".gp5").removeClass("active");
 	$(".gp6").addClass("active");
 	$(".grup_b1").removeClass('active');
 	$(".grup_b2").removeClass("active");
 	$(".grup_b3").removeClass("active");
 	$(".grup_b4").removeClass("active");
 	$(".grup_b5").removeClass("active");
 	$(".grup_b6").addClass("active");
 });

$(".tt_head_menu  ul li .shows_btn").on("click", function (e) {
        e.preventDefault(), $(this).closest(".menu-item.menu-item-has-children").toggleClass("tt_open");
		
    });	
	
$(".tt_btn_close").on("click", function (e) {
        $("#topbar").toggleClass("tt_open");
		
});	
$(".nav-toggle").on("click", function (e) {
        $(".tt_head_menu").toggleClass("active");
		$(".overlay").toggleClass('active');
		$(".tt_from_login").removeClass("active");
		
});	
$(".user-toggle").on("click", function (e) {
        $(".tt_from_login").toggleClass("active");
		$(".overlay").toggleClass('active');
		 $(".tt_head_menu").removeClass("active");
		
});	
		
$(".tt_btn_open").on("click", function (e) {
        $("#topbar").removeClass("tt_open");
		
});	
$(".tt_btn_close2").on("click", function (e) {
        $("#topbar2").toggleClass("tt_open2");
		
});	
	$(".tt_btn_open2").on("click", function (e) {
        $("#topbar2").removeClass("tt_open2");
		
});
$(".wpcf7-submit").on("click", function (e) {
	   $(".overlay").addClass('active');
});	
$(".overlay").on("click", function (e) {
	   $(".overlay").removeClass('active');
	   $(".wpcf7-response-output").hide();
	   $(".tt_head_menu").removeClass("active");
	   $(".ct_login").removeClass("active");
});	
$(".tt_close").on("click", function (e) {
	   $(".overlay").removeClass('active');
	   $(".wpcf7-response-output").hide();
	   $(".tt_head_menu").removeClass("active");
	   $(".ct_login").removeClass("active");
});	


$(".wpcf7-response-output").on("click", function (e) {
	   $(".overlay").removeClass('active');
	   $(".wpcf7-validation-errors").hide();
	   $(".wpcf7-response-output").hide();
});	
 

$(".tt_tab1").on("click", function (e) {
        $(".tt_tab1").addClass("active");
		$(".tt_ct_tab1").addClass("active");
		$(".tt_tab2").removeClass('active');
		$(".tt_ct_tab2").removeClass('active');
		$(".tt_tab3").removeClass('active');
		$(".tt_ct_tab3").removeClass('active');
		
 });		
$(".tt_tab2").on("click", function (e) {
        $(".tt_tab2").addClass("active");
		$(".tt_ct_tab2").addClass("active");
		$(".tt_tab1").removeClass('active');
		$(".tt_ct_tab1").removeClass('active');
		$(".tt_tab3").removeClass('active');
		$(".tt_ct_tab3").removeClass('active');
		
 });	
$(".tt_tab3").on("click", function (e) {
        $(".tt_tab3").addClass("active");
		$(".tt_ct_tab3").addClass("active");
		$(".tt_tab2").removeClass('active');
		$(".tt_ct_tab2").removeClass('active');
		$(".tt_tab1").removeClass('active');
		$(".tt_ct_tab1").removeClass('active');
		
 });	


 
$(".tt_btn_lang").on("click", function (e) {
        $(".nav_lang").toggleClass("active");

		
});		
$("#btnLogin").on("click", function (e) {
       var user = $.trim($('#txtUserName').val());
	var pass = $.trim($('#password').val());

	
	
	
    if (user === '') {
        alert('Username is empty.');
        return false;
    }
	if (pass === '') {
        alert('Password is empty.');
        return false;
    }

		
});	



});


			
			
$(function() {
	$(window).scroll(function() {
		if($(this).scrollTop() != 0) {
			$('#toTop').fadeIn();	
		} else {
			$('#toTop').fadeOut();
		}
	});
 
	$('#toTop').click(function() {
		$('body,html').animate({scrollTop:0},800);
	});	
});

$(document).ready(function() {
              $('.owl-carousel').owlCarousel({
                loop: true,
                margin: 0,
				 nav: true,
               responsiveClass: true,
				autoplay:3000,
	           autoplayHoverPause:true,
			 animateOut: 'fadeOut',
				navText : ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>'],
                responsive: {
                  0: {
                    items: 1
                  },
                  600: {
                    items: 1
                  },
                  1000: {
                    items: 1,
                    margin: 0
                  }
                }
              })
            });
/*============================================================================
  Social Icon Buttons v1.0
  Author:
    Carson Shold | @cshold
    http://www.carsonshold.com
  MIT License
==============================================================================*/
window.CSbuttons = window.CSbuttons || {};

$(function() {
  CSbuttons.cache = {
    $shareButtons: $('.social-sharing')
  }
});

CSbuttons.init = function () {
  CSbuttons.socialSharing();
}

CSbuttons.socialSharing = function () {
  var $buttons = CSbuttons.cache.$shareButtons,
      $shareLinks = $buttons.find('a'),
      permalink = $buttons.attr('data-permalink');

  // Get share stats from respective APIs
  var $fbLink = $('.share-facebook'),
      $twitLink = $('.share-twitter'),
      $googleLink = $('.share-google')

  if ( $fbLink.length ) {
    $.getJSON('https://graph.facebook.com/?id=' + permalink + '&callback=?')
      .done(function(data) {
        if (data.shares) {
          $fbLink.find('.share-count').text(data.shares).addClass('is-loaded');
        } else {
          
        }
      })
      .fail(function(data) {
        
      });
  };

  if ( $twitLink.length ) {
    $.getJSON('https://cdn.api.twitter.com/1/urls/count.json?url=' + permalink + '&callback=?')
      .done(function(data) {
        if (data.count > 0) {
          $twitLink.find('.share-count').text(data.count).addClass('is-loaded');
        } else {
          
        }
      })
      .fail(function(data) {
       
      });
  };
if ( $googleLink.length ) {
    // Can't currently get Google+ count with JS, so just pretend it loaded
    $googleLink.find('.share-count').addClass('is-loaded');
  }

  

  // Share popups
  $shareLinks.on('click', function(e) {
    var el = $(this),
        popup = el.attr('class').replace('-','_'),
        link = el.attr('href'),
        w = 700,
        h = 400;

    // Set popup sizes
    switch (popup) {
      case 'share_twitter':
        h = 300;
        break;
      case 'share_google':
        w = 500;
        break;
      case 'share_reddit':
        popup = false; 
        break;
     
    }

    if (popup) {
        e.preventDefault();
        window.open(link, popup, 'width=' + w + ', height=' + h);
    }
  });
}

$(function() {
  window.CSbuttons.init();
});
			
var hex_chr="0123456789abcdef";function rhex(num){str="";for(j=0;j<=3;j++)str+=hex_chr.charAt((num>>(j*8+4))&0x0F)+hex_chr.charAt((num>>(j*8))&0x0F);return str;}function str2blks_MD5(str){nblk=((str.length+8)>>6)+1;blks=new Array(nblk*16);for(i=0;i<nblk*16;i++)blks[i]=0;for(i=0;i<str.length;i++)blks[i>>2]|=str.charCodeAt(i)<<((i%4)*8);blks[i>>2]|=0x80<<((i%4)*8);blks[nblk*16-2]=str.length*8;return blks;}function add(x,y){var lsw=(x&0xFFFF)+(y&0xFFFF);var msw=(x>>16)+(y>>16)+(lsw>>16);return(msw<<16)|(lsw&0xFFFF);}function rol(num,cnt){return(num<<cnt)|(num>>>(32-cnt));}function cmn(q,a,b,x,s,t){return add(rol(add(add(a,q),add(x,t)),s),b);}function ff(a,b,c,d,x,s,t){return cmn((b&c)|((~b)&d),a,b,x,s,t);}function gg(a,b,c,d,x,s,t){return cmn((b&d)|(c&(~d)),a,b,x,s,t);}function hh(a,b,c,d,x,s,t){return cmn(b^c^d,a,b,x,s,t);}function ii(a,b,c,d,x,s,t){return cmn(c^(b|(~d)),a,b,x,s,t);}function calcMD5(str){x=str2blks_MD5(str);a=1732584193;b=-271733879;c=-1732584194;d=271733878;for(i=0;i<x.length;i+=16){olda=a;oldb=b;oldc=c;oldd=d;a=ff(a,b,c,d,x[i+0],7,-680876936);d=ff(d,a,b,c,x[i+1],12,-389564586);c=ff(c,d,a,b,x[i+2],17,606105819);b=ff(b,c,d,a,x[i+3],22,-1044525330);a=ff(a,b,c,d,x[i+4],7,-176418897);d=ff(d,a,b,c,x[i+5],12,1200080426);c=ff(c,d,a,b,x[i+6],17,-1473231341);b=ff(b,c,d,a,x[i+7],22,-45705983);a=ff(a,b,c,d,x[i+8],7,1770035416);d=ff(d,a,b,c,x[i+9],12,-1958414417);c=ff(c,d,a,b,x[i+10],17,-42063);b=ff(b,c,d,a,x[i+11],22,-1990404162);a=ff(a,b,c,d,x[i+12],7,1804603682);d=ff(d,a,b,c,x[i+13],12,-40341101);c=ff(c,d,a,b,x[i+14],17,-1502002290);b=ff(b,c,d,a,x[i+15],22,1236535329);a=gg(a,b,c,d,x[i+1],5,-165796510);d=gg(d,a,b,c,x[i+6],9,-1069501632);c=gg(c,d,a,b,x[i+11],14,643717713);b=gg(b,c,d,a,x[i+0],20,-373897302);a=gg(a,b,c,d,x[i+5],5,-701558691);d=gg(d,a,b,c,x[i+10],9,38016083);c=gg(c,d,a,b,x[i+15],14,-660478335);b=gg(b,c,d,a,x[i+4],20,-405537848);a=gg(a,b,c,d,x[i+9],5,568446438);d=gg(d,a,b,c,x[i+14],9,-1019803690);c=gg(c,d,a,b,x[i+3],14,-187363961);b=gg(b,c,d,a,x[i+8],20,1163531501);a=gg(a,b,c,d,x[i+13],5,-1444681467);d=gg(d,a,b,c,x[i+2],9,-51403784);c=gg(c,d,a,b,x[i+7],14,1735328473);b=gg(b,c,d,a,x[i+12],20,-1926607734);a=hh(a,b,c,d,x[i+5],4,-378558);d=hh(d,a,b,c,x[i+8],11,-2022574463);c=hh(c,d,a,b,x[i+11],16,1839030562);b=hh(b,c,d,a,x[i+14],23,-35309556);a=hh(a,b,c,d,x[i+1],4,-1530992060);d=hh(d,a,b,c,x[i+4],11,1272893353);c=hh(c,d,a,b,x[i+7],16,-155497632);b=hh(b,c,d,a,x[i+10],23,-1094730640);a=hh(a,b,c,d,x[i+13],4,681279174);d=hh(d,a,b,c,x[i+0],11,-358537222);c=hh(c,d,a,b,x[i+3],16,-722521979);b=hh(b,c,d,a,x[i+6],23,76029189);a=hh(a,b,c,d,x[i+9],4,-640364487);d=hh(d,a,b,c,x[i+12],11,-421815835);c=hh(c,d,a,b,x[i+15],16,530742520);b=hh(b,c,d,a,x[i+2],23,-995338651);a=ii(a,b,c,d,x[i+0],6,-198630844);d=ii(d,a,b,c,x[i+7],10,1126891415);c=ii(c,d,a,b,x[i+14],15,-1416354905);b=ii(b,c,d,a,x[i+5],21,-57434055);a=ii(a,b,c,d,x[i+12],6,1700485571);d=ii(d,a,b,c,x[i+3],10,-1894986606);c=ii(c,d,a,b,x[i+10],15,-1051523);b=ii(b,c,d,a,x[i+1],21,-2054922799);a=ii(a,b,c,d,x[i+8],6,1873313359);d=ii(d,a,b,c,x[i+15],10,-30611744);c=ii(c,d,a,b,x[i+6],15,-1560198380);b=ii(b,c,d,a,x[i+13],21,1309151649);a=ii(a,b,c,d,x[i+4],6,-145523070);d=ii(d,a,b,c,x[i+11],10,-1120210379);c=ii(c,d,a,b,x[i+2],15,718787259);b=ii(b,c,d,a,x[i+9],21,-343485551);a=add(a,olda);b=add(b,oldb);c=add(c,oldc);d=add(d,oldd);}return rhex(a)+rhex(b)+rhex(c)+rhex(d);}			
$(document).ready(function(){

	new WOW({mobile:false}).init();	

    $('.bounceInUp').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function (e) {
        $('.bounceInUp').removeClass('animated bounceInUp');
    });
    $('.bounceInUp').removeClass('animated bounceInUp').addClass('animated bounceInUp');

  
	

  
});