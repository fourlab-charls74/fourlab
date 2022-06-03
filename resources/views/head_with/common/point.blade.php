@extends('head_with.layouts.layout-nav')
@section('title','적립금 관리')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">적립금 관리</h1>
        <div>
            <a href="#" id="search_sbtn" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    <form action="" name="search">
        <input type="hidden" name="data" value="{{$data}}">
	    <input type="hidden" name="point_kinds" value="{{$point_kinds}}" />
    </form>
    <div id="send-area" class="card shadow mb-3 search_cum_form">
        <div class="card-body">
            <!-- 상태 -->
            <div class="row">
                <div class="col inner-td mt-3">
                    <div class="form-group">
                        <label for="type">상태 : </label>
                        <div class="flax_box">
                            <select id='state' class="form-control form-control-sm mr-1" style="width:100%;">
                                @foreach($states as $val)
                                    <option value="{{$val->code_id}}">{{$val->code_val}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 구분 -->
            <div class="row">
                <div class="col inner-td">
                    <div class="form-group">
                        <label for="type">구분 : </label>
                        <div class="flax_box">
                            <select id='type' class="form-control form-control-sm mr-1" style="width:100%;">
                                @foreach($types as $val)
                                    <option value="{{$val->code_id}}" @if($val->code_id == $kind) selected @endif >{{$val->code_val}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 금액 -->
            <div class="row">
                <div class="col inner-td">
                    <div class="form-group">
                        <label for="type">금액 : </label>
                        <div class="flax_box">
                            <input type="text" id="point" class="form-control form-control-sm text-right" onKeyup="currency(this)" >
                        </div>
                    </div>
                </div>
            </div>
            <!-- 내용 -->
            <div class="row">
                <div class="col inner-td">
                    <div class="form-group">
                        <label for="type">내용 : </label>
                        <div class="flax_box">
                            <input type="text" id="comment" class="form-control form-control-sm" >
                        </div>
                    </div>
                </div>
            </div>
            <!-- 유효기간 -->
			<!--
            <div class="row">
                <div class="col inner-td">
                    <div class="form-group">
                        <label for="type">유효기간 : </label>
                        <div class="flax_box">
                            <div class="docs-datepicker" style="width:100%">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm docs-date" name="expire_day" id="expire_day" autocomplete="off" disable>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="docs-datepicker-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			//-->
            <!-- 주문번호 -->
            <div class="row">
                <div class="col inner-td">
                    <div class="form-group">
                        <label for="type">주문번호 : </label>
                        <div class="flax_box">
                            <input type="text" id="ord_no" class="form-control form-control-sm" >
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="card shadow mb-3">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap mt-2">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box flax_box">
                        <a href="#" class="btn-sm btn btn-primary mr-1 apply-btn">적용</a>
                        <a href="#" class="btn-sm btn btn-primary mr-1 save-btn">지급</a>
                        <a href="#" class="btn-sm btn btn-primary delete-btn">삭제</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>

<script>

const editCellStyle = { 
    'background' : '#ffff99', 
    'border-right' : '1px solid #e0e7e7' 
};

const column = [
    {
        headerName: '',
        headerCheckboxSelection: true,
        checkboxSelection: true,
        width:28
    },
    {field: "user_id", headerName: "아이디"},
    {field: "name", headerName: "이름"},
    {field: "point", headerName: "금액", type: 'currencyType'},
    {field: "comment", headerName: "내용"},
    {field: "ord_no", headerName: "주문번호"},
    {field: "expire_day", headerName: "유효기간"}
];

const pApp = new App('', {gridId: "#div-gd"});
const gridDiv = document.querySelector(pApp.options.gridId);
const gx = new HDGrid(gridDiv, column);

gx.gridOptions.getRowNodeId = function(data) {
    return data.rownum;
}

// pApp.ResizeGrid();

const Search = () => {
    const data = $('form[name="search"]').serialize();
    gx.Request('/head/api/point/search', data, 1);
}

$('.delete-btn').click((e) => {
    e.preventDefault();

    gx.getSelectedRows().forEach((selectedRow, index) => {
        gx.gridOptions.api.updateRowData({remove: [selectedRow]});
        $("#gd-total").text(gx.getRows().length);
    });
});

$('.apply-btn').click((e) => {
    e.preventDefault();[]
    const type = $('#type').val();
    const ord_no = $('#ord_no').val();
    const point = unComma($('#point').val());
    const rows = gx.getSelectedRows();

    let comment = $('#comment').val();

    if (rows.length === 0) {
        alert("회원을 선택해주세요.");
        return false;
    }

    if (type == 2 && ord_no == '') {
		alert("구분이 주문인경우 주문번호를 반드시 입력해야 합니다.");
		return false;
    }

    if(point == ""){
        alert('적립금을 입력해 주십시오.');
        return false;
    }

	if(comment == ""){
		comment = $('#type > option:selected').html();
    }

    rows.forEach(function(data) {
        const nodeRow = gx.gridOptions.api.getRowNode(data.rownum);

        data.point = point;
        data.comment = comment;
        data.ord_no = ord_no;
        //data.expire_day = $("#expire_day").val().replace(/-/g, '');

        nodeRow.setData(data);

        gx.gridOptions.api.redrawRows({
            rowNodes : [nodeRow]
        });
    });
});

$('.save-btn').click(function(e){
    e.preventDefault();

    const rows = gx.getSelectedRows();
    const cnt = rows.length;

    if (cnt === 0) {
        alert("회원을 선택해주세요.");
        return false;
    }

    let data = [];
    let isSubmit = true;

    for(let i=0; i < cnt; i++) {
        let row = rows[i];

        if (!row.point) {
            alert(`[${row.user_id}]님의 적립금액이 0원 입니다. 정확하게 입력해주십시오.`);
            isSubmit = false;
            break;
        }

        if (!row.comment) {
			alert("적립금 내용을 입력해 주십시오.");
            isSubmit = false;
            break;
        }

        data.push(`${row.user_id}|${row.name}|${row.point}|${row.comment}|${row.ord_no}|${row.expire_day}|${row.no}`);
    }
    if (!isSubmit) return false;

    $.ajax({
        async: true,
        type: 'put',
        url: '/head/api/point',
        data: { 
            datas : data,
            state : $('#state').val(),
            type :  $('#type').val(),
            point_kinds : $('[name=point_kinds]').val()
         },
        success: function (data) {
            alert("적립되었습니다.");
            // window.close();
        },
        error: function(request, status, error) {
            console.log("error")
        }
    });
});
Search();
</script>
@stop