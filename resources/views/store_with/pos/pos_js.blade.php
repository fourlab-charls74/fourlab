<script type="text/javascript">

    /** 화면전환 */
    function setScreen(idx) {
        $("#pos > div:not(#pos_header)").removeClass("d-flex").addClass("d-none");
        $("#" + idx).addClass("d-flex");

        $("#home_btn").css("display", idx === "pos_order" ? "none" : "inline-block");
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

        gx.gridOptions.api.applyTransaction({add: [{...goods, qty: 1, total: 1 * goods.price}]});
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
    }

    /** 상품리스트에서 상품 삭제 */
    function removeProduct(prd_cd = '') {
        if(prd_cd == '') return;

        let list = gx.getRows();
        let goods = list.find(g => g.prd_cd === prd_cd);

        gx.gridOptions.api.applyTransaction({remove: [goods]});
    }
    
    /** 신용카드 금액 적용 */
    function setOrderPrice() {
        $('#cardModal').modal('hide');
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
                }
            } else {
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
        $("#payed_amt").text(Comma(payed_amt));
        $("#change_amt").text(Comma(payed_amt - order_price));

        $("[name=card_amt]").val(card_amt);
        $("[name=cash_amt]").val(cash_amt);
        $("[name=point_amt]").val(point_amt);
    }

</script>