// GET LOADING OAGE
function loadingPage() {
    $('#loading').show();
}

// FORMAT WALLET & CREDIT
var wallet = parseInt($('input.wallet').val());
var credit = parseInt($('input.credit').val());

$('input.wallet').val(wallet.toFixed(2) + " บาท");
$('input.credit').val(credit.toFixed(2) + " เครดิต");

// $(document).on("click", "#depositadw", function () {
//     swal({
//         title: "คำเตือน!",
//         text: "• เมื่อต้องการทำรายการฝาก กรุณาใช้ข้อมูลบัญชี หรือ เบอร์ทรูวอลเลต ให้ตรงกับที่สมัครไว้เท่านั้น \n• ถ้าข้อมูลบัญชี หรือ ทรูวอลเลต ไม่ตรงกับที่สมัคร ลูกค้าจะไม่ได้รับเครดิต ทางทีมงานจะไม่รับผิดชอบทุกกรณี",
//         icon: "warning",
//         buttons: "รับทราบ",
//         successMode: true,
//       })
//       .then((comfi) => {
//         if (comfi) {
//             window.location.href = "../topup";
//         } else {
//           swal("ยกเลิกสำเร็จแล้ว!");
//         }
//       });
//     });

// ====== CLIPBOARD =======
// SCB
var scb = new ClipboardJS('.copy-scb');
scb.on('success', function (e) {
    console.info('Action:', e.action);
    console.info('Text:', e.text);
    console.info('Trigger:', e.trigger);
    $('#copyClipboard').modal('show');
    setInterval(function() {
        $('#copyClipboard').modal('hide');
    },2000);
    e.clearSelection();
});
scb.on('error', function (e) {
    console.error('Action:', e.action);
    console.error('Trigger:', e.trigger);
});

// BAY
var bay = new ClipboardJS('.copy-bay');
bay.on('success', function (e) {
    console.info('Action:', e.action);
    console.info('Text:', e.text);
    console.info('Trigger:', e.trigger);
    $('#copyClipboard').modal('show');
    setInterval(function () {
        $('#copyClipboard').modal('hide');
    }, 2000);
    e.clearSelection();
});
bay.on('error', function (e) {
    console.error('Action:', e.action);
    console.error('Trigger:', e.trigger);
});

// KBANK
var kbank = new ClipboardJS('.copy-kbank');
kbank.on('success', function (e) {
    console.info('Action:', e.action);
    console.info('Text:', e.text);
    console.info('Trigger:', e.trigger);
    $('#copyClipboard').modal('show');
    setInterval(function() {
        $('#copyClipboard').modal('hide');
    },2000);
    e.clearSelection();
});
kbank.on('error', function (e) {
    console.error('Action:', e.action);
    console.error('Trigger:', e.trigger);
});

// TRUEWALLET
var tmw = new ClipboardJS('.copy-tmw');
tmw.on('success', function (e) {
    console.info('Action:', e.action);
    console.info('Text:', e.text);
    console.info('Trigger:', e.trigger);
    $('#copyClipboard').modal('show');
    setInterval(function() {
        $('#copyClipboard').modal('hide');
    },2000);
    e.clearSelection();
});
tmw.on('error', function (e) {
    console.error('Action:', e.action);
    console.error('Trigger:', e.trigger);
});

var aff = new ClipboardJS('.copy-aff');
aff.on('success', function (e) {
    console.info('Action:', e.action);
    console.info('Text:', e.text);
    console.info('Trigger:', e.trigger);
    $('#copyClipboard').modal('show');
    setInterval(function() {
        $('#copyClipboard').modal('hide');
    },2000);
    e.clearSelection();
});
aff.on('error', function (e) {
    console.error('Action:', e.action);
    console.error('Trigger:', e.trigger);
});

// // AFFCODE
// copy_clipboard(new ClipboardJS('.copy-aff-code'));
$('#CoupleSelect').change(function () {
    $("#inputCouple").show();
});

function topupx() {
    var _credit = parseInt(document.getElementById("credit").value.replace(/-/gi, ''));
    var _bonus = document.getElementById("bonus").value;

    if (isNaN(_credit)) {
        document.getElementById("show").innerHTML = "0";
    } else {
        document.getElementById("id_deposit").innerHTML = _credit.toFixed(2);
        document.getElementById("id_bonus").innerHTML = 0.00;
        document.getElementById("rqtopup").innerHTML = _credit.toFixed(2);

        if (_bonus == "0" || _bonus == "")
        {
            s = _credit;
            ivs = _credit;
            document.getElementById("show").innerHTML = _credit.toFixed(2);
            document.getElementById("id_bonus").innerHTML = 0.00;
            document.getElementById("rqtopup").innerHTML = _credit.toFixed(2);
        }
        else
        {   
            var formData = new FormData();
            formData.append('credit', _credit);
            formData.append('bonus', _bonus);
            formData.append('type', 'summary');

            $.ajax({
                type: 'POST',
                url: 'system/transfergame',
                data:formData,
                contentType: false,
                processData: false,
            }).done(function(res){
                result = res;
                document.getElementById("show").innerHTML = parseFloat(result.total).toFixed(2);
                document.getElementById("id_bonus").innerHTML = parseFloat(result.bonus).toFixed(2);
                document.getElementById("rqtopup").innerHTML = parseFloat(result.message).toFixed(2);
                console.clear();
            }).fail(function(jqXHR){
                res = jqXHR.responseJSON;
                console.log(res.message);
            });
        }
    }
}
