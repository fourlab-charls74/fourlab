@extends('head_with.layouts.layout')
@section('title','클레임내역')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">클레임내역</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 클레임/CS</span>
        <span>/ 클레임내역</span>
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
					<!-- 처리일자/주문번호/주문자/입금자 -->
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">처리일자</label>
                                <div class="form-inline date-select-inbox">
                                    <select name='date_type'' class="form-control form-control-sm" style="width:23%;margin-right:2%;">
                                       <option value="">전체</option>
										@foreach ($date_type_items as $date_type)
											<option value="{{ $date_type->code_id }}">{{ $date_type->code_val }}</option>
										@endforeach
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
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm search-all search-enter" name="user_nm" value="">
                                        </div>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm search-all search-enter" name="pay_nm" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				
					<!-- 클레임상태/유형/클레임사유/환불여부/환불지급사 -->
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">클레임상태/유형</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <select name="clm_state" id="ce" class="form-control form-control-sm">
                                                <option value="">전체</option>
												@foreach($clm_state_items as $clm_state)
													<option value="{{ $clm_state->code_id }}">{{ $clm_state->code_val }}</option>
												@endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <select name="clm_type" id="ce" class="form-control form-control-sm">
                                                <option value="">전체</option>
												@foreach($clm_type_item as $clm_type)
													<option value="{{ $clm_type->code_id }}">{{ $clm_type->code_val }}</option>
												@endforeach
                                               
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_type">클레임사유</label>
                                <div class="flax_box">
                                    <select name="clm_reason" id="ce" class="form-control form-control-sm search-enter">
                                        <option value="">전체</option>
										@foreach($clm_reason_item as $clm_reason)
											<option value="{{ $clm_reason->code_id }}">{{ $clm_reason->code_val }}</option>
										@endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_type">환불여부/환불지급사</label>
                                <div class="form-inline">

									<div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <select name="refund_yn" id="ce" class="form-control form-control-sm">
												<option value="">전체</option>
												@foreach($refund_yn_item as $refund_yn)
													<option value="{{ $refund_yn->code_id }}">{{ $refund_yn->code_val }}</option>
												@endforeach
											</select>

                                        </div>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <select class="form-control form-control-sm" name="srefund">
												<option value="">전체</option>
												@foreach($srefund_item as $srefund)
													<option value="{{ $srefund->code_id }}">{{ $srefund->code_val }}</option>
												@endforeach
											</select>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>

					
					<!-- 스타일넘버/상품명/출력자료수/상단홍보글 -->
                    <div class="row search-area-ext">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                               <label for="style_no">스타일넘버</label>
                               <div class="flax_box">
                                   <input type='text' class="form-control form-control-sm search-all ac-style-no2 search-enter" name='style_no' value='@if($style_no != '') {{ $style_no }} @endif'>
                               </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_nm">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm search-all ac-goods_nm search-enter" name='goods_nm' value=''>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_kind">출력자료수/상단홍보글</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <select name="limit" id="ce" class="form-control form-control-sm">
                                                <option value="100">100</option>
                                                <option value="500">500</option>
                                                <option value="1000">1000</option>
                                                <option value="2000">2000</option>
                                                <option value="-1">모두</option>
                                            </select>
                                        </div>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm search-enter" name='head_desc' value=''>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


					<!-- 품목/업체/환불여부/환불지급사 -->
                    <div class="row search-area-ext">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">품목</label>
                                <div class="flax_box">
                                    <select name="opt_kind_cd" id="ce" class="form-control form-control-sm">
										<option value="">전체</option>
										@foreach($opt_kind_cd_items as $opt_kind_cd)
											<option value="{{ $opt_kind_cd->id }}">{{ $opt_kind_cd->val }}</option>
										@endforeach
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
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_type">업체</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner select-box">
                                        <select name="com_type" id="" class="form-control form-control-sm">
                                            <option value="">전체</option>
                                            @foreach($com_types as $com_type)
                                                <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                            @endforeach
									    </select>
                                    </div>
                                    <div class="form-inline-inner input-box">
                                        <div class="form-inline inline_btn_box">
                                            <input type="hidden" name="cat_cd" id="cat_cd" value="">
                                            <input type="text" class="form-control form-control-sm search-all search-enter ac-company2" name='com_nm' id='com_nm' value='' autocomplete='off' readonly>
                                            <a href="#" class="btn btn-sm btn-outline-primary company-add-btn"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                               <label for="style_no">결제방법</label>
                               <div class="flax_box">
                                    <select name="stat_pay_type" class="form-control form-control-sm" style="width:calc(20% - 10px);margin-right:10px;">
                                        <option value="">전체</option>
                                            @foreach($stat_pay_types as $stat_pay_type)
                                                <option value="{{ $stat_pay_type->code_id }}">{{ $stat_pay_type->code_val }}</option>
                                            @endforeach
                                    </select>
                                    <div class="form-inline" style="width:80%;">
                                        <div class="custom-control custom-checkbox form-check-box">
                                            <input type="checkbox" name="not_complex" id="not_complex_y" class="custom-control-input" value="Y">
                                            <label class="custom-control-label" for="not_complex_y">복합결제 제외</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<!-- 결제방법/예금주/은행명/접수자 -->
                    <div class="row search-area-ext">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_nm">예금주/은행명</label>
                                <div class="form-inline">
									<div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm search-all search-enter" name="refund_nm" value="">
                                        </div>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm search-all search-enter" name="refund_bank" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_kind">접수자</label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm search-enter" name="req_nm" id="req_nm">
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
                <div id="div-gd" style="height:calc(100vh - 500px);min-height:300px;width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>

	<script language="javascript">
        var columns = [
            {headerName: '#', width:34, maxWidth: 90,type:'NumType', pinned:'left', cellStyle: {"background":"#F5F7F7"}},
            {field:"ord_no" , headerName:"주문번호",type:'HeadOrderNoType', pinned:'left', width:130},
			{field:"opt_kind_cd" , headerName:"품목", pinned:'left', width:70  },
            {field:"style_no" , headerName:"스타일넘버", pinned:'left', width:70, cellStyle: {'text-align':'center'}  },
            {field:"head_desc" , headerName:"상단홍보글", pinned:'left'  },
            {field:"goods_nm", headerName:"상품명", type:'HeadGoodsNameType', pinned:'left'},
            {field:"opt_val" , headerName:"옵션"  },
            {field:"cd" , headerName:"클레임사유"  },
            {field:"memo" , headerName:"클래임내용"  },
            {field:"cb" , headerName:"주문상태",cellStyle:StyleOrdState , width:72 },
            {field:"ce" , headerName:"클레임상태",cellStyle:StyleClmState,type:'HeadOrderNoType' , width:72 },
            {field:"user_nm" , headerName:"주문자", width:60 },
            {field:"user_id" , headerName:"아이디", type:"HeadUserType", width:60  },
            {field:"mobile" , headerName:"핸드폰", width:96 },
            {field:"cc" , headerName:"환불여부", width:72  },
            {field:"pay_amt" , headerName:"입금액",type:'currencyType', width:60  },
            {field:"refund_amt" , headerName:"환불금액",type:'currencyType', width:60},
            {field:"refund_nm" , headerName:"환불예금주", width:72  },
            {field:"refund_bank" , headerName:"환불은행", width:72 },
            {field:"refund_account" , headerName:"환불계좌"  },
            {field:"req_nm" , headerName:"접수자"  },
            {field:"req_dte" , headerName:"접수일"  },
            {field:"last_up_date" , headerName:"최종처리일", width:120  },
            {field:"srefund" , headerName:"환불지급사", width:72  },
            {field:"ca" , headerName:"결제방법", width:72  },
            {headerName: "", field: "nvl"}
        ];
    </script>
    <script type="text/javascript" charset="utf-8">
		const pApp = new App('', { gridId: "#div-gd" });
		const gridDiv = document.querySelector(pApp.options.gridId);
		const gx = new HDGrid(gridDiv, columns);

		pApp.ResizeGrid(265);
        pApp.BindSearchEnter();

		function Search() {
			let data = $('form[name="search"]').serialize();
            gx.Request('/head/cs/cs01/search', data,1);
		}
		
		$(function(){
			$('.ac-company2').autocomplete({
				//keydown 됬을때 해당 값을 가지고 서버에서 검색함.
				source : function(request, response) {
					$.ajax({
						method: 'get',
						url: '/head/auto-complete/company',
						data: { keyword : this.term },
						success: function (data) {
							response(data);
						},
						error: function(request, status, error) {
							console.log("error")
						}
					});
				},
				minLength: 1,
				autoFocus: true,
				delay: 100,
					select:function(event,ui){
					//console.log(ui.item);
					$("#com_id").val(ui.item.id);
				}
			});

			$('.ac-goods_nm').autocomplete({
				//keydown 됬을때 해당 값을 가지고 서버에서 검색함.
				source : function(request, response) {
					$.ajax({
						method: 'get',
						url: '/head/auto-complete/goods-nm',
						data: { keyword : this.term },
						success: function (data) {
							response(data);
						},
						error: function(request, status, error) {
							console.log("error")
						}
					});
				},
				minLength: 1,
				autoFocus: true,
				delay: 100
			});


			$(".ac-style-no2").autocomplete({
				//keydown 됬을때 해당 값을 가지고 서버에서 검색함.
				source : function(request, response) {
					$.ajax({
						method: 'get',
						url: '/head/auto-complete/style-no2',
						data: { keyword : this.term },
						success: function (data) {
							response(data);
						},
						error: function(request, status, error) {
							console.log("error");
							//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
						}
					});
				},
				minLength: 1,
				autoFocus: true,
				delay: 100,
			});

			$(".company-add-btn").click((e) => {
				e.preventDefault();

				searchCompany.Open((code, name) => {
					if (confirm("선택한 업체를 추가하시겠습니까?") === false) return;

					$("#com_nm").val(name);
					$("#com_id").val(code);
					
				});
			});


			Search();

		});
    </script>


@stop