@extends('head_with.layouts.layout-nav')
@section('title','판매처 엑셀 양식 변경')
@section('content')

<div class="py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">수기판매 일괄입력</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>판매처 엑셀 양식 변경</span>
            </div>
        </div>
        <div>
        </div>
    </div>
    <div id="search-area" class="search_cum_form">
        <form method="get" name="search">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div class="flax_box">
                        <a href="javascript:void(0);" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return FormatSearch();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <a href="javascript:void(0);" id="search_sbtn" onclick="FormatSave();" class="btn btn-sm btn-primary shadow-sm pl-2 mx-1">저장</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- end row -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name">판매업체</label>
                                <div class="flax_box">
                                    <select name='sale_place' id='sale_place' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($sale_places as $sale_place)
                                            <option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">

                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </form>
    </div>
    <div class="card shadow">
        <div class="card-body pt-3">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:30vh; width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>
<script>
    var columns = [
        {field: "name", headerName: "이름"},
        {field: "idx", headerName: " 주문 컬럼 순서",type:'numberType'},
        {field: "mat_idx", headerName: "판매처 엑셀번호", type:'numberType',width: 150,editable:true,cellClass:['hd-grid-number','hd-grid-edit']},
        {field: "mat_pattern", headerName: "패턴",width: 100,editable:true,cellClass:['hd-grid-number','hd-grid-edit']},
        {field: "mat_param", headerName: "인자",editable:true,cellClass:['hd-grid-number','hd-grid-edit']},
        {field: "mat_value", headerName: "변환",editable:true,cellClass:['hd-grid-number','hd-grid-edit']},
        {field: "nvl", headerName: " "},
    ];
</script>
<script>
    const pApp = new App('', {
        gridId: "#div-gd"
    });
    let gx;

    const gridDiv = document.querySelector(pApp.options.gridId);
    $(document).ready(function() {
        gx = new HDGrid(gridDiv, columns, {});
        pApp.ResizeGrid(210);
    });

    function FormatSearch(){
        var sale_place = $("#sale_place").val();
        if(sale_place === ""){
            alert('판매업체를 선택 해 주십시오');
            return;
        }
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/order/ord03/fmt/search', data,-1,function(){

            var clm2 = opener.columns2;
            gx.gridOptions.api.forEachNode(function(node) {
                //console.log(node.data);
                //console.log(clm2[node.data["idx"]+1]["headerName"]);
                node.data["name"] = clm2[node.data["idx"]+1]["headerName"];
            });
            gx.gridOptions.api.refreshCells();
            //console.log(opener.columns2);
        });
    }

    /**
     * @return {boolean}
     */
    function FormatSave(){

        if(confirm('저장을 하시겠습니까?')){
            gx.selectAll();
            let rows = gx.getSelectedRows();
            let sale_place = $("#sale_place").val();
            $.ajax({
                method: 'post',
                url: '/head/order/ord03/fmt/save',
                data: {'sale_place':sale_place,'data':rows},
                dataType: 'json',
                success: function (res) {
                    console.log(res);
                    if(res.code == '200'){
                        alert('저장하였습니다.');
                        gx.deselectAll();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
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

</script>
<!-- script -->
@include('head_with.order.ord03_js')
<!-- script -->

@stop
