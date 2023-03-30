@extends('head_with.layouts.layout-nav')
@section('title','수기판매')
@section('content')
    <form name="f1" method="post">
        @csrf
        <input type="hidden" name="cmd" value="{{@$cmd}}">
        <input type="hidden" name="ord_no" value="{{@$ord_no}}">
        <input type="hidden" name="ord_opt_no" value="{{@$ord_opt_no}}">
        <input type="hidden" name="p_ord_opt_no" id="p_ord_opt_no" value="{{@$p_ord_opt_no}}">
        <div class="container-fluid show_layout py-3">
            <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="d-inline-flex">수기판매</h3>
                    <div class="d-inline-flex location">
                        <span class="home"></span>
                        <span>/ 주문</span>
                        <span>/ 주문상세내역</span>
                        <span>/ 수기판매</span>
                    </div>
                </div>
                <div>
                    @if (@$cmd == 'edit')
                        <input type="button" value="판매수정" class="btn btn-sm btn-primary shadow-sm mr-1 save-btn" onclick="Validate(document.f1);"/>
                    @else
                        <input type="button" value="판매등록" class="btn btn-sm btn-primary shadow-sm mr-1 save-btn" onclick="Validate(document.f1);"/>
                    @endif
                    <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
                </div>
            </div>
            <div class="card_wrap aco_card_wrap">
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#" class="m-0 font-weight-bold">주문자 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="9%">
                                                <col width="25%">
                                                <col width="9%">
                                                <col width="25%">
                                                <col width="9%">
                                                <col width="25%">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <th>ID</th>
                                                <td style="padding:0px 10px 0px 10px;" colspan="6">
                                                    <div class="form-inline">
                                                        <input type="text" name="user_id" id="user_id" value='{{@$ord->user_id}}' class="form-control form-control-sm mr-1" style="width:260px;">
                                                        <a href="#" onclick="GetUserInfo(document.f1.user_id.value)" class="btn btn-sm btn-primary shadow-sm fs-12 mr-1">회원정보 불러오기</a>
                                                        <a href="#" onclick="SameInfo();" class="btn btn-sm btn-primary shadow-sm fs-12 mr-1">수령자정보 동일</a>
                                                        <a href="#" onclick="PopSearchOrder();" class="btn btn-sm btn-primary shadow-sm fs-12">기존 주문정보 불러오기</a>
                                                        <div id="p_ord_no" class="p-2"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>이름</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <input type="text" class="form-control form-control-sm" name="user_nm" id="user_nm" value="">
                                                    </div>
                                                </td>
                                                <th>전화</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <input type="text" class="form-control form-control-sm" name="phone" id="phone" value="">
                                                    </div>
                                                </td>
                                                <th>휴대전화</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <input type="text" class="form-control form-control-sm" name="mobile" id="mobile" value="">
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#" class="m-0 font-weight-bold">수령자 정보</a>
                    </div>
                    <div class="card-body mt-1">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="9%">
                                                <col width="25%">
                                                <col width="9%">
                                                <col width="25%">
                                                <col width="9%">
                                                <col width="25%">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <th>이름</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <input type="text" class="form-control form-control-sm" name="r_user_nm" id="r_user_nm" value="">
                                                    </div>
                                                </td>
                                                <th>전화</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <input type="text" class="form-control form-control-sm" name="r_phone" id="r_phone"  value="">
                                                    </div>
                                                </td>
                                                <th>휴대전화</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <input type="text" class="form-control form-control-sm" name="r_mobile" id="r_mobile" value="">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>주소</th>
                                                <td colspan="5">
                                                    <div class="input_box flax_box address_box">
                                                        <input type="text" id="r_zip_code" name="r_zip_code" class="form-control form-control-sm" value="" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
                                                        <input type="text" id="r_addr1" name="r_addr1" class="form-control form-control-sm" value="" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
                                                        <input type="text" id="r_addr2" name="r_addr2" class="form-control form-control-sm" value="" style="width:calc(25% - 10px);margin-right:10px;">
                                                        <a href="javascript:;" onclick="openFindAddress('r_zip_code', 'r_addr1')" class="btn btn-sm btn-primary shadow-sm" style="width:80px;">
                                                            <i class="fas fa-search fa-sm text-white-50"></i>검색</a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>출고메시지</th>
                                                <td colspan="5">
                                                    <div class="flax_box">
                                                        <textarea name="dlv_msg" id="dlv_msg" class="form-control form-control-sm" cols="0" rows="0" value=""></textarea>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>추가배송비</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <input type="text" class="form-control form-control-sm text-right" name="add_dlv_fee" id="add_dlv_fee" value="">
                                                    </div>
                                                </td>
                                                <th>배송비적용</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio" onclick="CheckPoint(this);">
                                                            <input type="radio" name="dlv_apply" id="dlv_apply_y" value="Y" class="custom-control-input" checked><label class="custom-control-label" for="dlv_apply_y">적용함</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="dlv_apply" id="dlv_apply_n" value="N" class="custom-control-input"><label class="custom-control-label" for="dlv_apply_n">적용안함</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <th>적립금지급</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio" onclick="CheckPoint(this);">
                                                            <input type="radio" name="give_point" id="give_point_y" value="Y" checked class="custom-control-input">
                                                            <label class="custom-control-label" for="give_point_y">지급함</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="give_point" id="give_point_n" value="N" class="custom-control-input">
                                                            <label class="custom-control-label" for="give_point_n">지급안함</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#" class="m-0 font-weight-bold">주문상태 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="94px">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <th>주문상태</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="ord_state" id="ord_state_5" class="custom-control-input" value="5" />
                                                            <label class="custom-control-label" for="ord_state_5">입금예정</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="ord_state" id="ord_state_10" class="custom-control-input" value="10"  />
                                                            <label class="custom-control-label" for="ord_state_10">출고요청</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="ord_state" id="ord_state_30" class="custom-control-input" value="30" />
                                                            <label class="custom-control-label" for="ord_state_30">출고완료</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>출고형태</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="form-inline form-radio-box">
                                                        @foreach($ord_types as $val)
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="ord_type" id="ord_type_{{$val->code_id}}" value="{{$val->code_id}}" class="custom-control-input" @if($val->code_id == 14) checked @endif>
                                                                <label class="custom-control-label" for="ord_type_{{$val->code_id}}">{{$val->code_val}}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>판매업체</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <select name="sale_place" id="sale_place" class="form-control form-control-sm">
                                                            <option value="">선택</option>
                                                            @foreach($sale_places as $val)
                                                                <option value="{{$val->com_id}}" @if($val->com_id == "HEAD_OFFICE") selected @endif>{{$val->com_nm}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>출고구분</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="ord_kind" id="ord_kind_20" value="20" class="custom-control-input" checked>
                                                            <label class="custom-control-label" for="ord_kind_20">출고가능</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="ord_kind" id="ord_kind_30" value="30" class="custom-control-input">
                                                            <label class="custom-control-label" for="ord_kind_30">출고보류</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>배송정보</th>
                                                <td colspan="5">
                                                    <div class="form-inline inline_input_box">
                                                        <select name="dlv_cd" id="dlv_cd" class="form-control form-control-sm mr-sm-1">
                                                            <option value=''>택배업체</option>
                                                            @foreach($dlv_cds as $val)
                                                                <option value="{{$val->code_id}}">
                                                                    {{$val->code_val}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="text" name="dlv_no" class="form-control form-control-sm mt-1 mt-sm-0"/>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#" class="m-0 font-weight-bold">부모주문 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered th_border_none">
                                            <thead>
                                            <tr>
                                                <th>주문일련번호</th>
                                                <th>상품명</th>
                                                <th>옵션(옵션가격)</th>
                                                <th>수량</th>
                                                <th>+판매가</th>
                                                <th>-적립금</th>
                                                <th>-쿠폰</th>
                                                <th>-할인</th>
                                                <th>+배송비</th>
                                                <th>-결제수수료</th>
                                                <th>-주문액</th>
                                                <th>-입금액</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($p_ord_opt->ord_opt_no))
                                                <tr>
                                                    <td>{{@$p_ord_opt->ord_opt_no}}</td>
                                                    <td class="text-left">
                                                        <a href="#" onclick="return openHeadProduct('{{@$p_ord_opt->goods_no}}');">
                                                            <span alt="{@$OLD_GOODS_NM|escape}"><i>{{@$p_ord_opt->head_desc}}</i> {{@$p_ord_opt->goods_nm}}</span></a>
                                                    </td>
                                                    <td class="text-left">
                                                    <span style=" width: 100%;">
                                                    - {{@$p_ord_opt->opt_val}}
                                                        @if (@$p_ord_opt->opt_amt > 0)
                                                            (+{{number_format($p_ord_opt->opt_amt/$p_ord_opt->qty)}}
                                                            원)
                                                        @endif
                                                    </span>
                                                        @foreach($p_ord_opt->addopts as $key => $addopt)
                                                            <br/>
                                                            - {{$addopt->addopt}}
                                                            @if ($addopt->addopt_amt > 0)
                                                                (+{{number_format($addopt->addopt_amt/$addopt->addopt_qty)}}
                                                                원)
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td class="text-right">{{number_format($p_ord_opt->qty)}}</td>
                                                    <td class="text-right">{{number_format($p_ord_opt->price)}}</td>
                                                    <td class="text-right">{{number_format($p_ord_opt->point_amt)}}</td>
                                                    <td class="text-right">{{number_format($p_ord_opt->coupon_amt)}}</td>
                                                    <td class="text-right">{{number_format($p_ord_opt->dc_amt)}}</td>
                                                    <td class="text-right">{{number_format($p_ord_opt->dlv_amt)}}</td>
                                                    <td class="text-right">{{number_format($p_ord_opt->pay_fee)}}</td>
                                                    <td class="text-right">{{number_format($p_ord_opt->recv_amt)}}</td>
                                                    <td class="text-right">{{number_format($p_ord_opt->total_amt)}}</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <div class="filter_wrap">
                            <div class="fl_box">
                                <a href="#" class="m-0 font-weight-bold">상품정보</a>
                            </div>
                            <div class="fr_box">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fr_box">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="div-gd" style="height:250px;width:100%;" class="ag-theme-balham"></div>
                        </div>
                    </div>
                </div>
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#" class="m-0 font-weight-bold">결제 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="9%">
                                                <col width="25%">
                                                <col width="9%">
                                                <col width="25%">
                                                <col width="9%">
                                                <col width="25%">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <th>결재수단</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <select name="pay_type" id="pay_type" class="form-control form-control-sm">
                                                            <option value="">==결제방법==</option>
                                                            @foreach($pay_types as $val)
                                                                <option value="{{$val->code_id}}">{{$val->code_val}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <th>입금은행</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <select name="bank_code" id="bank_code" class="form-control form-control-sm">
                                                            <option value="">선택하세요.</option>
                                                            @foreach($banks as $bank)
                                                                <option value="{{$bank->name}}">{{$bank->value}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <th>입금자</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <input type="text" class="form-control form-control-sm" name="bank_inpnm" id="bank_inpnm" value=""/>
                                                        <input type="hidden" name="coupon_no" value="">
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="text-center mt-3">
                @if (@$cmd == 'edit')
                    <input type="button" value=" 판매수정" class="btn btn-sm btn-primary shadow-sm mr-1 save-btn" onclick="Validate(document.f1);"/>
                @else
                    <input type="button" value=" 판매등록" class="btn btn-sm btn-primary shadow-sm mr-1 save-btn" onclick="Validate(document.f1);"/>
                @endif
            </div> -->
    </form>
    </div>
    <!-- script -->
    @include('head_with.order.ord20_js')
    <!-- script -->
    <script type="text/javascript">

        const columns = [
            {headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width:50},
            {field:"ord_opt_no" , headerName:"주문일련번호", width:100,type:'HeadOrdOptNoType'},
            {field:"goods_nm" , headerName:"상품명",type:"HeadGoodsNameType",width:200,wrapText:true,autoHeight:true},
            {field:"opt_val" , headerName:"옵션",width:100,
                editable: true,
                cellClass:['hd-grid-edit'],
                cellEditor: 'agRichSelectCellEditor',
                cellEditorParams:cellOpionsParams,
                // cellEditorSelector: function(params) {
                //     return {
                //         component: 'agRichSelectCellEditor',
                //         params: {
                //             values: []
                //         }
                //     };
                // }
            },
            {field:"qty" , headerName:"수량", width:90,type: 'numberType',
                editable: true,
                cellClass:['hd-grid-number','hd-grid-edit'],onCellValueChanged:EditAmt,
                //cellStyle:StyleChangeYN,
            },
            {field:"price" , headerName:"판매가", width:90, type: 'currencyType'  },
            {field:"ord_amt" , headerName:"주문액", width:90, type: 'currencyType'  },
            {field:"recv_amt" , headerName:"입금액", width:90, type: 'currencyType'  },
            {field:"point_amt" , headerName:"(-)적립금", width:90, type: 'currencyType'  },
            {field:"coupon_amt" , headerName:"(-)쿠폰", width:90, type: 'currencyType'  },
            {field:"dc_amt" , headerName:"(-)할인", width:90, type: 'currencyType'  },
            {field:"dlv_amt" , headerName:"(+)배송비", width:90, type: 'currencyType'  },
            {headerName: "", field: "nvl"}
        ];

        const pApp = new App('',{
            gridId:"#div-gd",
        });
        let gx;
        let _goods_options = {};

        $(document).ready(function() {

            let gridDiv = document.querySelector(pApp.options.gridId);
            if (gridDiv !== null) {
                gx = new HDGrid(gridDiv, columns);
                //gx.gridOptions.rowDragManaged = true;
                //gx.gridOptions.animateRows = true;
            }

            if($("#p_ord_opt_no").val() !== ""){
                //console.log($("#p_ord_opt_no").val());
                SetOrder('ord_no',$("#p_ord_opt_no").val());
            }
        });

    </script>
@stop
