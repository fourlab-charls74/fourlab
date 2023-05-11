@extends('partner_with.layouts.layout-nav')
@section('title','상품일괄등록')
@section('content')

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">상품일괄등록</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품관리</span>
                <span>/ 상품일괄등록</span>
            </div>
        </div>
    </div>
    <form name="detail">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header">
                    <a href="#">일괄등록품목</a>
                </div>
                <div class="fr_box flax_box" style="position: absolute; right: 2%;">
                    <a href="#" class="btn-sm btn btn-primary mr-1 apply-btn">적용</a>
                    <a href="#" onclick="document.detail.reset()"  class="btn btn-sm btn-primary shadow-sm">취소</a>
                </div>
                <style>
                    .required:after {
                      content:" *"; color: red;
                    }
                </style>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" id="dataTable">
                                        <colgroup>
                                            <col width="15%">
                                            <col width="35%">
                                            <col width="15%">
                                            <col width="35%">
                                        </colgroup>
                                        <tr>
                                            <th>상품수</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="prd_cnt" id="prd_cnt" class="form-control form-control-sm search-all" style="width: 86%">&nbsp;개&nbsp;
                                                </div>
                                            </td>
                                            <th class="required">업체</th>
                                            <td>
                                                <div class="flax_box txt_box">{{$com_info->com_nm}}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">품목</th>
                                            <td>
                                                <div class="select_box">
                                                    <select name="op_cd" id="op_cd" class="form-control form-control-sm search-all">
                                                        <option value="">선택하세요.</option>
                                                        @foreach($opt_cd_list as $opt_cd)
                                                            <option value="{{$opt_cd->name}}">{{$opt_cd->value}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <th class="required">브랜드</th>
                                            <td>
                                                <div class="form-inline inline_btn_box">
                                                    <input type="text" class="form-control form-control-sm search-all" name="brand" id="brand_nm" value="" style="width: 100%">
                                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">대표카테고리</th>
                                            <td>
                                                <div class="form-inline inline_btn_box">
                                                    <input type="text" value="" name="rep_cat_nm" id="rep_cat_nm" class="form-control form-control-sm search-all" style="width: 100%"/>
                                                    <input type="hidden" value="" name="rep_cat_cd" id="rep_cat_cd"/>
                                                    <a href="#" class="btn btn-sm btn-outline-primary"
                                                        onclick="searchCategory.Open('DISPLAY',function(code,name){
                                                            $('#rep_cat_cd').val(code);
                                                            $('#rep_cat_nm').val(name);
                                                        });"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                </div>
                                            </td>
                                            <th>용도카테고리</th>
                                            <td>
                                                <div class="form-inline inline_btn_box">
                                                    <input type="text" value="" name="u_cat_nm" id="u_cat_nm" class="form-control form-control-sm search-all" style="width: 100%"/>
                                                    <input type="hidden" id="u_cat_cd" name="u_cat_cd" value=""/>
                                                    <a href="#" class="btn btn-sm btn-outline-primary"
                                                        onclick="searchCategory.Open('ITEM',function(code,name){
                                                            $('#u_cat_cd').val(code);
                                                            $('#u_cat_nm').val(name);
                                                        });"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">배송비</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="dlv_fee_cfg" value="S" onclick="change_dlv_cfg_form('s')" id="dlv_fee_cfg1" class="custom-control-input" checked="">
                                                        <label class="custom-control-label" for="dlv_fee_cfg1">쇼핑몰 설정</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mr-2">
                                                        <input type="radio" name="dlv_fee_cfg" value="G" onclick="change_dlv_cfg_form('g')" id="dlv_fee_cfg2" class="custom-control-input">
                                                        <label class="custom-control-label" for="dlv_fee_cfg2">상품 개별 설정</label>
                                                    </div>
                                                    <div class="dlv_config_detail_div txt_box" id="dlv_config_detail_s_div">
                                                        유료, 배송비 2,500원(50,000원 이상 구매 시 무료)
                                                    </div>
                                                    <div class="dlv_config_detail_div" id="dlv_config_detail_g_div" style="display:none;">
                                                        <div class="flax_box">
                                                            <div class="select_box mr-1">
                                                                <select name="bae_yn" class="form-control form-control-sm search-all">
                                                                    <option value="Y" selected>유료</option>
                                                                    <option value="N">무료</option>
                                                                </select>
                                                            </div>
                                                            <div class="input_box"><input type="text" name="baesong_price" id="baesong_price" class="form-control form-control-sm search-all" style="width:100px;text-align:right;"></div>
                                                            <div class="txt_box">원</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <th class="required">배송비 지불</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="dlv_pay_type" value="P" id="dlv_pay_type1" class="custom-control-input" checked="">
                                                        <label class="custom-control-label" for="dlv_pay_type1">선불</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mr-2">
                                                        <input type="radio" name="dlv_pay_type" value="F" id="dlv_pay_type2" class="custom-control-input">
                                                        <label class="custom-control-label" for="dlv_pay_type2">착불</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">적립금</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="point_cfg" value="S" onclick="change_point_cfg_form('s')" id="point_cfg1" class="custom-control-input" checked="">
                                                        <label class="custom-control-label" for="point_cfg1">쇼핑몰 설정</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mr-2">
                                                        <input type="radio" name="point_cfg" value="G" onclick="change_point_cfg_form('g')" id="point_cfg2" class="custom-control-input">
                                                        <label class="custom-control-label" for="point_cfg2">상품 개별 설정</label>
                                                    </div>
                                                    <div class="point_config_detail_div txt_box" id="point_config_detail_s_div">
                                                        (지급함, 상품 가격의 {{$point_info->value}}% 적립금 지급)
                                                    </div>
                                                    <div class="point_config_detail_div" id="point_config_detail_g_div" style="display:none;">
                                                        <div class="flax_box">
                                                            <div class="select_box mr-1">
                                                                <select name="point_yn" class="form-control form-control-sm search-all">
                                                                    <option value="Y" selected>지급함</option>
                                                                    <option value="N">지급안함</option>
                                                                </select>
                                                            </div>
                                                            <div class="input_box"><input type="text" name="point" id="point" class="form-control form-control-sm search-all" style="width:100px;text-align:right;"></div>
                                                            <div class="txt_box">원</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <th class="required">과세구분</th>
                                            <td>
                                                <div class="flax_box select_box">
                                                    <select name="tax_yn" class=" form-control form-control-sm search-all">
                                                        <option value="Y" selected>과세</option>
                                                        <option value="N">면세</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">원산지</th>
                                            <td>
                                                <div class="input_box">
                                                    <input type="text" name="org_nm" id="org_nm" class="form-control form-control-sm search-all" />
                                                </div>
                                            </td>
                                            <th>제조사</th>
                                            <td>
                                                <div class="input_box">
                                                    <input type="text" name="make" id="make" class="form-control form-control-sm search-all" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">옵션사용</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="is_option_use" value="Y" id="is_option_use1" class="custom-control-input" checked="">
                                                        <label class="custom-control-label" for="is_option_use1">사용함</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mr-2">
                                                        <input type="radio" name="is_option_use" value="N" id="is_option_use2" class="custom-control-input">
                                                        <label class="custom-control-label" for="is_option_use2">사용안함</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <th class="required">옵션구분</th>
                                            <td>
                                                <div class="form-inline inline_input_box flax_box">
                                                    <div class="form-inline-inner text-box" style="margin-bottom:5px">
                                                        <div class="form-group flax_box">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="chk_option_kind1" id="chk_option_kind1" class="custom-control-input" checked>
                                                                <label class="custom-control-label" for="chk_option_kind1">&nbsp;</label>
                                                            </div>
                                                            <input type="text" class="form-control form-control-sm" name="option_kind1" style="width:85%;" placeholder="사이즈" />
                                                        </div>
                                                    </div>
                                                    <div class="form-inline-inner text-box">
                                                        <div class="form-group flax_box">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="chk_option_kind2" id="chk_option_kind2" class="custom-control-input" >
                                                                <label class="custom-control-label" for="chk_option_kind2">&nbsp;</label>
                                                            </div>
                                                            <input type="text" class="form-control form-control-sm" name="option_kind2" style="width:85%;" placeholder="컬러" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">재고수량관리</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="is_unlimited" value="P" id="is_unlimited1" class="custom-control-input" checked="">
                                                        <label class="custom-control-label" for="is_unlimited1">수량 관리함</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mr-2">
                                                        <input type="radio" name="is_unlimited" value="F" id="is_unlimited2" class="custom-control-input">
                                                        <label class="custom-control-label" for="is_unlimited2">수량 관리 안함(무한재고)</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <th>재입고 알림</th>
                                            <td>
                                                <div class="flax_box select_box">
                                                    <select name="restock_yn" class=" form-control form-control-sm search-all">
                                                        <option value="Y" selected>재입고함</option>
                                                        <option value="N">안함</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="show_layout px-sm-3">
    <div class="card shadow">
        <form method="post" name="save" id ="insert_form" action="/partner/stock/stk01">
            @csrf
            <textarea style="display:none" name="form_str" id="csvResult"></textarea>
            <div class="card-body shadow">
                <div class="card-title">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                        <div class="fr_box">
                            <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Cmder('save')">저장</a>
                            <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Cmder('del');">삭제</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="width:100%;min-height:400px;" class="ag-theme-balham"></div>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- <style>
    #toc-content {
    display: none;
    }
    #toc-toggle {
    cursor: pointer;
    }
    #toc-toggle:hover {
    text-decoration: underline;
    }
</style>
<div class="card-title">
    <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle" id="toc-toggle" onclick="openCloseToc()"> Help</h6>
    <h3>클릭해 주세요</h3>
</div>
<ul id="toc-content">
<li>취소 클릭시 상품 및 일괄 등록 폼이 초기화 됩니다.</li> 
<li>수정할수 있는 항목: 스타일넘버, 상품명, 판매가, 홍보글/단축명, 상품상세, 제품사양, 예약/배송 (등록하려는 엑셀 파일 필드 Ctrl+C, Ctrl+V)</li> 
<li>저장 클릭시 상품 선택(체크박스 체크)과 무관하게 일괄 등록 됩니다.</li> 
<li>옵션 등록 도움말</li> 
<li>단일 옵션 입력 시 "옵션구분" 입력란에 사이즈 또는 size 과 같이 옵션구분명을 입력합니다. "옵션1" 모두 필수 입력.</li> 
<li>멀티 옵션 입력 시 "옵션구분" 입력란에 사이즈^컬러 또는 size^color과 같이 공백 없이 "^"로 연결하여 옵션구분명을 입력합니다. "옵션1", "옵션2" 모두 필수 입력.</li> 
<li>단일 옵션 입력 시 : S,M,L 또는 검정,파랑,노랑,초록 과 같이 공백 없이 쉼표(,)로 연결하여 "옵션1" 항목에 입력합니다.</li> 
<li>멀티 옵션 입력 시 : "옵션1" 항목에 검정,파랑,노랑,초록, "옵션2" 항목에 S,M,L 와 같이 공백 없이 쉼표(,)로 연결하여 입력합니다.</li>
<li>"옵션1", "옵션2" 항목에 입력된 옵션은 "검정^S","검정^M","검정^L","파랑^S", .. 와 같은 형태로 옵션이 등록되며, 쇼핑몰에서는 멀티옵션으로 표시됩니다.</li> 
<li>수량 입력 시 : 100,200,300 또는 0,0,300과 같이 공백 없이 쉼표(,)로 연결하여 "수량" 항목에 입력합니다. "옵션1" 항목을 기준으로 적용되므로 "옵션1" 항목의 갯수와 "수량" 항목의 갯수는 같아야 합니다. </li>
<li>옵션 가격 입력 시 : 100,200,300 또는 0,0,300과 같이 공백 없이 쉼표(,)로 연결하여 "옵션가격" 항목에 입력합니다. "옵션1" 항목을 기준으로 적용되므로 "옵션1" 항목의 갯수와 "옵션가격" 항목의 갯수는 같아야 합니다.</li> 
<li>옵션등록 샘플</li> 
<li>사이즈 또는 컬러 선택시 single_option.jpg</li> 
<li>컬러/사이즈 선택시 multi_option.jpg</li> 
</ul> --}}

    <script language="javascript">

        var columns = [
            {
                headerName: '#',pinned:'left',
                width:50,
                maxWidth: 100,
                // it is important to have node.id here, so that when the id changes (which happens
                // when the row is loaded) then the cell is refreshed.
                valueGetter: 'node.id',
                cellRenderer: 'loadingRenderer',
            },
            {field:"com_nm" , headerName:"업체", pinned:'left',},
            {field:"op_cd" , headerName:"품목", pinned:'left',},
            {field:"brand" , headerName:"브랜드", pinned:'left',},
            {field:"rep_cat_cd" , headerName:"대표카테고리코드", pinned:'left',},
            {field:"u_cat_cd" , headerName:"용도카테고리코드", pinned:'left',},
            {field:"rep_cat_nm" , headerName:"대표카테고리", pinned:'left',},
            {field:"u_cat_nm" , headerName:"용도카테고리", pinned:'left',},
            {field:"style_no" , headerName:"스타일넘버", pinned:'left', editable: true, },
            {field:"goods_nm" , headerName:"상품명", type:"GoodsNameType", pinned:'left', editable: true, },
            {field:"goods_nm_eng" , headerName:"상품 영문명", editable: true,   },
            {field:"price" , headerName:"판매가", type: 'currencyType', editable: true, },
            {field:"margin_rate" , headerName:"마진율", },
            {field:"wonga" , headerName:"원가", type: 'currencyType', editable: true,  },
            {field:"chk_option_kind1" , headerName:"옵션구분", editable: true, },
            {field:"option_kind1" , headerName:"옵션1", editable: true,   },
            {field:"chk_option_kind2" , headerName:"옵션구분",  editable: true,   },
            {field:"option_kind2" , headerName:"옵션2",     },
            {field:"qty" , headerName:"수량", editable: true,   },
            {field:"opt_price" , headerName:"옵션가격", type: 'currencyType', editable: true,   },
            {field:"head_desc" , headerName:"상단홍보글", },
            {field:"ad_desc" , headerName:"하단홍보글", },
            {field:"baesong_info" , headerName:"배송방법", },
            {field:"baesong_kind" , headerName:"배송처리", },
            {field:"dlv_pay_type" , headerName:"배송비지불", },
            {field:"dlv_fee_cfg" , headerName:"배송비설정", },
            {field:"bae_yn" , headerName:"배송비여부", },
            {field:"baesong_price" , headerName:"배송비", },
            {field:"point_cfg" , headerName:"적립금설정", },
            {field:"point_yn" , headerName:"적립금여부", },
            {field:"point_rate" , headerName:"적립율", },
            {field:"point" , headerName:"적립금", },
            {field:"org_nm" , headerName:"원산지", },
            {field:"md_nm" , headerName:"md_nm", },
            {field:"md_id" , headerName:"md_id", },
            {field:"make" , headerName:"제조사", },
            {field:"goods_cont" , headerName:"상품상세", editable: true,   },
            {field:"spec_desc" , headerName:"제품사양", editable: true,   },
            {field:"baesong_desc" , headerName:"예약/배송", editable: true,   },
            {field:"opinion" , headerName:"MD상품평", editable: true,   },
            {field:"restock_yn" , headerName:"재입고알림", },
            {field:"tax_yn" , headerName:"과세구분", },
        ];

        const pApp = new App('', {
            gridId: "#div-gd",
        });
        const gridDiv = document.querySelector(pApp.options.gridId);
        let gx;

        $(document).ready(function() {
            gx = new HDGrid(gridDiv, columns);
            pApp.ResizeGrid(250);
            //Search();
        });

        function apply(){
            var check = true;
            if($("#op_cd option:selected").val() == ""){
                check = false;
                alert("품목을 선택하세요.");
                $("#op_cd").focus();
            }else if($("#prd_cnt").val() == "" || $("#prd_cnt").val() < 0 ){
                check = false;
                alert("상품 수를 입력해주세요.");
                $("#prd_cnt").focus();
            }else if($("#brand_nm").val() == ""){
                check = false;
                alert("브랜드를 선택해주세요.");
                $("#brand_nm").focus();
            // }else if($("#rep_cat_cd").val() == ""){
            //     check = false;
            //     alert("카테고리를 선택해주세요.");
            //     $("#rep_cat_nm").focus();
            }

            if(check){
                var goods_cnt = $("#prd_cnt").val();
                var goods = [];
                var form_arr = $("#f1").serializeArray();

                for (i = 0; i < goods_cnt; i++) {
                    let obj = {};
                    for(j=0; j < form_arr.length; j++){
                        obj[form_arr[j].name] = form_arr[j].value;
                    }
                    goods.push(obj);
                    console.log(obj);
                }
                $("#goods-total").text(goods.length);
                gx.gridOptions.api.setRowData(goods);
            }
        }

        function insert(){
            $('#csvResult').val(function(){
                return gx.gridOptions.api.getDataAsCsv(
                    {
                        suppressQuotes: "none",
                        columnSeparator: ",",
                        customHeader: "",
                        customFooter: "",
                    }
                );
            });

            $.ajax({
                async: true,
                type: 'post',
                url: '/partner/product/prd06/bundle/',
                data: $("#insert_form").serialize(),
                success: function (data) {

                    //console.log(data);

                    alert("상품이 등록되었습니다.");
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        }

        function change_dlv_cfg_form(value){
            $(".dlv_config_detail_div").css("display","none");
            $("#dlv_config_detail_"+value+"_div").css("display","inline");
        }
        function change_point_cfg_form(value){
            $(".point_config_detail_div").css("display","none");
            $("#point_config_detail_"+value+"_div").css("display","inline");
        }

        function Cmder(cmd){

            if(cmd =="del"){
                Delete();
            } else if(cmd =="save"){
                if(validate()){
                    Save();
                }
            }
            } 


        // function openCloseToc() {
        //     if(document.getElementById('toc-content').style.display === 'block') {
        //     document.getElementById('toc-content').style.display = 'none';
        //     document.getElementById('toc-toggle').textContent = 'Help';
        //     } else {
        //     document.getElementById('toc-content').style.display = 'block';
        //     document.getElementById('toc-toggle').textContent = 'Help';
        //     }
        // }

    </script>
@stop
