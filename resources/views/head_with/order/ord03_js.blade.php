<script>

    var out_order_errors = new Object();
    out_order_errors["-100"] = "판매처 주문번호 부정확";
    out_order_errors["-101"] = "상품번호 없음";
    out_order_errors["-102"] = "옵션 없음";
    out_order_errors["-106"] = "수량 없음";
    out_order_errors["-107"] = "금액 없음";
    out_order_errors["-108"] = "주문자 없음";
    out_order_errors["-110"] = "수령자 없음";
    out_order_errors["-111"] = "수령자 우편번호 없음";
    out_order_errors["-112"] = "수령자 주소 없음";
    out_order_errors["-210"] = "상품번호 부정확";
    out_order_errors["-220"] = "옵션 부정확";
    out_order_errors["-310"] = "주문 중복";
    out_order_errors["-320"] = "묶음주문 주문자명 불일치";
    out_order_errors["-330"] = "묶음주문 주문번호 없음";
    out_order_errors["-500"] = "시스템오류";
    out_order_errors["110"] = "재고 부족";

	// DB error code
	out_order_errors["23000"] = "중복 등록";

    function validateFile(files) {
        if (files === null || files.length === 0) {
            alert("입력할 파일을 선택해주세요.");
            return false;
        }

        if (files.length > 1) {
            alert("파일은 1개만 올려주세요.");
            return;
        }

        if (!/(.*?)\.(xls|csv|tsv)$/i.test(files[0].name)) {
            alert("지원하지 않는 파일 형식입니다.");
            return false;
        }

        return true;
    }

    function openImportPopup() {
        const url = '/head/order/ord03/import?sale_place=' + $('#sale_place').val();
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }

    function openFormatPopup() {
        const url = '/head/order/ord03/fmt';
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=600");
    }

    function uploadFile(files) {

        if (validateFile(files) === false) {
            return false;
        }

        var form_data = new FormData();
        form_data.append('file', files[0]);
        form_data.append('_token', "{{ csrf_token() }}");
        $.ajax({
            url: '/head/order/ord03/upload', // point to server-side PHP script
            dataType: 'json', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function(res) {
                console.log(res);
                if (res.code == "200") {
                    var url = res.file;

                    var columns = {
                        'A': 'out_ord_no',
                        'B': 'ord_date',
                        'C': 'style_no',
                        'D': 'goods_no',
                        'E': 'goods_opt',
                        'F': 'goods_nm',
                        'G': 'qty',
                        'H': 'ord_amt',
                        'I': 'dlv_pay_type',
                        'J': 'dlv_amt',
                        'K': 'dlv_add_amt',
                        'L': 'pay_type',
                        'M': 'pay_date',
                        'N': 'user_id',
                        'O': 'user_nm',
                        'P': 'phone',
                        'Q': 'mobile',
                        'R': 'r_nm',
                        'S': 'r_zipcode',
                        'T': 'r_addr',
                        'U': 'r_phone',
                        'V': 'r_mobile',
                        'W': 'dlv_msg',
                        'X': 'dlv_nm',
                        'Y': 'dlv_cd',
                        'Z': 'dlv_comment',
                        'AA': 'fee_rate',
                        'AB': 'chk',

                    };
                    var xlsx = new HDXls();
                    xlsx.importExcel(url,gx,columns);
                } else {
                    console.log(res.errmsg);
                }
            },
            error: function(e) {
                console.log(e);
                console.log(e.responseText)
            }
        });
        return false;
    }

    function uploadImportFile(files) {

        var sale_place = $("#sale_place").val();
        if(sale_place === ""){
            alert('판매업체를 선택 해 주십시오');
            $("#file").val('');
            return;
        }

        if (validateFile(files) === false) {
            $("#file").val('');
            //files = null;
            return false;
        }
        $('#file-label').html(files[0].name);

        var form_data = new FormData();
        form_data.append('file', files[0]);
        form_data.append('_token', "{{ csrf_token() }}");
        $.ajax({
            url: '/head/order/ord03/upload/ord03_imp', // point to server-side PHP script
            dataType: 'json', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function(res) {
                console.log(res);
                if (res.code == "200") {
                    var url = res.file;
                    var columns = {};
                    for(i=1;i<=50;i++) {
                        if(i>26) {
                            cd = 'A' + String.fromCharCode(65+i-27)
                        } else {
                            cd = String.fromCharCode(65+i-1)
                        }
                        columns[cd] = cd;
                    }
                    var xlsx = new HDXls();
                    xlsx.importExcel(url,gx,columns,function(){
                        ApplyImportFile();
                    });
                } else {
                    console.log(res.errmsg);
                }
            },
            error: function(e) {
                console.log(e);
                console.log(e.responseText)
            }
        });
        return false;
    }

    function ApplyImportFile(){

        var sale_place = $("#sale_place").val();
        if(sale_place === ""){
            $("#sale_place").focus();
            alert('판매업체를 선택 해 주십시오');
            return;
        }

        $.ajax({
            url: '/head/order/ord03/fmt/search', // point to server-side PHP script
            dataType: 'json', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            data: {"sale_place":sale_place},
            type: 'get',
            success: function(res) {
                //console.log(res);
                if (res.code == "200") {

                    var format = res.body;
                    var fmt = {};
                    var rowData = [];

                    for(i=0;i<format.length;i++){
                        var column_idx = format[i]["mat_idx"];
                        //console.log(i + ',' + column_idx + columns2[i+1]["field"]);
                        if(column_idx >= 0){
                            var cd = columns[column_idx]["field"];
                            format[i]["cd"] = cd;
                            fmt[columns2[i+1]["field"]] = format[i];
                        }
                    }
                    //console.log(fmt);
                    var dlv_amt_yn = $("#dlv_amt_yn").is(":checked");
                    var dlv_amt_minus_yn = $("#dlv_amt_minus_yn").is(":checked");
                    var dlv_amt_limit =  toNumber($("#dlv_amt_limit").val());
                    var dlv_amt =  toNumber($("#dlv_amt").val());
                    var fee = $("#fee").val();
                    gx.gridOptions.api.forEachNode(function(node) {
                        //console.log(node.data);
                        var row = [];
                        for (var key in fmt) {
                            row[key] = node.data[fmt[key]["cd"]];
                        }
                        if(dlv_amt_yn === true){
                            if(toNumber(row["ord_amt"]) > dlv_amt_limit){
                                row["dlv_amt"] = dlv_amt;
                            }
                        }
                        if(dlv_amt_minus_yn === true){
                            row["ord_amt"] = toNumber(row["ord_amt"]) - toNumber(row["dlv_amt"]);
                        }

                        row["fee_rate"] = fee;

                        rowData.push(row);
                    });
                    //
                    //console.log(rowData);
                    gx2.gridOptions.api.setRowData(rowData);
                    $("#" + gx2.gridTotal).text(numberWithCommas(rowData.length));
                } else {
                    console.log(res.errmsg);
                }
            },
            error: function(e) {
                console.log(e.responseText)
            }
        });

    }

    function SetImportData(rows){
        gx.gridOptions.api.setRowData(rows);
    }

    function ApplyImportData(){
        gx2.selectAll();
        var rows = gx2.getSelectedRows();
        opener.SetImportData(rows);
        self.close();
    }

    /**
     * @return {boolean}
     */
    function Save(){

        if($("#sale_place").val() == ""){
            alert("판매업체를 선택해 주십시오.");
            $("#sale_place").focus();
            return false;
        }

        if($("#bank_code").val() == ""){
            alert("입금은행을 선택해 주십시오.");
            $("#bank_code").focus();
            return false;
        }

        let orders = [];
        gx.getSelectedNodes().forEach((selectedRow, index) => {
            console.log(selectedRow);
            orders.push(selectedRow);
        });

        if(orders.length > 0) {
        } else {
            alert("자료가 없습니다. 파일을 선택하시거나 자료를 불어오기 해 주십시오.");
            //document.f1.FILE.focus();
            return false;
        }

        if(confirm('수기주문을 저장하시겠습니까?')){
            SaveOrder(orders,0);
        }
    }

    function SaveOrder(orders,index){

        var sale_place = $("#sale_place").val();
        var bank_code = $("#bank_code").val();
        var ord_type = $("#ord_type").val();

        if(index < orders.length){

            let rowid = orders[index].id;
            //console.log("rowid : " + rowid);
            //console.log(orders[index].data);
            orders[index].setDataValue('code','[' + rowid + '] 저장중...');

            $.ajax({
                type: 'post',
                url: '/head/order/ord03/save', // point to server-side PHP script
                dataType: 'json', // what to expect back from the PHP script, if anything
                cache: false,
                data: {'sale_place':sale_place,'bank_code':bank_code,'ord_type':ord_type,'order':JSON.stringify($.extend({}, orders[index].data))},
                success: function(res) {
                    var rownode = gx.getRowNode(rowid);
                    let result = "";
                    if(res.code == "200"){
                        result = "[" + res.code + "] 성공";
                    } else {
                        if (out_order_errors.hasOwnProperty(res.code)) {
                            result = "[" + res.code + "] " + out_order_errors[res.code];
                        } else {
                            result = "[" + res.code + "] ";
                        }
                    }
                    rownode.setDataValue('code',result);
                    rownode.setDataValue('ord_opt_no', res.ord_opt_no);
                    rownode.setDataValue('ord_no', res.ord_no);
                },
                error: function(e) {
                    console.log('error');
                    console.log(e.responseText);
                },
                complete : function() {
                    index++;
                    //console.log('index :' + index);
                    setTimeout(SaveOrder,100,orders,index);
                },
            });
        }else{
            alert("작업이 완료되었습니다.");
        }
    }

	$('.sale_place_select').change(function(){
		get_fee();
	});

	function get_fee(){
        sale_place	= $('#sale_place').val();
		if( sale_place != "" ){
			$.ajax({
				async: true,
				type: 'put',
				url: '/head/order/ord03/get-fee',
				data: {'sale_place':sale_place},
				success: function (data) {
					$('#fee').val(data);
				},
				error: function(e) {
					console.log(e.responseText)
				}
			});
		}
	}
    
</script>
