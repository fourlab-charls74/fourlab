{{-- @extends('head_skote.layouts.master-without-nav') --}}
@extends('head_with.layouts.layout-nav')
@section('title','템플릿 검색')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">SMS</h1>
        <div>
            <a href="#" id="search_sbtn" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    <div class="card shadow mb-3">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box flax_box">
                        <a href="#" class="btn-sm btn btn-primary mr-1 order-memo-btn">변경내용저장</a>
                        <a href="#" class="btn-sm btn btn-primary mr-1 cancel-order-btn">주문취소</a>
                        <a href="#" class="btn-sm btn btn-primary confirm-order-btn">구매확정</a>
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
/**
 * 파일 : 상품 검색 팝업
 * 
 * [사용법]
 * window open 한 php 파일에
 * selectTemplate 메서드를 만든다.
 * 
 * selectTemplate 메서드에 파라메터로 선택한 템플릿의 제목 및 내용을 담은 json 데이터가 들어감
 * 
 */
    let selectRow = null;

    const columns = [
        {field:"subject" , headerName:"제목", width:180},
        {field:"ans_msg" , headerName:"내용", width:300},
        {
            field: "", 
            headerName: "선택",
            cellRenderer: function(params) {
                return `<a href="#" onClick="selectTemplate('${params.node.rowIndex}')">선택</a>`;
            }
        }
    ];

    const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns);

    pApp.ResizeGrid();

    function Search(){
        gx.Request('/head/api/template/search', "");
    }

    function selectTemplate(idx) {
        if (idx === '') return;

        if(opener && opener.selectTemplate) opener.selectTemplate(gx.gridOptions.api.getRowNode(idx).data)

        window.close();
    }

    Search();
</script>
@stop