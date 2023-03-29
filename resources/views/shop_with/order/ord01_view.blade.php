@extends('shop_with.layouts.layout-nav')
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
                                                <th>주문매장</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="d-flex flex-column pt-2 pb-1">
                                                        <div class="flax_box mr-2 mb-1" style="width: 307px;">
                                                            <input type='hidden' id="store_no" name="store_no" value="{{$store_cd}}">
                                                            <input type='text' id="store_nm" name="store_nm" class="form-control form-control-sm mt-1 mt-sm-0" value="{{$store_nm}}" readonly>
                                                        </div>
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
    <script type="text/javascript">

        const columns = [
            {headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width:50},
            {field:"ord_opt_no" , headerName:"주문일련번호", width:100,type:'HeadOrdOptNoType'},
            {field:"goods_nm" , headerName:"상품명",type:"HeadGoodsNameType",width:200,wrapText:true,autoHeight:true},
            {field:"opt_val" , headerName:"옵션",width:100,
                editable: true,
                cellClass:['hd-grid-edit'],
                cellEditor: 'agRichSelectCellEditor',
                cellEditorParams: function(params) {
                    var goods_no = params.data.goods_no;
                    var options = [];
                    if(_goods_options.hasOwnProperty(goods_no)){
                        options =  _goods_options[goods_no];
                    } else {
                    }
                    return {
                        values :options
                    }
                },
            
            },
            {field:"qty" , headerName:"수량", width:90,type: 'numberType',
                editable: true,
                cellClass:['hd-grid-number','hd-grid-edit'],onCellValueChanged: function(params) {
                    if (params.oldValue !== params.newValue) {
                        console.log(params);
                        var rowNode = params.node;
                        var qty = params.data.qty;
                        var price = params.data.price;
                        var option_price = 0;
                        price += option_price;

                        var ord_amt = qty * price;
                        var point_amt = params.data.point_amt;
                        var coupon_amt = params.data.coupon_amt;
                        var dc_amt = params.data.dc_amt;
                        var dlv_amt = params.data.dlv_amt;
                        var pay_fee = 0;
                        var recv_amt = ord_amt - point_amt - coupon_amt - dc_amt + pay_fee;

                        //ff.recv_amt.value = ord_amt + dlv_amt - point - coupon - dc + pay_fee;
                        params.data.ord_amt = ord_amt;
                        params.data.recv_amt = recv_amt;
                        gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
                    }
                },
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

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    let is_processing = false;

    const Validate = (ff) => {
        if (is_processing) {
            alert("잠시만 기다려 주십시오. 지금 등록중입니다.");
            return;
        }

        const ord_state = $("[name=ord_state]:checked");

        if(ord_state.length === 0){
            alert('주문상태를 선택해 주십시오.');
            return false;
        }

        if( ord_state.val() == 30 ){
            if( ff.dlv_cd.value == "" ){
                alert("택배사를 선택하십시오.");
                ff.dlv_cd.focus();
                return false;
            }
            if( ff.dlv_no.value == "" ){
                alert("택배 송장번호를 입력하십시오.");
                ff.dlv_no.focus();
                return false;
            }
        }
        const ord_type = $('[name=ord_type]:checked');

        if( ord_type.length === 0 ){
            alert('출고형태를 선택해 주십시오.');
            return false;
        }

       
        const ord_kind = $('[name=ord_kind]:checked');

        if(ord_kind.length === 0){
            alert('출고구분을 선택해 주십시오.');
            return false;
        }

        if(gx.getRowCount() == 0){
            alert("수기판매등록하실 상품을 검색해 주십시오.");
            return false;
        } else {
            var is_option = true;
            gx.gridOptions.api.forEachNode(function(node) {
                if(node.data.opt_val === undefined || node.data.opt_val === ""){
                    is_option = false;
                    gx.gridOptions.api.setFocusedCell(node.rowIndex,"opt_val");
                    return false;
                }
            });
            if(is_option === false){
                alert('옵션을 선택 해 주십시오.');
                return false;
            }
        }


        if(ff.pay_type.value == ""){
            alert("결제수단을 선택해 주십시오.");
            ff.pay_type.focus();
            return false;
        }

        if(ff.pay_type.value == "1" || ff.pay_type.value == "5" || ff.pay_type.value == "9" || ff.pay_type.value == "13"){

            if(ff.bank_code.value == ""){
                alert("입금은행을 선택해 주십시오.");
                ff.bank_code.focus();
                return false;
            }

            if(ff.bank_inpnm.value == "") {
                alert("입금자를 입력해 주십시오.");
                ff.bank_number.focus();
                return false;
            }
        }

        if(ff.pay_type.value == "9" || ff.pay_type.value=="13"){
            if(ff.coupon_no.value == ""){
                alert("쿠폰번호를 입력해 주십시오.");
                ff.coupon_no.focus();
                return false;
            }
        }

        if(confirm("수기판매로 등록하시면 더이상 수정하실 수 없습니다.\n등록 하시겠습니까?")){
            if( ! is_processing ){
                is_processing = true;
                save();
            }
        }
        return;
    }

    $('[name=ord_state]').change(function(){
        $('#delivery_info').css('display', this.value == '30' ? 'block' : 'none');
    });

    // 상품 옵션변경(옵션 가격)에 따른 판매가 초기화
    const CheckOptPrice = (goods_opt) => {
        if(goods_opt == "")
        {
            $("price").value = $("goods_price").value;
        }
        else
        {
            var ff = document.f1;
            var opt_price = 0;

            //옵션 가격 처리
            m_val = goods_opt.split("|");
            opt_price = (m_val[9] > 0) ? unComma(m_val[9]) : 0;

            // 추가옵션 가격
            var add_opt_cnt = 0;
            if(ff.addopt_cnt){
                add_opt_cnt = ff.addopt_cnt.value;
            }

            addopt = "";
            for( var i = 1; i<= add_opt_cnt; i++)
            {
                var addopt_obj = ff["addopt" + i];
                if( addopt_obj.value ){
                    addopt += ( addopt != "" ) ? "^":"";
                    addopt += addopt_obj.value;
                }
            }

            // 추가옵션 포맷 : 옵션값|상품번호|상품하위번호|추가옵션가격|추가옵션일련번호
            var addopt_price = 0;
            if( addopt ){
                is_multi = addopt.indexOf("^");
                is_price = addopt.indexOf("|");
                if( is_price > -1 ){
                    if( is_multi > -1 ){
                        tmp_addopt = addopt.split("^");
                        for(i = 0; i < tmp_addopt.length; i++){
                            tmp = tmp_addopt[i].split("|");
                            addopt_price += parseInt(tmp[3]);
                        }
                    } else {
                        tmp = addopt.split("|");
                        addopt_price += parseInt(tmp[3]);
                    }
                }
            }

            $("price").value = numberFormat(parseInt($("goods_price").value) + (opt_price + addopt_price));
        }
    }

    function EditAmt(params){
        if (params.oldValue !== params.newValue) {
            console.log(params);
            var rowNode = params.node;
            var qty = params.data.qty;
            var price = params.data.price;
            var option_price = 0;
            price += option_price;

            var ord_amt = qty * price;
            var point_amt = params.data.point_amt;
            var coupon_amt = params.data.coupon_amt;
            var dc_amt = params.data.dc_amt;
            var dlv_amt = params.data.dlv_amt;
            var pay_fee = 0;
            var recv_amt = ord_amt - point_amt - coupon_amt - dc_amt + pay_fee;

            //ff.recv_amt.value = ord_amt + dlv_amt - point - coupon - dc + pay_fee;
            params.data.ord_amt = ord_amt;
            params.data.recv_amt = recv_amt;
            gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
        }
    }

    function CalAmt() {
        var ff = document.f1;
        var qty = ff.qty.value;
        var price = (ff.price.value != "") ? unComma(ff.price.value):0;
        var goods_price = (ff.goods_price.value != "") ? unComma(ff.goods_price.value):0;
        var goods_opt = ff.goods_opt.value;

        //옵션 가격 처리
        m_val = goods_opt.split("|");
        opt_price = (m_val[9] > 0) ? unComma(m_val[9]) : 0;

        // 추가옵션
        if(ff.addopt_cnt){
            add_opt_cnt = ff.addopt_cnt.value;
        } else {
            add_opt_cnt = 0;
        }

        addopt = "";
        for( var i = 1; i<= add_opt_cnt; i++)
        {
            var addopt_obj = ff["addopt" + i];

            if( addopt_obj.value ){
                addopt += ( addopt != "" ) ? "^":"";
                addopt += addopt_obj.value;
            } else {
            }
        }

        // 추가옵션 포맷 : 옵션값|상품번호|상품하위번호|추가옵션가격|추가옵션일련번호
        var addopt_price = 0;
        if( addopt ){
            is_multi = addopt.indexOf("^");
            is_price = addopt.indexOf("|");
            if( is_price > -1 ){
                if( is_multi > -1 ){
                    tmp_addopt = addopt.split("^");
                    for(i = 0; i < tmp_addopt.length; i++){
                        tmp = tmp_addopt[i].split("|");
                        addopt_price += parseInt(tmp[3]);
                    }
                } else {
                    tmp = addopt.split("|");
                    addopt_price += parseInt(tmp[3]);
                }
            }
        }

        ff.goods_addopt.value = addopt;
        ff.addopt_price.value = addopt_price;

        // 옵션 가격이 있는 경우는 상품 가격을 수정할 수 없음.
        if((opt_price + addopt_price) == 0){
            $("price").className = "input";
            $("price").readOnly = false;
        } else {
            $("price").className = "input-disable";
            $("price").readOnly = true;
            // 가격에 옵션금액 포함
            $("price").value = numberFormat(goods_price + opt_price + addopt_price);
        }

        price = (ff.price.value != "") ? unComma(ff.price.value) : 0;
        var point = (ff.point_amt.value != "") ? unComma(ff.point_amt.value):0;
        var coupon = (ff.coupon_amt.value != "") ? unComma(ff.coupon_amt.value):0;
        var pay_fee = (ff.pay_fee.value != "") ? unComma(ff.pay_fee.value):0;
        var dc = (ff.dc_amt.value != "") ? unComma(ff.dc_amt.value):0;
        var dlv_amt = (ff.dlv_amt.value != "") ? unComma(ff.dlv_amt.value):0;
        var add_dlv_fee = unComma(add_dlv_fee);

        var dlv_apply = $('[name=dlv_apply]:checked').val();

        var ord_amt = price * qty;

        // 도매회원 배송비 무료
        if(ff.group_type.value == "WS" && $("#group_apply_y")[0].checked){
            free_dlv_fee_limit = wholesale_free_dlv_fee_limit;
        } else {
            free_dlv_fee_limit = base_free_dlv_fee_limit;
        }

        if( dlv_apply == "Y"){
            if( ord_amt < free_dlv_fee_limit ){
                dlv_amt = parseInt(dlv_fee) + parseInt(add_dlv_fee);
            } else {
                dlv_amt = 0 + parseInt(add_dlv_fee);
            }
            ff.dlv_amt.value = dlv_amt;
        } else {
            dlv_amt = 0 + parseInt(add_dlv_fee);
            ff.dlv_amt.value = dlv_amt;
        }

        com(ff.dlv_amt);

        ff.ord_amt.value = ord_amt;
        com(ff.ord_amt);

        ff.recv_amt.value = ord_amt + dlv_amt - point - coupon - dc + pay_fee;
        com(ff.recv_amt);
    }

    function GetUserInfo(){
        var user_id = $("#user_id").val();
        if(user_id == ""){
            alert("아이디를 입력해 주십시오.");
        }else{
            $.ajax({
                async: true,
                dataType: "json",
                type: 'get',
                url: "/shop/member/mem01/" + user_id + "/get",
                success: function (res) {
                    console.log(res);
                    if(res.hasOwnProperty('user')){
                        var user = res.user;
                        $('#user_nm').val(user.name);
                        $('#phone').val(user.phone);
                        $('#mobile').val(user.mobile);
                        $('#r_user_nm').val(user.name);
                        $('#r_phone').val(user.phone);
                        $('#r_mobile').val(user.mobile);
                        $('#r_zip_code').val(user.zip);
                        $('#r_addr1').val(user.addr);
                        $('#r_addr2').val(user.addr2);
                        $('#give_point_y').attr("checked", true);;

                    } else {
                        alert("아이디를 정확하게 입력해 주십시오.");
                    }
                },
                error: function(e) {
                    console.log(e.responseText);
                },
            });
        }
    }

    function ApplyGroup(obj){
        if( obj.checked ){
            var ff = document.f1;
            var group_type = ff.group_type.value;
            if( obj.value == "Y" ){
                if( group_type == "DC"){
                    var price = ( ff.price.value != "" ) ? unComma(ff.price.value):0;
                    var dc_ratio = ( ff.group_ratio.value != "" ) ? unComma(ff.group_ratio.value):0;
                    ff.dc_amt.value = parseInt(price * ( dc_ratio / 100 ));
                    com(ff.DC_AMT);
                } else if( group_type == "PT"){

                } else if( group_type == "WS"){
                    ff.price.value = ff.wholesale_price.value;
                    ff.com_price.value = ff.wholesale_price.value;
                    com(ff.PRICE);
                }
            } else {
                if( group_type == "DC"){
                    ff.dc_amt.value = 0;
                } else if( group_type == "PT"){

                } else if( group_type == "WS"){
                    ff.price.value = ff.org_price.value;
                    ff.com_price.value = ff.org_price.value;
                    com(ff.PRICE);
                }
            }
            CalAmt();
        }
    }

    function GetSecondOption(goods_no, goods_sub, goods_opt, option_cnt){

        var ff = document.f1;
        var opt_select2 = ff.goods_opt2;
        var cnt = opt_select2.length;
        for( i = 0; i < cnt; i++)
        {
            opt_select2.options[0] = null;
        }

        var is_price_include = goods_opt.indexOf("|");
        if( is_price_include > -1){
            tmp = goods_opt.split("|");
            goods_opt = tmp[0];
        }
    }

    function SetOptionValue(value, depth) {
        var ff = document.f1;
        if(depth == 1)
        {
            ff.goods_opt.value = value;
        }
        else if(depth == 2)
        {
            var goods_opt = ff.goods_opt1.value;
            var a_goods_opt = goods_opt.split("|");
            var opt1 = a_goods_opt[0];
            var opt = opt1 + "^" + value;
            goods_opt = goods_opt.replace(opt1, opt);
            ff.goods_opt.value = goods_opt.replace("|" + opt1 + "|", "|" + opt + "|");
        }

        CalAmt();
    }

    function PopSearchOrder() {
        const url='/shop/api/order?isld=Y';
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }

    function PopOrder(obj){
        openOrder2(obj.innerHTML);
    }

    function selectOrder(data) {
        const params = [
            `p_ord_opt_no=${data.ord_opt_no}`,
            `goods_no=${data.goods_no}`,
            `goods_sub=${data.goods_sub}`,
            `ord_state=${data.ord_state_cd}`,
            `ord_type=${data.ord_type_cd}`,
            `ord_kind=${data.ord_kind_cd}`,
            `sale_place=${data.sale_place}`
        ];
        SetOrder(data.ord_no,data.ord_opt_no);
    }

    function SetOrder(ord_no,ord_opt_no = ''){

        if(ord_opt_no == ''){
            url = '/shop/order/ord01/get/' + ord_no + '?fmt=json';
        } else {
            url = '/shop/order/ord01/get/' + ord_no + '/'  + ord_opt_no + '?fmt=json'
        }

        $.ajax({
            type: "get",
            url: url,
            contentType: "application/x-www-form-urlencoded; charset=utf-8",
            dataType: 'json',
            // data: {},
            success: function(res) {
                let ord = res.ord;
                let pay = res.pay;
                let ord_lists = res.ord_lists;

                $("#p_ord_no").html("<a href='javascript:void(0);' onclick='return PopOrder(this);'>" + res.ord_no + "</a>");
                $('#user_id').val(ord.user_id);
                $('#user_nm').val(ord.user_nm);
                $('#phone').val(ord.phone);
                $('#mobile').val(ord.mobile);
                $('#r_user_nm').val(ord.r_nm);
                $('#r_phone').val(ord.r_phone);
                $('#r_mobile').val(ord.r_mobile);
                $('#r_zip_code').val(ord.r_zipcode);
                $('#r_addr1').val(ord.r_addr1);
                $('#r_addr2').val(ord.r_addr2);
                $('#dlv_msg').val(ord.dlv_msg);
                $('#r_mobile').val(ord.r_mobile);
                $('#add_dlv_fee').val(ord.add_dlv_fee);
                $('#sale_place').val(ord.sale_place);
                $('#pay_type').val(pay.pay_type);
                // $('#bank_code').val(pay.bank_code);
                $('#bank_number').val(pay.bank_number);
                $('#sale_place').val(ord.com_id);
                $('#bank_inpnm').val(pay.bank_inpnm);
                $('#bank_code').val(pay.bank_code + '_' + pay.bank_number).prop("selected",true);


                if($('#ord_type_' +  ord.ord_type)){
                    $('#ord_type_' +  ord.ord_type).attr("checked", true);
                }

                $('input[name="ord_state"]').each(function() {
                    //console.log(this.name + '-' + this.value);
                    if(this.value == ord.ord_state){
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });
                // console.log(count($ord_lists));

                $('input[name="ord_kind"]').each(function() {
                    //console.log(this.name + '-' + this.value);
                    if(this.value == ord.ord_kind){
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });

                console.log(ord_lists);
                let goods_lists = [];
                for(i=0;i<ord_lists.length;i++){
                    if(ord_opt_no == "" || ord_opt_no == ord_lists[i]["ord_opt_no"]){

                        goods_no = ord_lists[i]["goods_no"];
                        ord_lists[i]["ord_amt"] = ord_lists[i]["price"] * ord_lists[i]["qty"];
                        goods_lists.push(ord_lists[i]);

                        $.ajax({
                            type: "get",
                            url: '/shop/product/prd01/' + goods_no + '/get',
                            contentType: "application/x-www-form-urlencoded; charset=utf-8",
                            dataType: 'json',
                            // data: {},
                            success: function (res) {
                                //console.log(res);
                                var options = [];
                                for (var j = 0; j < res.options.length; j++) {
                                    if (res.options[j].qty > 0) {
                                        options.push(res.options[j].goods_opt);
                                    }
                                }
                                _goods_options[goods_no] = options;
                            },

                            error: function (e) {
                                console.log(e.responseText);
                            }
                        });
                    }
                }

                //console.log(ord_lists);
                gx.gridOptions.api.setRowData(goods_lists);
            },
            error: function(e){
                console.log(e.responseText);
            }
        });
    }

    function cellOpionsParams(params){
        var goods_no = params.data.goods_no;
        var options = [];
        if(_goods_options.hasOwnProperty(goods_no)){
            options =  _goods_options[goods_no];
        } else {
        }
        return {
            values :options
        }
    }

    function openFindAddress(zipName, addName) {
        new daum.Postcode({
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다..
            oncomplete: function(data) {
                $("#" + zipName).val(data.zonecode);
                $("#" + addName).val(data.address);
            }
        }).open();
    }

    function getForm2JSON($form){
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function(n, i){
            indexed_array[n['name']] = n['value'];
        });
        return indexed_array;
    }

    function save() {

        var order_data = getForm2JSON($('form[name=f1]'));
        order_data["cart"] = gx.getRows();

        $.ajax({
            async: true,
            dataType: "json",
            type: 'post',
            url: "/shop/order/ord01/save",
            data: order_data,
            success: function (res) {
                console.log(res);
                is_processing = false;
                alert("저장되었습니다.");
                document.location.href = '/shop/order/ord01/order/' + res.ord_no;
            },
            error: function(e) {
                is_processing = false;
                console.log('[error] ' + e.responseText);
                var err = JSON.parse(e.responseText);
                if(err.hasOwnProperty("code") && err.code == "500"){
                    alert(err.msg);
                }
            },
        });
    }

    if ($('#goods_opt1').length > 0) {
        $('#goods_opt1').change(function(){
            SetOptionValue(this.value, 1);
            GetSecondOption('{$goods->goods_no}', '{$goods->goods_sub}', this.value, '{$option_cnt}');
            CalAmt();
        });
    }

</script>

@stop
