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

        if(ff.sale_place.value == ""){
            alert('판매업체를 선택해 주십시오.');
            ff.sale_place.focus();
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
            let is_option = true;
            let is_nan = false;
            
            gx.gridOptions.api.forEachNode(function(node) {
                if (node.data.opt_val === undefined || node.data.opt_val === "") {
                    if(node.data.goods_no !== undefined) is_option = false;
                    gx.gridOptions.api.setFocusedCell(node.rowIndex,"opt_val");
                    return false;
                }
                if (isNaN(node.data.qty) || isNaN(node.data.price)) {
                    if(node.data.goods_no !== undefined) is_nan = true;
                }
            });
            
            if (!is_option) {
                alert('옵션을 선택 해 주십시오.');
                return false;
            }
            if (is_nan) {
                alert('상품의 수량과 판매가를 올바르게 입력해 주십시오.');
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

            if(ff.bank_number.value == "") {
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
            var rowNode = params.node;
            var qty = params.data.qty;
            var price = parseInt(params.data.price);
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

			_goods_list_of_com_type[params.data.com_type === 2 ? params.data.com_id : 'etc'].find(g => g.idx === params.data.idx).chg_qty = qty;
			_goods_list_of_com_type[params.data.com_type === 2 ? params.data.com_id : 'etc'].find(g => g.idx === params.data.idx).chg_price = price;
			_goods_list_of_com_type[params.data.com_type === 2 ? params.data.com_id : 'etc'].find(g => g.idx === params.data.idx).chg_opt_val = params.data.opt_val;
			
            if ($("input[name=dlv_apply]:checked").val() === "Y") {
                setDlvFeeOfComType();
            } else {
                EditAmtTable(0);
                let list = gx.getRows().map(g => ({...g, dlv_amt: 0}));
                gx.gridOptions.api.setRowData(list);
                gx.setFocusedWorkingCell();
            }
			
        }
    }
	
    // com_type별 배송비합계 세팅
    function setDlvFeeOfComType(direct_dlv = '', direct_com_id = '') {
        let rows = gx.getRows();
        let nodes = [], dlv_amts = [];
        let count = 0, ord_amt = 0;

        if (direct_dlv !== '' && direct_com_id !== '' && !isNaN(direct_dlv * 1) && direct_dlv > 0) {
            $("#dlv_apply_y").prop('checked', true);
        }

        gx.gridOptions.api.forEachNode(node => {
            count++;
            let dlv;
            
            if ($("input[name=dlv_apply]:checked").val() === "N") {
                dlv = 0;
            } else if (!node.data.goods_no) {
                let arr = rows.splice(0, count);
                let total = arr.reduce((a, c) => (c.price || 0) * (c.qty || 0) + a, 0);
                
                if (direct_dlv !== '' && node.data.com_id === direct_com_id && !isNaN(direct_dlv * 1)) {
                    dlv = direct_dlv * 1;
                    node.data.direct_dlv = dlv;
                } else if (node.data.direct_dlv !== undefined) {
                    dlv = node.data.direct_dlv;
                } else if (direct_dlv !== '' && direct_dlv < 1) {
                    dlv = node.data.dlv_amt;
                } else {
                    dlv = total < free_dlv_amt ? base_dlv_fee : 0;
                }

                if (isNaN(dlv)) dlv = 0;
                dlv_amts.push([node.data.com_id, dlv, arr.length - 1]);
                count = 0;
            } else {
                dlv = (node.data?.price * node.data?.qty) < free_dlv_amt ? base_dlv_fee : 0;
                if (isNaN(dlv)) dlv = 0;
            }
            
            if (!node.data.goods_no) {
                node.data.ord_amt = ord_amt;
                ord_amt = 0;
            } else {
                ord_amt += node.data.ord_amt || 0;
            }
            
            node.data.dlv_amt = dlv;
            nodes.push(node);
        });
        
        let com_dlv_cnt = 0
        nodes = nodes.map(node => {
            if (node.data.goods_no) {
                com_dlv_cnt++;
                let com_id = node.data.com_type === 2 ? node.data.com_id : 'etc';
                let dlv_amt = dlv_amts.find(d => d[0] === com_id) || [];
                if (com_dlv_cnt >= dlv_amt[2]) {
                    node.data.dlv_amt = dlv_amt[1];
                } else {
                    node.data.dlv_amt = 0;
                }
            } else {
                com_dlv_cnt = 0;
            }
            return node;
        });

        gx.gridOptions.api.redrawRows({ rowNodes: nodes });
        gx.setFocusedWorkingCell();

        EditAmtTable(dlv_amts.reduce((a, c) => a + c[1], 0));
    }

    function EditAmtTable(total_dlv_amt) {
        var rows = gx.getRows();

        var dlv_amt_total = total_dlv_amt !== undefined ? total_dlv_amt : unComma($("[name='dlv_amt']").val()); // 배송비합계

        var ord_amt_total = rows.reduce((a,c) => c.goods_no ? c.ord_amt + a : a, 0); // 주문액합계

        if(total_dlv_amt !== undefined) {
            $("[name='dlv_amt']").val(dlv_amt_total.toLocaleString('ko-KR'));
            $("[name='ord_amt']").val(ord_amt_total.toLocaleString('ko-KR'));
        }

        var point_amt = unComma($("[name='point_amt']").val());
        var recv_amt_total = ord_amt_total - parseInt(point_amt) + dlv_amt_total; // 총입금액
        $("[name='recv_amt']").val(recv_amt_total.toLocaleString('ko-KR'));

        var supply_amt = Math.round(recv_amt_total/1.1); // 공급가액
        $("[name='supply_amt']").val(supply_amt.toLocaleString('ko-KR'));

        var vat_amt = Math.round(recv_amt_total - supply_amt); // 세액
        $("[name='vat_amt']").val(vat_amt.toLocaleString('ko-KR'));
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
                url: "/head/member/mem01/" + user_id + "/get",
                success: function (res) {
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

    function SameInfo(){
        $('#r_user_nm').val($('#user_nm').val());
        $('#r_phone').val($('#phone').val());
        $('#r_mobile').val($('#mobile').val());
    }

    function CheckAddDlvArea(obj){
        // var param = "CMD=check_add_dlv_area";
        // param += "&ZIPCODE=" + obj.value;
        // var http = new xmlHttp();
        // http.onexec("ord20_detail.php","POST",param,true,cbCheckAddDlvArea);
    }

    function cbCheckAddDlvArea(res){
        // var add_dlv_fee = res.responseText;
        // var ff = document.f1;

        // ff.ADD_DLV_FEE.value = add_dlv_fee;
        // com(ff.ADD_DLV_FEE);

        // CalAmt();
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

        // if ( option_cnt > 1 && goods_opt != "") {
        //     var param = "CMD=get_second_option";
        //     param += "&GOODS_NO=" + goods_no + "&GOODS_SUB=" + goods_sub + "&GOODS_OPT=" + urlEncode(goods_opt);
        //     var http = new xmlHttp();
        //     http.onexec("ord20_detail.php","POST",param,true,cbGetSecondOption);
        // } else {
        //     opt_select2[opt_select2.length] = new Option("옵션(옵션가격)", "");
        // }
    }

    function cbGetSecondOption(res)
    {
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
        const url='/head/api/order?isld=Y';
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1400,height=800");
    }

    function PopOrder(obj){
        openOrder(obj.innerHTML);
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

                //console.log(ord_lists);
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

        let rows = [];
        gx.gridOptions.api.forEachNode(node => {
            if(node.data.goods_no !== undefined) {
                rows.push(node.data);
            }
        })
        order_data["cart"] = rows;

        $.ajax({
            async: true,
            dataType: "json",
            type: 'post',
            url: "/head/order/ord02/save",
            data: order_data,
            success: function (res) {
                is_processing = false;
                alert("저장되었습니다.");
                document.location.href = '/head/order/ord01/' + res.ord_no;
            },
            error: function(e) {
                is_processing = false;
                console.log('[error] ' + e.responseText);
                var err = JSON.parse(e.responseText);
                if(err.hasOwnProperty("code") && err.code == "500"){
                    alert(err.msg);
                    console.log(err.error_msg);
                }
            },
        });
    }

	function AddGoods(){
		var url = '/head/product/prd01/choice';
		var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
	}
	
    /**
     * @return {boolean}
     */
	function AddSelectedGoods() {
		const rows = gx.getSelectedRows();
		ChoiceGoodsNo(rows.map(row => row.goods_no));
	}

    /**
     * @return {boolean}
     */
    function ChoiceGoodsNo(goods_nos){
        if(goods_nos.length < 1) return;
        const goods_list = [];
        for(var i=0;i<goods_nos.length;i++){
            $.ajax({
                type: "get",
                url: '/head/product/prd01/' + goods_nos[i] + '/get',
                contentType: "application/x-www-form-urlencoded; charset=utf-8",
                dataType: 'json',
                // data: {},
                success: function(res) {
                    goods_list.push(res);
                    if(goods_list.length >= goods_nos.length) setGoodsGrid(goods_list);
                },
                error: function(e){
                    console.log(e.responseText);
                }
            });
        }
        return true;
    }

    // 선택한 상품 정보 목록 세팅
    function setGoodsGrid(goods_list) {

        goods_list.forEach(goods => {
            let item = {...goods, idx: _goods_idx_cnt++};
            if(goods.goods_info.com_type === 2) {
                let com_id = goods.goods_info.com_id;
                if(_goods_list_of_com_type[com_id]) {
                    _goods_list_of_com_type[com_id].push(item);
                } else {
                    _goods_list_of_com_type[com_id] = [item];
                }
            } else {
                if(_goods_list_of_com_type.etc === null) {
                    _goods_list_of_com_type.etc = [];
                }
                _goods_list_of_com_type.etc.push(item);
            }
        });
        gx.gridOptions.api.setRowData([]);
        Object.keys(_goods_list_of_com_type).forEach(key => {
            if(_goods_list_of_com_type[key] !== null) {
                _goods_list_of_com_type[key].forEach(goods => {
                    setGoodsRow(goods);
                })
                if(key !== "etc" || _goods_list_of_com_type[key].length > 0) {
                    gx.addRows([{
                        com_id: key,
                        ord_opt_no: key === "etc" ? "매입상품" : _goods_list_of_com_type[key][0].goods_info.com_nm,
                        dc_amt: "배송비 : ",
                        dlv_amt: 0,
						deselect: true,
                    }]);
                }
            }
        })
        setDlvFeeOfComType();
        setDlvFeeUse($("input[name=dlv_apply]:checked").val() === "Y");
        $("#amt_table").css("display", "block");
    }

    function setGoodsRow(res) {
		var qty = res.chg_qty || 1;
        gx.addRows([{
            "idx":res.idx,
            "goods_no":res.goods_no,
            "com_type": res.goods_info.com_type,
            "com_id": res.goods_info.com_id,
            "ord_opt_no":res.goods_no,
            "goods_nm":res.goods_info.goods_nm,
            "style_no" : res.goods_info.style_no,
            "opt_val" : res.chg_opt_val || res.goods_info.opt_val,
            "qty" : qty,
            "price" : res.chg_price || res.goods_info.price,
            "ord_amt" : res.goods_info.price * qty,
            "recv_amt" : res.goods_info.price * qty + parseInt(res.goods_info.baesong_price),
            "point_amt" : 0,
            "coupon_amt" : 0,
            "dc_amt" : 0,
            "dlv_amt" : res.goods_info.baesong_price,
        }]);
        var options = [];
        for(var j = 0; j < res.options.length;j++){
            if(res.options[j].qty > 0){
                options.push(res.options[j].goods_opt);
            }
        }
        _goods_options[res.goods_no] = options;
    }

    /**
     * @return {boolean}
     */
    function DelGoods(){
        let delrow = gx.getSelectedRows();
        delrow.forEach((d,i) => {
            if(d.goods_no) {
                const type = d.com_type == 2 ? d.com_id : "etc";
				_goods_list_of_com_type[type] = _goods_list_of_com_type[type]?.filter(item => item.goods_no !== d.goods_no || item.idx !== d.idx) || [];
                if(_goods_list_of_com_type[type].length < 1) {
                    _goods_list_of_com_type[type] = null;
                    gx.gridOptions.api.forEachNode(node => {
                        if(node.data.com_id === d.com_id || (type === "etc" && node.data.com_id === "etc")) node.setSelected(true);
                    })
                }
            }
        })

        gx.delSelectedRows();

		Object.keys(_goods_list_of_com_type).forEach(key => {
			if (_goods_list_of_com_type[key] === null) {
				gx.gridOptions.api.forEachNode(node => {
					if (!node.data.goods_no && node.data.com_id === key) gx.gridOptions.api.applyTransaction({ remove: [node.data] });
				});
			}
		});
		
        setDlvFeeOfComType();
        if(gx.getRowCount() === 0) {
            $("#amt_table").css("display", "none");
            // $("#add_goods").attr("disabled", false);
        }
    }

    if ($('#goods_opt1').length > 0) {
        $('#goods_opt1').change(function(){
            SetOptionValue(this.value, 1);
            GetSecondOption('{$goods->goods_no}', '{$goods->goods_sub}', this.value, '{$option_cnt}');
            CalAmt();
        });
    }

    $("#point_amt").change(function() {
        var point = unComma(this.value);

        if(isNaN(point)) this.value = 0;
        else this.value = point.toLocaleString("ko-KR");
		
        EditAmtTable();
    })

    // 배송비 적용/적용안함 설정
    function setDlvFeeUse(is_use) {
        if(is_use) setDlvFeeOfComType();
        else {
            EditAmtTable(0);
            let list = gx.getRows().map(g => ({...g, dlv_amt: 0, direct_dlv: undefined}));
            gx.gridOptions.api.setRowData(list);
        }
    }

</script>
