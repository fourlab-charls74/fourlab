<script type="text/javascript">
    const BRAND_CODE = "F"; // 피엘라벤

    /** 화면전환 */
    function setScreen(idx) {
        $("#pos > div:not(#pos_header)").removeClass("d-flex").addClass("d-none");
        $("#" + idx).addClass("d-flex");

        if(idx === "pos_order") {
            initOrderScreen();
            $("#home_btn").css("display", "none");
        } else {
            // setNewOrdNo();
            $("#home_btn").css("display", "inline-block");
        }
    }

    /** 주문등록화면 초기화 */
    function initOrderScreen() {
        if(gx) {
            setNewOrdNo(true);
            gx.gridOptions.api.setRowData([]);
            setProductDetail();
            
            $("[name=card_amt]").val(0);
            $("[name=cash_amt]").val(0);
            $("[name=point_amt]").val(0);
            
            $("#total_order_amt").text(0);
            $("#total_order_amt2").text(0);
            $("#payed_amt").text(0);
            $("#change_amt").text(0);
            $("#due_amt").text(0);
            
            $("#card_amt").text(0);
            $("#cash_amt").text(0);
            $("#point_amt").text(0);
        }
    }

    /** 새로운 주문번호 조회 */
    async function setNewOrdNo(create = false) {
        if(create) {
            const { data: { ord_no } } = await axios({ 
                url: '/store/pos/search/ordno',
                method: 'get' 
            });
            $("#ord_no").text(ord_no);
        } else {
            $("#ord_no").text('');
        }
    }

    /** 상품검색 */
    function Search() {
        let type = $("[name=search_prd_type]").val();
        let keyword = $("[name=search_prd_keyword]").val();

        let data = "search_type=" + type + "&search_keyword=" + keyword;
        gx2.Request("/store/pos/search/goods", data, 1);
    }

    /** 전체취소 */
    function cancelOrder() {
        if(!confirm("해당주문건을 취소하시겠습니까?")) return;
        setScreen('pos_main');
    }
    
    /** 상품리스트에 상품 추가 */
    function addProduct(prd_cd = '') {
        if(prd_cd == '') return;

        let list = gx2.getRows();
        let goods = list.find(g => g.prd_cd === prd_cd);

        gx.gridOptions.api.applyTransaction({add: [{...goods, qty: 1, total: 1 * goods.price, sale_type: sale_types[0].sale_kind, pr_code: pr_codes[0].pr_code}]});
        gx.gridOptions.api.forEachNode((node) => {
            if(node.data.prd_cd === prd_cd) {
                node.setSelected(true);
            }
        });

        $('#searchProductModal').modal('hide');
        $("#search_prd_keyword").val('');
        gx2.setRows([]);
    }

    /** 우측 상품상세정보 조회 */
    function setProductDetail(prd_cd = '') {
        if(prd_cd == '') {
            // 초기화
            $("#cur_goods_nm").text('');
            $("#cur_goods_opt").text('');
            $("#cur_prd_cd").text('');
            $("#cur_qty").text('-');
            $("#cur_price").text('-');
            $("#cur_goods_sh").text('-');
            $("#cur_img").attr('src', '');
            $("#cur_img").attr('alt', '');
            $("#sale_type").find("option").remove();
            $('#pr_code').val('JS').prop("selected", true);
            return;
        }

        let list = gx.getRows();
        let goods = list.find(g => g.prd_cd === prd_cd);

        $("#cur_goods_nm").text(goods.goods_nm);
        $("#cur_goods_opt").text(goods.goods_opt);
        $("#cur_prd_cd").text(goods.prd_cd);
        $("#cur_qty").text(Comma(goods.qty));
        $("#cur_price").text(Comma(goods.price));
        $("#cur_goods_sh").text(Comma(goods.goods_sh));
        $("#cur_img").attr('src', "{{config('shop.image_svr')}}" + "/" + goods.img);
        $("#cur_img").attr('alt', goods.goods_nm);

        let isJsGoods = goods.price == goods.goods_sh;

        let sale_type = document.getElementById("sale_type");
        $(sale_type).find("option").remove();
        sale_types.forEach((type, key) => {
            if(isJsGoods || (!isJsGoods && ((type.amt_kind == 'per' && type.sale_per <= 0) || (type.amt_kind == 'amt' && type.sale_amt <= 0)))) {
                sale_type[sale_type.options.length] = new Option(type.sale_type_nm, type.sale_kind);
            }
        });
        if(goods.sale_type != "") {
            $(sale_type).val(goods.sale_type).prop("selected", true);
        }
         
        if(goods.pr_code != "") {
            $('#pr_code').val(goods.pr_code).prop("selected", true);
        } else {
            if(goods.brand != BRAND_CODE) {
                $('#pr_code').val('J2').prop("selected", true);
            } else if(!isJsGoods) {
                $('#pr_code').val('GL').prop("selected", true);
            } else {
                $("#pr_code").prop("selectedIndex", 0);
            }
        }
    }

    /** 상품리스트에서 상품 삭제 */
    function removeProduct(prd_cd = '') {
        if(prd_cd == '') return;

        let list = gx.getRows();
        let goods = list.find(g => g.prd_cd === prd_cd);

        gx.gridOptions.api.applyTransaction({remove: [goods]});
    }

    /** 수량 및 금액 변경 시 업데이트 */
    async function updateOrderValue(key = '', value = '') {
        if(key != '') {
            let curRow = gx.getSelectedNodes();
            if(curRow.length > 0) {
                let rowData = curRow[0].data;
                if(key === 'cur_qty') {
                    $("#cur_qty").text(Comma(value));
                    curRow[0].setData({...rowData, qty: value, total: rowData.price * value});
                } else if(key === 'cur_price') {
                    $("#cur_price").text(Comma(value));
                    curRow[0].setData({...rowData, price: value, total: rowData.qty * value});
                } else if(key === 'sale_type') {
                    curRow[0].setData({...rowData, sale_type: value});
                } else if(key === 'pr_code') {
                    curRow[0].setData({...rowData, pr_code: value});
                }
            } 
            if(['card_amt', 'cash_amt', 'point_amt'].includes(key)) {
                await $(`[name=${key}]`).val(value);
            }
        }

        let list = gx.getRows();

        let order_price = list.reduce((a, c) => a + c.total, 0);
        let card_amt = $("[name=card_amt]").val() * 1;
        let cash_amt = $("[name=cash_amt]").val() * 1;
        let point_amt = $("[name=point_amt]").val() * 1;
        let payed_amt = card_amt + cash_amt + point_amt;
        
        $("#total_order_amt").text(Comma(order_price));
        $("#total_order_amt2").text(Comma(order_price));
        $("#payed_amt").text(Comma(payed_amt));
        $("#change_amt").text(Comma(payed_amt - order_price > 0 ? payed_amt - order_price : 0));
        $("#due_amt").text(Comma(order_price - payed_amt > 0 ? order_price - payed_amt : 0));

        $("#card_amt").text(Comma(card_amt));
        $("#cash_amt").text(Comma(cash_amt));
        $("#point_amt").text(Comma(point_amt));
    }

    /** 남은금액 클릭 시 input 반영 */
    function setDueAmt() {
        let due_amt = $("#due_amt").text();
        $("#pay_press_amt").val(due_amt);
    }

    /** 주문등록 (판매) */
    function sale() {
        if(!validate()) return;

        let card_amt = $("[name=card_amt]").val() * 1;
        let cash_amt = $("[name=cash_amt]").val() * 1;
        let point_amt = $("[name=point_amt]").val() * 1;
        let cart = gx.getRows();
        
        axios({
            async: true,
            url: '/store/pos/save',
            method: 'post',
            dataType: "json",
            data: {
                ord_state: '30', // 출고완료 처리
                card_amt,
                cash_amt,
                point_amt,
                cart,
                user_id: '',
            },
        }).then(function (res) {
            if(res.data.code === '200') {
                alert("주문이 정상적으로 등록되었습니다.");
                initOrderScreen();
            } else if(res.data.code !== '500') {
                alert(res.data.msg);
            } else {
                console.log(res);
                alert("주문등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    /** 주문등록 시 null check */
    function validate() {
        if(gx.getRows().length < 1) return alert("판매할 상품을 선택해주세요.");

        let due_amt = unComma($("#due_amt").text());
        if(due_amt > 0) return alert("결제할 금액이 남아있습니다.");
        
        return true;
    }

</script>