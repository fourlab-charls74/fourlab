@extends('shop_with.layouts.layout-nav')
@section('title', '수선관리 등록')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">수선관리 등록</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 고객/수선관리</span>
                <span>/ 수선관리</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="Save()" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 수선요청</a>
            <a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <style> 
        .table th {min-width: 120px;}
        .table td {width: 25%;}
        
        @media (max-width: 740px) {
            .table td {float: unset !important;width: 100% !important;}
        }
    </style>

    <div class="card_wrap aco_card_wrap">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">입력</a>
            </div>
            <div class="card-body">
                <form name="f1">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <th class="required">접수일자</th>
                                            <td>
                                                <div class="form-inline">
                                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control form-control-sm docs-date" id="receipt_date" name="edate" value="{{@$edate}}" autocomplete="off">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="docs-datepicker-container"></div>
                                                    </div>
                                                    <p class="fs-14"></p>
                                                </div>
                                            </td>
                                            <th class="required">접수매장</th>
                                            <td>
                                                <span id="store_no" name="store_no">{{@$user_store_nm}}</span>
                                            </td>
                                            <th class="required">접수구분</th>
                                            <td>
                                                <div class="flex_box">
                                                    <select id="as_type" name="as_type" class="form-control form-control-sm">
                                                        <option value="">선택</option>
                                                        <option value="1">매장접수(A/S)</option>
                                                        <option value="2">매장접수(불량)</option>
                                                        <option value="3">매장접수(심의)</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">고객아이디/고객명</th>
                                            <td>
                                                <div class="flex_box">
                                                    <input type="text" class="form-control form-control-sm search-enter" name='customer_no' id="customer_no" value="" style="width:41%;" readonly="readonly">
                                                    <span class="text_line mx-2">/</span>
                                                    <input type="text" style="width:41%;" class="form-control form-control-sm search-enter" name="customer" id="customer" value="">
                                                    <a href="#" onclick="getMember();"class="ml-2 btn btn-sm btn-outline-primary"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                </div>
                                            </td>
                                            <th class="required">핸드폰 번호</th>
                                            <td>
                                                <div class="flex_box">
                                                    <div class="form-inline mr-0 mr-sm-1" style="width:100%;">
                                                        <div class="form-inline-inner input_box" style="width:30%;">
                                                            <input type="text" id="phone1" name="mobile[]" class="form-control form-control-sm" maxlength="3" value="" onkeyup="onlynum(this)">
                                                        </div>
                                                        <span class="text_line">-</span>
                                                        <div class="form-inline-inner input_box" style="width:29%;">
                                                            <input type="text" id="phone2" name="mobile[]" class="form-control form-control-sm" maxlength="4" value="" onkeyup="onlynum(this)">
                                                        </div>
                                                        <span class="text_line">-</span>
                                                        <div class="form-inline-inner input_box" style="width:29%;">
                                                            <input type="text" id="phone3" name="mobile[]" class="form-control form-control-sm" maxlength="4" value="" onkeyup="onlynum(this)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <th class="required">주소</th>
                                            <td>
                                                <div class="input_box flex_box address_box">
                                                    <input type="text" id="zipcode" name="zipcode" class="form-control form-control-sm" value="" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
                                                    <input type="text" id="addr1" name="addr1" class="form-control form-control-sm" value="" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
                                                    <input type="text" id="addr2" name="addr2" class="form-control form-control-sm" value="" style="width:calc(25% - 10px);margin-right:10px;">
                                                    <a href="javascript:;" onclick="openFindAddress('zipcode', 'addr1')" class="btn btn-sm btn-primary shadow-sm fs-12" style="width:80px;"> <i class="fas fa-search fa-sm text-white-50"></i>검색</a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">바코드</th>
                                            <td>
                                                <div class="flex_box">
                                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='prd_cd' id="prd_cd" value='' style="width:87%">
                                                    <a href="#" class="btn btn-sm ml-2 btn-outline-primary sch-prdcd"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                </div>
                                            </td>
                                            <th class="required">수선 상품명</th>
                                            <td>
                                                <div class="flex_box">
                                                    <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
                                                </div>
                                            </td>
                                            <th class="required">컬러 / 사이즈</th>
                                            <td>
                                                <div class="flex_box">
                                                    <select name='color' id='color' class="form-control form-control-sm" style="width:54%">
														<option value=''>선택</option>
														@foreach ($colors as $color)
														    <option value='{{ $color->code_id }}'>{{ $color->code_id }} : {{ $color->code_val }}</option>
														@endforeach
													</select>
                                                    <span class="text_line mx-2">/</span>
                                                    <select name='size' id='size' class="form-control form-control-sm"  style="width:40%">
														<option value=''>선택</option>
														@foreach ($sizes as $size)
														    <option value='{{ $size->code_id }}'>{{ $size->code_val }} : {{ $size->code_val2 }}</option>
														@endforeach
													</select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">수량</th>
                                            <td>
                                                <div class="flex_box">
                                                    <input type='text' class="form-control form-control-sm" name='qty' id="qty" value='' onkeyup="onlynum(this)">
                                                </div>
                                            </td>
                                            <th class="required">수선 유료구분</th>
                                            <td>
                                                <div class="flex_box">
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="is_free" id="use_y" class="custom-control-input" value="Y"/>
                                                            <label class="custom-control-label" for="use_y">유료</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="is_free" id="use_n" class="custom-control-input" value="N" checked/>
                                                            <label class="custom-control-label" for="use_n">무료</label>
                                                        </div>
                                                    </div>
                                                    <div >
                                                        <input type='text' class="form-control form-control-sm" name='as_amt' id="as_amt" style="width:100%" placeholder="금액을 입력해주세요." value='' onkeyup="onlynum(this)">
                                                    </div>
                                                </div>
                                            </td>
                                            <th></th>
                                            <td>
                                                <!-- <div class="flex_box">
                                                    <select id="as_state" name="as_state" class="form-control form-control-sm">
                                                        <option value="">선택</option>
                                                        @foreach ($as_states as $as_state)
                                                            <option value="{{ $as_state->code_id }}">{{ $as_state->code_val }}</option>
                                                        @endforeach
                                                    </select>
                                                </div> -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="as_content">수선내용</label>    
                                            </th>
                                            <td colspan="5">
                                                <div class="flex_box">
                                                    <textarea name="content" id="as_content" class="form-control form-control-sm" style="height: 400px;"></textarea>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript" charset="utf-8">
    //
    // 고객아이디/ 고객명 / 핸드폰번호 콜백
    //

    var goodsCallback = (row) => {
        const { name } = row;
        const { user_id } = row;
        const { phone } = row;

        let num = phone.split('-');


        $('#customer').val(name);
        $('#customer_no').val(user_id);
        $('#phone1').val(num[0]);
        $('#phone2').val(num[1]);
        $('#phone3').val(num[2]);
    };

    const getMember = () => {
        const url=`/shop/api/members`;
        const pop_up = window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=800,height=800");
    };

    // 주소 api
    function openFindAddress(zipName, addName) {
        new daum.Postcode({
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다..
            oncomplete: function(data) {
                $("#" + zipName).val(data.zonecode);
                $("#" + addName).val(data.address);
            }
        }).open();
    }

    // 바코드 검색 클릭 이벤트 바인딩 및 콜백 사용
	$(".sch-prdcd").on("click", function() {
		searchPrdcd.Open(null, true);
    });

    //수선금액 무료/유료 여부에 따라 input box 출력


    $(document).ready(function() {
        $('#as_amt').hide();
    });

    
    $("input:radio[name=is_free]").click(function(){
            
        if($("input:radio[name=is_free]:checked").val()=='Y'){
            $("#as_amt").show();
        }else{
            $("#as_amt").hide();                    
        }
    });


    

    const validate = () => {
        if ($("#receipt_date").val() == "") {
            alert("접수일자를 선택해 주세요");
            return false;
        }

        if(!$("[name=store_no]").val()) {
            alert("접수매장을 선택해주세요.");
            return false;
        }

        if ($("#as_type").val() == "") {
            alert("접수구분을 선택해 주세요");
            return false;
        }

        if ($("#customer_no").val() == "") {
            alert("고객아이디/고객명을 입력해 주세요");
            return false;
        }

        if ($("#phone1").val() == "" || $("#phone2").val() == "" || $("#phone3").val() == "") {
            alert("핸드폰 번호를 입력해 주세요");
            return false;
        }

        if ($("#zipcode").val() == "") {
            alert("주소를 입력해 주세요");
            return false;
        }

        if ($("#prd_cd").val() == "") {
            alert("바코드를 입력해 주세요");
            return false;
        }

        if ($("#goods_nm").val() == "") {
            alert("수선 상품명을 입력해 주세요");
            return false;
        }

        if ($("#color").val() == "") {
            alert("컬러를 선택해 주세요");
            return false;
        }

        if ($("#size").val() == "") {
            alert("사이즈를 선택해 주세요");
            return false;
        }

        if ($("#qty").val() == "") {
            alert("수량을 입력해 주세요");
            return false;
        }

        if($("input:radio[name=is_free]:checked").val()=='Y'){
            if ($("#as_amt").val() == "") {
                alert("수선금액을 입력해 주세요");
                return false;
            }
        }

    }

    function Save() {
        if (validate() === false) return;
        if (confirm('수선정보를 등록하시겠습니까?') === false) return;

        axios({
            url: '/shop/standard/std11/repair-info-save',
            method: 'post',
            data: $('form[name="f1"]').serialize(),
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                window.close();
                opener.Search();
            } else {
                console.log(res.data);
                alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });



    }
        

</script>
@stop
