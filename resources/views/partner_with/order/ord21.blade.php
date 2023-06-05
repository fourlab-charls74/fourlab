@extends('partner_with.layouts.layout')
@section('title','배송출고요청')
@section('content')

<style>
    input[type="text"]::placeholder {
        color: #aaa;
        text-align: right;
    }
</style>

    <div class="page_tit">
        <h3 class="d-inline-flex">배송출고요청</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 배송출고요청</span>
        </div>
    </div>
    <form method="get" name="search">
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
                                <label for="formrow-firstname-input">주문일자</label>
                                <div class="form-inline date-select-inbox">
                                    <select name='date_type' class="form-control form-control-sm" style="width:23%;margin-right:2%;">
                                        <option value=''>사용자</option>
                                        <option value="1">금일</option>
                                        <option value="2">어제</option>
                                        <option value="3">최근1주</option>
                                        <option value="4">최근2주</option>
                                        <option value="5">최근1달</option>
                                        <option value="6">금월</option>
                                        <option value="7">전월</option>
                                    </select>
                                    <div class="docs-datepicker form-inline-inner" style="width:35%;">
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
                                    <span class="text_line" style="width:5%;">~</span>
                                    <div class="docs-datepicker form-inline-inner" style="width:35%;">
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
                                <label for="dlv_kind">주문상태</label>
                                <div class="flax_box">
                                    <select name='ord_state' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($ord_states as $ord_state)
                                            <option
                                                value='{{ $ord_state->code_id }}'
                                                {{ ($ord_state->code_id == '10') ? 'selected' : '' }}
                                            >
                                                {{ $ord_state->code_val }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-email-input">배송구분/배송방식</label>
	                            <div class="form-inline">
		                            <div class="form-inline-inner input_box">
			                            <select name='dlv_kind' class="form-control form-control-sm">
				                            <option value=''>전체</option>
				                            @foreach ($dlv_kinds as $dlv_kind)
					                            <option value='{{ $dlv_kind->code_id }}'>{{ $dlv_kind->code_val }}</option>
				                            @endforeach
			                            </select>
		                            </div>
		                            <span class="text_line">/</span>
		                            <div class="form-inline-inner input_box">
			                            <select name='dlv_type' class="form-control form-control-sm">
				                            <option value=''>전체</option>
				                            @foreach ($dlv_types as $dlv_type)
					                            <option value='{{ $dlv_type->code_id }}'>{{ $dlv_type->code_val }}</option>
				                            @endforeach
			                            </select>
		                            </div>
	                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="user_nm">주문자/수령자</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='user_nm' id="user_nm" value=''>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='r_nm' id="r_nm" value=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="style_no">스타일넘버</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="wqty_low">재고수량</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='wqty_low' value='' placeholder="이상">
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='wqty_high' value='' placeholder="이하">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_no">주문번호</label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm search-all search-enter" name="ord_no" id="ord_no" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_type">주문구분</label>
                                <div class="flax_box">
                                    <select name='ord_type' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($ord_types as $ord_type)
                                            <option value='{{ $ord_type->code_id }}'>{{ $ord_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_kind">출고구분</label>
                                <div class="flax_box">
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
                    <div class="search-area-ext d-none row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_type">품목</label>
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
            <div class="resul_btn_wrap mb-3">
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
    <!-- DataTales Example -->
    <div id="filter-area" class="card shadow-none ty2 last-card">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box flax_box">
                        <div class="custom-control custom-checkbox form-check-box">
                            <input type="checkbox" name="chk_ord_no" id="chk_ord_no" class="custom-control-input" checked="">
                            <label class="custom-control-label text-left" for="chk_ord_no" style="line-height:30px;justify-content:left">주문단위로 품절검사, </label>
                        </div>
                        <span style="font-weight:500;line-height:30px;margin-left:5px;vertical-align:middle;" class="mr-1">출고차수 :</span>
                        <div class="mr-1">
                            <input
                                type="text"
                                id="dlv_series_no"
                                class="form-control form-control-sm"
                                name="dlv_series_no"
                                value="{{date('YmdH').'_'.$com_id}}"
                            >
                        </div>
                        <a href="#" onclick="updateState()" class="btn btn-sm btn-primary shadow mr-1">출고처리중 변경</a>
                        <div class="mr-1">
                            <select id="u_ord_kind" class="form-control form-control-sm" style="width:120px;">
                                @foreach ($ord_kinds as $ord_kind)
                                <option value='{{ $ord_kind->code_id }}'>{{ $ord_kind->code_val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="#" onclick="updateKind()" class="btn btn-sm btn-primary shadow">출고상태 변경</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    <script language="javascript">
        var columns = [
            {headerName: '#', width: 40, maxWidth: 100, valueGetter: 'node.id', cellRenderer: 'loadingRenderer', cellClass: 'hd-grid-code', pinned: 'left'},
            {headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width: 28, pinned: 'left'},
            {field: "ord_type_nm", headerName: "주문구분", width: 60, cellClass: 'hd-grid-code', pinned: 'left'},
            {field: "ord_kind_nm", headerName: "출고구분", cellStyle: StyleOrdKind, width: 60, pinned: 'left'},
            {field: "ord_no", headerName: "주문번호", width: 140, cellStyle: StyleOrdNo, type: 'OrderNoType', cellClass: 'hd-grid-code', pinned: 'left'},
            {field: "ord_opt_no", headerName: "주문일련번호", sortable: "ture", width: 75, cellClass: 'hd-grid-code', pinned: 'left',
                cellRenderer: function(params) {
                    if (params.value !== undefined) {
                        return '<a href="/partner/order/ord01/"' + params.data.ord_opt_no + '" rel="noopener">' + params.value + '</a>';
                    }
                }
            },
            {field: "ord_state_nm", headerName: "주문상태", cellStyle: StyleOrdState, width: 60},
            {field: "pay_stat_nm", headerName: "입금상태", cellClass: 'hd-grid-code', width: 60},
            {field: "clm_state_nm", headerName: "클레임상태", cellStyle: StyleClmState, width: 70},
            {field: "goods_type_nm", headerName: "상품구분", cellStyle: StyleGoodsTypeNM, width: 60},
            {field: "style_no", headerName: "스타일넘버", width: 70, cellClass: 'hd-grid-code'},
            {field: "goods_nm", headerName: "상품명", type: "GoodsNameType", width: 200},
            {
              field:"img" ,
              headerName:"이미지", width:80,
              hide: true,
              cellRenderer: function(params) {
                  if (params.value !== undefined) {
                      return '<img src="{{config('shop.image_svr')}}/' + params.data.img + '"/>';
                  }
              }
            },
            {field: "opt_val", headerName: "옵션", width: 150},
            {field: "sale_qty", headerName: "주문수량", type: "numberType", width: 60},
            {field: "qty", headerName: "온라인재고", width: 70, type: "numberType",
                cellRenderer: (params) => {
				    return '<a href="#" onclick="return openStock(' + params.data.goods_no + ',\'' + params.data.opt_val +'\');">' + Comma(params.value) + '</a>';
			    }
            },
            {field:"user_nm", headerName: "주문자(아이디)", cellClass: 'hd-grid-code'},
            {field:"r_nm", headerName: "수령자", cellClass: 'hd-grid-code'},
            {field:"price" , headerName:"판매가", type: 'currencyType'  },
            {field:"sale_amt" , headerName:"쿠폰/할인", type: 'currencyType'  },
            {field:"gift" , headerName:"사은품"  },
            {field:"dlv_amt" , headerName:"배송비" , type: 'currencyType'  },
            {field:"pay_fee" , headerName:"결제수수료", type: 'currencyType'  },
            {field:"pay_type" , headerName:"결제방법"   },

            /**
             * 3차 QA - 기존과 다르게 개발도중 추가되었던 컬럼을 주석처리
             */
            // {field:"fintech" , headerName:"간편결제"},
            // {field:"cash_apply_yn" , headerName:"현금영수증신청"},
            // {field:"cash_yn" , headerName:"현금영수증발행"},

            /**
             * 3차 QA에서 요청한 기존 누락된 컬럼 추가
             */
            {field:"dlv_msg", headerName: "특이사항"},
            {field:"dlv_comment", headerName: "출고 메시지"},
            {field:"proc_state", headerName: "처리현황"},
            {field:"proc_memo", headerName: "메모"},
            {field: "sale_place", headerName: "판매처", width: 70, cellClass: 'hd-grid-code'},
            {field:"out_ord_no", headerName: "판매처주문번호"},

            {field: "baesong_kind", headerName:"배송구분", width: 70, cellClass: 'hd-grid-code'},
            {field: "dlv_type", headerName:"배송방식", width: 60, cellClass: 'hd-grid-code'},
            //출고요청이라 택배업체, 송장번호는 필요없다고 판단되었습니다.
            // {field:"dlv_nm" , headerName:"택배업체"  },
            // {field:"dlv_no" , headerName:"송장번호"  },
            {field: "ord_date", headerName: "주문일시", type: 'DateTimeType'},
            {field: "pay_date", headerName: "입금일시", type: 'DateTimeType'},
            // {field:"dlv_end_date" , headerName:"배송일시"},
            {field: "last_up_date", headerName: "클레임일시", type: 'DateTimeType'},
            {field:"goods_no", headerName:"goods_no",hide:true },
            {field:"goods_sub", headerName:"goods_sub",hide:true },
            {field:"img", headerName:"goods_img",hide:true },
            {field:"goods_type", headerName:"goods_type",hide:true },
            {field:"level", headerName:"level",hide:true },
            {field:"sms_name", headerName:"order_name",hide:true  },
            {field:"sms_mobile", headerName:"order_mobile",hide:true  },
            {width: "auto"}
        ];


        // let the grid know which columns to use

        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }

    </script>
    <script type="text/javascript" charset="utf-8">

        const pApp = new App('', { gridId: "#div-gd", height: 265 });
        const gridDiv = document.querySelector(pApp.options.gridId);
        let gx;
        $(document).ready(function () {
            gx = new HDGrid(gridDiv, columns, {
                isRowSelectable : function(node){
                    return node.data.ord_state < '30';
                }
            });
            pApp.ResizeGrid(265);
			pApp.BindSearchEnter();
            Search();

            $('.search-all').keyup(function(){
                date_use_check();
            });

        });

        function Search() {
            let data = $('form[name="search"]').serialize();
            gx.Request('/partner/order/ord21/search', data, -1, searchCallback);
    }

    function searchCallback(data) {}

    </script>

    <script type="text/javascript" charset="utf-8">

        function updateState() {
          var checkRows = gx.gridOptions.api.getSelectedRows();
          var dlvSeriesNo  = $("#dlv_series_no").val();
          var isOutSuccess = true;

          for (var i =0; i < checkRows.length && isOutSuccess; i++) {
            isOutSuccess = checkRows[i].ord_kind < 30;
          }

          if (isOutSuccess === false) {
            alert("출고보류 주문은 출고처리중으로 변경이 불가능합니다.");
            return;
          }

          if (dlvSeriesNo == "") {
            alert("출고차수를 입력해주세요.");
            return;
          }

          if (checkRows.length === 0) {
            alert("출고요청하실 주문건을 선택해주세요.");
            return;
          }

          if(confirm("선택하신 주문을 출고처리중으로 변경하시겠습니까?")) {
            var ord_opt_nos = checkRows.map(function(row) {
              return [row.ord_no, row.ord_opt_no];
            });

            $.ajax({
                async: true,
                type: 'put',
                url: '/partner/order/ord21/update/state',
                data: {
                  ord_opt_nos : ord_opt_nos,
                  dlv_series_no : dlvSeriesNo,
                  chk_ord_no : $("#chk_ord_no").prop("checked") ? "Y" : "N",
                  ord_state : 20
                },
                success: function (data) {
                  if (data == 1) {
                    alert("변경되었습니다.");
                  } else {
                    alert("품절된 제품이 있습니다.");
                  }
                  Search();
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
          }
        }

        function updateKind() {
          var checkRows = gx.gridOptions.api.getSelectedRows();

          if (checkRows.length === 0) {
            alert("출고상태를 변경할 주문건을 선택해주세요.");
            return;
          }

          if(confirm("선택하신 주문의 출고상태를 변경하시겠습니까?")) {
            var ordOptNos = checkRows.map(function(row) {
              return row.ord_opt_no;
            });

            $.ajax({
                async: true,
                type: 'put',
                url: '/partner/order/ord21/update/kind',
                data: {
                  ord_opt_nos : ordOptNos,
                  ord_kind : $("#u_ord_kind").val()
                },
                success: function (data) {
                  alert("변경되었습니다.");
                  Search();
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
          }
        }


        $(document).ready(function(){
            var $eventSelect = $(".select2-events");
            $eventSelect.select2();
            $eventSelect.on("select2:select", function (e) {
                if(e.params.data.id == "img"){
                    $("#div-gd").addClass("gd-lh50");
                    gx.gridOptions.api.resetRowHeights();
                    gx.gridOptions.columnApi.setColumnVisible("img", true);
                }
            });
            $eventSelect.on("select2:unselect", function (e) {
                if(e.params.data.id == "img"){
                    $("#div-gd").removeClass("gd-lh50");
                    gx.gridOptions.api.resetRowHeights();
                    gx.gridOptions.columnApi.setColumnVisible("img", false);
                }
            });
        });
    </script>


@stop
