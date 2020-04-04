function checkPilihan() {
    $('.btn-group-toggle').change(function (e) {
        $('.pilihan').removeClass('btn-danger').addClass('btn-secondary');
        var terpilih = $(e.target).parent();
        $(terpilih).removeClass('btn-secondary').addClass('btn-danger');
    });
    $('#ragu_button').bootstrapToggle({
        on: 'Ragu-ragu',
        off: 'Yakin',
        onstyle: 'warning',
        offstyle: 'success',
        size: 'lg'
    });
    $('#ragu_button').change(function () {
        $('#ragu').val('');
        if ($(this).prop('checked')) {
            $('#ragu').val(1);
        }
    });
    var originalSize = $('div#isi-ujian').css('font-size');
    // reset        
    $(".resetMe").click(function () {
        $('div#isi-ujian').css('font-size', originalSize);
    });

    // Increase Font Size          
    $(".increase").click(function () {
        var currentSize = $('div#isi-ujian').css('font-size');
        var currentSize = parseFloat(currentSize) * 1.2;
        $('div#isi-ujian').css('font-size', currentSize);
        return false;
    });
    // Decrease Font Size       
    $(".decrease").click(function () {
        var currentFontSize = $('div#isi-ujian').css('font-size');
        var currentSize = $('div#isi-ujian').css('font-size');
        var currentSize = parseFloat(currentSize) * 0.8;
        $('div#isi-ujian').css('font-size', currentSize);
        return false;
    });
    $(".refresh").click(function () {
        var url = window.location.href;
        window.location.replace(url);
        //console.log(url);
        //getExams(url);
        return false;
    });
}
function getUrlParameter(url, sParam = 'page') {
    url = new URL(url);
    var sPageURL = url.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        } else {
            return 1;
        }
    }
};