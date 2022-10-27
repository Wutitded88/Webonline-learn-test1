// เข้าสู่ระบบสมาชิก
$("#btn_login").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('username', $("#username").val());
    formData.append('password', $("#password").val());
    formData.append('ip', $("#ip").val());
    $('#btn_login').attr('disabled', 'disabled');
    $('#loading').show();

    $.ajax({
        type: 'POST',
        url: 'system/login',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = './wallet';
        });
        console.clear();
        $('#btn_login').removeAttr('disabled');
        $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
        });
        console.clear();
        $('#btn_login').removeAttr('disabled');
        $('#loading').hide();
    });
});

// สมัครสมาชิก
$("#btn_register").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('firstname', $("#firstname").val());
    formData.append('bank_id', $("#bank").val().split(":")[0]);
    formData.append('bank_name', $("#bank").val().split(":")[1]);
    formData.append('acc_no', $("#acc_no").val());
    formData.append('tel', $("#tel").val());
    formData.append('lineid', $("#lineid").val());
    formData.append('password', $("#password").val());
    formData.append('refer_id', $("#refer").val().split(":")[0]);
    formData.append('refer_name', $("#refer").val().split(":")[1]);
    formData.append('ip', $("#ip").val());
    formData.append('aff', $("#aff").val());
    formData.append('url', $("#url").val());

    captcha = grecaptcha.getResponse();
    formData.append('captcha', captcha);
    $('#btn_register').attr('disabled', 'disabled');
    $('#loading').show();

    $.ajax({
        type: 'POST',
        url: 'system/register',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        grecaptcha.reset();
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = './wallet';
        });
        console.clear();
        $('#btn_register').removeAttr('disabled');
        $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        grecaptcha.reset();
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
        });
        console.clear();
        $('#btn_register').removeAttr('disabled');
        $('#loading').hide();
    });
});

$(document).ready(function()
{
    if(window.location.pathname == "/auto-system-renew/auto-system/wallet" || window.location.pathname == "/wallet" ||
       window.location.pathname == "/auto-system-renew/auto-system/withdrawal" || window.location.pathname == "/withdrawal" ||
       window.location.pathname == "/auto-system-renew/auto-system/freecredit" || window.location.pathname == "/freecredit" ||
       window.location.pathname == "/auto-system-renew/auto-system/gambling" || window.location.pathname == "/gambling")
    {
        getBalance();
    }
});
function getBalance()
{
    var formData = new FormData();
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
        type: 'POST',
        url: 'system/getbalance',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        result = res;
        $('#credit_total_balance').html(parseFloat(result.message).toFixed(2));
    }).fail(function(jqXHR){
        $('#credit_total_balance').html('<span style="color: red;">Error</span>' );
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ไม่สามารถดึงยอดเครดิตได้ กรุณาติดต่อแอดมิน'
        });
    });
}

function startGame()
{
    var u_agent_id = '<?=get_wallet("u_agent_id")?>';
    if(u_agent_id != "")
    {
        document.getElementById("btnStartGame").click();
    }
    else
    {
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: 'คุณยังไม่ได้สร้างรหัสเกม กรุณาติดต่แอดมิน'
        });
    }
}

function get_bonus(id)
{
    var formData = new FormData();
    formData.append('id', id);
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
        type: 'POST',
        url: 'system/promotion',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = './wallet';
        });
        $('#loading').hide();
    }).fail(function(jqXHR){
        $('#credit_total_balance').html('<span style="color: red;">Error</span>' );
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ไม่สามารถดึงยอดเครดิตได้ กรุณาติดต่อแอดมิน'
        });
        $('#loading').hide();
    });
}

// เปลี่ยนรหัสผ่าน
$("#send-mail").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('oldpass', $("#oldpass").val());
    formData.append('newpass', $("#newpass").val());
    formData.append('newpass1', $("#newpass1").val());
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());
    $('#send-mail').attr('disabled', 'disabled');
    $('#loading').show();

    $.ajax({
        type: 'POST',
        url: 'system/changepassword',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = 'system/logout';
        });
        console.clear();
        $('#send-mail').removeAttr('disabled');
        $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
        });
        console.clear();
        $('#send-mail').removeAttr('disabled');
        $('#loading').hide();
    });
});

// รับเครดิตฟรี
$("#btn_freecredit").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());
    formData.append('code', $("#code").val());

    $('#btn_freecredit').attr('disabled', 'disabled');
    $.ajax({
        type: 'POST',
        url: 'system/freecredit',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = './freecredit';
        });
        getBalance();
        console.clear();
        $('#btn_freecredit').removeAttr('disabled');
        $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
        });
        console.clear();
        $('#btn_freecredit').removeAttr('disabled');
        $('#loading').hide();
    });
});

// แจ้งถอน
$("#btn_withdraw").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('username', $("#username").val());
    formData.append('credit', $("#amount").val());
    formData.append('ip', $("#ip").val());

    $('#btn_withdraw').attr('disabled', 'disabled');
    $.ajax({
        type: 'POST',
        url: 'system/withdraw',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = './withdrawal';
        });
        console.clear();
        $('#btn_withdraw').removeAttr('disabled');
        $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
        });
        console.clear();
        $('#btn_withdraw').removeAttr('disabled');
        $('#loading').hide();
    });
});

// แนะนำเพื่อน
$("#btn_withdraw_aff").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('username', $("#username").val());
    formData.append('credit', $("#amount").val());
    formData.append('ip', $("#ip").val());

    $('#btn_withdraw_aff').attr('disabled', 'disabled');
    $.ajax({
        type: 'POST',
        url: 'system/affiliate',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = './affiliate';
        });
        console.clear();
        $('#btn_withdraw_aff').removeAttr('disabled');
        $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
        });
        console.clear();
        $('#btn_withdraw_aff').removeAttr('disabled');
        $('#loading').hide();
    });
});

// อัพเดท wallet id
$("#btn_updatewallet_id").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('truewallet_id', $("#txt_truewallet_id").val());
    formData.append('ip', $("#ip").val());

    $('#btn_updatewallet_id').attr('disabled', 'disabled');
    $.ajax({
        type: 'POST',
        url: 'system/profile',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        result = res;
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = './wallet';
        });
        console.clear();
        $('#btn_updatewallet_id').removeAttr('disabled');
        $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
        });
        console.clear();
        $('#btn_updatewallet_id').removeAttr('disabled');
        $('#loading').hide();
    });
});

// topup tmwvoucher
function topuptmwvoucher($url) {
    var txt_link = $('#txt_link').val();
    var formData = new FormData();
    formData.append('link', txt_link);
    formData.append('ip', $('#ip').val());
    $('#btn_tmwtopup').attr('disabled', 'disabled');

    $.ajax({
        type: 'POST',
        url: $url+'/system/topup',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = './withdrawal';
        });
        console.clear();
        $('#btn_tmwtopup').removeAttr('disabled');
        $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
        });
        console.clear();
        $('#btn_tmwtopup').removeAttr('disabled');
        $('#loading').hide();
    });
}

// อัพเดท wallet id
$("#btn_winloss").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('type', 'receivewinloss');
    formData.append('ip', $("#ip").val());

    $('#btn_winloss').attr('disabled', 'disabled');
    $.ajax({
        type: 'POST',
        url: 'system/winloss',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        result = res;
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = './winloss';
        });
        console.clear();
        $('#btn_winloss').removeAttr('disabled');
        $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
        });
        console.clear();
        $('#btn_winloss').removeAttr('disabled');
        $('#loading').hide();
    });
});

// ดึงเงินออกจาก AG
$("#btn_withdraw_ag").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('ip', $("#ip").val());

    $('#btn_withdraw_ag').attr('disabled', 'disabled');
    $.ajax({
        type: 'POST',
        url: 'system/withdrawalag',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = './wallet';
        });
        console.clear();
        $('#btn_withdraw_ag').removeAttr('disabled');
        $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน'
        });
        console.clear();
        $('#btn_withdraw_ag').removeAttr('disabled');
        $('#loading').hide();
    });
});

function get_fixed_deposit(id)
{
    var formData = new FormData();
    formData.append('id', id);
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
        type: 'POST',
        url: 'system/fixed_deposit',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        Swal.fire({
            type: 'success',
            title: 'สำเร็จ',
            text: res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน',
        }).then((result) => {
            window.location = './fixed-deposit';
        });
        $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        Swal.fire({
          type: 'error',
          title: 'ผิดพลาด',
          text: res ? res.message : 'ไม่สามารถดึงยอดเครดิตได้ กรุณาติดต่อแอดมิน'
        });
        $('#loading').hide();
    });
}

function alertMSG(_text = "", _type = "success", _title = "สำเร็จ", _url = "./wallet") {
    Swal.fire({
        type: _type,
        title: _title,
        text: _text,
    }).then((result) => {
        window.location = _url;
    });
}