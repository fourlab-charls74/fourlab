@extends('head_with.layouts.layout')
@section('title','상품별 클레임 통계')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">상품별 클레임 통계</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 상품별 클레임 통계</span>
        </div>
    </div>
    <form method="get" name="search" id="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-firstname-input">등록일</label>
                                <div class="form-inline">
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date month" name="edate" value="{{ $edate }}" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">구분</label>
                                <div class="form-inline form-check-box">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="type_e" id="type_e" value="E" checked>
                                        <label class="custom-control-label" for="type_e">교환</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="type_r" id="type_r" value="R" checked>
                                        <label class="custom-control-label" for="type_r">환불</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="type_c" id="type_c" value="C" checked>
                                        <label class="custom-control-label" for="type_c">취소</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>

                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box flax_box">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 500px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<script language="javascript">
    var columns = [
        {headerName: "스타일넘버", field: "style_no",
        cellRenderer: function(params) {
            s_style_no = $('[name=style_no]').val();
			return '<a href="/head/cs/cs01/?style_no='+ s_style_no +'"target="_new">'+ params.value+'</a>'
		    },
        aggSum:"합계"},
        {headerName: "상품명", field: "goods_nm",type:"HeadGoodsNameType"},
        {headerName: "합계", field: "clm_cnt",  type: 'numberType',cellStyle: StyleValue,aggregation:true},
        {headerName: "배송지연", field: "배송지연",  type: 'numberType',cellStyle: StyleValue,aggregation:true},
        {headerName: "오배송", field: "오배송",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "시스템오류", field: "시스템오류",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "결제 오류", field: "결제 오류",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "상품불량", field: "상품불량",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "품절(오류)", field: "품절(오류)",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "품절(재고분실)", field: "품절(재고분실)",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "품절", field: "품절",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "재결제", field: "재결제",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "A/S관련", field: "A/S관련",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "중복주문", field: "중복주문",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "미입금취소", field: "미입금취소",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "퀄리티 불만", field: "퀄리티 불만",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "화면과 다름(퀄리티)", field: "화면과 다름(퀄리티)",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "출하전 취소(주문서변경)", field: "출하전 취소(주문서변경)",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "출하전 취소(재주문)", field: "출하전 취소(재주문)",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "출하전 취소(변심환불)", field: "출하전 취소(변심환불)",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "고객오류", field: "고객오류",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "상세 실측 오류", field: "상세 실측 오류",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "화면과 다름(재질)", field: "화면과 다름(재질)",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "화면과 다름(디자인)", field: "화면과 다름(디자인)",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "화면과 다름(색상)", field: "화면과 다름(색상)",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "사이즈 맞지 않음(단순)", field: "사이즈 맞지 않음(단순)",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "교환제품 품절", field: "교환제품 품절",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "업무 처리 지연", field: "업무 처리 지연",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "고객센터 불만족", field: "고객센터 불만족",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "기타", field: "기타",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "배송중분실", field: "배송중분실",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
        {headerName: "고객변심(스타일)", field: "고객변심(스타일)",  type: 'numberType',cellStyle: StyleValue, aggregation:true},
    ];

    function StyleValue(params){
      var style = {'text-align':'right'};
      if (params.value != undefined) {
          //var style ="'text-align':'right',"
          if (params.value > 0) {
            style.backgroundColor = "#def1ff";
          }
      }
      return style;
    }

</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({
            "sum":"top",
        });
        gx.Request('/head/cs/cs73/search', data);
    }
</script>



@stop
