// tabs Home---------------------------------------------------------
$('#backtohome').click(function(){
  document.getElementById("defaultOpen").click();
});

function opentab(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";

  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");

  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";






  if ($("#mainsection").css('display')=='block') {
    $("#backtohome").hide();
    $("#mainbtnheader").show();
  }else{
    $("#backtohome").show();
    $("#mainbtnheader").hide();
  }



}
document.getElementById("defaultOpen").click();
// End tabs Home---------------------------------------------------------




// tabs friend---------------------------------------------------------
function openfriendtab(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("containinputwd");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";

  }
  tablinks = document.getElementsByClassName("ininwrapgrid001");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");

  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";


}
document.getElementById("tabfriendopen").click();
// Endtabs friend---------------------------------------------------------



// OPEN CLOSE MENU

   $('.iningridbank').click(function(){
     $('.inbankselectpopup').addClass("closeanimationselectbank");
     setTimeout(function(){ 
       $('.bankselectpopup').hide();
     }, 150);
   });
   $('.btnclosebankselect').click(function(){
    $('.inbankselectpopup').addClass("closeanimationselectbank");
    setTimeout(function(){ 
     $('.bankselectpopup').hide();
   }, 150);
    
   });
   
   
   $('.clickshowmenu').click(function(){
    $('.inbankselectpopup').removeClass("closeanimationselectbank");
    $('.bankselectpopup').show();
   });
   

// END OPEN CLOSE MENU









// Copy---------------------------------------------------------
$(document).ready(function(){
  $(".copybtn").click(function(event){
    var $tempElement = $("<input>");
    $("body").append($tempElement);
    $tempElement.val($(this).closest(".copybtn").find("span").text()).select();
    document.execCommand("Copy");
    $tempElement.remove();

  });
});
function copylink(){
  $(".myAlert-top").show();
  setTimeout(function(){
    $(".myAlert-top").hide(); 
  }, 2000);
}



$(".copylink").click(function(event){
   var copyText = document.getElementById("friendlink");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");

  });


// Copy---------------------------------------------------------











 var swiper = new Swiper(".promotion", {
  slidesPerView: "auto",
  centeredSlides: true,
  initialSlide:1,
  spaceBetween: 15,
  observer: true,
  observeParents: true,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
    dynamicBullets: true,
  },

});














//เมาส์ hover ไลน์โชว์ QRcode
const tip = document.querySelector(".tip");
const trigger = document.querySelector(".tip_trigger");

trigger.addEventListener("mouseover", function () {
  if(window.innerWidth > 768){
   tip.style.display = "unset";
   setTimeout(() => {
    tip.style.opacity = 1;
    tip.style.transform = "scale(1)"
  }, 1)
 }else{
  
 }

});

trigger.addEventListener("mouseleave", function () {
  tip.style.transform = "scale(0.95)"
  tip.style.opacity = 0;
  tip.style.display = "none";
});

trigger.addEventListener("mousemove", function (e) {
  let mousex = e.pageX + 20;
  let mousey = e.pageY + 20;
  const tipWidth = tip.offsetWidth;
  const tipHeight = tip.offsetHeight;
  const tipVisX = window.innerWidth - (mousex + tipWidth);
  const tipVisY = window.innerHeight - (mousey + tipHeight);

  if (tipVisX < 20) mousex = e.pageX - tipWidth - 20;
  if (tipVisY < 20) mousey = e.pageY - tipHeight - 20;

  tip.style.top = mousey + 'px';
  tip.style.left = mousex + 'px';
});
