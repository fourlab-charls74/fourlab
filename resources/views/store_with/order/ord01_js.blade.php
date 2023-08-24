<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    const out_order_errors = {
        '-100': "매장 주문번호 없음",
        '-101': "바코드 없음",
        '-102': "바코드 부정확",
        '-103': "수량정보 부정확",
        '-104': "판매가 부정확",
        '-105': "재고 부족",
        '500': "서버 오류",
    };

    /**
     * 수기판매 등록
    */
    let is_processing = false;

    // 등록 시 값 체크
    const Validate = (ff) => {
        if (is_processing) return alert("잠시만 기다려 주십시오. 지금 등록중입니다.");

        const ord_state = $("[name=ord_state]:checked");
        if(ord_state.length === 0) return alert('주문상태를 선택해 주십시오.');
        // if(ord_state.val() == 30) {
        //     if(ff.dlv_cd.value == "") {
        //         ff.dlv_cd.focus();
        //         return alert("택배사를 선택하십시오.");
        //     }
        //     if(ff.dlv_no.value == "") {
        //         ff.dlv_no.focus();
        //         return alert("택배 송장번호를 입력하십시오.");
        //     }
        // }

        const ord_type = $('[name=ord_type]:checked');
        if( ord_type.length === 0 ) return alert('출고형태를 선택해 주십시오.');

        const ord_kind = $('[name=ord_kind]:checked');
        if(ord_kind.length === 0) return alert('출고구분을 선택해 주십시오.');

        if(gx.getRowCount() == 0) return alert("수기판매등록하실 상품을 검색해 주십시오.");

        if(ff.pay_type.value == "") {
            ff.pay_type.focus();
            return alert("결제수단을 선택해 주십시오.");
        }

        if(ff.pay_type.value == "1" || ff.pay_type.value == "5" || ff.pay_type.value == "9" || ff.pay_type.value == "13") {
            if(ff.bank_code.value == "") {
                ff.bank_code.focus();
                return alert("입금은행을 선택해 주십시오.");
            }
            if(ff.bank_inpnm.value == "") {
                ff.bank_inpnm.focus();
                return alert("입금자를 입력해 주십시오.");
            }
        }

        if(ff.pay_type.value == "9" || ff.pay_type.value=="13") {
            if(ff.coupon_no.value == "") {
                ff.coupon_no.focus();
                return alert("쿠폰번호를 입력해 주십시오.");
            }
        }

        if(!confirm("수기판매로 등록하시면 더이상 수정하실 수 없습니다.\n등록 하시겠습니까?")) return;
        if(!is_processing) {
            is_processing = true;
            save();
        }
    }

    // 폼데이터 반환
    function getForm2JSON($form){
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function(n, i){
            indexed_array[n['name']] = n['value'];
        });
        return indexed_array;
    }

    // 판매등록
    function save(reservation_yn = 'N') {
        let order_data = getForm2JSON($('form[name=f1]'));
        
        let rows = [];
        gx.gridOptions.api.forEachNode(node => {
            if(node.data.goods_no !== undefined) {
                rows.push(node.data);
            }
        })
        order_data["cart"] = rows;
        order_data["reservation_yn"] = reservation_yn;

        $.ajax({
            async: true,
            dataType: "json",
            type: 'post',
            url: "/store/order/ord01/save",
            data: order_data,
            success: function (res) {
                is_processing = false;
                if(res.code == '200') {
                    alert("저장되었습니다.");
                    opener.Search();
                    document.location.href = '/store/order/ord01/order/' + res.ord_no;
                } else {
                    if (res.code === '-105' && reservation_yn === 'N') {
                        if (confirm("해당상품의 매장재고가 부족합니다.\n예약판매하시겠습니까?")) {
                            save('Y');
                        }
                    } else {
                        alert("저장에 실패했습니다.\n실패 사유 : " + out_order_errors[res.code]);
                    }
                }
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

    /**
     * 주문자정보 수정
    */

    // 회원정보 불러오기
    function GetUserInfo() {
        let user_id = $("#user_id").val();
        if(user_id == "") {
            alert("아이디를 입력해 주십시오.");
        } else {
            $.ajax({
                async: true,
                dataType: "json",
                type: 'get',
                url: "/head/member/mem01/" + user_id + "/get",
                success: function (res) {
                    if(res.hasOwnProperty('user')){
                        let user = res.user;
                        $('#user_nm').val(user.name);
                        $('#phone').val(user.phone);
                        $('#mobile').val(user.mobile);
                        $('#r_user_nm').val(user.name);
                        $('#r_phone').val(user.phone);
                        $('#r_mobile').val(user.mobile);
                        $('#r_zip_code').val(user.zip);
                        $('#r_addr1').val(user.addr);
                        $('#r_addr2').val(user.addr2);
                        $('#give_point_y').attr("checked", true);
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

    // 수령자정보 동일
    function SameInfo(){
        $('#r_user_nm').val($('#user_nm').val());
        $('#r_phone').val($('#phone').val());
        $('#r_mobile').val($('#mobile').val());
    }

    // 기존 주문정보 불러오기
    function PopSearchOrder() {
        const url='/head/api/order?isld=Y';
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }

    /**
     * 수령자정보 수정
    */

    // 주소검색팝업 오픈
    function openFindAddress(zipName, addName) {
        new daum.Postcode({
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다..
            oncomplete: function(data) {
                $("#" + zipName).val(data.zonecode);
                $("#" + addName).val(data.address);
            }
        }).open();
    }

    /**
     * 주문상태정보 변경
    */

    // 매장변경 시 선택상품 초기화
    $("[name=store_no]").on("change", async function(e) {
        gx.gridOptions.api.setRowData([]);
        EditAmtTable();
        $("#amt_table").css("display", "none");

		// 해당매장 (or 창고) 의 사용중인 판매처수수료목록 조회
		let res = await axios({ method: 'get', url: '/store/order/ord01/store/' + this.value });
		if (res.status === 200) {
			pr_codes = res.data.pr_codes;
			$("#ord_state_30").attr('disabled', res.data.is_online > 0);
			if (res.data.is_online > 0 && $("#ord_state_30").is(':checked')) {
				$("#ord_state_10").prop('checked', true);
			}
		}
    });

    /**
     * 상품정보 변경
    */

    // grid 내 값 변경 시 각종금액 수정
    function EditAmt(params) {
        if (params.oldValue !== params.newValue) {
			let data = params.data;
            let qty = data.qty;
            let price = parseInt(data.price);
			
			if (params.column.colId === 'sale_type' && data.sale_type !== '') {
				let goods_price = parseInt(data.goods_price);
				let sale_type = sale_types.filter(st => st.sale_type_nm === data.sale_type)[0] || {};
				let sale_type_dc_amt = sale_type.amt_kind === 'per' ? (goods_price * sale_type.sale_per / 100) : (sale_type.sale_amt || 0) * 1;
				price = goods_price - sale_type_dc_amt;
			}

            let ord_amt = qty * price;
            let point_amt = data.point_amt;
            let coupon_amt = data.coupon_amt;
            let dc_amt = data.dc_amt;
            let dlv_amt = data.dlv_amt;
            let pay_fee = 0;
            let recv_amt = ord_amt - point_amt - coupon_amt - dc_amt + pay_fee;

			params.data.price = price;
            params.data.ord_amt = ord_amt;
            params.data.recv_amt = recv_amt;
            gx.gridOptions.api.redrawRows({ rowNodes: [ params.node ] });

            EditAmtTable();
        }
    }

    // 금액테이블 각종금액 수정
    function EditAmtTable() {
        let rows = gx.getRows();
        let is_dlv_apply = $("input[name=dlv_apply]:checked").val() === "Y";

        let ord_amt_total = rows.reduce((a, c) => parseInt(c.ord_amt || 0) + a, 0); // 주문액합계
        let dlv_amt_total = is_dlv_apply ? (ord_amt_total < free_dlv_amt ? base_dlv_fee : 0) : 0; // 배송비합계

        $("[name='dlv_amt']").val(dlv_amt_total.toLocaleString('ko-KR'));
        $("[name='ord_amt']").val(ord_amt_total.toLocaleString('ko-KR'));

        let point_amt = unComma($("[name='point_amt']").val());

        let recv_amt_total = ord_amt_total - point_amt + dlv_amt_total; // 총입금액
        $("[name='recv_amt']").val(recv_amt_total.toLocaleString('ko-KR'));
        $("[name='point_amt']").val(point_amt.toLocaleString('ko-KR'));

        let supply_amt = Math.round(recv_amt_total/1.1); // 공급가액
        $("[name='supply_amt']").val(supply_amt.toLocaleString('ko-KR'));

        let vat_amt = Math.round(recv_amt_total - supply_amt); // 세액
        $("[name='vat_amt']").val(vat_amt.toLocaleString('ko-KR'));
    }

    // 상품추가 시 팝업 오픈
     function AddGoods(){
        const store_cd = $("[name=store_no]").val();
        const url = `/store/api/goods/show?store_cd=${store_cd || 'ALL'}`;
        window.open(url, "_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1800,height=1000");
        
    }

    // 상품추가팝업 callback
    var goodsCallback = (row) => {
        setGoodsGrid([row]);
    };
    
    // 상품추가팝업 callback
    var multiGoodsCallback = (rows) => {
        if (rows && Array.isArray(rows)) setGoodsGrid(rows);
    };

    // 선택한 상품 정보 목록 세팅
    function setGoodsGrid(goods_list) {
        let ord_qty = 1;
        goods_list = goods_list.map(g => ({
            ...g,
            com_type: g.com_type_d,
            goods_price: g.price,
            qty: ord_qty,
            ord_amt: g.price * ord_qty,
            point_amt: 0,
            coupon_amt: 0,
            dc_amt: 0,
            dlv_amt: 0,
	        sale_type: '',
	        sale_kind_cd: '',
	        pr_code: '',
	        pr_code_cd: '',
	        memo: '',
        }));
        gx.addRows(goods_list);
        EditAmtTable();
        $("#amt_table").css("display", "block");
    }

    // 상품삭제
    function DelGoods(){
        gx.delSelectedRows();
        EditAmtTable();
        if(gx.getRowCount() === 0) {
            $("#amt_table").css("display", "none");
        }
    }

    // 적립금사용 금액 변경
    $("#point_amt").change(function() {
		let point = unComma(this.value);

		if(isNaN(point)) this.value = 0;
		else this.value = point.toLocaleString("ko-KR");

        EditAmtTable();
    });

    /**
     * ETC
    */
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
        var point = (ff.point_amt.value != "") ? unComma(ff.point_amt.value) : 0;
        var coupon = (ff.coupon_amt.value != "") ? unComma(ff.coupon_amt.value) : 0;
        var pay_fee = (ff.pay_fee.value != "") ? unComma(ff.pay_fee.value) : 0;
        var dc = (ff.dc_amt.value != "") ? unComma(ff.dc_amt.value) : 0;
        var dlv_amt = (ff.dlv_amt.value != "") ? unComma(ff.dlv_amt.value) : 0;
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

    function PopOrder(obj) {
        openOrder(obj.innerHTML);
    }

    function SetOrder(ord_no,ord_opt_no = '') {
        $("#p_ord_no").html("<a href='javascript:void(0);' onclick='return PopOrder(this);'>" + ord_no + "</a>");

        if(ord_opt_no == ''){
            url = '/head/order/ord01/get/' + ord_no + '?fmt=json';
        } else {
            url = '/head/order/ord01/get/' + ord_no + '/'  + ord_opt_no + '?fmt=json'
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
                $('#bank_code').val(pay.bank_code);
                $('#bank_number').val(pay.bank_number);
                $('#sale_place').val(ord.com_id);
                if($('#ord_type_' +  ord.ord_type)){
                    $('#ord_type_' +  ord.ord_type).attr("checked", true);
                }

                $('input[name="ord_state"]').each(function() {
                    if(this.value == ord.ord_state){
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });

                $('input[name="ord_kind"]').each(function() {
                    if(this.value == ord.ord_kind){
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });

                gx.gridOptions.api.setRowData(ord_lists);

                for(i=0;i<ord_lists.length;i++){
                    goods_no = ord_lists[i]["goods_no"];
                    $.ajax({
                        type: "get",
                        url: '/head/product/prd01/' + goods_no + '/get',
                        contentType: "application/x-www-form-urlencoded; charset=utf-8",
                        dataType: 'json',
                        // data: {},
                        success: function (res) {
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
</script>
