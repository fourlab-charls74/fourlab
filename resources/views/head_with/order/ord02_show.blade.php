@extends('head_with.layouts.layout-nav')
@section('title','수기판매')
@section('content')

<style>
    .none_goods div {
        font-weight: bold;
        background-color:#d2d2d2;
    }
</style>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">수기판매</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>수기판매</span>
            </div>
        </div>
        <div>
            <a href="javascript:Validate(document.f1);" class="btn btn-sm btn-primary submit-btn"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 판매등록</a>
            <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm" onclick="window.close()"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <!-- FAQ 세부 정보 -->
    <form name="f1">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">주문자 정보</a>
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
                    <a href="#">수령자 정보</a>
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
                                                <th class="required">배송비적용</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="dlv_apply" id="dlv_apply_y" value="Y" class="custom-control-input" checked onclick="setDlvFeeUse(true);"><label class="custom-control-label" for="dlv_apply_y">적용함</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="dlv_apply" id="dlv_apply_n" value="N" class="custom-control-input" onclick="setDlvFeeUse(false);"><label class="custom-control-label" for="dlv_apply_n">적용안함</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <th class="required">적립금지급</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio" onclick="CheckPoint(this);">
                                                            <input type="radio" name="give_point" id="give_point_y" value="Y" class="custom-control-input">
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
                    <a href="#">주문상태정보</a>
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
                                                <th class="required">주문상태</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="ord_state" id="ord_state_5" class="custom-control-input" value="1" />
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
                                                <th class="required">출고형태</th>
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
                                                <th class="required">판매업체</th>
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
                                                <th class="required">출고구분</th>
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
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <a href="#" class="m-0 font-weight-bold">상품정보</a>
                        </div>
                        <div class="fr_box">
                            <button type="button" id="add_goods" class="btn-sm btn btn-secondary sms-send-btn fs-12" onclick="return AddGoods();">상품추가</button>
                            <button type="button" class="btn-sm btn btn-secondary sms-list-btn fs-12" onclick="return DelGoods();">상품삭제</button>
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
                        <div id="div-gd" style="height:250px; width:100%;" class="ag-theme-balham"></div>
                    </div>

                    <div id="amt_table" class="row_wrap mt-2" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <colgroup>
                                            <col width="15%">
                                            <col width="35%">
                                            <col width="15%">
                                            <col width="35%">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <th>배송비합계</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box" style="justify-content:flex-end;">
                                                        <input type="text" id="dlv_amt" name="dlv_amt" class="form-control form-control-sm" placeholder="0" style="width:calc(40% - 10px);margin-right:10px;text-align: right;" readonly>
                                                        <span>원</span>
                                                    </div>
                                                </td>
                                                <th>주문액합계</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box" style="justify-content:flex-end;">
                                                        <input type="text" id="ord_amt" name="ord_amt" class="form-control form-control-sm" placeholder="0" style="width:calc(40% - 10px);margin-right:10px;text-align: right;" readonly>
                                                        <span>원</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>적립금사용</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box" style="justify-content:flex-end;">
                                                        <input type="text" id="point_amt" name="point_amt" class="form-control form-control-sm" placeholder="0" style="width:calc(40% - 10px);margin-right:10px;text-align: right;">
                                                        <span>원</span>
                                                    </div>
                                                </td>
                                                <th>총입금액</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box" style="justify-content:flex-end;">
                                                        <input type="text" id="recv_amt" name="recv_amt" class="form-control form-control-sm" placeholder="0" style="width:calc(40% - 10px);margin-right:10px;text-align: right;" readonly>
                                                        <span>원</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>공급가액</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box" style="justify-content:flex-end;">
                                                        <input type="text" id="supply_amt" name="supply_amt" class="form-control form-control-sm" placeholder="0" style="width:calc(40% - 10px);margin-right:10px;text-align: right;" readonly>
                                                        <span>원</span>
                                                    </div>
                                                </td>
                                                <th>세액</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box" style="justify-content:flex-end;">
                                                        <input type="text" id="vat_amt" name="vat_amt" class="form-control form-control-sm" placeholder="0" style="width:calc(40% - 10px);margin-right:10px;text-align: right;" readonly>
                                                        <span>원</span>
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
                    <a href="#">결제정보</a>
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
                                                <th class="required">결제수단</th>
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
                                                <th class="required">입금은행</th>
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
                                                <th class="required">입금자</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <input type="text" class="form-control form-control-sm" name="bank_number" id="bank_number" value=""/>
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
    </form>
    <div class="resul_btn_wrap mt-3 d-block">
        <a href="javascript:Validate(document.f1);" class="btn btn-sm btn-primary submit-btn"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 판매등록</a>
        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm" onclick="window.close()"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
    </div>
</div>
<!-- script -->
@include('head_with.order.ord02_js')
<!-- script -->
<script type="text/javascript">

    const columns = [
        {headerName: '', headerCheckboxSelection: true, width: 28,
            checkboxSelection: function(params) {
                if(params.data.goods_no) return true;
                return false;
            },
        },
        {field:"ord_opt_no" , headerName:"주문일련번호", width: 80, sortable:"ture", cellStyle: { 'text-align': 'center' },
            type: function(params) {
                if(params.data.goods_no) return 'HeadOrdOptNoType';
                return "none";
            }
        },
        {field: "goods_nm" ,headerName: "상품명",type:"HeadGoodsNameType", width: 300, wrapText: true, autoHeight: true},
        {field: "opt_val" ,headerName: "옵션", width: 200,
            editable: function(params) {if(params.data.goods_no) return true; return false;},
            cellClass: ['hd-grid-edit'],
            cellEditor: 'agRichSelectCellEditor',
            cellEditorParams: cellOpionsParams,
        },
        {field:"qty" , headerName:"수량", width: 60,type: 'numberType',
            editable: function(params) {
                if(params.data.goods_no) return true;
                return false;
            },
            cellClass:['hd-grid-number','hd-grid-edit'],onCellValueChanged:EditAmt,
            //cellStyle:StyleChangeYN,
        },
        {field:"price" , headerName:"판매가", width: 100, type: 'currencyType', editable: function(params) {if(params.data.goods_no) return true; return false;}, cellClass: ['hd-grid-number', 'hd-grid-edit'], onCellValueChanged: EditAmt },
        {field:"ord_amt" , headerName:"주문액", width: 100, type: 'currencyType'},
        // {field:"recv_amt" , headerName:"입금액", width:90, type: 'currencyType',onCellValueChanged:EditAmt },
        {field:"point_amt" , headerName:"(-)적립금", hide: true, width:90, type: 'currencyType',editable: function(params) {if(params.data.goods_no) return true; return false;}, onCellValueChanged:EditAmt },
        {field:"coupon_amt" , headerName:"(-)쿠폰", hide: true, width:90, type: 'currencyType',editable: function(params) {if(params.data.goods_no) return true; return false;}, onCellValueChanged:EditAmt },
        {field:"dc_amt" , headerName:"(-)할인", hide: true, width:90, type: 'currencyType',editable: function(params) {if(params.data.goods_no) return true; return false;}, onCellValueChanged:EditAmt },
        {field:"dlv_amt" , headerName:"(+)배송비", width: 100, type: 'currencyType',
            onCellValueChanged: (e) => setDlvFeeOfComType(e.newValue, e.data?.com_id || ''),
            editable: function(params) {if(params.data.goods_no) return false; return true;},
            cellStyle: function(params) { return {'background-color': params.data.goods_no ? 'none' : '#ffff99'} },
        },
        {width: "auto"}
    ];

    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;
    let _goods_options = {};
    let _goods_list_of_com_type = {etc:[]};

    const base_dlv_fee = parseInt('{{ @$dlv_fee[base_dlv_fee] }}');
    const add_dlv_fee = parseInt('{{ @$dlv_fee[add_dlv_fee] }}');
    const free_dlv_amt = parseInt('{{ @$dlv_fee[free_dlv_amt] }}');

    $(document).ready(function() {
        let gridDiv = document.querySelector(pApp.options.gridId);
        if (gridDiv !== null) {
            gx = new HDGrid(gridDiv, columns, {
                groupDisplayType: 'singleColumn',
                rowClassRules: {
                    'none_goods': function (params) {
                        return !params.data.goods_no;
                    },
                },
            });
        }
    });

    // gx.gridOptions.getRowNodeId = function(data) {
    //     return data.id;
    // }


</script>
@stop
