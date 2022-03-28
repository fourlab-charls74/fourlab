@extends('partner_with.layouts.layout')
@section('title','주문내역')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">주문내역</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 주문내역</span>
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
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="formrow-firstname-input">주문일자</label>
                            <div class="form-inline inline_input_box">
                                <div class="docs-datepicker form-inline-inner input_box">
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
                                <div class="docs-datepicker form-inline-inner input_box">
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
                                <div style="position: absolute; top: 0; left: 0; z-index: -2;"
                                    class="d-none custom-control custom-switch date-switch-pos"  data-toggle="tooltip" data-placement="top" data-original-title="주문일자 사용">
                                    <input type="checkbox" class="custom-control-input" name="s_nud" id="s_nud" checked="" value="N" onClick="ManualNotUseData();">
                                    <label class="" for="s_nud" data-on-label="ON" data-off-label="OFF"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="ord_no">주문번호</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter search-all" id="ord_no" name='ord_no' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">주문자/수령자</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm search-enter search-all" name='user_nm' value=''>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm search-enter search-all" name='r_nm' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="style_no">스타일넘버/상품코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input-box" style="width:47%">
                                    <div class="form-inline-inner inline_btn_box">
                                        <input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value='' readonly>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputState">아이디</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all" name='user_id' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputZip">검색항목</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner select-box">
                                    <select name='cols' class="form-control form-control-sm">
                                        <option value=''>선택하세요.</option>
                                        <option value="b.mobile"  selected>주문자핸드폰번호</option>
                                        <option value="b.phone" >주문자전화번호</option>
                                        <option value="b.r_mobile" >수령자핸드폰번호</option>
                                        <option value="b.r_phone" >수령자전화번호</option>
                                        <option value="d.pay_nm" >입금자명</option>
                                        <option value="a.dlv_no" >송장번호</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input-box">
                                    <input type='text' name='key' class="form-control form-control-sm search-enter search-all"  value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row align-items-center">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">품목</label>
                            <div class="flax_box">
                                <select name="item" class="form-control form-control-sm">
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
                            <label for="brand_nm">브랜드</label>
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
                                <input type='text' class="form-control form-control-sm ac-goods-nm" name='goods_nm' value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row align-items-center">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_type">상품구분</label>
                            <div class="flax_box">
                                <select name="goods_type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($goods_types as $goods_type)
                                        <option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">배송구분</label>
                            <div class="flax_box">
                                <select name="dlv_kind" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($dlv_kinds as $dlv_kind)
                                        <option value='{{ $dlv_kind->code_id }}'>{{ $dlv_kind->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="ord_type">출고형태/구분</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <select name='ord_type' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($ord_types as $ord_type)
                                            <option value='{{ $ord_type->code_id }}'>{{ $ord_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <select name='ord_kind' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($ord_kinds as $ord_kind)
                                            <option value='{{ $ord_kind->code_id }}'>{{ $ord_kind->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row align-items-center">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="ord_state">주문상태/클레임상태</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <select name='ord_state' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($ord_states as $ord_state)
                                            <option value="{{ $ord_state->code_id }}">{{ $ord_state->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <select name='clm_state' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($clm_states as $clm_state)
                                            <option value="{{ $clm_state->code_id }}">{{ $clm_state->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">상단홍보글</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm" name='head_desc' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="limit">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="a.ord_date" selected>주문일자</option>
                                        <option value="b.user_nm" >주문자</option>
                                        <option value="b.r_nm" >수령자</option>
                                        <option value="c.goods_nm" >상품명</option>
                                        <option value="c.style_no" >스타일넘버</option>
                                        <option value="a.head_desc" > 상단홍보글</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                    <input type="radio" name="ord" id="sort_asc" value="asc">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn mr-1" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>

                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </div>
</form>
        <!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
    <div class="card-body">
        <div class="card-title mb-3">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                    
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="min-height:600px;width:100%;" class="ag-theme-balham gd-lh50"></div>
        </div>
    </div>
</div>

<script language="javascript">
    var columns = [
        {headerName: '#', width:50, maxWidth: 100,/* it is important to have node.id here, so that when the id changes (which happens when the row is loaded) then the cell is refreshed.*/ valueGetter: 'node.id', cellRenderer: 'loadingRenderer',},
        {field:"ord_no" , headerName:"주문번호", width:170, cellStyle:StyleOrdNo, type:'OrderNoType'},
        {field:"ord_opt_no" , headerName:"일련번호",sortable:"true",
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return openOrder(\'' + params.data.ord_no + '\',\'' + params.value +'\');">' + params.value + '</a>';
                }
            }
        },
        {field:"ord_state" , headerName:"주문상태",cellStyle:StyleOrdState  },
        {field:"clm_state" , headerName:"클레임상태",cellStyle:StyleClmState  },
        {field:"pay_stat" , headerName:"입금상태"  },
        {field:"goods_type_nm" , headerName:"상품구분",cellStyle:StyleGoodsType  },
        {field:"style_no" , headerName:"스타일넘버"  },
        {field:"img" , headerName:"이미지",type:'GoodsImageType'},
        {field:"goods_nm" , headerName:"상품명",width:200, type:"GoodsNameType"},
        {field:"opt_val" , headerName:"옵션"  },
        {field:"goods_addopt" , headerName:"추가옵션"  },
        {field:"qty" , headerName:"수량"  },
        {field:"user_nm" , headerName:"주문자(아이디)"  },
        {field:"r_nm" , headerName:"수령자"  },
        {field:"price" , headerName:"판매가", type: 'currencyType' },
        {field:"sale_amt" , headerName:"쿠폰/할인"  },
        {field:"gift" , headerName:"사은품"  },
        {field:"dlv_amt" , headerName:"배송비"  , type: 'currencyType'},
        {field:"pay_fee" , headerName:"결제수수료"  },
        {field:"pay_type" , headerName:"결제방법"   },
        {field:"fintech" , headerName:"간편결제"   },
        {field:"cash_apply_yn" , headerName:"현금영수증신청"   },
        {field:"cash_yn" , headerName:"현금영수증발행"   },
        {field:"ord_type" , headerName:"주문구분"  },
        {field:"ord_kind" , headerName:"출고구분",cellStyle:StyleOrdKind  },
        {field:"baesong_kind" , headerName:"배송구분"  },
        {field:"dlv_type" , headerName:"배송방식"  },
        {field:"dlv_nm" , headerName:"택배업체"  },
        {field:"dlv_no" , headerName:"송장번호"  },
        {field:"ord_date" , headerName:"주문일시"},
        {field:"pay_date" , headerName:"입금일시"},
        {field:"dlv_end_date" , headerName:"배송일시"},
        {field:"last_up_date" , headerName:"클레임일시", width: 120},
        {field:"goods_no", headerName:"goods_no",hide:true },
        {field:"goods_sub", headerName:"goods_sub",hide:true },
        {field:"img", headerName:"goods_img",hide:true },
        {field:"goods_type", headerName:"goods_type",hide:true },
        {field:"level", headerName:"level",hide:true },
        {field:"sms_name", headerName:"order_name",hide:true  },
        {field:"sms_mobile", headerName:"order_mobile",hide:true  }
    ];
    function date_use_check(){
        $("#check_use_date").attr("checked","checked");
    }
</script>

<script type="text/javascript" charset="utf-8">

    const pApp = new App('', {
        gridId: "#div-gd",
    });
    const gridDiv = document.querySelector(pApp.options.gridId);
    let gx;
    $(document).ready(function () {
        gx = new HDGrid(gridDiv, columns);
        pApp.ResizeGrid(290);
        pApp.BindSearchEnter();
        Search();
        $('.search-all').keyup(function(){
            date_use_check();
        });
    });
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/partner/order/ord01/search', data, 1);
    }
</script>


<script type="text/javascript" charset="utf-8">

    $(".sort_toggle_btn label").on("click", function(){
        $(".sort_toggle_btn label").attr("class","btn btn-secondary");
        $(this).attr("class","btn btn-primary");
    });

    $(document).ready(function() {
        document.search.user_id.onkeyup	= checkNotUseDate;
        document.search.user_nm.onkeyup	= checkNotUseDate;
        document.search.ord_no.onkeyup	= checkNotUseDate;
        document.search.r_nm.onkeyup	= checkNotUseDate;
        document.search.cols.onchange	= checkNotUseDate;
        document.search.key.onkeyup		= checkNotUseDate;
    });

    function IsNotUseDate()
    {
        var ff = document.search;
        var is_not_use_date = false;

        // 주문번호, 회원아이디, 주문자, 수령자, 주문자핸드폰/전화, 수령자 핸드폰 일때 날짜 검색 무시

        if( ff.user_id.value != "" )
            is_not_use_date = true;
        else if( ff.user_nm.value != "" )
            is_not_use_date = true;
        else if( ff.ord_no.value != "" )
            is_not_use_date = true;
        else if( ff.r_nm.value.length >= 2 )
            is_not_use_date = true;
        else if(ff.cols.value == "b.mobile" && ff.key.value.length >= 8)
            is_not_use_date = true;
        else if(ff.cols.value == "b.phone" && ff.key.value.length >= 8)
            is_not_use_date = true;
        else if(ff.cols.value == "b.r_mobile" && ff.key.value.length >= 8)
            is_not_use_date = true;

        return is_not_use_date;
    }


    function checkNotUseDate()
    {
        if( IsNotUseDate() )
        {
            $('#s_nud').prop("checked", false);
        }
        else
        {
            $('#s_nud').prop("checked", true);
        }
        ManualNotUseData();
    }

    function ManualNotUseData()
    {
        if( $("[name=s_nud]").is(":checked") == true )
        {
            $("[name=sdate]").prop("disabled", false);
            $("[name=edate]").prop("disabled", false);
        }
        else
        {
            $("[name=sdate]").prop("disabled", true);
            $("[name=edate]").prop("disabled", true);
        }
    }


</script>
@stop

