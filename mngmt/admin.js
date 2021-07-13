var form = document.dataForm;

$(document).ready(function(){
    if(_TB_) ajaxGetList();
    $(document).on('mouseover', '.table01 tbody tr:not(.dark, .bg2, .bg3, .expire)', function(){
        $(this).find('td').css({background:'#eee'});
    });
    $(document).on('mouseout', '.table01 tbody tr:not(.dark, .bg2, .bg3, .expire)', function(){
        $(this).find('td').css({background:''});
    });
    $('#selState').bind('change', function(){
        form.page.value = "";
        form.stat.value = $(this).val();
        ajaxGetList();
    });
    $('#selRows').bind('change', function(){
        form.page.value = "";
        form.rows.value = $(this).val();
        ajaxGetList();
    });
    /*$('#ymd').bind('change', function(){
        ajaxGetList();
    });*/
    $('#cashReceipt').bind('click', function(e){
        if($(this).prop('checked') == true){
            form.receipt.value = 1;
        }else{
            form.receipt.value = '';
        }
        ajaxGetList();
    });
    $('.table01 .sort').bind('click', function(e){
        e.preventDefault();
        $('.table01 .sort').not($(this)).removeClass('up');
        $('.table01 .sort').not($(this)).removeClass('down');

        form.sort.value = $(this).attr('href');
        if(!$(this).hasClass('up') && !$(this).hasClass('down')){
            $(this).addClass('up');
            form.sod.value = 'ASC';
        }else if($(this).hasClass('up')){
            $(this).removeClass('up');
            $(this).addClass('down');
            form.sod.value = 'DESC';
        }else{
            $(this).removeClass('down');
            form.sod.value = '';
            form.sort.value = '';
        }
        ajaxGetList();
    });
    $('#btnSearch').bind('click', function(){
        form.page.value = "";
        ajaxGetList();
    });
    $(document).on('click', 'a.pg_page', function(e){
        e.preventDefault();
        form.page.value = $(this).attr('href');
        ajaxGetList();
        return false;
    });
    $('.dateInput').datepicker({
        dateFormat: 'yy-mm-dd'
    });
    $('.dateWrap>i').bind('click', function(){
        $(this).prev().focus();
    });
    $('.dateWrap>button').bind('click', function(){
        var start = $(this).parent().find('input').eq(0);
        var end = $(this).parent().find('input').eq(1);
        var sdate, edate;
        var now = new Date();
        var yy = now.getFullYear();
        var mm = now.getMonth()+1;
        if(mm < 10) mm = "0" + mm;
        var dd = now.getDate();
        if(dd < 10) dd = "0" + dd;

        if($(this).attr('id') != 'today'){
            var now2 = new Date();
            switch($(this).attr('id')){
                case 'yesterday': now2.setTime(now2.getTime() - (1*24*60*60*1000)); break;
                case '1week': now2.setTime(now2.getTime() - (7*24*60*60*1000)); break;
                case '2week': now2.setTime(now2.getTime() - (14*24*60*60*1000)); break;
                case '1month': now2.setMonth(now2.getMonth()-1); break;
                case '3month': now2.setMonth(now2.getMonth()-3); break;
                case '6month': now2.setMonth(now2.getMonth()-6); break;
                case '1year': now2.setYear(now2.getFullYear()-1); break;
            }
            var yy2 = now2.getFullYear();
            var mm2 = now2.getMonth()+1;
            if(mm2 < 10) mm2 = "0" + mm2;
            var dd2 = now2.getDate();
            if(dd2 < 10) dd2 = "0" + dd2;
        }
        switch($(this).attr('id')){
            case 'today': sdate = edate = String(yy)+'-'+String(mm)+'-'+String(dd); break;
            case 'yesterday': sdate = edate = String(yy2)+'-'+String(mm2)+'-'+String(dd2); break;
            case '1week':
            case '2week':
            case '1month':
            case '3month':
            case '6month':
            case '1year':
                sdate = String(yy2)+'-'+String(mm2)+'-'+String(dd2);
                edate = String(yy)+'-'+String(mm)+'-'+String(dd);
            break;
        }
        start.val(sdate);
        end.val(edate);
    });
    $('.detailLayer>.close>button').bind('click', function(){
        $("#overlayer").fadeOut();
        $(this).parent().parent().fadeOut();
        if(document.nodeForm != undefined){
            document.nodeForm.reset();
        }
    });
    $(document).on('click', 'dd.coinDevelop>button', function(){
        var obj = $(this).parent();
        var num = obj.attr('mid');
        var job = $(this).attr('class');
        if(!obj.find('.coinCnt').val()){
            alert('코인 수량을 입력하세요.');
            obj.find('.coinCnt').focus();
            return false;
        }
        if(!obj.find('.coinBigo').val()){
            alert('사유를 입력하세요.');
            obj.find('.coinBigo').focus();
            return false;
        }
        switch(job){
            case 'plusCoin': var msg = "추가"; break;
            case 'minusCoin': var msg = "차감"; break;
        }
        if(!confirm('코인을 '+msg+'하시겠습니까?')){
            return false;
        }
        $.ajax({
            type: "post",
            url: _URL+"/inc/coinAdmin",
            data: {
                no: num,
                job: msg,
                amount: obj.find('.coinCnt').val(),
                content: obj.find('.coinBigo').val()
            },
            dataType: "text",
            async: false,
            success: function(data){
                switch(data){
                    case 'success': userDetail(num); break;
                    case 'error': alert('권한이 없습니다.'); break;
                    case 'payrow': alert('구축신청 내역이 있는 회원은 증/차감 기능을 사용할 수 없습니다.'); break;
                    case 'fail': alert('Database Error!'); break;
                }
            }
        });
    });
    $('#chkall').bind('click', function(){
        if($(this).hasClass('on')){
            $(this).removeClass('on');
            $(".listCheckbox").prop('checked', false);
        }else{
            $(this).addClass('on');
            $(".listCheckbox").prop('checked', true);
        }
    });

    var block = setInterval(function(){
        $.ajax({
            type: "post",
            url: _URL+"/inc/checkBlock",
            data: {},
            dataType: "json",
            async: false,
            success: function(data){
                if(data.length > 0){
                    //alert(data);
                    console.log(data);
                }else{
                    console.log('no problem!');
                }
            }
        });
    }, 60000);

    $('#excelDown').bind('click', function(){
        form.target = 'hiddenframe';
        form.action = _URL+'/excelList';
        form.submit();
    });
});

var addField = "";
function ajaxGetList(){
    $.ajax({
        type: "post",
        url: _URL+"/dataList",
        data: {
            table: _TB_,
            stat: form.stat.value,
            page: form.page.value,
            sca: form.sca.value,
            sort: form.sort.value,
            sod: form.sod.value,
            rows: form.rows.value,
            sdate: form.sdate.value,
            edate: form.edate.value,
            selField: form.selField.value,
            stx: form.stx.value,
            receipt: form.receipt.value
        },
        dataType: "json",
        async: false,
        success: function(data){
            console.log(data.sql);
            //$('#totalSum').text(data.totalSum);
            $('#totalCount').text(numberFormat(data.total));
            $('#dataList tbody').html(data.content);
            $('.pageWrap').html(data.pageing);
            if((form.selField.value && form.stx.value) || (form.sdate.value && form.edate.value)) $('.btnReset').show();
            else $('.btnReset').hide();
            return false;
        }
    });
    return false;
}

function userDetail(num){
    var _left_ = ($(window).width()-800)/2;
    $.ajax({
        type: "post",
        url: _URL+"/inc/userDetail",
        data: { no: num },
        dataType: "html",
        async: false,
        success: function(data){
            $(".detailLayer>.detail").html(data);
            $("#overlayer").fadeIn();
            $(".detailLayer").css({left: _left_});
            $(".detailLayer").fadeIn();
            return false;
        }
    });
}

function paidComplete(no, mid){
    if(!confirm('입금 완료처리 하시겠습니까?')){
        return false;
    }
    $.ajax({
        type: "post",
        url: _URL+"/inc/procAdmin",
        data: {
            act: 'paidComplete',
            no: no,
            mid: mid
        },
        dataType: "text",
        async: false,
        success: function(data){
            switch(data){
                case 'success': ajaxGetList(); break;
                case 'error': alert('권한이 없습니다.'); break;
                case 'fail': alert('Database Error!'); break;
            }
        }
    });
}

function paidDelete(no, st, mid){
    if(!confirm('삭제하시겠습니까?')){
        return false;
    }
    $.ajax({
        type: "post",
        url: _URL+"/inc/procAdmin",
        data: {
            act: 'paidDelete',
            no: no,
            status: st,
            mid: mid
        },
        dataType: "text",
        async: false,
        success: function(data){
            switch(data){
                case 'success': alert('삭제되었습니다.'); ajaxGetList(); break;
                case 'nodeing': alert('이미 구동중인 마스터노드가 있습니다.\n서버 관리자에게 문의하십시오.'); break;
                case 'error': alert('권한이 없습니다.'); break;
                case 'fail': alert('Database Error!'); break;
            }
        }
    });
}

function nodeModify(a, b, c, d, e, f, g, h, i, j){
    var form = document.nodeForm;
    if(a == 1){
        form.w.value = '';
        form.no.value = b;
        form.month.value = c;
        $('#nodeSubmit').text('등록');
    }else if(a == 3){
        form.w.value = 'u';
        form.no.value = b;
        form.name.value = d;
        form.ip.value = e;
        form.addr.value = f;
        form.privatekey.value = g;
        form.txid.value = h;
        form.txindex.value = i;
        form.walletip.value = j;
        $('#nodeSubmit').text('수정');
    }else{
        return false;
    }
    var _left_ = ($(window).width()-800)/2;
    $("#overlayer").fadeIn();
    $(".detailLayer").css({left: _left_});
    $(".detailLayer").fadeIn();
}

function chkNodeForm(form){
    $.ajax({
        type: "post",
        url: _URL+"/inc/procAdmin",
        data: {
            act: 'serverUpdate',
            w: form.w.value,
            no: form.no.value,
            name: form.name.value,
            ip: form.ip.value,
            addr: form.addr.value,
            privatekey: form.privatekey.value,
            txid: form.txid.value,
            txindex: form.txindex.value,
            wallet: form.walletip.value
        },
        dataType: "text",
        async: false,
        success: function(data){
            switch(data){
                case 'success1': alert('등록되었습니다.'); $('.detailLayer>.close>button').click(); ajaxGetList(); break;
                case 'success2': alert('수정되었습니다.'); $('.detailLayer>.close>button').click(); ajaxGetList(); break;
                case 'error': alert('권한이 없습니다.'); break;
                case 'fail': alert('Database Error!'); break;
            }
        }
    });
    return false;
}

function serverEnd(no){
    if(!confirm('종료처리 하시겠습니까?')){
        return false;
    }
    $.ajax({
        type: "post",
        url: _URL+"/inc/procAdmin",
        data: {
            act: 'serverEnd',
            no: no
        },
        dataType: "text",
        async: false,
        success: function(data){
            switch(data){
                case 'success': alert('변경되었습니다.'); ajaxGetList(); break;
                case 'error': alert('권한이 없습니다.'); break;
                case 'fail': alert('Database Error!'); break;
            }
        }
    });
}

function userUpdate(form){
    if(!confirm('수정하시겠습니까?')){
        return false;
    }
    $.ajax({
        type: "post",
        url: _URL+"/inc/procAdmin",
        data: {
            act: 'userUpdate',
            no: form.no.value,
            username: form.username.value,
            tel: form.tel.value,
            email: form.email.value,
            addr: form.address.value,
            level: form.level.value
        },
        dataType: "text",
        async: false,
        success: function(data){
            switch(data){
                case 'success': alert('수정되었습니다.'); userDetail(form.no.value); ajaxGetList(); break;
                case 'error': alert('권한이 없습니다.'); break;
                case 'fail': alert('Database Error!'); break;
            }
        }
    });
    return false;
}

function prodChange(no, cnt, prod){
    if(!confirm('변경하시겠습니까?')){
        ajaxGetList();
        return false;
    }
    $.ajax({
        type: "post",
        url: _URL+"/inc/procAdmin",
        data: {
            act: 'prodChange',
            no: no,
            mncnt: cnt,
            prod: parseInt(prod)
        },
        dataType: "text",
        async: false,
        success: function(data){
            switch(data){
                case 'success': alert('변경되었습니다.'); ajaxGetList(); break;
                case 'error': alert('권한이 없습니다.'); break;
                case 'fail': alert('Database Error!'); break;
            }
        }
    });
    return false;
}

function methodChange(no, mh){
    if(!confirm('변경하시겠습니까?')){
        ajaxGetList();
        return false;
    }
    $.ajax({
        type: "post",
        url: _URL+"/inc/procAdmin",
        data: {
            act: 'methodChange',
            no: no,
            method: parseInt(mh)
        },
        dataType: "text",
        async: false,
        success: function(data){
            switch(data){
                case 'success': alert('변경되었습니다.'); ajaxGetList(); break;
                case 'error': alert('권한이 없습니다.'); break;
                case 'fail': alert('Database Error!'); break;
            }
        }
    });
    return false;
}

function withdrawComplete(no, obj, wal){
    if(!confirm('출금 처리 하시겠습니까?')){
        return false;
    }
    console.log(obj);
    obj.prop('disabled', true);
    $(".loader").fadeIn(100);
    $(".loader>i").addClass('xi-spin');
    $("#overlayer").fadeIn(100, function(){
        $.ajax({
            type: "post",
            url: _URL+"/inc/withdrawComplete",
            data:{
                no: no,
                wallet: wal
            },
            dataType: "json",
            async: false,
            success: function(data){
                console.log(data);
                var status = parseInt(data.status);
                switch(status){
                    case 200: alert('정상 처리되었습니다.'); ajaxGetList(); break;
                    case 1000: alert('쿼리 전송 실패!'); break;
                    case 1001: alert('Param 오류!'); break;
                    case 1002: alert('Method 오류!'); break;
                    case 1003: alert('Database 오류!'); break;
                }
                $(".loader").fadeOut(800);
                $("#overlayer").fadeOut(800, function(){
                    $(".loader>i").removeClass('xi-spin');
                    obj.prop('disabled', false);
                });
            }
        });
    });
}

$(document).on('click', '.table01 td .btnDiskette', function(){
    if(!confirm('입금자명을 수정하시겠습니까?')){
        $(this).prev().val($(this).prev().prop('defaultValue'));
        return false;
    }
    $.ajax({
        type: "post",
        url: _URL+"/inc/procAdmin",
        data: {
            act: 'nameChange',
            no: $(this).attr('pid'),
            name: $(this).prev().val()
        },
        dataType: "text",
        async: false,
        success: function(data){
            switch(data){
                case 'success': alert('수정되었습니다.'); ajaxGetList(); break;
                case 'error': alert('권한이 없습니다.'); break;
                case 'fail': alert('Database Error!'); break;
            }
        }
    });
    return false;
});

function paidUpdateAll(){
    var chk = 0;
    $('.listCheckbox').each(function(i){
        if($(this).prop('checked') == true) chk = 1;
    });
    if(chk == 0){
        alert('수정할 내역을 선택하세요.');
        return false;
    }else{
        if(!confirm('수정하시겠습니까?')){
            return false;
        }
    }
}

function noticeSubmit(f){
    $.ajax({
        type: "post",
        url: _URL+"/inc/notice",
        data: {
            subject: f.subject.value,
            content: f.content.value
        },
        dataType: "text",
        async: false,
        success: function(data){
            switch(data){
                case 'success': alert('저장되었습니다.'); document.location.reload(); break;
                case 'error': alert('권한이 없습니다.'); break;
                case 'fail': alert('Database Error!'); break;
            }
        }
    });
    return false;
}

// 천단위 콤마찍기
function numberFormat( number, decimals, dec_point, thousands_sep ){
	var n = number, prec = decimals, dec = dec_point, sep = thousands_sep;
	n = !isFinite(+n) ? 0 : +n;
	prec = !isFinite(+prec) ? 0 : Math.abs(prec);
	sep = sep == undefined ? ',' : sep;
	var s = n.toFixed(prec), abs = Math.abs(n).toFixed(prec), _, i;
	if(abs > 1000){
		_ = abs.split(/\D/);
		i = _[0].length % 3 || 3;
		_[0] = s.slice(0,i + (n < 0)) + _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
		s = _.join(dec || '.');
	}else{
		s = abs.replace('.', dec_point);
	}
	return s;
}
