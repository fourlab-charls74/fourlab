<script type="text/javascript">

    /** 화면전환 */
    function setScreen(idx) {
        $("#pos > div:not(#pos_header)").removeClass("d-flex").addClass("d-none");
        $("#" + idx).addClass("d-flex");

        $("#home_btn").css("display", idx === "pos_order" ? "none" : "inline-block");
    }

    /** 상품검색 */
    function Search() {
        let prd_cd = $("[name=prd_cd]").val();
        let prd_nm = $("[name=prd_nm]").val();

        console.log(prd_cd, prd_nm);
    }

    /** 전체취소 */
    function cancelOrder() {
        if(!confirm("해당주문건을 취소하시겠습니까?")) return;
        setScreen('pos_main');
    }
    
    /** 상품리스트에 상품 추가 */
    function addProduct(prd_cd = '') {
        $('#searchProductModal').modal('hide');
    }
    
    /** 신용카드 금액 적용 */
    function setOrderPrice() {
        $('#cardModal').modal('hide');
    }

</script>