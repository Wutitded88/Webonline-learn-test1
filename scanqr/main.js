get_result();
setInterval(() => {
    get_result();
}, 2000)

function get_result() {
    $.get("get_result.php", function (data) {
        $("#result").html(data);
    });
}
$("#upload").submit(function (e) {
    e.preventDefault();
    var allData = new FormData(this);
    $.ajax({
        type: "POST",
        url: "upload.php",
        dataType: 'text', // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: allData,
        success: function (data) {
            alert(data);
        }

    });
});

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
    alert("คัดลอกเลขบัญชีแล้ว");
}