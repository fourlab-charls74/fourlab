@extends('partner_with.layouts.layout')
@section('title','클레임내역')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">클레임내역</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 클레임내역</span>
        </div>
    </div>

    <form method="get" name="search" id="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">처리일자</label>
                                <div class="form-inline date-select-inbox">
                                    <select name='date_type' class="form-control form-control-sm search-all search-enter" style="width:23%;margin-right:2%;">
                                       <option value="">전체</option>
                                       <option value="10">요청</option>
                                       <option value="20">처리</option>
                                       <option value="30">완료</option>
                                    </select>
                                    <div class="docs-datepicker form-inline-inner" style="width:35%;">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date search-all search-enter" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                    <span class="text_line" style="width:5%;">~</span>
                                    <div class="docs-datepicker form-inline-inner" style="width:35%;">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date search-all search-enter" name="edate" value="{{ $edate }}" autocomplete="off">
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
                                <label for="ord_no">주문번호</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm search-all search-enter" name='ord_no' value=''>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">주문자/입금자</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='user_nm' value=''>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='r_nm' value=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="search-area-ext d-none  row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">클레임상태/유형</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <select name="clm_state" id="ce" class="form-control form-control-sm search-all search-enter">
                                            <option value="">전체</option>
                                            <option value="40">교환요청</option>
                                            <option value="41">환불요청</option>
                                            <option value="50">교환처리중</option>
                                            <option value="51">환불처리중</option>
                                            <option value="60">교환완료</option>
                                            <option value="61">환불완료</option>
                                            <option value="-10">주문취소</option>
                                            <option value="-30">클레임무효</option>
                                            <option value="1">임시저장</option>
                                            <option value="90">(클레임없음)</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <select name="clm_type" id="ce" class="form-control form-control-sm search-all search-enter">
                                            <option value="">전체</option>
                                            <option value="3">고객센터 전화</option>
                                            <option value="1">품절보상제</option>
                                            <option value="2">환불</option>
                                            <option value="9">자료이상</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_type">클레임사유</label>
                                <div class="flax_box">
                                    <select name="clm_reason" id="ce" class="form-control form-control-sm search-all search-enter">
                                        <option value="">전체</option>
                                        <option value="30">A/S관련</option>
                                        <option value="31">재결제</option>
                                        <option value="24">퀄리티 불만</option>
                                        <option value="50">결제 오류</option>
                                        <option value="1">고객변심</option>
                                        <option value="2">고객오류</option>
                                        <option value="3">중복주문</option>
                                        <option value="4">품절</option>
                                        <option value="5">상품불량</option>
                                        <option value="6">시스템오류</option>
                                        <option value="7">오배송</option>
                                        <option value="9">배송지연</option>
                                        <option value="10">배송중분실</option>
                                        <option value="11">기타</option>
                                        <option value="25">미입금취소</option>
                                        <option value="12">고객센터 불만족</option>
                                        <option value="13">업무 처리 지연</option>
                                        <option value="14">교환제품 품절</option>
                                        <option value="15">사이즈 맞지 않음(단순)</option>
                                        <option value="16">화면과 다름(색상)</option>
                                        <option value="17">화면과 다름(디자인)</option>
                                        <option value="18">화면과 다름(재질)</option>
                                        <option value="19">상세 실측 오류</option>
                                        <option value="20">출하전 취소(변심환불)</option>
                                        <option value="21">출하전 취소(재주문)</option>
                                        <option value="22">출하전 취소(주문서변경)</option>
                                        <option value="23">화면과 다름(퀄리티)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_type">환불여부</label>
                                <div class="flax_box">
                                    <select name="refund_yn" id="ce" class="form-control form-control-sm search-all search-enter">
                                        <option value="">전체</option>
                                        <option value="n">환불안함</option>
                                        <option value="y">환불함</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="style_no">스타일넘버</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm search-all ac-style-no search-enter" name='style_no' value=''>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_nm">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm search-all ac-goods-nm search-enter" name='goods_nm' value=''>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_kind">출력자료수/상단홍보글</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <select name="limit" id="ce" class="form-control form-control-sm search-all search-enter">
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                            <option value="2000">2000</option>
                                            <option value="-1">모두</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='head_desc' value=''>
                                    </div>
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
<div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
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
            <div id="div-gd" style="height:calc(100vh - 500px);min-height:300px;width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
    <script language="javascript">
        var columns = [
            {headerName: '#', width:50, maxWidth: 100,type:'NumType'},
            {field:"ord_no" , headerName:"주문번호",type:'OrderNoType'},
            {field:"style_no" , headerName:"스타일넘버"  },
            {field:"head_desc" , headerName:"상단홍보글"  },
            {field:"goods_nm", headerName:"상품명", type:'GoodsNameType'},
            {field:"opt_val" , headerName:"옵션"  },
            {field:"cd" , headerName:"클레임사유"  },
            {field:"memo" , headerName:"클래임내용"  },
            {field:"cb" , headerName:"주문상태",cellStyle:StyleOrdState  },
            {field:"ce" , headerName:"클레임상태",cellStyle:StyleClmState, type:'OrderNoType',
                cellRenderer: function (params) {
                    if (params.value !== undefined) {
                        return '<a href="#" style="color: #FF0000; font-weight: bold;" onclick="return openOrder(\'' + params.data.ord_no + '\',\'' + params.data.ord_opt_no +'\');">' + params.value + '</a>';
                    }
                }
            },
            {field:"user_nm" , headerName:"주문자"  },
            {field:"use_id" , headerName:"아이디"  },
            {field:"mobile" , headerName:"핸드폰", width: 100  },
            {field:"cc" , headerName:"환불여부"  },
            {field:"pay_amt" , headerName:"입금액",type:'currencyType'  },
            {field:"refund_amt" , headerName:"환불금액",type:'currencyType'},
            {field:"refund_nm" , headerName:"환불예금주"  },
            {field:"refund_bank" , headerName:"환불은행"  },
            {field:"refund_account" , headerName:"환불계좌"  },
            {field:"req_nm" , headerName:"접수자"  },
            {field:"req_dte" , headerName:"접수일"  },
            {field:"last_up_date" , headerName:"최종처리일"  },
            {field:"srefund" , headerName:"환불지급사"  },
            {field:"ca" , headerName:"결제방법"  },
            {headerName: "", field: "", width: "auto"}
        ];
    </script>
    <script type="text/javascript" charset="utf-8">
        const pApp = new App('',{
            gridId:"#div-gd",
        });
        let gx;
        $(document).ready(function() {
            pApp.ResizeGrid(300);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns);
            Search();
        });
        function Search() {
            let data = $('form[name="search"]').serialize();
            gx.Request('/partner/cs/cs01/search', data,1);
        }
    </script>


@stop
