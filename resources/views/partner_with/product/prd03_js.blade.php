<script type="text/javascript">

    $(document).ready(function() {
    });

    $(".del-btn").on("click", function(){
        var selectrows = gx.getSelectedRows();
        if(selectrows.length === 0){
            alert('삭제할 자료를 선택 해 주십시오.');
        } else {
            if(confirm('삭제하시겠습니까?')){
                gx.delSelectedRows();
            }
        }
    });

    $(".copy-btn").on("click", function() {
        var url = '/partner/product/prd01/choice';
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    });

    $(".add-btn").on("click", function(){
        var product = [{
            "goods_no":"",
            "goods_nm":"상품명",
            "com_nm":"",
            "opt_kind_nm":"",
            "brand_nm":"",
            "full_nm":"",
            "style_no":"",
            "sale_stat_cl":"",
            "img":"",
            "head_desc":"",
            "goods_nm":"",
            "ad_desc":"",
            "before_sale_price":"",
            "price":"",
            "wonga":"",
            "margin_rate":"",
            "margin_amt":"",
            "option_type":"",
            "option":"",
            "md_nm":"",
            "baesong_info":"",
            "baesong_kind":"",
            "dlv_pay_type":"",
            "baesong_price":"",
            "point":"",
            "org_nm":"",
            "make":"",
            "goods_cont":"",
            "goods_location":"",
            "goods_type":"상품설명",
            "com_type":"",
        }];
        gx.addRows(product);
    });

    $(".save-btn").on("click", function(){
        var selectrows = gx.getSelectedRows();
        if(selectrows.length === 0) {
            alert('추가 할 자료를 선택 해 주십시오.');
        } else {
            console.log(selectrows[0]);
            product = selectrows[0];
            console.log(product);
            if (confirm('저장하시겠습니까?')) {
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd03/save',
                    data: product,
                    success: function (res) {
                        console.log(res.responseText);
                        alert('저장하였습니다.');
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            }
        }
    });

    /**
     * @return {boolean}
     */
    function ChoiceGoodsNo(goods_nos){
        cnt = $("#cnt").val();
        for(var i=0;i<goods_nos.length;i++){
            goods_no = goods_nos[i];
            $.ajax({
                method: 'get',
                dataType:"JSON",
                url: '/partner/product/prd03/' + goods_no + '/get',
                success: function (res) {
                    if(cnt > 0){
                        for(var j=0;j<cnt;j++){
                            res["goods_no"] = "";
                            gx.addRows([res]);
                        }
                        $("#gd-total").text(numberWithCommas(gx.getRowCount()));
                    } else {
                        res["goods_no"] = "";
                        gx.addRows([res]);
                        $("#gd-total").text(numberWithCommas(gx.getRowCount()));
                    }
                    console.log(res);
                },
                error: function(e) {
                    console.log(e.responseText)
                }
            });
        }
        return true;
    }

</script>
