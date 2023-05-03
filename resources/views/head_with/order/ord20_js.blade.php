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
                
                let goods_lists = [];
                for(i=0;i<ord_lists.length;i++){
                    if(ord_opt_no == "" || ord_opt_no == ord_lists[i]["ord_opt_no"]){

                        goods_no = ord_lists[i]["goods_no"];
                        ord_lists[i]["ord_amt"] = ord_lists[i]["price"] * ord_lists[i]["qty"];
                        goods_lists.push(ord_lists[i]);

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
                }

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
                }
            },
        });
    }

    /**
     * @return {boolean}
     */
    function AddGoods(){
        var url = '/head/product/prd01/choice';
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

    /**
     * @return {boolean}
     */
    function ChoiceGoodsNo(goods_nos){
        for(var i=0;i<goods_nos.length;i++){
            $.ajax({
                type: "get",
                url: '/head/product/prd01/' + goods_nos[i] + '/get',
                contentType: "application/x-www-form-urlencoded; charset=utf-8",
                dataType: 'json',
                // data: {},
                success: function(res) {
                    qty = 1;
                    gx.addRows([{
                        "goods_no":res.goods_no,
                        "goods_nm":res.goods_info.goods_nm,
                        "style_no" : res.goods_info.style_no,
                        "opt_val" : res.goods_info.opt_val,
                        "qty" : qty,
                        "price" : res.goods_info.price,
                        "ord_amt" : res.goods_info.price * qty,
                        "recv_amt" : res.goods_info.price * qty + res.goods_info.baesong_price,
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
                    _goods_options[goods_no] = options;
                },

                error: function(e){
                    console.log(e.responseText);
                }
            });
        }
        return true;
    }

    /**
     * @return {boolean}
     */
    function DelGoods(){
        gx.delSelectedRows();
    }

    if ($('#goods_opt1').length > 0) {
        $('#goods_opt1').change(function(){
            SetOptionValue(this.value, 1);
            GetSecondOption('{$goods->goods_no}', '{$goods->goods_sub}', this.value, '{$option_cnt}');
            CalAmt();
        });
    }

</script>
