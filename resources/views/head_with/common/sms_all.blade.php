@extends('head_with.layouts.layout-nav')
@section('title','SMS 관리')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">SMS</h1>
        <div>
            <a href="#" id="search_sbtn" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active"
               id="send-tab"
               data-toggle="tab"
               href="#send"
               role="tab"
               aria-controls="send"
               aria-selected="true">발송</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link"
               id="list-tab"
               data-toggle="tab"
               href="#list"
               role="tab"
               aria-controls="list"
               aria-selected="true">내역</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane show active" id="send" role="tabpanel" aria-labelledby="send-tab">
            <div id="send-area" class="card shadow mb-3">
                <div class="card-body" style="border-top: none;">
                    <div class="row mb-2 mb-lg-3">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group mb-0">
                                <label for="type">송신자 : </label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm send-phone" value="{{$phone}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group mb-0">
                                <label for="type">수신자 : </label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm name" value="{{$s_name}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group mb-0">
                                <label for="type">수신자 휴대폰번호('-'포함) : </label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm phone" value="{{$s_phone}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-0">
                                <label for="type">메시지 내용</label>
                                <div class="flax_box justify-content-end">
                                    <textarea id="msg" cols="30" rows="5" class="form-control form-control-sm" placeholder="여기에 메시지를 입력해주십시오."></textarea>
                                    <div class="pt-1">
                                        <span class="msg-count">0</span>/80
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-2 mb-lg-3">
                            <div>
                                <a href="/multi.sms.xls" download>multi.sms.xls</a>
                            </div>
                            <div class="inline-inner-box triple">
                                <div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file" aria-describedby="inputGroupFileAddon03">
                                        <label class="custom-file-label" for="file">파일 찾아보기</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-center mt-3">
                            <a href="#" class="btn btn-sm btn-outline-primary shadow-sm batch-msg-btn">메시지 일괄입력</a>
                            <a href="#" class="btn btn-sm btn-outline-primary shadow-sm template-btn">템플릿 선택</a>
                            <a href="#" class="btn btn-sm btn-primary shadow-sm add-msg-btn"><i class="fa fa-plus fa-sm mr-1"></i> 추가</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow mb-3">
                <div class="card-body shadow" style="border-top: none;">
                    <div class="card-title">
                        <div class="filter_wrap">
                            <div class="fl_box">
                                <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                            </div>
                            <div class="fr_box flax_box">
                                <a href="#" class="btn-sm btn btn-primary mr-1 phone-check-btn">전화번호 검사</a>
                                <a href="#" class="btn-sm btn btn-primary mr-1 delete-btn">삭제</a>
                                <a href="#" class="btn-sm btn btn-primary send-btn">발송</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div id="div-gd-send" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="list" role="tabpanel" aria-labelledby="list-tab">
            <div id="search-area" class="search_cum_form">
                <form method="get" name="search">
                    <div class="card shadow mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col inner-td">
                                    <div class="form-group">
                                        <label for="user_yn">발송일자 : </label>
                                        <div class="form-inline inline_input_box date-switch-wrap">
                                            <div class="docs-datepicker form-inline-inner" style="width:42%;">
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="docs-datepicker-container"></div>
                                            </div>
                                            <span class="text_line">~</span>
                                            <div class="docs-datepicker form-inline-inner" style="width:42%;">
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="docs-datepicker-container"></div>
                                            </div>
                                            <div class="ml-1">
                                                <a href="#" id="search_sbtn" class="btn btn-sm btn-primary shadow-sm search-btn">검색</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="user_yn">수신자 휴대전화 : </label>
                                        <div class="flax_box">
                                            <input type='text' class="form-control form-control-sm" name='phone' value='{{$s_phone}}'>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="user_yn">수신자 : </label>
                                        <div class="flax_box">
                                            <input type='text' class="form-control form-control-sm" name='name' value='{{$s_name}}'>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card shadow mb-3">
                <div class="card-body shadow">
                    <div class="card-title form-inline">
                        <div class="filter_wrap">
                            <div class="fl_box">
                                <h6 class="m-0 font-weight-bold">총 : <span id="gd-list-total" class="text-primary">0</span> 건</h6>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div id="div-gd-list" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.5/xlsx.full.min.js"></script>
<script>
// 글자수 byte검사
String.prototype.bytes = function() {
	var str = this;
	var l = 0;
	for (var i=0; i<str.length; i++) l += (str.charCodeAt(i) > 128) ? 2 : 1;
	return l;
}

const editCellStyle = {
    'background' : '#ffff99',
    'border-right' : '1px solid #e0e7e7'
};

const snedColumn = [
    {
        headerName: '',
        headerCheckboxSelection: true,
        checkboxSelection: true,
        width:28

    },
    {field: "name", headerName: "수신자", width: 70, cellStyle: {"text-align": "center"}},
    {field: "phone", headerName: "휴대폰번호", cellStyle: {"text-align": "center"}},
    {field: "msg", headerName: "메시지", width: "auto", minWidth:250},
    {field: "byte", headerName: "바이트", type: "currencyType"}
];

const listColumn = [
	//{field: "date_client_req", headerName: "등록일시", width:130},
	{field: "regdate", headerName: "전송일시", width:130},
	{field: "tpl_code", headerName: "종류", width:80},
	{field: "recvname", headerName: "수신자", width:80},
	{field: "receiver", headerName: "휴대전화", width:120},
	//{field: "msg_rlt", headerName: "결과", width:80},
	{field: "message", headerName: "메시지", width:450 }
];

const sendApp = new App('', {gridId: "#div-gd-send"});
const sendGridDiv = document.querySelector(sendApp.options.gridId);
const sendGx = new HDGrid(sendGridDiv, snedColumn);

const listApp = new App('', {gridId: "#div-gd-list"});
const listGridDiv = document.querySelector(listApp.options.gridId);
const listGx = new HDGrid(listGridDiv, listColumn);

sendGx.gridOptions.getRowNodeId = function(data) {
    return data.idx;
}

sendApp.ResizeGrid();
listApp.ResizeGrid();

const searchRows = [];
const fr = new FileReader();
const validateFile = () => {
    const target = $('#file')[0].files;

    if (target.length > 1) {
        alert("파일은 1개만 올려주세요.");
        return false;
    }

    if (target === null || target.length === 0) {
        alert("업로드할 파일을 선택해주세요.");
        return false;
    }

    if (!/(.*?)\.(xls|XLS|xlsx|XLSX)$/i.test(target[0].name)) {
        alert("Excel파일만 업로드해주세요.");
        return false;
    }

    return true;
}

const loadData = () => {
    const target = $('#file')[0].files[0];

    fr.onload = loadExcelData;
    fr.readAsBinaryString(target);
}

const loadExcelData = () => {
    const data = fr.result;
    const workBook = XLSX.read(data, { type: 'binary' });
    const rows = XLSX.utils.sheet_to_csv(workBook.Sheets[workBook.SheetNames[0]]);

    createRows(rows);
}

const createRows = (rowText) => {
    const temp_rows = rowText.split('\n');
    const rows = [];
    let count = searchRows.length;

    temp_rows.forEach((row, idx) => {
        if (idx === 0) return;
        if (row != "") {
            row = row.split(',');

            addRow({
                'name'  : row[1],
                'phone' : row[2],
                'msg'   : row[3],
                'byte'  : row[3].bytes(),
                'idx'   : count + idx
            });
        }
    });
}

const addRow = (row) => {
    searchRows.push(row);
    sendGx.gridOptions.api.updateRowData({add: [row]});
    $("#gd-total").text(sendGx.getRows().length);
}

const msgByteCount = () => {
    $(".msg-count").html($('#msg').val().bytes());
}

//window open에서 arrow function 인식을 못함
function selectTemplate(data) {
    $('#msg').val(data.ans_msg);
    $(".msg-count").html(data.ans_msg.bytes());
}

const Search = () => {
    const data = $('form[name="search"]').serialize();
    listGx.Request('/head/api/sms/search', data, 1);
}

$('.phone-check-btn').click((e) => {
    e.preventDefault();

    sendGx.gridOptions.api.forEachNode(function (node) {
        node.setSelected(!/^01(?:0|1|[6-9])-(?:\d{3}|\d{4})-\d{4}$/.test(node.data.phone));
    });
});

$('.delete-btn').click((e) => {
    e.preventDefault();
    sendGx.gridOptions.api.applyTransaction({ remove: sendGx.getSelectedRows() });
    $("#gd-total").text(sendGx.getRows().length);
});

// 메시지 일괄입력
$(".batch-msg-btn").on("click", function(e) {
    e.preventDefault();
    const msg = $("#msg").val();
    const rows = sendGx.getRows();
    sendGx.gridOptions.api.applyTransaction({ update: rows.map(row => ({...row, msg})) });
    $("#gd-total").text(sendGx.getRows().length);
});

$('.add-msg-btn').click((e) => {
    e.preventDefault();

    if ($('.name').val() == "") {
        alert("수신자를 입력해주세요.");
        return;
    }

    if (!/^01(?:0|1|[6-9])-(?:\d{3}|\d{4})-\d{4}$/.test($('.phone').val())) {
        alert("수신자 휴대전화 번호를 확인해주세요.");
        return;
    }

    const msg = $('#msg').val();

    addRow({
        'name'  : $('.name').val(),
        'phone' : $('.phone').val(),
        'msg'   : msg,
        'byte'  : msg.bytes(),
        'idx'   : searchRows.length
    });
});

$('#file').change((e) => {
  if (validateFile() === false) return;

  $('.custom-file-label').html($('#file')[0].files[0].name);

  loadData();
});

$('.template-btn').click((e) => {
    e.preventDefault();
    const url='/head/api/template';
    const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
});

$('.send-btn').click((e) => {
    e.preventDefault();
    const rows = sendGx.getSelectedRows();
    const cnt = rows.length;

    if (rows.length  === 0) {
        alert("선택된 전화번호가 없습니다.");
        return;
    }

    if ($('.send-phone').val() === "") {
        alert("송신자 전화번호를 입력해주세요.");
        return;
    }

    rows.forEach((row, idx) => {
        $.ajax({
            type: 'put',
            url: '/head/api/sms/send',
            dataType:'json',
            data: { "data" : row, "shop_tel" : $('.send-phone').val() },
            success: function (data) {
                if (cnt -1 === idx) {
                    alert("전송되었습니다.");
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    });
});

$('.search-btn').click((e) => {
    e.preventDefault();

    Search();
});

$('#msg').change(msgByteCount);
$('#msg').keyup(msgByteCount);

msgByteCount();

//부트스트랩 스크립트가 로드가 되어야 실행 가능
$(function(){
    $('#{{$type}}-tab').tab('show');

    '{{$type}}' === 'list' && Search();

    let users = '{{ @$users }}';
    users = JSON.parse(users.replace(/&quot;/ig,'"'));
    if (users.length > 0) {
        for (let user of users) {
            addRow({
                'name'  : user.s_name,
                'phone' : user.s_phone,
                'msg'   : '',
                'byte'  : ''.bytes(),
                'idx'   : searchRows.length
            });
        }
    }
});
</script>
@stop
