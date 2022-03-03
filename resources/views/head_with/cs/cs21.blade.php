@extends('head_with.layouts.layout-nav')
@section('title','업체 클레임 조회')
@section('content')
<div class="container-fluid show_layout py-3">
    <form method="get" name="search">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800">업체 클레임 조회</h1>
            <div>
                <a href="#" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                <input type="reset" class="btn btn-sm btn-primary shadow-sm brand-add-btn" value="검색 조건 초기화">
                <div id="search-btn-collapse" class="btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
        
        <div id="search-area" class="search_cum_form">
            <div class="card mb-1">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="brand_cd">요청일자 :</label>
                                <div class="form-inline inline_input_box">
                                    <div class="docs-datepicker form-inline-inner">
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
                                    <div class="docs-datepicker form-inline-inner">
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
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="com_type">상품명 :</label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm ac-goods-nm search-enter" name="goods_nm" value="">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="type">주문번호 :</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm search-all search-enter" name='ord_no' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">CS유형 : </label>
                                <div class="flax_box">
                                    <select id="cs_type" name="cs_type" class="form-control form-control-sm w-100">
                                        <option value="">전체</option>
                                        @foreach ($cs_types as $cs_type)
                                            <option value="{{ $cs_type->code_id }}">{{ $cs_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="com_type">주문자 :</label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm search-all search-enter" name="user_nm" value="">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="type">작성자 :</label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm search-all search-enter" name="req_nm" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">업체 : </label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 pr-1">
                                        <select id="com_type" name="com_type" class="form-control form-control-sm w-100">
                                            <option value="">전체</option>
                                            @foreach ($com_types as $com_type)
                                                <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input-box w-75">
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company" style="width:100%">
                                            <a href="#" class="btn btn-sm btn-secondary sch-company">...</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="com_type">처리상태 :</label>
                                <div class="flax_box">
                                    <select name="state" id="state" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        <option value="Y">처리</option>
                                        <option value="N">미처리</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow mb-3">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
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
    const columns = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:50
        },
        {
            field:"regi_date", 
            headerName:"클레임 요청일시", 
            width:170, 
            cellStyle:StyleOrdNo, 
            type:'HeadOrderNoType'
        },
        {field:"cs_form" , headerName:"CS유형"},
        {field:"state" , headerName:"상태"  },
        {field:"ord_no" , headerName:"주문번호", type:'HeadOrderNoType'  },
        {field:"head_desc" , headerName:"상단홍보글"  },
        {field:"goods_nm" , headerName:"상품명", type:'HeadGoodsNameType'  },
        {field:"user_nm" , headerName:"주문자"  },
        {field:"name" , headerName:"작성자"},
        {field:"state2" , headerName:"처리상태"  },
        {field:"memo" , headerName:"클레임내용"  }
    ];

    const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns);

    function Search(){
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/cs/cs21/search', data);
    }

    Search();
</script>
@stop
