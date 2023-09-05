<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">
    const BRAND_CODE = "F"; // 피엘라벤

    const ORD_STATE = {
        NEW: "new", // 신규주문
        WAITING: "waiting", // 기존 대기주문
    };

    /** 화면전환 */
    function setScreen(idx) {
        $("#pos > div:not(#pos_header)").removeClass("d-flex").addClass("d-none");
        $("#" + idx).addClass("d-flex");

        if(idx === "pos_order") {
            $("#back_btn").addClass('d-none');
            $("#search_prd_keyword_out").trigger("focus");
			document.search_order_history.reset();
        } else {
            $("#back_btn").removeClass('d-none');
            $("#search_prd_keyword_out").val('');
        }
    }

    /** 매출분석 & 직전결제내역 데이터 조회 */
    async function getOrderAnalysisData() {
        const { status, data } = await axios({
            url: '/shop/pos/search/analysis',
            method: 'get'
        });

        if(status === 200) {
            $("#to_ord_amt").text(Comma(data.today_order?.ord_amt || 0));
            $("#to_pay_amt").text(Comma(data.today_order?.pay_amt || 0));
            $("#to_qty").text(Comma(data.today_order?.ord_qty || 0));
            $("#to_ord_cnt").text(Comma(data.today_order?.ord_cnt));

            $("[name=po_ord_no]").val(data.prev_order?.ord_no || '');
            $("#po_recv_amt").text(Comma(data.prev_order?.recv_amt || 0));
            $("#po_ord_amt").text(Comma(data.prev_order?.ord_amt || 0));
            $("#po_dc_amt").text(Comma(data.prev_order?.dc_amt || 0));
            $("#po_coupon_amt").text(Comma(data.prev_order?.coupon_amt || 0));
            $("#po_point_amt").text(Comma(data.prev_order?.point_amt || 0));
            $("#po_ord_date").text(data.prev_order?.ord_date || '-');
        }
    }

    /** 직전결제내역 영수증 조회 */
    function viewPrevOrder() {
        $("[name=ord_sdate]").val(getFormatDate(new Date()));
        $("[name=ord_edate]").val(getFormatDate(new Date()));
        $("#ord_field").val("desc").prop("selected", true);

        setScreen("pos_today");

        let ord_no = $("[name=po_ord_no]").val();
        SearchOrder(ord_no);
        setOrderDetail(ord_no);
    }

    /** 대기내역 조회 */
    function searchWaiting() {
        gx5.Request("/shop/pos/search/waiting", "", -1, function(d) {
            $("#waiting_cnt").text(d.head.total);
        });
    }

    /** 주문대기 삭제 */
    async function removeWaiting() {
        let order = gx5.getSelectedRows();
        if(order.length < 1) return;
        if(!confirm("해당 주문대기내역을 삭제하시겠습니까?")) return;

        order = order[0];

        let res = await axios({ method: "delete", url: "/shop/pos/remove-waiting?ord_no=" + order.ord_no });
        if(res.data.code === '200') {
            searchWaiting();
        } else {
            alert("해당 주문대기내역 삭제 중 오류가 발생했습니다.\n다시 시도해주세요.");
            console.log(res);
        }
    }

    /** 주문대기 적용 */
    async function applyWaiting() {
        let order = gx5.getSelectedRows();
        if(order.length < 1) return;

        order = order[0];
        let res = await axios({ method: "get", url: "/shop/pos/search/order-detail?ord_no=" + order.ord_no });

        if(res.data.code === '200') {
            $('#searchWaitingModal').modal('hide');
            setScreen("pos_order");
            $("[name=cur_ord_state]").val(ORD_STATE.WAITING);

            order = res.data.data[0];
            $("#ord_no").text(order.ord_no);
            $("#memo").val(order.dlv_comment);
            setMember('', order);

            for(let goods of res.data.data) {
                await gx.gridOptions.api.applyTransaction({
                    add: [{
                        ...goods,
                        total: goods.qty * goods.price,
                        sale_type: goods.sale_kind || sale_types[0]?.sale_kind || '',
                        pr_code: goods.pr_code || pr_codes[0]?.pr_code || ''
                    }],
                });
                await gx.gridOptions.api.forEachNode((node) => {
                    if(node.data.prd_cd === goods.prd_cd) {
                        node.setSelected(true);
                        updateOrderValue('sale_type', goods.sale_kind || sale_types[0]?.sale_kind || '');
                    }
                });
            }

        } else {
            console.log(res);
            alert("주문 적용 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
        }
    }

    /** 주문등록화면 초기화 */
    function initOrderScreen() {
        if(gx) {
            gx.gridOptions.api.setRowData([]);
            setProductDetail();
            setMember();

            $("[name=cur_ord_state]").val(ORD_STATE.NEW);
            $("[name=removed_goods]").val("");

            $("[name=card_amt]").val(0);
            $("[name=cash_amt]").val(0);
            $("[name=point_amt]").val(0);

            $("#total_goods_sh_amt").text(0);
            $("#total_dc_amt").text(0);
            $("#total_order_amt").text(0);
            $("#total_order_amt2").text(0);
            $("#payed_amt").text(0);
            $("#change_amt").text(0);
            $("#due_amt").text(0);
            $("#total_order_qty").text(0);

            $("#card_amt").text(0);
            $("#cash_amt").text(0);
            $("#point_amt").text(0);

            $("#memo").val('');
        }
    }

    /**
     * 전체취소
     * 주문초기화로 기능변경 - 20230314
     */
    function cancelOrder() {
        // if(!confirm("해당주문건을 취소하시겠습니까?")) return;
        initOrderScreen();
        // if($("[name=cur_ord_state]").val() == ORD_STATE.WAITING) setNewOrdNo(true);
        // setScreen('pos_main');
    }

    /** 새로운 주문번호 조회 */
    async function setNewOrdNo(create = false) {
        if(create) {
            const { data: { ord_no } } = await axios({
                url: '/shop/pos/search/ordno',
                method: 'get'
            });
            $("#ord_no").text(ord_no);
        } else {
            $("#ord_no").text('');
        }
    }

    /** 상품검색 */
    function Search(callback = null) {
        let type = $("[name=search_prd_type]").val();
        let keyword = $("[name=search_prd_keyword]").val();

        let data = "search_type=" + type + "&search_keyword=" + keyword;
        gx2.Request("/shop/pos/search/goods", data, 1, function (e) {
			if (callback !== null) callback(e);
        });
    }

    /** 상품리스트에 상품 추가 */
    function addProduct(prd_cd = '') {
        if(prd_cd == '') return;

        let list = gx2.getRows();
        let goods = list.find(g => g.prd_cd === prd_cd);

        let same_goods = gx.getRows().find(g => g.prd_cd === prd_cd);
        if(same_goods === undefined) {
            gx.gridOptions.api.applyTransaction({ add: [{...goods, qty: 1, total: 1 * goods.price, sale_type: '', pr_code: pr_codes[0]?.pr_code || ''}] });
            gx.gridOptions.api.forEachNode((node) => {
                if(node.data.prd_cd === prd_cd) {
                    node.setSelected(true);
                }
            });

            $('#searchProductModal').modal('hide');
            $("#search_prd_keyword").val('');
            $("#search_prd_keyword_out").val('');
            gx2.setRows([]);

            $("#search_prd_keyword_out").trigger("focus");
        } else {
            alert("이미 선택된 상품입니다.");
        }
    }

    /** 우측 상품상세정보 조회 */
    function setProductDetail(prd_cd = '') {
        if(prd_cd == '') {
            // 초기화
            $("#cur_goods_nm").text('');
            $("#cur_goods_color").text('');
            $("#cur_goods_size").text('');
            $("#cur_prd_cd").text('');
			$("#cur_img").attr('src', '');
			$("#cur_img").attr('alt', '');
            $("#cur_goods_sh").text('-');
            $("#cur_wqty").text('-');
            $("#cur_ori_price").text('-');
            $("#cur_qty").text('');
            $("#cur_dc_rate").text('-');
            $("#cur_price").text('-');
            $("#sale_type").find("option").remove();
            $('#pr_code').val('JS').prop("selected", true);
            $('#coupon_no').val('').prop("selected", true);
            return;
        }

        let list = gx.getRows();
        let goods = list.find(g => g.prd_cd === prd_cd);

        $("#cur_goods_nm").text(goods.goods_nm);
        $("#cur_goods_color").text(goods.color);
        $("#cur_goods_size").text(goods.size);
        $("#cur_prd_cd").text(goods.prd_cd);
        $("#cur_img").attr('src', "{{config('shop.image_svr')}}" + "/" + goods.img);
        $("#cur_img").attr('alt', goods.goods_nm);
        $("#cur_goods_sh").text(Comma(goods.goods_sh));
		$("#cur_wqty").text(Comma(goods.wqty));
		$("#cur_ori_price").text(Comma(goods.ori_price));
		$("#cur_qty").text(goods.qty);
		$("#cur_dc_rate").text(goods.dc_rate + ' %');
		$("#cur_price").text(Comma(goods.price));

        let isJsGoods = goods.ori_price == goods.goods_sh;

        let sale_type = document.getElementById("sale_type");
        $(sale_type).find("option").remove();
        sale_types.forEach((type, key) => {
            if(isJsGoods || (!isJsGoods && type.sale_apply === 'price' && ((type.amt_kind == 'per' && type.sale_per >= 0) || (type.amt_kind == 'amt' && type.sale_amt >= 0)))) {
				if (!type.brands || (type.brands?.split(',') || []).includes(goods.brand)) {
                    sale_type[sale_type.options.length] = new Option(type.sale_type_nm, type.sale_kind);
				}
            }
        });
        if(goods.sale_type != "") {
            $(sale_type).val(goods.sale_type).prop("selected", true);
        } else {
            $(sale_type).prop("selectedIndex", 0);
			updateOrderValue('sale_type', $(sale_type).val());
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

        // 쿠폰 조건에 따라 disabled 처리
        const selected_coupon_nos = list.filter(d => d.coupon_no !== '').map(d => d.coupon_no);
        const usable_coupon_nos = $("#coupon_no option").toArray()
            .filter(opt => {
                let d = opt.dataset;
                if (d.apply === 'AG' && d.ex_goods_nos.split(",").includes(goods.goods_no + '')) return false;
                if (d.apply === 'SG' && !d.goods_nos.split(",").includes(goods.goods_no + '')) return false;
                if (selected_coupon_nos.includes(opt.value) && goods.coupon_no !== opt.value) return false;
                return true;
            }).map(opt => opt.value);
        $("#coupon_no > option").toArray().forEach(opt => {
            $(opt).prop("disabled", !usable_coupon_nos.includes(opt.value));
            if (opt.value === goods.coupon_no) $(opt).prop("selected", true);
        });
    }

    /** 상품리스트에서 상품 삭제 */
    async function removeProduct(prd_cd = '') {
        if(prd_cd == '') return;

        let list = gx.getRows();
        let goods = list.find(g => g.prd_cd === prd_cd);

        if($("[name=cur_ord_state]").val() == ORD_STATE.WAITING) {
            $("[name=removed_goods]").val(($("[name=removed_goods]").val() || "") + "," + (goods.ord_opt_no || ""));
        }

        await gx.gridOptions.api.applyTransaction({remove: [goods]});
        updateOrderValue();
    }

    /** 수량 및 금액 변경 시 업데이트 */
    async function updateOrderValue(key = '', value = '', event = null) {
        if(key != '') {
            if(['card_amt', 'cash_amt', 'point_amt'].includes(key)) {
                if(key === 'point_amt') {
                    let max_point = unComma($("#user_point").text());
                    if(value > max_point) return alert(`보유적립금을 초과하여 사용할 수 없습니다.\n(보유적립금: ${max_point}원)`);
                }
                await $(`[name=${key}]`).val(value);
            }

            let curRow = gx.getSelectedNodes();
            if(curRow.length > 0) {
                let rowData = curRow[0].data;
                if(key === 'cur_qty') {
					let prd_cd = event;
					if (value < 1 || rowData.prd_cd !== prd_cd) return false;
                    $("#cur_qty").text(value);
					$("#cur_dc_rate").text(Comma(100 - Math.round(((rowData.price * value) - (rowData.coupon_discount_amt || 0)) / value / rowData.goods_sh * 100)) + ' %');
                    curRow[0].setData({
	                    ...rowData, 
	                    qty: value,
						dc_rate: 100 - Math.round(((rowData.price * value) - (rowData.coupon_discount_amt || 0)) / value / rowData.goods_sh * 100),
	                    total: (rowData.price * value) - (rowData.coupon_discount_amt || 0)
					});
                // 단가변경 기능 사용안함 - 20230314
                // } else if(key === 'cur_price') {
                    // $("#cur_price").text(Comma(value));
                    // curRow[0].setData({...rowData, price: value, total: rowData.qty * value});
                } else if(key === 'sale_type') {
                    let st = sale_types.find(s => s.sale_kind == value);
                    let std_price = st.sale_apply === 'tag' ? rowData.goods_sh : rowData.ori_price;
                    let discount_amt = st.sale_amt || 0;
                    if(st.amt_kind === 'per') discount_amt = std_price * ((st.sale_per || 0) / 100);
                    $("#cur_dc_rate").text(Comma(100 - Math.round((std_price - discount_amt) / rowData.goods_sh * 100)) + ' %');
                    $("#cur_price").text(Comma(std_price - discount_amt));
                    curRow[0].setData({
                        ...rowData,
                        sale_type: value,
                        price: std_price - discount_amt,
	                    dc_rate: 100 - Math.round((std_price - discount_amt) / rowData.goods_sh * 100),
                        total: rowData.qty * (std_price - discount_amt) - (rowData.coupon_discount_amt || 0)
                    });
                } else if(key === 'pr_code') {
                    curRow[0].setData({...rowData, pr_code: value});
                } else if(key === 'coupon_no') {
                    const cp = event.target.selectedOptions[0]?.dataset;
                    const discount_amt = cp.amt_kind === 'P'
                        ? Math.round(rowData.goods_sh * rowData.qty * ((cp.per || 0) * 1) / 100)
                        : ((cp.amt || 0) * 1);
					$("#cur_dc_rate").text(Comma(100 - Math.round(((rowData.price * rowData.qty) - discount_amt) / rowData.qty / rowData.goods_sh * 100)) + ' %');
                    curRow[0].setData({
                        ...rowData,
                        coupon_no: value,
                        c_no: cp.c_no,
                        coupon_discount_amt: discount_amt,
						dc_rate: 100 - Math.round(((rowData.price * rowData.qty) - discount_amt) / rowData.qty / rowData.goods_sh * 100),
                        total: rowData.price * rowData.qty - discount_amt
                    })
	                
	                $("#cur_price").text();
                }
            }
        }

        let list = gx.getRows();

        let goods_sh = list.reduce((a, c) => a + (c.goods_sh * c.qty), 0);
		let dc_amt = list.reduce((a, c) => a + ((c.goods_sh - c.price) * c.qty) + (c.coupon_discount_amt || 0), 0);
        let order_price = list.reduce((a, c) => a + c.total, 0);
        let order_qty = list.reduce((a, c) => a + c.qty, 0);
        let card_amt = $("[name=card_amt]").val() * 1;
        let cash_amt = $("[name=cash_amt]").val() * 1;
        let point_amt = $("[name=point_amt]").val() * 1;
        let payed_amt = card_amt + cash_amt + point_amt;

		$("#total_goods_sh_amt").text(Comma(goods_sh));
		$("#total_dc_amt").text(Comma(dc_amt));
        $("#total_order_amt").text(Comma(order_price));
        $("#total_order_amt2").text(Comma(order_price));
        $("#payed_amt").text(Comma(payed_amt));
        $("#change_amt").text(Comma(payed_amt - order_price > 0 ? payed_amt - order_price : 0));
        $("#due_amt").text(Comma(order_price - payed_amt > 0 ? order_price - payed_amt : 0));
        $("#total_order_qty").text(Comma(order_qty));

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
    function sale(reservation_yn = 'N') {
        if(!validate()) return;

        let card_amt = $("[name=card_amt]").val() * 1;
        let cash_amt = $("[name=cash_amt]").val() * 1;
        let point_amt = $("[name=point_amt]").val() * 1;
        let cart = gx.getRows();
        let memo = $("[name=memo]").val().trim();
        let ord_no = "";
        let removed_goods = [];

        // 판매유형이 '온라인판매'(81)일 경우, 특이사항에 온라인주문번호 등 해당정보를 반드시 기입할 수 있도록 설정
        if (cart.filter((c) => c.sale_type === '81').length > 0 && memo === '') {
            return alert("'온라인판매'의 경우, 특이사항에 온라인주문번호 등 해당정보를 반드시 기입해주세요.");
        }

        if($("[name=cur_ord_state]").val() == ORD_STATE.WAITING) {
            ord_no = $("#ord_no").text();
            removed_goods = $("[name=removed_goods]").val().split(",").filter(g => g !== '');
        }

        // 쿠폰사용 최저가최고가 체크
        const all_coupons = $("#coupon_no option").toArray().map(opt => ({ ...opt.dataset, coupon_no: opt.value }));
        const unavailable_coupons = cart.filter(c => {
            if (c.coupon_no === '') return false;

            let cp = all_coupons.find(coupon => coupon.coupon_no === c.coupon_no);
            if (cp === undefined) return false;

            if (cp.price_yn === 'Y' && (cp.low_price >= 0 && (cp.low_price > (c.ori_price * c.qty)) || (cp.high_price > 0 && cp.high_price < (c.ori_price * c.qty)))) return true;
            return false;
        });
        if (unavailable_coupons.length > 0) {
            let cp = all_coupons.find(coupon => coupon.coupon_no === unavailable_coupons[0].coupon_no);
            return alert(`[${cp.coupon_nm}]은 주문금액이 최소 ${Comma(cp.low_price)}원 / 최대 ${Comma(cp.high_price)}원인 상품에만 적용할 수 있습니다.`);
        }

        axios({
            async: true,
            url: '/shop/pos/save',
            method: 'post',
            dataType: "json",
            data: {
                ord_no,
                ord_state: '30', // 출고완료 처리
                card_amt,
                cash_amt,
                point_amt,
                cart,
                removed_cart: removed_goods,
                memo,
                user_id: $("#user_id_txt").text(),
                reservation_yn: reservation_yn
            },
        }).then(function (res) {
            if(res.data.code === '200') {
                alert("주문이 정상적으로 등록되었습니다.");
                initOrderScreen();
                setNewOrdNo(true);
                getOrderAnalysisData();
                searchWaiting();
            } else if(res.data.code !== '500') {
                if (res.data.code === '-105' && reservation_yn === 'N') {
                    if (confirm("재고가 부족한 상품이 있습니다.\n예약판매하시겠습니까?")) {
                        sale('Y');
                    }
                } else if (res.data.code === '-104') {
                    alert("재고가 부족한 상품이 있습니다. 재고가 1개 이상 존재할 때는 예약판매할 수 없습니다.");
                } else {
                    alert(res.data.msg);
                }
            } else {
                console.log(res);
                alert("주문등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    /** 주문등록 시 null check */
    function validate(is_new = true) {
        if(gx.getRows().length < 1) return alert("판매할 상품을 선택해주세요.");

        if(is_new) {
            let due_amt = unComma($("#due_amt").text());
            if(due_amt > 0) return alert("결제할 금액이 남아있습니다.");
        }

        return true;
    }

    /**
     * 대기
     * 대기기능제거(본사요청) - 20230314
     */
    // function waiting() {
    //     if(!validate(false)) return;

    //     let cart = gx.getRows();
    //     let memo = $("[name=memo]").val();
    //     let ord_no = "";
    //     let removed_goods = [];

    //     if($("[name=cur_ord_state]").val() == ORD_STATE.WAITING) {
    //         ord_no = $("#ord_no").text();
    //         removed_goods = $("[name=removed_goods]").val().split(",").filter(g => g !== '');
    //     }

    //     axios({
    //         async: true,
    //         url: '/shop/pos/save',
    //         method: 'post',
    //         dataType: "json",
    //         data: {
    //             ord_no,
    //             ord_state: '1', // 입금예정 처리
    //             card_amt: 0,
    //             cash_amt: 0,
    //             point_amt: 0,
    //             cart,
    //             removed_cart: removed_goods,
    //             memo,
    //             user_id: $("#user_id_txt").text(),
    //         },
    //     }).then(function (res) {
    //         if(res.data.code === '200') {
    //             initOrderScreen();
    //             setNewOrdNo(true);
    //             searchWaiting();
    //         } else if(res.data.code !== '500') {
    //             alert(res.data.msg);
    //         } else {
    //             console.log(res);
    //             alert("대기처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
    //         }
    //     }).catch(function (err) {
    //         console.log(err);
    //     });
    // }

    /** 고객검색 */
    function SearchMember() {
        let type = $("[name=search_member_type]").val();
        let keyword = $("[name=search_member_keyword]").val();

        let data = "search_type=" + type + "&search_keyword=" + keyword;
        gx3.Request("/shop/pos/search/member", data, 1);
    }

    /** 선택한 고객 정보 반영 */
    function setMember(user_id = '', user = null) {
        if(user_id == '' && user == null) {
            $("#no_user").removeClass("d-none");
            $("#no_user").addClass("d-flex");
            $("#user").addClass("d-none");
            $("#user_id_txt").text('');

            getUserCouponList();
            return;
        }

        let memb = user;
        if(user_id != '') {
            let list = gx3.getRows();
            memb = list.find(m => m.user_id === user_id);
            gx3.setRows([]);
        }

        if(memb.user_id) {
            $("#user_nm").text(memb.user_nm);
            $("#user_info").text(`(${memb.gender || '-'}, ${memb.yyyy ? `${memb.yyyy}.${memb.mm}.${memb.dd}` : '-'})`);
            $("#user_id_txt").text(memb.user_id);
            $("#user_phone").text(memb.mobile);
            $("#user_email").text(memb.email || "-");
            $("#user_address").text(memb.addr ? `${memb.addr} ${memb.addr2}` : "-");
            $("#user_point").text(Comma(memb.point || 0));

            $("#no_user").removeClass("d-flex");
            $("#no_user").addClass("d-none");
            $("#user").removeClass("d-none");

            getUserCouponList(memb.user_id);
        }

        if(user_id != '') {
            $('#searchMemberModal').modal('hide');
            $("#search_member_keyword").val('');
        } else if(user != null) {
            $('#addMemberModal').modal('hide');
            initAddMemberModal();
        }
    }

    /** 선택한 고객의 사용가능한 쿠폰목록 조회 */
    async function getUserCouponList(user_id = '') {
        let html = "<option value=''>-- 선택 안함 --</option>";

        if (user_id === '') {
            $("#coupon_no").html(html);
            return;
        }

        const { data: { body }, status } = await axios({ method: "get", url: "/shop/pos/search/member-coupon?user_id=" + user_id });
        if (status === 200) {
            html += body.reduce((a,c) => a + `
                <option value='${c.coupon_no}'
                    data-c_no='${c.c_no}'
                    data-coupon_nm='${c.coupon_nm}'
                    data-apply='${c.coupon_apply}'
                    data-goods_nos='${c.goods_nos}'
                    data-ex_goods_nos='${c.ex_goods_nos}'
                    data-amt_kind='${c.coupon_amt_kind}'
                    data-amt='${c.coupon_amt}'
                    data-per='${c.coupon_per}'
                    data-price_yn='${c.price_yn}'
                    data-low_price='${c.low_price}'
                    data-high_price='${c.high_price}'
                >
                    ${c.coupon_nm}(${c.coupon_amt_kind === 'P' ? c.coupon_per + '%' : c.coupon_amt + '원'})
                </option>
            `, "");
            $("#coupon_no").html(html);

            const nodes = gx.getSelectedRows();
            if (nodes.length > 0) {
                setProductDetail(nodes[0]?.prd_cd);
            } else {
                setProductDetail(gx.getRows()?.[0]?.prd_cd);
            }
        }
    }

    /** 고객 아이디 중복확인 */
    async function checkUserId() {
        let user_id = $("#user_id").val();
        if(user_id.trim().length < 1) return alert("아이디를 입력해주세요.");

        let { data: cnt, status } = await axios({ url: '/shop/member/mem01/check-id/' + user_id, method: 'get'});

        if(status != 200) return alert("중복확인 중 오류가 발생했습니다.\n다시 시도해주세요.");

        if(cnt > 0) {
            $("#user_id_check").val("N");
            alert("이미 사용중인 아이디입니다.");
        } else {
            $("#user_id_check").val("Y");
            alert("사용가능한 아이디입니다.");
        }
    }

    /** 고객 휴대폰 중복확인 */
    async function checkUserPhone() {
        let form = $("form[name=add_member]");
        if(!validateMember(getForm2JSON(form), true)) return;

        let { data, status } = await axios({ url: '/shop/pos/check-phone?' + form.serialize(), method: 'get'});
        if(status != 200) return alert("중복확인 중 오류가 발생했습니다.\n다시 시도해주세요.");

        if (data.mobile_cnt > 0) {
			$("#user_mobile_check").val("N");
			alert("해당 휴대폰을 사용하는 고객정보가 이미 존재합니다.");
		} else if (data.user_cnt > 0) {
            $("#user_mobile_check").val("N");
            alert("해당 휴대폰이 아이디로 등록된 고객정보가 이미 존재합니다.");
        } else {
            $("#user_mobile_check").val("Y");
            alert("사용가능한 휴대폰정보입니다.");
        }
    }

    /** 주소검색 */
    function openFindAddress(zipName, addName) {
        new daum.Postcode({
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다..
            oncomplete: function(data) {
                $("#" + zipName).val(data.zonecode);
                $("#" + addName).val(data.address);
            }
        }).open();
    }

    /** 고객등록 영역 초기화 */
    function initAddMemberModal() {
        document.add_member.reset();
        $("#user_id_check").val("N");
        $("#user_mobile_check").val("N");
    }

    /** 고객등록 */
    function addMember() {
        let form = $("form[name=add_member]");

        if(!validateMember(getForm2JSON(form))) return;

        axios({
            async: true,
            url: '/shop/pos/add-member',
            method: 'post',
            data: form.serialize(),
        }).then(function (res) {
            if(res.data.code === '200') {
                alert("고객정보가 정상적으로 등록되었습니다.");
                initAddMemberModal();
                setMember('', res.data.user);
            } else {
                console.log(res);
                alert("주문등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    /** 고객등록 시 null check */
    function validateMember(data, only_phone = false) {
        if (!only_phone) {
            if(data.name.trim().length < 1) return alert("이름을 입력해주세요.");
        }

        const mobile_reg = /^01(?:0|1|[6-9])$/;
        if (!mobile_reg.test(data.mobile1)) return alert("휴대폰 앞3자리를 정확하게 입력해주세요.");
        if (!data.mobile2 || !data.mobile3) return alert("휴대폰번호를 입력해주세요.");

        if (!only_phone) {
            if(data.user_mobile_check !== 'Y') return alert("휴대폰 중복확인을 진행해주세요.");
        }

		if (!only_phone) {
			if(data.id_mobile_same_yn !== 'Y' && data.user_id.trim().length < 1) return alert("아이디를 입력해주세요.");
			if(data.id_mobile_same_yn !== 'Y' && data.user_id_check !== 'Y') return alert("아이디 중복확인을 진행해주세요.");
		}
		
        return true;
    }

    function getForm2JSON($form){
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function(n, i){
            indexed_array[n['name']] = n['value'];
        });
        return indexed_array;
    }

    /** 판매내역 조회 */
    function SearchOrder(ord_no = '') {
        let sdate = $("[name=ord_sdate]").val();
        let edate = $("[name=ord_edate]").val();
		let keyword = $("[name=order_search_keyword]").val();
        let ord = $("#ord_field").val();
        let limit = $("#limit").val();

        let data = "sdate=" + sdate + "&edate=" + edate + "&keyword=" + keyword + "&ord=" + ord + "&limit=" + limit;

        if(ord_no != '') data += "&ord_no=" + ord_no;
        gx4.Request("/shop/pos/search/order", data, 1);
    }

    /** 판매내역 상세 조회 */
    async function setOrderDetail(ord_no = '') {
        if(ord_no === '') return;

        let res = await axios({ method: "get", url: "/shop/pos/search/order-detail?ord_no=" + ord_no });
        if(res.status === 200) {
            let data = res.data.data;
            let ord = data[0];
			if(!ord) return alert('해당 주문내역을 조회할 수 없습니다.');

            $("#od_ord_no").text(ord.ord_no);
            $("#od_ord_date").text(ord.ord_date);
            $("#od_ord_amt").text(Comma(data.reduce((a,c) => (c.goods_sh * c.qty) + a, 0)));

            $("#od_total_dc_amt").text(Comma((ord.total_dc_amt * -1) + (ord.total_coupon_amt * -1)));
            $("#od_dc_amt").text(Comma(ord.total_dc_amt * -1));
            $("#od_coupon_amt").text(Comma(ord.total_coupon_amt * -1));
            $("#od_point_amt").text(Comma(ord.total_point_amt * -1));
            $("#od_recv_amt").text(Comma(ord.total_recv_amt));
            $("#od_ord_qty").text(Comma(data.reduce((a,c) =>c.qty + a, 0)));

            $("#od_pay_type").text(ord.pay_type_nm.replaceAll("무통장", "현금"));
            $("#od_user_info").text((ord.user_nm || '') + (ord.user_id ? ` (${ord.user_id})` : ''));
            $("#od_phone").text(ord.mobile || "-");
            $("#od_dlv_comment").text(ord.dlv_comment || "-");

            let html = "";
            for(let o of data) {
                html += `
                    <tr>
                        <td class="pt-2 pb-2 pl-1">
                            <div class="position-relative d-flex flex-column align-items-start fs-08 pr-2">
                                <p class="fc-gray fs-08" style="text-decoration: underline;">No. ${o.ord_opt_no}</p>
                                <p class="fw-sb fs-09">${o.goods_nm}</p>
                                <p class="fc-white br-05 bg-gray pl-2 pr-2 mt-1 mb-1">${o.prd_cd || '-'}</p>
                                ${o.goods_opt.split("^").map(opt => `<p class="fc-gray fw-sb pl-3">&#8735; ${opt}</p>`).join("")}
                                ${o.clm_state == 61 ? `
                                    <div class="position-absolute d-flex flex-column justify-content-center align-items-center fc-red fs-20 fw-b w-75" style="top:50%;left:50%;transform:translate(-50%, -50%) rotate(-2deg);height:85px;border:3px solid #ED2939;text-shadow: -1px 0 #fff, 0 1px #fff, 1px 0 #fff, 0 -1px #fff;">
                                    	<span>환불완료</span>
										<span style="font-size:0.9rem;">${o.clm_state_date.slice(0,4) + '-' + o.clm_state_date.slice(4, 6) + '-' + o.clm_state_date.slice(6,8)}</span>
									</div>
                                ` : ''}
                            </div>
                        </td>
                        <td class="text-center">${Comma(o.price)}</td>
                        <td>
                            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                <p>${o.qty}</p>
                                ${o.clm_state != 61 && o.qty > 0 ? `<button type="button" class="butt fc-white fs-08 br-05 bg-red mt-1" style="width:60px;height:25px;" onclick="openRefundModal('${o.ord_no}', '${o.ord_opt_no}', ${o.qty}, ${o.recv_amt}, ${o.point_amt});">환불하기</button>` : ''}
                                ${o.ord_type === 4 ? `<button type="button" class="butt fc-white fs-08 br-05 bg-success mt-1" style="width:80px;height:25px;" onclick="setReservationOrdType('${o.ord_no}', '${o.ord_opt_no}');">예약상품지급</button>` : ''}
                            </div>
                        </td>
                        <td class="pt-2 pb-2 pr-1">
                            <div class="d-flex flex-column align-items-end">
                                ${(o.coupon_amt > 0) ? `
                                    <span class="text-white fs-08 fw-sb br-05 bg-info pl-2 pr-2 mb-1">쿠폰사용</span>
                                ` : ''}
                                ${(o.sale_amount > 0 || o.sale_kind == '99') ? `
                                    <span class="text-white fs-08 fw-sb br-05 bg-warning pl-2 pr-2">${o.sale_type_nm}</span>
                                ` : ''}
                                ${(o.sale_amount > 0 || o.sale_kind == '99' || o.coupon_amt > 0) ? `
                                    <del class="fc-gray fs-08">${Comma(o.qty * o.price)}</del>
                                ` : ''}
                                <p class="fw-sb">${Comma(o.recv_amt + o.point_amt)}</p>
                            </div>
                        </td>
                    </tr>
                `;
            }
            $("#od_prd_list").html(html);
        }
    }

    /** 매장환불 모달 세팅*/
    function openRefundModal(ord_no, ord_opt_no, qty, amt, point_amt) {
        $("[name=ord_no]").val(ord_no);
        $("[name=ord_opt_no]").val(ord_opt_no);
        $("#store_clm_qty").text(qty);
        $("#store_refund_amt").text(Comma(amt));
        $("#store_refund_point_amt").text(Comma(point_amt));

        $("[name=store_clm_reason]").prop("selectedIndex", 0);
        $("[name=store_refund_bank]").val('');
        $("[name=store_refund_nm]").val('');
        $("[name=store_refund_account]").val('');
        $("[name=store_refund_memo]").val('환불완료 (매장환불)');

        $('#StoreClaimModal').modal({
            keyboard: false
        });
    }

    /** 매장환불완료처리 */
    function refundStoreOrder() {
        if($("[name=store_clm_reason]").val() == '') return alert("환불사유를 선택해주세요.");

        const frm = $("form[name=store_refund]");

        axios({
            url: '/shop/stock/stk03/order/store_refund',
            method: 'post',
            data: frm.serialize(),
        }).then(function (res) {
            if(res.data.code == 200) {
                alert("환불되었습니다.");
                let ord_no = $("[name=ord_no]").val();
                setOrderDetail(ord_no);
                $('#StoreClaimModal').modal('hide');
            } else {
                alert(res.data.msg);
                console.log(res);
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    /** 주문번호로 환불할 주문건 검색 */
    async function searchOrderByOrdNo(ord_no) {
        const { status, data } = await axios({
            url: '/shop/pos/search/order-by-ordno?ord_no=' + ord_no,
            method: 'get'
        });

        let html = "";
        if(status == 200) {
            if(data.code == 200 && data.ord_no != '') {
                html = `
                    <button type="button" class="butt d-flex align-items-center fs-12 bg-white" onclick="moveToOrderDetail('${data.ord_no}');">
                        <i class="bx bx-receipt mr-2" aria-hidden="true"></i>
                        <p class="fc-blue">${data.ord_no}</p>
                    </button>
                `;
            } else {
                html = `<p class="fc-gray fs-10">검색하신 주문번호에 해당하는 주문건이 존재하지 않습니다.</p>`;
            }
        } else {
            html = `<p class="fc-gray fs-10">검색 중 오류 발생</p>`;
        }
        $("#search_ord_no_result").html(html);
    }

    /** 예약판매된 상품 지급처리 (예약주문건 정상주문처리) */
    function setReservationOrdType(ord_no, ord_opt_no) {
        if (!confirm("예약판매된 해당상품을 지급완료처리하시겠습니까?")) return;

        axios({
            url: '/shop/pos/complete-reservation',
            method: 'post',
            data: { ord_no, ord_opt_no },
        }).then(function (res) {
            if(res.data.code == 200) {
                setOrderDetail(ord_no);
            } else {
                alert(res.data.msg);
                console.log(res);
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    /** 해당 주문건 상세로 이동 */
    function moveToOrderDetail(ord_no) {
        setScreen('pos_today');
        SearchOrder(ord_no);
        setOrderDetail(ord_no);

        $('#searchOrdNoModal').modal('hide');
        $("#search_ord_no").val('');
        $("#search_ord_no_result").html('');
    }

    /** 쿠폰등록 시 휴대폰번호 뒷자리로 고객정보 검색 */
    async function searchUserInCouponModal() {
        const keyword = $("#cp_user_phone").val();

        if (keyword == '') return $('#cp_user_phone').trigger('focus');

        const { data, status } = await axios({
            method: 'get',
            url: "/shop/pos/search/member?search_type=phone&search_keyword=" + keyword
        });
        if (status === 200) {
            const users = data.body;
            let html = "";

            if (users.length < 1) {
                html += "<option value=''>-- 선택 안함 --</option>";
            } else {
                html += users.reduce((a, c) => a + `<option value="${c.user_id}">[${c.user_id}] ${c.user_nm} ${c.mobile}</option>`, "");
            }
            $("#cp_user_id").html(html);
        }
    }

    /** 오프라인 쿠폰 등록 */
    function addCoupon() {
        const user_id = $("#user_id_txt").text().trim();
		const serial_num = $("#cp_serial_num").val().trim();

        if (user_id === '') {
			alert("고객정보를 선택해주세요.");
			$('#searchMemberModal').modal('show');
			return false;
        }
        if (serial_num === '') return alert("쿠폰의 시리얼넘버를 입력해주세요.");

        axios({
            url: '/shop/pos/add-coupon',
            method: 'post',
            data: { user_id, serial_num },
        }).then(function (res) {
            if(res.data.code == 200) {
				alert("쿠폰이 정상적으로 등록되었습니다.");
				getUserCouponList(user_id);
				$("#cp_serial_num").val('');
            } else {
                alert(res.data.msg);
                console.log(res);
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    /** ETC */

    function leftPad(value) {
        if (value >= 10) return value;
        return `0${value}`;
    }

    function getFormatDate(date, delimiter = '-') {
        const year = date.getFullYear();
        const month = leftPad(date.getMonth() + 1);
        const day = leftPad(date.getDate());

        return [year, month, day].join(delimiter);
    }

    function getClock() {
        const date = new Date();
        const year = String(date.getFullYear());
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        const hours = String(date.getHours()).padStart(2, "0");
        const min = String(date.getMinutes()).padStart(2, "0");
        const sec = String(date.getSeconds()).padStart(2, "0");
        $("#clock").text(`${year}년 ${month}월 ${day}일 ${hours}:${min}:${sec}`);
    }

</script>
