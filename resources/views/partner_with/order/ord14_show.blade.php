@extends('partner_with.layouts.layout-nav')
@section('title','계좌관리')
@section('content')

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">계좌관리</h3>
        </div>
    </div>
    <div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Add()">추가</a>
                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Save();">저장</a>
                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Delete()">삭제</a>
                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="window.close()">닫기</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
    <script language="javascript">
        var columns = [
            {
                headerName: '',
                headerCheckboxSelection: true,
                checkboxSelection: true,
                width:50
            },
            {
                headerName: '#',
                width:50,
                maxWidth: 100,
                // it is important to have node.id here, so that when the id changes (which happens
                // when the row is loaded) then the cell is refreshed.
                valueGetter: 'node.id',
                cellRenderer: 'loadingRenderer',
            },
            {field:"number", headerName:"계좌번호"},
            {field:"bkname", headerName:"은행명"},
            {field:"bankda_id", headerName:"뱅크다 아이디"},
            {field:"bankda_pwd", headerName:"뱅크다 패스워드"},
            {field:"use_yn", headerName:"사용여부"},
            {field:"rt", headerName:"등록일시"},
            {field:"ut", headerName:"수정일시"},
            {headerName: "", field: "nvl"}
        ];
    </script>
</div>

<script>

    let code = '{{ $code  }}';

    function Add(){
	
	var buffer = "";
	var tab = "\t";
	buffer += tab + 1 + tab + tab + tab + tab + tab + "Y" + tab + tab + tab + tab;
	gxt.AddRow(gx,buffer);
}

    function Save() {  

    // if ($('#type').val() === '') {
    //     $('#type').focus();
    //     alert('타입를 꼭 입력해 주세요.');
    //     return false;
    // }   

    if (!confirm('저장하시겠습니까?')) {
        return false;
    }   

    var frm = $('form');    

    if (type == "") {
        console.log('store');
        $.ajax({
            method: 'post',
            url: '/partner/order/ord14',
            data: frm.serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.code == '200') {
                    alert("정상적으로 저장 되었습니다.");
                    self.close();
                    opener.Search(1);
                } else if (res.code == '501') {
                    alert('이미 등록 된 아이디입니다.');
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    console.log(res.code);
                    console.log(res.msg);
                }
            },
            error: function(e) {
                console.log(e.responseText)
            }
        });
    } else {
        $.ajax({
            method: 'put',
            url: '/partner/order/ord14/' + type + '/' + name,
            data: frm.serialize(),
            dataType: 'json',
            success: function(res) {
                // console.log(res);
                if (res.code == '200') {
                    alert("정상적으로 변경 되었습니다.");
                    self.close();
                    opener.Search(1);
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.code=');
                    console.log(res.code);
                    console.log(res.msg);
                }
            },
            error: function(e) {
                console.log(e.responseText)
            }
        });
    }
    return true;
    }   

    function Delete() {
    if (confirm('삭제 하시겠습니까?')) {
        $.ajax({
            method: 'delete',
            url: '/partner/order/ord14/' + type + '/' + name,
            dataType: 'json',
            success: function(res) {
                if (res.code == '200') {
                    alert("삭제되었습니다.");
                    self.close();
                    opener.Search(1);
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(e) {
                console.log(e.responseText);
            }
        });
    }
    }
</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = '';
        gx.Request('/partner/order/ord14/' + code + '/search', data);
    }
</script>
@stop