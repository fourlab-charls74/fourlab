@extends('head_skote.layouts.master-without-nav')
@section('title','수기판매')
@section('content')
<div class="container-fluid show_layout py-3">
    <form name="f1">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800">수기판매</h1>
            <div>
                <input type="button" value=" 판매수정" class="btn btn-sm btn-primary shadow-sm mr-1 save-btn" onclick="Validate(document.f1);" />
                <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
            </div>
        </div>
        <div class="card_wrap mb-3 search_cum_form">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <h5 class="m-0 font-weight-bold">주문자 정보</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 inner-td">
                            <div class="form-group">
                                <label for="">아이디 : </label>
                                <div class="form-inline">
                                    <input type="text" name="user_id" value="" class="form-control form-control-sm">
                                    <a href="#" onclick="GetUserInfo(document.f1.user_id.value)" class="btn btn-sm btn-primary shadow-sm mr-1">회원정보 불러오기</a>
                                    <a href="#" onclick="SameInfo();" class="btn btn-sm btn-primary shadow-sm mr-1">주문자와 동일</a>
                                    <a href="#" onclick="PopSearchOrder();" class="btn btn-sm btn-primary shadow-sm">기존 주문정보 불러오기</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">이름 : </label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm" name="user_nm" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">전화 : </label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm" name="phone" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">휴대전화 : </label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm" name="mobile" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card_wrap mb-3 search_cum_form">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <h5 class="m-0 font-weight-bold">수령자 정보</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">이름 : </label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm" name="r_nm" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">전화 : </label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm" name="r_phone" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">휴대전화 : </label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm" name="r_mobile" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 inner-td">
                            <div class="form-group">
                                <label for="">주소 : </label>
                                <div class="form-inline inline_input_box">
                                    <input type="text" name="r_zip_code" id="r_zip_code" readonly value="" class="form-control form-control-sm">
                                    <input type="text" name="r_addr1" id="r_addr1" readonly value="" class="form-control form-control-sm">
                                    <input type="text" name="r_addr2" id="r_addr2" value="" class="form-control form-control-sm">
                                    <a href="#" onclick="openFindAddress('r_zip_code', 'r_addr1')" class="btn btn-sm btn-primary shadow-sm">
                                        <i class="fas fa-search fa-sm text-white-50"></i>
                                        우편번호 찾기
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 inner-td">
                            <div class="form-group">
                                <label for="">출고메시지 : </label>
                                <div class="flax_box">
                                    <textarea name="dlv_msg" id="dlv_msg" class="form-control form-control-sm" cols="0" rows="0" value=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">추가배송비 : </label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm text-right" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">배송비적용 : </label>
                                <div>
                                    <input type="radio" name="dlv_apply" id="dlv_apply_y" value="Y" onclick="CheckPoint(this);" checked>
                                    <label for="dlv_apply_y">적용함</label>
                                    <input type="radio" name="dlv_apply" id="dlv_apply_n" value="N">
                                    <label for="dlv_apply_n">적용안함</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">적립금지급 : </label>
                                <div>
                                    <input type="radio" name="give_point" id="give_point_y" @if(true) checked @endif>
                                    <label for="give_point_y">지급함</label>
                                    <input type="radio" name="give_point" id="give_point_n" @if(true) checked @endif>
                                    <label for="give_point_n">지급안함</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card_wrap mb-3 search_cum_form">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <h5 class="m-0 font-weight-bold">주문상태 정보</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 inner-td">
                            <div class="form-group">
                                <label for="">주문상태 : </label>
                                <div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="delivery_info" style="display:;">
                        <div class="col-lg-12 inner-td">
                            <div class="form-group">
                                <label for="">배송정보 : </label>
                                <div class="form-inline inline_input_box">
                                    <select name="dlv_cd" id="dlv_cd" class="form-control form-control-sm">
                                        <option value=''>택배업체</option>
                                    </select>
                                    <input type="text" name="dlv_no" class="form-control form-control-sm" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 inner-td">
                            <div class="form-group">
                                <label for="">출고형태 : </label>
                                <div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="">판매업체 : </label>
                                <div class="flax_box">
                                    <select name="sale_place" id="sale_place" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="">출고구분 : </label>
                                <div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card_wrap mb-3">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <h5 class="m-0 font-weight-bold">주문자 정보</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    </div>
                </div>
            </div>
        </div>
        <div class="card_wrap mb-3 search_cum_form">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <h5 class="m-0 font-weight-bold">결제 정보</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">결제수단 : </label>
                                <div class="flax_box">
                                    <select name="pay_type" id="pay_type" class="form-control form-control-sm">
                                        <option value="">==결제방법==</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">입금은행 : </label>
                                <div class="flax_box">
                                    <select name="bank_code" id="bank_code" class="form-control form-control-sm">
                                        <option value="">선택하세요.</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">입금자 : </label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm" name="bank_inpnm" value="">
                                    <input type="hidden" name="coupon_no" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 inner-td">
                            <div class="form-group">
                                <label for="">쿠폰 : </label>
                                <a href="#"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
        </div>
    </form>
</div>

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    let is_processing = false;

    const Validate = (ff) => {
        if (is_processing) {
            alert("잠시만 기다려 주십시오. 지금 등록중입니다.");
            return;
        }

        const ord_state = $("[name=ord_state]:checked");

        if (ord_state.length === 0) {
            alert('주문상태를 선택해 주십시오.');
            return false;
        }

        if (ord_state.val() == 30) {
            if (ff.dlv_cd.value == "") {
                alert("택배사를 선택하십시오.");
                ff.dlv_cd.focus();
                return false;
            }
            if (ff.dlv_cd.value == "") {
                alert("택배 송장번호를 입력하십시오.");
                ff.dlv_cd.select();
                ff.dlv_cd.focus();
                return false;
            }
        }
        const ord_type = $('[name=ord_type]:checked');

        if (ord_type.length === 0) {
            alert('출고형태를 선택해 주십시오.');
            return false;
        }

        if (ff.sale_place.value == "") {
            alert('판매업체를 선택해 주십시오.');
            ff.sale_place.focus();
            return false;
        }
        const ord_kind = $('[name=ord_kind]:checked');

        if (ord_kind.length === 0) {
            alert('출고구분을 선택해 주십시오.');
            return false;
        }

        if (ff.goods_nm_disp.value == "") {
            alert("수기판매등록하실 상품을 검색해 주십시오.");
            return false;
        }

        if (ff.style_no.value == "") {
            alert("스타일넘버를 입력해 주십시오.");
            return false;
        }

        if (ff.goods_no.value == "") {
            alert("상품코드를 입력해 주십시오.");
            return false;
        }
        if (ff.goods_sub.value == "") {
            alert("상품코드를 입력해 주십시오.");
            return false;
        }

        if (ff.goods_nm.value == "") {
            alert("수기판매등록하실 상품을 선택해 주십시오.");
            return false;
        }

        // 옵션 체크
        if (ff.goods_opt1 && ff.goods_opt1.value == "") {
            alert("[" + ff.goods_nm.value + '] 상품의 첫번째 옵션을 선택해 주십시오.');
            ff.goods_opt1.focus();
            return false;
        }

        if (ff.goods_opt2 && ff.goods_opt2.value == "") {
            alert("[" + ff.goods_nm.value + '] 상품의 두번째 옵션을 선택해 주십시오.');
            ff.goods_opt2.focus();
            return false;
        }

        if (ff.goods_opt.value == "") {
            alert("[" + ff.goods_nm.value + '] 상품의 옵션을 선택해 주십시오.');
            ff.goods_opt.focus();
            return false;
        }

        // 추가옵션 체크
        add_opt_cnt = ff.addopt_cnt.value;

        for (var i = 1; i <= add_opt_cnt; i++) {
            var addopt_obj = ff["addopt" + i];

            if (addopt_obj && addopt_obj.value == "" && ff["addopt_required_yn" + i].value == "Y") {
                alert("[" + ff.goods_nm.value + '] 상품의 추가옵션을 선택해 주십시오.');
                addopt_obj.focus();
                return false;
            }
        }

        if (ff.pay_type.value == "") {
            alert("결제수단을 선택해 주십시오.");
            ff.pay_type.focus();
            return false;
        }

        if (ff.pay_type.value == "1" || ff.pay_type.value == "5" || ff.pay_type.value == "9" || ff.pay_type.value == "13") {
            if (ff.bank_inpnm.value == "") {
                alert("입금자를 입력해 주십시오.");
                ff.bank_inpnm.focus();
                return false;
            }

            if (ff.bank_code.value == "") {
                alert("입금은행을 선택해 주십시오.");
                ff.bank_code.focus();
                return false;
            }
        }

        if (ff.pay_type.value == "9" || ff.pay_type.value == "13") {
            if (ff.coupon_no.value == "") {
                alert("쿠폰번호를 입력해 주십시오.");
                ff.coupon_no.focus();
                return false;
            }
        }

        if (confirm("수기판매로 등록하시면 더이상 수정하실 수 없습니다.\n등록 하시겠습니까?")) {
            if (!is_processing) {
                ff.cmd.value = "add";
                ff.target = "";
                is_processing = true;
                save();
            }
        }
        return;
    }

    $('[name=ord_state]').change(function() {
        $('#delivery_info').css('display', this.value == '30' ? 'block' : 'none');
    });

    // 상품 옵션변경(옵션 가격)에 따른 판매가 초기화
    const CheckOptPrice = (goods_opt) => {
        if (goods_opt == "") {
            $("price").value = $("goods_price").value;
        } else {
            var ff = document.f1;
            var opt_price = 0;

            //옵션 가격 처리
            m_val = goods_opt.split("|");
            opt_price = (m_val[9] > 0) ? unComma(m_val[9]) : 0;

            // 추가옵션 가격
            var add_opt_cnt = 0;
            if (ff.addopt_cnt) {
                add_opt_cnt = ff.addopt_cnt.value;
            }

            addopt = "";
            for (var i = 1; i <= add_opt_cnt; i++) {
                var addopt_obj = ff["addopt" + i];
                if (addopt_obj.value) {
                    addopt += (addopt != "") ? "^" : "";
                    addopt += addopt_obj.value;
                }
            }

            // 추가옵션 포맷 : 옵션값|상품번호|상품하위번호|추가옵션가격|추가옵션일련번호
            var addopt_price = 0;
            if (addopt) {
                is_multi = addopt.indexOf("^");
                is_price = addopt.indexOf("|");
                if (is_price > -1) {
                    if (is_multi > -1) {
                        tmp_addopt = addopt.split("^");
                        for (i = 0; i < tmp_addopt.length; i++) {
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

    function CalAmt() {
        return;
        var ff = document.f1;
        var qty = ff.qty.value;
        var price = (ff.price.value != "") ? unComma(ff.price.value) : 0;
        var goods_price = (ff.goods_price.value != "") ? unComma(ff.goods_price.value) : 0;
        var goods_opt = ff.goods_opt.value;

        //옵션 가격 처리
        m_val = goods_opt.split("|");
        opt_price = (m_val[9] > 0) ? unComma(m_val[9]) : 0;

        // 추가옵션
        if (ff.addopt_cnt) {
            add_opt_cnt = ff.addopt_cnt.value;
        } else {
            add_opt_cnt = 0;
        }

        addopt = "";
        for (var i = 1; i <= add_opt_cnt; i++) {
            var addopt_obj = ff["addopt" + i];

            if (addopt_obj.value) {
                addopt += (addopt != "") ? "^" : "";
                addopt += addopt_obj.value;
            } else {}
        }

        // 추가옵션 포맷 : 옵션값|상품번호|상품하위번호|추가옵션가격|추가옵션일련번호
        var addopt_price = 0;
        if (addopt) {
            is_multi = addopt.indexOf("^");
            is_price = addopt.indexOf("|");
            if (is_price > -1) {
                if (is_multi > -1) {
                    tmp_addopt = addopt.split("^");
                    for (i = 0; i < tmp_addopt.length; i++) {
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
        if ((opt_price + addopt_price) == 0) {
            $("price").className = "input";
            $("price").readOnly = false;
        } else {
            $("price").className = "input-disable";
            $("price").readOnly = true;
            // 가격에 옵션금액 포함
            $("price").value = numberFormat(goods_price + opt_price + addopt_price);
        }

        price = (ff.price.value != "") ? unComma(ff.price.value) : 0;
        var point = (ff.point_amt.value != "") ? unComma(ff.point_amt.value) : 0;
        var coupon = (ff.coupon_amt.value != "") ? unComma(ff.coupon_amt.value) : 0;
        var pay_fee = (ff.pay_fee.value != "") ? unComma(ff.pay_fee.value) : 0;
        var dc = (ff.dc_amt.value != "") ? unComma(ff.dc_amt.value) : 0;
        var dlv_amt = (ff.dlv_amt.value != "") ? unComma(ff.dlv_amt.value) : 0;
        var add_dlv_fee = unComma(add_dlv_fee);

        var dlv_apply = $('[name=dlv_apply]:checked').val();

        var ord_amt = price * qty;

        // 도매회원 배송비 무료
        if (ff.group_type.value == "WS" && $("#group_apply_y")[0].checked) {
            free_dlv_fee_limit = wholesale_free_dlv_fee_limit;
        } else {
            free_dlv_fee_limit = base_free_dlv_fee_limit;
        }

        if (dlv_apply == "Y") {
            if (ord_amt < free_dlv_fee_limit) {
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
    // function SetSaleInfo(ord_no,ord_opt_no) {
    //     var param = "CMD=sale_info";
    //     param += "&INFO_ORD_NO=" + ord_no;
    //     param += "&INFO_ORD_OPT_NO=" + ord_opt_no;
    //     param += "&UID=" + Math.random();

    //     var http = new xmlHttp();
    //     http.onexec("ord20_detail.php","POST",param,true,cbSetSaleInfo);
    // }

    // function cbSetSaleInfo(res){
    //     var object = Dom2Obj(res);
    //     if(object["record"]){
    //         var o = object["record"][0];
    //         $("USER_ID").value = o.user_id;
    //         $("USER_NM").value = o.user_nm;
    //         $("PHONE").value = o.phone;
    //         $("MOBILE").value = o.mobile;
    //         $("EMAIL").value = o.email;
    //         $("R_NM").value = o.r_nm;
    //         $("R_PHONE").value = o.r_phone;
    //         $("R_MOBILE").value = o.r_mobile;
    //         $("R_ZIP_CODE").value = o.r_zipcode;
    //         $("R_ZIP_CODE").onchange();
    //         $("R_ADDR1").value = o.r_addr1;
    //         $("R_ADDR2").value = o.r_addr2;
    //         $("DLV_MSG").value = o.dlv_msg;
    //     }
    // }

    function SetGoods(goods_no, goods_nm, styleno, opt_kind_cd, brand, brand_nm, goods_nm_eng, com_id) {

        var ff = document.f1;

        var p_ord_opt_no = ff.p_ord_opt_no.value;

        var ord_state = $('[name=ord_state]:checked').val();
        if (!ord_state) ord_state = "";

        var ord_type = $('[name=ord_type]:checked').val();
        if (!ord_type) ord_type = "";

        var ord_kind = $('[name=ord_kind]:checked').val();
        if (!ord_kind) ord_kind = "";

        var sale_place = ff.sale_place.value;

        var url = "?goods_no=" + goods_no + "&style_no=" + styleno + "&goods_nm=" + goods_nm;
        url += "&p_ord_opt_no=" + p_ord_opt_no + "&ord_kind=" + ord_kind + "&ord_type=" + ord_type + "&ord_state=" + ord_state + "&sale_place=" + sale_place;

        document.location.href = url;
    }

    function GetUserInfo(id) {
        alert("작동하지 않는 기능입니다.");
        return;
        if (id == "") {
            alert("아이디를 입력해 주십시오.");
        } else {
            var param = "cmd=user_info";
            param += "&user_id=" + id;
            param += "&uid=" + Math.random();
            // var http = new xmlHttp();
            // http.onexec("ord20_detail.php","POST",param,true,cbGetUserInfo);
        }
    }

    //유저 정보 가져올때 콜백함수
    function cbGetUserInfo(res) {
        // var object = Dom2Obj(res);
        // if(object["record"]){
        //     var o = object["record"][0];
        //     $("user_id").value = o.user_id;
        //     $("USER_NM").value = o.name;
        //     $("PHONE").value = o.phone;
        //     $("MOBILE").value = o.mobile;
        //     $("EMAIL").value = o.email;
        //     $("R_NM").value = o.name;
        //     $("R_PHONE").value = o.phone;
        //     $("R_MOBILE").value = o.mobile;
        //     $("R_ZIP_CODE").value = o.zip;
        //     $("R_ZIP_CODE").onchange();
        //     $("R_ADDR1").value = o.addr;
        //     $("R_ADDR2").value = o.addr2;

        //     // 회원인 경우 적립금 지급함	선택
        //     document.f1.give_point[0].checked = true;

        //     if( o.group_nm != "" ){
        //         $("user_group").style.display = "";
        //     } else {
        //         $("DC_AMT").value = 0;
        //         document.f1.GROUP_APPLY[1].checked = true;
        //         $("user_group").style.display = "none";

        //         CalAmt();
        //     }
        // }
    }

    function SameInfo() {
        f1.r_nm.value = f1.user_nm.value;
        f1.r_phone.value = f1.phone.value;
        f1.r_mobile.value = f1.mobile.value;
    }

    function CheckAddDlvArea(obj) {
        // var param = "CMD=check_add_dlv_area";
        // param += "&ZIPCODE=" + obj.value;
        // var http = new xmlHttp();
        // http.onexec("ord20_detail.php","POST",param,true,cbCheckAddDlvArea);
    }

    function cbCheckAddDlvArea(res) {
        // var add_dlv_fee = res.responseText;
        // var ff = document.f1;

        // ff.ADD_DLV_FEE.value = add_dlv_fee;
        // com(ff.ADD_DLV_FEE);

        // CalAmt();
    }

    function CheckPoint(obj) {
        var ff = document.f1;
        if (obj.checked) {
            if (ff.user_id.value == "") {
                alert("회원에게만 포인트를 지급할 수 있습니다.");
                ff.give_point[1].checked = true;
                return false;
            }
        }

        // var param = "CMD=check_point_user";
        // param += "&USER_ID=" + ff.USER_ID.value;
        // var http = new xmlHttp();
        // http.onexec("ord05_detail.php","POST",param,true,cbCheckPoint);
    }

    function cbCheckPoint(res) {
        var ff = document.f1;
        var result = res.responseText;
        if (result != 1) {
            ff.give_point[1].checked = true;
            alert("회원에게만 포인트를 지급할 수 있습니다.");
        }
    }

    function ApplyGroup(obj) {
        if (obj.checked) {
            var ff = document.f1;
            var group_type = ff.group_type.value;
            if (obj.value == "Y") {
                if (group_type == "DC") {
                    var price = (ff.price.value != "") ? unComma(ff.price.value) : 0;
                    var dc_ratio = (ff.group_ratio.value != "") ? unComma(ff.group_ratio.value) : 0;
                    ff.dc_amt.value = parseInt(price * (dc_ratio / 100));
                    com(ff.DC_AMT);
                } else if (group_type == "PT") {

                } else if (group_type == "WS") {
                    ff.price.value = ff.wholesale_price.value;
                    ff.com_price.value = ff.wholesale_price.value;
                    com(ff.PRICE);
                }
            } else {
                if (group_type == "DC") {
                    ff.dc_amt.value = 0;
                } else if (group_type == "PT") {

                } else if (group_type == "WS") {
                    ff.price.value = ff.org_price.value;
                    ff.com_price.value = ff.org_price.value;
                    com(ff.PRICE);
                }
            }
            CalAmt();
        }
    }

    function GetSecondOption(goods_no, goods_sub, goods_opt, option_cnt) {

        var ff = document.f1;
        var opt_select2 = ff.goods_opt2;
        var cnt = opt_select2.length;
        for (i = 0; i < cnt; i++) {
            opt_select2.options[0] = null;
        }

        var is_price_include = goods_opt.indexOf("|");
        if (is_price_include > -1) {
            tmp = goods_opt.split("|");
            goods_opt = tmp[0];
        }

        // if ( option_cnt > 1 && goods_opt != "") {
        //     var param = "CMD=get_second_option";
        //     param += "&GOODS_NO=" + goods_no + "&GOODS_SUB=" + goods_sub + "&GOODS_OPT=" + urlEncode(goods_opt);
        //     var http = new xmlHttp();
        //     http.onexec("ord20_detail.php","POST",param,true,cbGetSecondOption);
        // } else {
        //     opt_select2[opt_select2.length] = new Option("옵션(옵션가격)", "");
        // }
    }

    function cbGetSecondOption(res) {
        // var ff = document.f1;
        // var object = Dom2Obj(res);
        // var second_opt_list = (object["record"]) ? object["record"] : "";
        // var obj = ff.GOODS_OPT2;

        // //브랜드 리스트를 select에 option 넣기
        // obj[obj.length] = new Option("옵션(옵션가격)", "");
        // for(i=0; i < second_opt_list.length; i++)
        // {
        //     var goods_opt 		= second_opt_list[i].goods_opt;

        //     var tmp_txt	= "";
        //     var tmp_val	= "";

        //     var tmp = goods_opt.split("^");

        //     tmp_txt	= tmp[1];
        //     tmp_val	= tmp[1];
        //     obj[obj.length] = new Option(tmp_txt, tmp_val);
        // }
    }

    function SetOptionValue(value, depth) {
        var ff = document.f1;
        if (depth == 1) {
            ff.goods_opt.value = value;
        } else if (depth == 2) {
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
        const url = '/head/api/order';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
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

        location.href = `/head/order/ord02/show/?${params.join('&')}`;
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

    function save() {
        $.ajax({
            async: true,
            type: 'put',
            url: `/head/order/ord20/save/`,
            data: $('form[name=f1]').serialize(),
            success: function(data) {
                is_processing = false;
                alert("저장되었습니다.");
                // location.reload();
            },
            error: function(request, status, error) {
                is_processing = false;
                console.log("error")
            }
        });
    }


    if ($('#goods_opt1').length > 0) {
        $('#goods_opt1').change(function() {
            // SetOptionValue(this.value, 1);
            // GetSecondOption('{$goods->goods_no}', '{$goods->goods_sub}', this.value, '{$option_cnt}');
            // CalAmt();
        });
    }

    $(function() {
        CalAmt();
    });
</script>
@stop