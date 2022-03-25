<script type="text/javascript" charset="utf-8">
    const goods_no = '{{$goods_no}}';
    const goods_sub = '{{@$goods_info->goods_sub}}';

    var ed;

    $(document).ready(function() {
        var editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview','help']]
        ];
        var editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 100,
            height: 150,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload:{
                dir:'/images/goods_cont',
                maxWidth:1280,
                maxSize:10
            }
        }
        ed = new HDEditor('.editor1',editorOptions, true);

        const TYPE = "{{$type}}";
        if (TYPE == "create") {
            document.f1.reset();
        }

    });


    $("#search_brand_nm").keyup(function(e){
        if(e.keyCode == 13){
            search_brand();
        }
    });

    function PopPrdDetail(goods_no, goods_sub){
        window.open("/partner/product/prd01/"+goods_no,"Product Detail");
    }

    $(".btn-rep-add").click(function(){ addRepCategory(); });

    $(".btn-item-add").click(function(){ addSearchCategory('item'); });
    $(".btn-display-add").click(function(){ addSearchCategory('display'); });

    $(".btn-item-delete").click(function(){ deleteCategory('item') });
    $(".btn-display-delete").click(function(){ deleteCategory('display') });


    function addRepCategory()
    {
        var cat_type	= "display";

        searchCategory.Open(cat_type.toUpperCase(), function(code, name, full_name, mx_len)
        {
            if(code.length < mx_len)
            {
                alert("최하단에서만 등록 가능합니다");
                return false;
            }
            $("[name=rep_cat_cd]").val(code);
            $('#txt_rep_cat_nm').html(full_name);
            addCategory(cat_type, code, name, full_name, mx_len);
        });
    }

    function addSearchCategory(cat_type)
    {
        searchCategory.Open(cat_type.toUpperCase(), function(code, name, full_name, mx_len)
        {
            addCategory(searchCategory.type, code, name, full_name, mx_len);
        });
    }

    function addCategory(cat_type, code, name, full_name, mx_len)
    {
        if(code.length < mx_len)
        {
            alert("최하단에서만 등록 가능합니다");
            return false;
        }

        cat_type = cat_type.toLowerCase();
        const options	= $('#category_select_'+cat_type+' option');

        var codes = [];
        $.each(options, function(idx, option){
            if(option.value !== ""){
                const txt = option.value.split("|");
                if (txt[0] === code) {
                    alert("중복된 카테고리가 있습니다.");
                    return false;
                }
                codes.push(txt[0]);
            }
        });

        var seq = 1;
        $('#category_select_'+cat_type+' option[value=""]').remove();

        // 전시 뎁스
        arr_txt	= full_name.split('>');
        cat_txt	= "";

        for( i = 0; i < arr_txt.length; i++ )
        {
            options.length++;
            if( i !== 0 )	cat_txt = cat_txt + ">";
            cat_txt	+= arr_txt[i];
            icode	= code.substring(0,(i*3)+3);
            if(codes.includes(icode) === false){
                $('#category_select_'+cat_type).append(`<option value="${icode}|${seq}|Y">${cat_txt} - ${icode}</option>`);
            }
        }
    }

    function deleteCategory(cat_type)
    {
        if( $("#category_select_"+cat_type+" option:selected").val() == undefined )
        {
            alert("삭제할 카테고리를 선택해 주십시오.");
            return false;
        }
        var ar		    = $("#category_select_"+cat_type+" option:selected").val().split('|');
        var d_cat_cd	= ar[0];

        const options	= $('#category_select_'+cat_type+' option');
        $.each(options, function(idx, option){
            var ar2		= option.value.split("|");
            if( d_cat_cd === ar2[0].substring(0, d_cat_cd.length) )
            {
                option.remove();
            }
        });
    }

    function SelectCompany()
    {
        searchCompany.Open(function(com_cd, com_nm, com_type, baesong_kind, baesong_info, margin_type, dlv_amt){
            /*
            console.log(com_cd);
            console.log(com_nm);
            console.log(com_type);
            console.log(baesong_kind);
            console.log(baesong_info);
            console.log(margin_type);
            console.log(dlv_amt);
            */

            if( com_type == "3" || com_type == "4" || com_type == "5" || com_type == "9" || com_type == "999" )
            {
                alert('공급업체 또는 입점업체의 상품만 등록하실 수 있습니다.');
                return false;
            }
            else if( com_type == "2" )
            {	// 입점업체
                $('#com_id').val(com_cd);
                $('#com_nm').val(com_nm);
                $('#com_type').val(com_type);
                $('#margin_type').val(margin_type);
                $('#goods_type').val("P");
                $('#bae_yn').val("N");

                if( dlv_amt > 0 )	$('#bae_yn').val("Y");

                $('#baesong_price').val(Comma(dlv_amt));
                $('#is_unlimited1').attr('checked', true);
                $('#is_unlimited2').attr('disabled', false);

                $('#baesong_kind').val(baesong_kind);
                $('#baesong_info').val(baesong_info);
            }
            else if( com_type == "1" )
            {	// 공급업체
                $('#com_id').val(com_cd);
                $('#com_nm').val(com_nm);
                $('#com_type').val(com_type);
                $('#margin_type').val(margin_type);
                $('#goods_type').val("S");
                $('#bae_yn').val("N");

                if( dlv_amt > 0 )	$('#bae_yn').val("Y");

                $('#baesong_price').val(Comma(dlv_amt));
                $('#is_unlimited1').attr('checked', true);
                $('#is_unlimited2').attr('disabled', false);

                $('#baesong_kind').val(baesong_kind);
                $('#baesong_info').val(baesong_info);
            }

        });
    }

    $(".btn-select-company").click(function(){ SelectCompany() });

    function change_dlv_cfg_form(value){
        $(".dlv_config_detail_div").css("display","none");
        $("#dlv_config_detail_"+value+"_div").css("display","inline");
    }

    function change_point_cfg_form(value){
        $(".point_config_detail_div").css("display","none");
        $("#point_config_detail_"+value+"_div").css("display","inline");

        if( value == "g" )
            $('#point').prop("readonly",false);
        else
            $('#point').prop("readonly",true);
    }

    if($("#new_product_type2").is(":checked")){
        $("#new_product_day").css('display','block');
    }
    function display_new_prd_day(value){
        if(value == "y"){
            $("#new_product_day").css('display','block');
        }else{
            $("#new_product_day").css('display','none');
        }
    }

    function validate(){
        var f = document.f1;

        if( $('#rep_cat_cd').val() == "" ){
            alert("대표카테고리를 선택해 주십시오.");
            return false;
        }

        if( $("#goods_nm").val() == "" ){
            alert("상품명을 입력해 주십시오.");
            $("#goods_nm").focus();
            return false;
        }
        if( $("#goods_nm").val().match(/[',|]/) ){
            alert("상품명에 특수문자(\',|)를 입력할 수 없습니다.");
            $("#goods_nm").focus();
            return false;
        }
        if( $("#goods_nm_eng").val() == "" ){
            alert("상품명(영문)을 입력해 주십시오.");
            $("#goods_nm_eng").focus();
            return false;
        }
        if( $("#opt_kind_cd").val() == "" ){
            alert("품목을 선택해 주십시오.");
            $("#opt_kind_cd").focus();
            return false;
        }
        if( $("#brand_nm").val() == "" ){
            alert("브랜드를 선택해 주십시오.");
            $("#brand_nm").focus();
            return false;
        }
        if( $('#sale_stat_cl').val() == "" ){
            alert("상품상태를 선택해 주십시오.");
            $('#sale_stat_cl').focus();
            return false;
        }
        if( $("#style_no").val() == "" ){
            alert("스타일넘버를 입력해 주십시오.");
            $("#style_no").focus();
            return false;
        }
        if( $('#goods_type').val() == "" ){
            alert('상품구분을 선택해 주십시오.');
            $('#goods_type').focus();
            return false;
        }
        if( $('#com_id').val() == "" ){
            alert('업체를 선택해 주십시오.');
            $('#com_nm').focus();
            return false;
        }
        if( $("#org_nm").val() == "" ) {
            alert("원산지를 입력해 주십시오.");
            $("#org_nm").focus();
            return false;
        }
        if( $("#price").val() == "0" ) {
            if( !confirm("입력하신 정상가는 0원 입니다. 저장 하시겠습니까?") ){
                $("#price").focus();
                return false;
            }
        }
        if( $('#tax_yn').val() == "" ){
            alert("과세구분을 선택해 주십시오.");
            $('#tax_yn').focus();
            return false;
        }
        if( $("#md_id").val() == "" ){
            alert("MD를 선택해 주십시오.");
            $("#md_id").focus();
            return false;
        }
        if( $("#baesong_info").val() == "" ){
            alert("배송지역를 선택해 주십시오.");
            $("#baesong_info").focus();
            return false;
        }
        if( $("#baesong_kind").val() == "" ){
            alert("배송업체를 선택해 주십시오.");
            $("#baesong_kind").focus();
            return false;
        }
        if( $('#dlv_fee_cfg').val() == "G" ){
            if( $('#bae_yn').val() == "Y" && $('#baesong_price').val() == "" ){
                alert("배송비를 입력해 주십시오.");
                $('#baesong_price').focus();
                return false;
            }
        }
        if( $('#point_cfg').val() == "G" )
        {
            if( $('#point_yn').val() == "Y" && $('#point').val() == "" ){
                alert("지급 적립금을 입력해 주십시오.");
                $('#point').focus();
                return false;
            }

            if( $('#point_unit').val() == "P" && $('#point').val() >= 100 ){
                alert("적립금을 100% 이상 지급할 수 없습니다.");
                $('#point').val(0);
                $('#point').focus();
                return false;
            }
        }

        return true;
    }

    //store
    $('.save-btn').click(function(){
        if (!validate()) return;

        $("#restock:checked").attr("name", "restock_yn");

        let frm	= $("#f1");
        let d_cat_str	= "";
        let u_cat_str	= "";
        let md_nm	= $('#md_id > option:selected').html();

        md_nm = md_nm.replace(/(\s)|(\t)|(\n)/g, "");

        $('#md_nm').val(md_nm);
        $("#goods_cont").val(ed.html());

        //전시 카테고리 전송값
        $("#category_select_display option").each(function(){
            if( $(this).val() != "" ){
                //d_cat_str	+= ","+$(this).text();
                d_cat_str	+= ","+$(this).val();
            }
        });

        $("#d_category_s").val(d_cat_str);

        //용도 카테고리 전송값
        $("#category_select_item option").each(function(){
            if($(this).val() !="") {
                //u_cat_str += ","+$(this).text();
                u_cat_str += ","+$(this).val();
            }
        });

        $("#u_category_s").val(u_cat_str);

            @if ($type === '')
        const type	= 'put';
            @else
        const type	= 'post';
        @endif

        $.ajax({
            async: true,
            type: type,
            url: '/partner/product/prd01',
            data: frm.serialize(),
            success: function (data) {
                if (!isNaN(data * 1)) {
                    const TYPE = "{{$type}}";
                    if (TYPE == "create") {
                        alert("상품이 등록되었습니다.");
                    } else {
                        alert("변경된 내용이 정상적으로 저장 되었습니다.");
                        location.href="/partner/product/prd01/" + data;
                    }
                }
            },
            error: function(e) {
                console.log(e.responseText)
            }
        });
    });

    $('[name=new_product_day]').change(function(){
        this.value = this.value.replace(/[-]/g, '');
    });

    $(".sch-goods-category").click(function(e){
        e.preventDefault();
        searchCategory.Open('DISPLAY', function(code, name, full_name){
            $("#goods_cat_cd").val(code);
            $(".goods_cat_nm").html(full_name);
        });
    });
    $("#img-setting").click(function(){
        //console.log('image');
        @if( $type == 'create' )
        alert('상품을 먼저 등록 하신 후 이미지를 등록할 수 있습니다.');
        @else
        window.open("/partner/product/prd02/"+goods_no+"/image","_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=960");
        @endif
    });

    $("#img-show").click(function(){
        window.open($(this).attr("data-no"));
    });

    //수정 페이지에서 복사버튼 클릭했을 경우.
    $(".copy-btn").click(function(e){
        e.preventDefault();

        location.href="/partner/product/prd01/"+goods_no+"?type=copy";
    });

    //배송비 지불 방식을 선택했을 경우.
    $('[name=dlv_pay_type]').change(function(e){
        if (this.value !== 'F') return;
        alert('현재 선불방식만 사용하실 수 있습니다.');
        $("#dlv_pay_type1").click();
    });

    //배송비 개별설정에서 유료인지 무료인지 선택했을 경우.
    $('[name=bae_yn]').change(function(){
        $("#baesong_price").attr('readonly', this.value === 'N');
    });

    $('#price, #goods_price, #margin').keyup(function(){

        if( $('#goods_type').val() == '' ){
            alert("상품구분을 선택해주십시오.");
            $('#price').val('0');

            return false;
        }

        if( $('#margin').val() > 100 ){
            alert("마진율은 100%를 넘을 수 없습니다.");
            $('#margin').val('0');

            return false;
        }

        if( $('#goods_type').val() == "P" ){
            var price	= unComma($('#price').val());
            if(price > 0){
                var margin	= unComma($('#margin').val());
                if( margin == '' )	margin = 0;
                var wonga = parseInt(Math.round(price * (1-margin/100)),10);
                $("#wonga").val(Comma(wonga));
            }
        }
        else if( $('#goods_type').val() == "S" ){
            //공급업체
            var price	= unComma($('#price').val());
            var margin	= unComma($('#margin').val());
            var wonga	= unComma($('#wonga').val());

            @if( $type == 'create' )
            if( price > 0 ){
                if( margin == '' )	margin = 0;
                var wonga = parseInt(Math.round(price * (1-margin/100)),10);
                $("#wonga").val(Comma(wonga));
            }
            @else
            if( wonga > 0 ){
                if( this.id == "margin" ){
                    price	= parseInt(Math.round(wonga / (1 - (margin / 100))),10);
                    $("#price").val(Comma(price));
                }else if( this.id == "price" ){
                    margin	= parseFloat(((price - wonga) / price) * 100).toFixed(2);
                    $("#margin").val(margin);
                }
            }
            @endif

            $('#price').val(numberFormat(price));

        }

    });

    $('.stock-stk-btn').click(function(){
        window.open("/partner/product/prd01/"+goods_no+"/in-qty","_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=800");
    });

    //상품정보고시 카드가 나올경우만 실행
    if( $('#goods-class-grid').length > 0 ){

        //선택한 항목 상태변경
        $('.goods-info-change-btn').click(function(e){
            e.preventDefault();

            const s_goods_class_cd	= $('.goods_class').val();
            // const s_goods_class_nm	= $('.goods_class > option:selected').html();

            if( s_goods_class_cd === '' ) {
                alert('품목변경할 정보고시내용을 선택해주세요.');
                return;
            }

            goodsClassSearch();
            // row	= gx.gridOptions.api.getRowNode(0);
            // row.data.class		= s_goods_class_nm.replace(/(\s)|(\t)|(\n)/g,"");
            // row.data.class_cd	= s_goods_class_cd;
            // gx.gridOptions.api.redrawRows({
            //     rowNodes : [row]
            // });
        });

        //선택된 상품정보고시 저장
        $('.goods-info-save-btn').click(function(e){
            e.preventDefault();

            classes	= gx.getRows();
            //return;
            $.ajax({
                async: true,
                type: 'put',
                url: `/partner/product/prd01/goods-class-update`,
                data: {'goods_no':goods_no,'goods_sub':goods_sub,'classes':classes},
                success: function (data) {
                    alert("저장하였습니다.");
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText)
                }
            });
        });

        //선택된 상품정보고시 삭제
        $('.goods-info-delete-btn').click(function(e){

            if( confirm("삭제하시겠습니까?") ){

                $.ajax({
                    async: true,
                    type: 'put',
                    url: `/partner/product/prd01/goods-class-delete`,
                    data: {'goods_no':goods_no,'goods_sub':goods_sub},
                    success: function (data) {
                        alert("삭제하였습니다.");
                    },
                    error: function(request, status, error) {
                        console.log("error")
                    }
                });
            }
        });

        gx.gridOptions.getRowNodeId = function(data) {
            return data.rownum;
        };

        function goodsClassSearch() {
            const class_value = $('.goods_class').val();
            const data = `goods_no=${goods_no}&goods_sub=${goods_sub}&goods_class=${class_value}`;
            gx.Request(`/partner/product/prd01/${goods_no}/goods-class`, data, -1);
        }
        goodsClassSearch();
    }

    $(document).ready(function(){
        var popSlideWidth = 0;
        var popSlideWrap = $(".cum_slider_thum");
        var ouTnum = 0;
        var end = 0;
        var $dots2 = 0;
        var pgwidth = 0;
        var selectImg = $(".cum_slider_cont img");
        var selectIdx = 0;
        $(".cum_slider_thum_wrap ul li").each(function(e){
            popSlideWidth += $(this).outerWidth() + parseInt($(this).css("margin-left"));
            if($(".cum_slider_thum_wrap ul li").last().index() == e){
                popSlideWrap.css("width", popSlideWidth+"px");
            }
        });
        end = $(".cum_slider_thum_wrap").outerWidth() - popSlideWidth;
        $dots2 = $(".cum_slider_thum_wrap ul li");
        pgwidth = ($dots2.width()+10) * $(".cum_slider_thum_wrap").width() / $dots2.width();
        popSlideWrap.css({left:0});
        $(".cum_slider_thum li a").on("click", function(){
            var elMargin = parseInt($(this).parent().css("margin-left"));
            var elWrapWidth = ($(".cum_slider_thum_wrap").outerWidth() / 2) + elMargin;
            var elWidth = $(this).parent().outerWidth() + elMargin;
            var num = ((($(this).parent().index() * elWidth)+(elWidth/2)))-elWrapWidth+5;
            ouTnum = num * -1;
            if(!$(this).hasClass("active")){
                if(num > 0){
                    if($(".cum_slider_thum_wrap").outerWidth() < popSlideWidth){
                        if(num*-1 < end){
                            popSlideWrap.animate({left:end},200);
                        }else{
                            popSlideWrap.animate({left:num * -1},200);
                        }
                    }
                }else{
                    popSlideWrap.animate({left:0},200);
                }
                $(".cum_slider_thum_wrap ul li a").removeClass("active");
                if(selectIdx != $(this).parent().index()){
                    $(this).addClass("active");
                    selectIdx = $(this).parent().index();
                    selectImg.clearQueue();
                    selectImg.css("opacity", "0").attr({
                        "src" : $(this).find("img").attr("src"),
                        "alt" : $(this).find("img").attr("alt")
                    });
                    selectImg.animate({"opacity" : "1"},500);
                }
            }
            return false;
        });

        $(".pop_sd_btn").on("click", function(){
            console.log(ouTnum,pgwidth,ouTnum-pgwidth);
            if($(this).hasClass("sd_next")){
                if(ouTnum-pgwidth < end){
                    popSlideWrap.animate({left:end},200);
                    ouTnum = end;
                }else{
                    popSlideWrap.animate({left: ouTnum -pgwidth},200);
                    ouTnum = ouTnum - pgwidth;
                }
            }else{
                if(ouTnum+pgwidth >= 0){
                    popSlideWrap.animate({left: 0},200);
                    ouTnum = 0;
                }else{
                    popSlideWrap.animate({left: ouTnum + pgwidth},200);
                    ouTnum = ouTnum + pgwidth;
                }
            }
        });
    });

    //ESC 클릭시 창 닫기
    $(document).keydown(function(e){
        // ESCAPE key pressed
        if (e.keyCode == 27) {
            window.close();
        }
    });

</script>



<script language="javascript">

    const pApp1 = new App('',{
        gridId:"#div-gd-optkind",
    });
    let gx1;

    const pApp2 = new App('',{
        gridId:"#div-gd-opt",
    });
    let gx_opt = null;
    let gridOptDiv = document.querySelector("#div-gd-option");

    var columns_stock = [
        {field: "option", headerName: "옵션", width: 200, sortable: "true"},
    ];

    //상품 옵션 종류
    if( $('#div-gd-optkind').length > 0 ){
        var columns_optkind = [
            {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
            {field:"no",headerName:"번호",
                cellRenderer: function(params) {
                    if (params.value !== undefined) {
                        if(params.data.type === "extra"){
                            return '<a href="javascript:void(0);" onclick="return Search_optextra(' + params.data.goods_no + ',\'\');">' + params.value + '</a>';
                        } else {
                            return params.value;
                        }
                    }
                }
            },
            {field:"type",headerName:"유형", width:70, cellStyle:{"text-align":"center"},
                editable: function(params){ return (params.data !== undefined && params.data.no > 0)? false:true; },
                cellClass:['hd-grid-edit'],
                //cellEditor: 'agRichSelectCellEditor',
                //cellEditorParams: cellOpionsParams,
                cellEditorSelector: function(params) {
                    return {
                        component: 'agRichSelectCellEditor',
                        params: {
                            values: ['basic','extra']
                        }
                    };
                }
            },
            {field:"name",headerName:"옵션구분", width:120,editable:true,cellClass:['hd-grid-edit'] },
            {field:"required_yn",headerName:"필수", width:70, cellStyle:{"text-align":"center"} },
            {field:"use_yn",headerName:"사용", width:70, cellStyle:{"text-align":"center"}},
    ];

        $(document).ready(function() {
            let gridDiv = document.querySelector(pApp1.options.gridId);
            gx1 = new HDGrid(gridDiv, columns_optkind);

            Search_optkind();

        });

        function Search_optkind() {
            gx1.Request(`/partner/product/prd01/${goods_no}/get-option-name`, '', -1);
        }
    }

    //상품 옵션 재고
    if( $('#div-gd-opt').length > 0 ){
        $(document).ready(function() {
            Search_opt();
        });
        function Search_opt() {
            gx2.Request(`/partner/product/prd01/${goods_no}/get-option`, '', -1);
        }
    }

    $(".optionkind-add-btn").on("click", function(){
        gx1.addRows([{
            "chk":0,
            "type":'basic',
            "name" : '',
            "required_yn" : 'Y',
            "use_yn" : 'Y',
            "no" : '',
        }]);
    });

    $(".optionkind-del-btn").on("click", function(){
        var selectrows = gx1.getSelectedRows();
        if(selectrows.length === 0){
            alert('삭제할 옵션구분을 선택 해 주십시오.');
        } else {
            if(confirm('삭제하시겠습니까?')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/del-option-name',
                    data:{'optionkinds':selectrows},
                    success: function (res) {
                        gx1.delSelectedRows();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });

    $(".optionkind-sav-btn").on("click", function(){
        var selectrows = gx1.getSelectedRows();
        if(selectrows.length === 0){
            alert('저장할 옵션구분을 선택 해 주십시오.');
        } else {
            if(confirm('저장하시겠습니까?')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/save-option-name',
                    data:{'optionkinds':selectrows},
                    success: function (res) {
                        alert('저장하였습니다.');
                        Search_optkind();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });

    $(".option-add-btn").on("click", function(){
        var selectrows = gx1.getSelectedRows();
        if(selectrows.length === 0){
            alert('추가할 옵션구분을 선택 해 주십시오.');
        } else {
            if(selectrows[0]["no"] > 0){
                gx2.addRows([{
                    "chk":0,
                    "name":selectrows[0]["name"],
                    "option" : '',
                    "price" : 0,
                    "memo" : '',
                    "option_no" : selectrows[0]["no"],
                    "no" : '',
                }]);
            } else {
                alert('추가할 옵션구분을 저장 후 선택 해 주십시오.')
            }
        }
    });

    $(".option-sav-btn").on("click", function(){
        var selectrows = gx2.getSelectedRows();
        if(selectrows.length === 0){
            alert('저장할 옵션을 선택 해 주십시오.');
        } else {
            if(confirm('저장하시겠습니까?')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/save-option',
                    data:{'options':selectrows},
                    success: function (res) {
                        alert('저장하였습니다.');
                        Search_opt();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });

    $(".option-del-btn").on("click", function(){
        var selectrows = gx2.getSelectedRows();
        if(selectrows.length === 0){
            alert('삭제할 옵션을 선택 해 주십시오.');
        } else {
            if(confirm('삭제하시겠습니까? 삭제하시면 재고수량도 삭제됩니다.')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/del-option',
                    data:{'options':selectrows},
                    success: function (res) {
                        gx2.delSelectedRows();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });


    $(document).ready(function() {
        if(goods_no > 0){
            GoodsOption();
        }
    });

    function ViewOptions(options,qty,wqty,sales){
        //console.log(options);
        //console.log(qty);
        if (gridOptDiv !== null) {

            if(gx_opt == null){
                for(i=0;i<options[0].length;i++){
                    columns_stock.push(
                        {field: 'opt_' + options[0][i], headerName:options[0][i],type:'numberType', editable: true, cellClass:['hd-grid-number','hd-grid-edit'],
                            cellStyle:function(params){
                                return (params.value === 0 || params.value === '')? {color:'#FF0000'}: {};
                            },
                            onCellValueChanged:EditQty
                        },
                    );
                }
                gx_opt = new HDGrid(gridOptDiv, columns_stock);
            }

            rows = [];
            //console.log(options[1].length);
            if(options[1].length == 0){
                var row = {"option":''};
                for(i=0;i<options[0].length;i++){
                    var opt2 = options[0][i];
                    var field = "opt_" + opt2;
                    sale = 0;
                    if (sales.hasOwnProperty(opt2)) {
                        // your code here
                        sale  = wqtys[opt2];
                    }
                    //row[field] = qty[opt2] + ' / ' + wqty[opt2] + ' / ' + sale;
                    row[field] = qty[opt2];
                }
                rows.push(row);
            } else {
                for(j=0;j<options[1].length;j++){
                    var opt1= options[1][j];
                    var row = {"option":opt1};
                    for(i=0;i<options[0].length;i++){
                        var opt2 = options[0][i];
                        var field = "opt_" + opt2;
                        sale = 0;
                        if (sales.hasOwnProperty(opt2+'^'+opt1)) {
                            // your code here
                            sale  = wqtys[opt2+'^'+opt1];
                        }
                        //row[field] = qty[opt2+'^'+opt1] + ' / ' + wqty[opt2+'^'+opt1] + ' / ' + sale;
                        row[field] = qty[opt2+'^'+opt1];
                    }
                    rows.push(row);
                }
            }
            gx_opt.setRows(rows);
            gx_opt.gridOptions.api.setDomLayout('autoHeight');
            // auto height will get the grid to fill the height of the contents,
            // so the grid div should have no height set, the height is dynamic.
            document.querySelector('#div-gd-option').style.height = '';
            if(columns_stock.length <= 5){
                gx_opt.gridOptions.api.sizeColumnsToFit();
            }
        }
    }

    $('[name=is_option_use]').change(function(e){
        if( $('#is_option_use_n').is(":checked") == true ){
            //작업 해야함
            $('#option_kind_area').css('display','none');
            gx_opt = null;
            columns_stock = [columns_stock.shift()];
            $('#div-gd-option').html('');
            GoodsOption();
        }else{
            $('#option_kind_area').css('display','');
            gx_opt = null;
            columns_stock = [columns_stock.shift()];
            $('#div-gd-option').html('');
            GoodsOption();
        }
        // if(confirm("변경 시 등록되어 있는 옵션 정보와 재고 수량이 모두 삭제됩니다.\n변경 하시겠습니까?")){
        //     //DeleteOptionAll();
        // }
    });

    function GoodsOption() {

        var is_option_use = $("input[name=is_option_use]:checked").val();

        $.ajax({
            type: "get",
            url: '/partner/product/prd01/' + goods_no + '/get-stock',
            dataType: 'json',
            data:{'is_option_use':is_option_use},
            // data: {},
            success: function (data) {
                console.log(data);
                ViewOptions(data.options,data.qty,data.wqty,[]);
            },
            error: function (e) {
                console.log(e.responseText);
            }
        });
    }

    $(".stock-sav-btn").on("click", function(){
        if(confirm('재고정보를 수정하시겠습니까?')){
            console.log(_stock_qty);
            var is_option_use = $("input[name=is_option_use]:checked").val();

            $.ajax({
                type: 'post',
                url: '/partner/product/prd01/' + goods_no + '/save-stock',
                dataType:'json',
                data:{'stocks':_stock_qty,'is_option_use':is_option_use},
                success: function (res) {
                    alert('재고정보를 수정햐였습니다.');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    });

    var _stock_qty = new Object();

    function EditQty(params){
        if (params.oldValue !== params.newValue) {
            var opt = params.colDef.field;
            if(params.data.option !== ""){
                opt = opt + '^' + params.data.option;
            }
            _stock_qty[opt] = params.newValue;
            // params.data[params.colDef.field + '_chg_yn'] = 'Y';
            // var rowNode = params.node;
            // rowNode.setSelected(true);
            // gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
            // //gridOptions.api.refreshCells({rowNodes:[rowNode]});
            // gx.gridOptions.api.setFocusedCell(rowNode.rowIndex, params.colDef.field);
        }
    }

    function Search_optextra() {
        $('#optionTab a[href="#option-extra-tab"]').tab('show');
        gx3.Request(`/partner/product/prd01/${goods_no}/get-option-extra`, '', -1);
    }

    $(".optionextra-add-btn").on("click", function(){
        var selectrows = gx1.getSelectedRows();
        if(selectrows.length === 0){
            alert('추가할 옵션구분을 선택 해 주십시오.');
        } else {
            gx3.addRows([{
                "chk":0,
                "name":selectrows[0]["name"],
                "option" : '',
                "price" : 0,
                "qty" : 0,
                "wqty" : 0,
                "memo" : '',
                "option_no" : selectrows[0]["no"],
                "no" : '',
            }]);
        }
    });

    $(".optionextra-sav-btn").on("click", function(){
        var selectrows = gx3.getSelectedRows();
        if(selectrows.length === 0){
            alert('저장할 옵션을 선택 해 주십시오.');
        } else {
            if(confirm('저장하시겠습니까?')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/save-option-extra',
                    data:{'optionextras':selectrows},
                    success: function (res) {
                        alert('저장하였습니다.');
                        Search_opt();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });

    $(".optionextra-del-btn").on("click", function(){
        var selectrows = gx3.getSelectedRows();
        if(selectrows.length === 0){
            alert('삭제할 옵션을 선택 해 주십시오.');
        } else {
            if(confirm('삭제하시겠습니까?')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/del-option-extra',
                    data:{'optionextras':selectrows},
                    success: function (res) {
                        gx3.delSelectedRows();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });


</script>

<script language="javascript">

    $(document).ready(function() {
        if(goods_no > 0){
            get_add_info();
        }
    });

    function get_add_info(){
        $.ajax({
            type: "get",
            url: '/partner/product/prd01/' + goods_no + '/get-addinfo',
            dataType: 'json',
            // data: {},
            success: function (res) {
                console.log(res);
                gx_related_goods.setRows(res.goods_related);
                gx_history.setRows(res.modify_history);
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }

        });
    }

</script>
