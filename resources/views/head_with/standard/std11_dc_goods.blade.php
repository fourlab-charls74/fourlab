@extends('head_with.layouts.layout-nav')
@section('title','광고 상세')
@section('content')
<div class="container-fluid py-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">상품별할인</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 기준정보</span>
            <span>/ 광고할인관리</span>
            <span>/ 상품별할인</span>
        </div>
    </div>
    <div id="search-area" class="search_cum_form">
        <form method="get" name="search">
            <input type="hidden" name="no" value="{{$no}}">
            <div class="card mb-1">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <button onclick="return Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</button>
                        <button class="btn btn-sm btn-outline-primary shadow-sm goods-add-btn">상품 추가</button>
                        <button class="btn btn-sm btn-outline-primary shadow-sm goods-delete-btn">상품 삭제</button>
                        <button class="btn btn-sm btn-primary shadow-sm goods-submit-btn">저장</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="user_yn">상품구분/상품상태</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <select name='goods_type' class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            @foreach ($goods_types as $goods_type)
                                                <option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <select name='goods_stat' class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            @foreach ($goods_stats as $goods_stat)
                                                <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="style_no"">스타일넘버/상품코드</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <input type="text" class="form-control form-control-sm search-enter" name="goods_no" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="com_type">업체</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <select id="com_type" name="com_type" class="form-control form-control-sm" style="width:100%">
                                            <option value="">전체</option>
                                            @foreach ($com_types as $com_type)
                                                <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="state">품목</label>
                                <div class="flax_box">
                                    <select name="opt_kind_cd" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="brand_cd">브랜드</label>
                                <div class="form-inline inline_btn_box">
                                    <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-email-input">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
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
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box flax_box">
                        <div class="flax_box">할인율 : <div class="mx-1"><input type="text" id="dc_rate" class="form-control form-control-sm" value="0" style="text-align:right;width:50px;"></div>/ </div>
                        <div class="flax_box ml-1">할인금액 : <div class="mx-1"><input type="text" id="dc_amt" class="form-control form-control-sm" value="0" style="text-align:right;width:50px;"></div> 원 /</div>
                        <div class="flax_box ml-1">마진율 제한 : <div class="mx-1"><input type="text" id="limit_margin_rate" class="form-control form-control-sm" value="0" style="text-align:right;width:50px;"></div>%</div>
                        <a href="#" onclick="" class="btn btn-sm btn-primary shadow-sm apply-btn ml-1">제휴할인 적용</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="goods-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>

<!-- 스크립트 선언부 -->
<script>
    const editCellStyle = { 
        'background' : '#ffff99', 
        'border-right' : '1px solid #e0e7e7' 
    };
    
    //상품별 할인 컬럼
    const goodsColumns = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:28
        },
        {field: "goods_no", headerName: "상품코드"},
        {field: "goods_type", headerName: "상품구분", width: 75},
        {field: "com_nm", headerName: "업체", width: 80},
        {field: "opt_kind_nm", headerName: "품목", width: 80},
        {field: "brand_nm", headerName: "브랜드"},
        {field: "style_no", headerName: "스타일넘버", width: 90},
        {field: "goods_nm", headerName: "상품명"},
        {field: "sale_stat_cl", headerName: "상품상태", cellStyle: StyleGoodsTypeNM},
        {field: "price", headerName: "판매가", type:'currencyType'},
        {field: "wonga", headerName: "원가", type:'currencyType'},
        {field: "dc_rate", headerName: "할인율(%)", editable: true, cellStyle: editCellStyle, type:'currencyType' },
        {field: "dc_amt", headerName: "할인금액(원)", editable: true, cellStyle: editCellStyle, type:'currencyType' },
        {field: "admin_nm", headerName: "관리자명"},
        {field: "ut", headerName: "최근수정일시", width: 130}
    ];

    //상품별 할인 설정
    const goodsApp = new App('', { gridId: "#goods-gd" });
    const goodsGrid = document.querySelector(goodsApp.options.gridId);
    const goodsGx = new HDGrid(goodsGrid, goodsColumns);

    goodsGx.gridOptions.getRowNodeId = function(data) {
        return createGoodsData(data);
    }

    const no = '{{$no}}';
    const pageNo = -1;

    const Search = () => {
        const data = $('form[name="search"]').serialize();
        goodsGx.Request('/head/standard/std11/search/dc-goods', data, pageNo, function(res){
            $('#goods-total').html(res.head.total);
        });
    };

    const addDCGoods = (data) => {
        $.ajax({
            async: true,
            type: 'post',
            url: `/head/standard/std11/dc/goods/${no}`,
            data: 'goods_nos='+data,
            success: function (res) {
                alert("상품별 할인이 추가되었습니다.");
                Search();
            },
            error: function(request, status, error) {
                alert(request.responseJSON.message);
            }
        });
    }

    const createGoodsData = (row) => {
        if (!row) return "";
        return row.goods_no+"|"+row.goods_sub;
    }

    //window open 에서는 arrow function이 인식이 안됨.
    function goodsCallback(row) {
        addDCGoods(createGoodsData(row));
    }

    function multiGoodsCallback(rows) {
        const dataRow = [];

        rows.forEach((row) => {
            dataRow.push(createGoodsData(row));
        });

        addDCGoods(dataRow.join(","));
    }
</script>

<!-- 스크립트 동작 -->
<script>
    goodsApp.ResizeGrid();

    Search();
    
    //상품별 할인 이벤트 정의
    $('.goods-add-btn').click((e) => {
        e.preventDefault();
        const url='/head/api/goods/show';
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=600");
    });
    
    $('.goods-delete-btn').click((e) => {
        e.preventDefault();

        const s_goods_cnt = goodsGx.getSelectedRows().length;

        if (s_goods_cnt === 0) {
            alert('삭제할 상품를 선택해주세요.');
            return;
        }

        if (confirm("선택한 상품를 삭제하시겠습니까?") === false) return;

        const delRows = [];

        goodsGx.getSelectedRows().forEach((row, idx) => {        
            delRows.push(createGoodsData(row));
        });
        
        $.ajax({
            async: true,
            type: 'delete',
            url: `/head/standard/std11/dc/goods/${no}`,
            data: { 'goods_nos' : delRows.join(',') },
            success: function (res) {
                alert('삭제되었습니다.');
                Search();
            },
            error: function(request, status, error) {
                console.log(request);
                alert(request.responseJSON.message);
            }
        });
    });

    $('.goods-submit-btn').click((e) => {
        e.preventDefault();
        const s_goods_cnt = goodsGx.getSelectedRows().length;

        if (s_goods_cnt === 0) {
            alert('수정할 상품를 선택해주세요.');
            return;
        }

        if (confirm("선택한 상품를 수정하시겠습니까?") === false) return;

        goodsGx.getSelectedRows().forEach((row, idx) => {        
            $.ajax({
                async: true,
                type: 'put',
                url: `/head/standard/std11/dc/goods/${no}`,
                data: row,
                success: function (res) {
                    if (idx === s_goods_cnt -1) {
                        alert('수정되었습니다.');
                        Search();
                    }
                },
                error: function(request, status, error) {
                    console.log(request);
                    alert(request.responseJSON.message);
                }
            });
        });
    });

    $('.apply-btn').click(e => {
        e.preventDefault();
        goodsGx.getSelectedRows().forEach(function(data) {
            const nodeRow = goodsGx.gridOptions.api.getRowNode(createGoodsData(data));
            data.dc_rate = $('#dc_rate').val();
            data.dc_amt = $('#dc_amt').val();
            data.limit_margin_rate = $('#limit_margin_rate').val();

            nodeRow.setData(data);
        });
    });
</script>
@stop
