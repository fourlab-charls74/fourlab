<script>
    const out_order_errors = {
        '-100': "매장 주문번호 없음",
        '-101': "바코드 없음",
        '-102': "바코드 부정확",
        '-103': "수량정보 부정확",
        '-104': "판매가 부정확",
        '-105': "재고 부족",
    };

    $(document).ready(function() {
        // 파일선택 시 화면에 표기
        $('#excel_file').on('change', function(e){
            if (validateFile() === false) {
                $('.custom-file-label').html("");
                return;
            }
            $('.custom-file-label').html(this.files[0].name);
        });

        // 주문매장선택 시 매장정보 불러오기
        $('#store_no').on('change', function(e) {
            if(this.value != '') setStoreInfo(this.value);
        })
    });
    
    // 선택파일형식 검사
    const validateFile = () => {
        const target = $('#excel_file')[0].files;

        if (target.length > 1) return alert("파일은 1개만 올려주세요.");

        if (target === null || target.length === 0) return alert("업로드할 파일을 선택해주세요.");

        if (!/(.*?)\.(xlsx|XLSX)$/i.test(target[0].name)) return alert("Excel파일만 업로드해주세요.(xlsx)");

        return true;
    };

    const convertDataToWorkbook = (data) => {
		/* convert data to binary string */
		data = new Uint8Array(data);
		const arr = new Array();

		for (let i = 0; i !== data.length; ++i) {
			arr[i] = String.fromCharCode(data[i]);
		}

		const bstr = arr.join("");

		return XLSX.read(bstr, {type: "binary"});
	};

    const populateGrid = async (workbook) => {
		var firstSheetName = workbook.SheetNames[0]; // our data is in the first sheet
		var worksheet = workbook.Sheets[firstSheetName];

		var excel_columns = {
			// 'A': 'out_ord_no', // 매장 주문번호
			'B': 'ord_date', // 주문일
			'C': 'prd_cd', // 바코드
            'D': 'goods_no', // 상품번호
            'E': 'goods_nm', // 상품명
            'F': 'goods_opt', // 옵션
            'G': 'qty', // 수량
            'H': 'price', // 판매가
            'I': 'dlv_amt', // 배송비
            'J': 'add_dlv_amt', // 추가배송비
            'K': 'pay_type', // 결제방법
            'L': 'pay_date', // 입금일자
            'M': 'bank_inpnm', // 입금자명
            'N': 'user_id', // 주문자 ID
            'O': 'user_nm', // 주문자명
            'P': 'phone', // 주문자 전화
            'Q': 'mobile', // 주문자 휴대전화
            'R': 'r_nm', // 수령자명
            'S': 'r_phone', // 수령자 전화
            'T': 'r_mobile', // 수령자 휴대전화
            'U': 'r_zipcode', // 수령 우편번호
            'V': 'r_addr1', // 수령 주소
            'W': 'r_addr2', // 수령 상세주소
            'X': 'dlv_msg', // 배송메세지
            'Y': 'dlv_cd', // 택배사
            'Z': 'dlv_no', // 송장번호
            'AA': 'dlv_comment', // 출고메세지
            'AB': 'fee_rate', // 판매수수료율
		}

        var firstRowIndex = 4; // 엑셀 4행부터 시작 (샘플데이터 참고)
		var rowIndex = firstRowIndex; 

        let count = gx.gridOptions.api.getDisplayedRowCount();
        let rows = [];

		while (worksheet['C' + rowIndex]) {
			let row = {};
			Object.keys(excel_columns).forEach((column) => {
                let item = worksheet[column + rowIndex];
				if(item !== undefined && item.w) {
					row[excel_columns[column]] = item.w;
				}
			});
        
            rows.push({ ...row, count: ++count });
            rowIndex++;
		}

        if(rows.length < 1) return alert("한 개 이상의 주문건을 입력해주세요.");

        rows = rows.filter(r => Object.keys(r).length > 1);
        // .sort((a, b) => a.out_ord_no - b.out_ord_no)

	    // 하나의 row를 하나의 주문건(order_mst)로 설정 (2023-09-15)
        // rows.forEach((row, i) => {
        //     let prev = rows[i - 1];
        //     // if(i > 0 && row.out_ord_no === prev.out_ord_no) {
        //     if(i > 0) {
        //         rows[i] = {
        //             ...row, 
        //             ord_date: prev.ord_date,
	    //             price: row.price || 0,
        //             dlv_amt: prev.dlv_amt,
        //             add_dlv_amt: prev.add_dlv_amt,
        //             pay_type: prev.pay_type,
        //             pay_date: prev.pay_date,
        //             bank_inpnm: prev.bank_inpnm,
        //             user_id: prev.user_id,
        //             user_nm: prev.user_nm,
        //             phone: prev.phone,
        //             mobile: prev.mobile,
        //             r_nm: prev.r_nm,
        //             r_phone: prev.r_phone,
        //             r_mobile: prev.r_mobile,
        //             r_zipcode: prev.r_zipcode,
        //             r_addr1: prev.r_addr1,
        //             r_addr2: prev.r_addr2,
        //             dlv_msg: prev.dlv_msg,
        //             dlv_cd: prev.dlv_cd,
        //             dlv_no: prev.dlv_no,
	    //             dlv_comment: row.dlv_comment || '',
        //             fee_rate: prev.fee_rate,
        //         }
        //     }
        // });

        await gx.gridOptions.api.applyTransaction({ add : rows });
	};

	const makeRequest = (method, url, success, error) => {
		var httpRequest = new XMLHttpRequest();
		httpRequest.open("GET", url, true);
		httpRequest.responseType = "arraybuffer";

		httpRequest.open(method, url);
		httpRequest.onload = () => {
			success(httpRequest.response);
		};
		httpRequest.onerror = () => {
			error(httpRequest.response);
		};
		httpRequest.send();
	};

    const importExcel = async (url) => {
		await makeRequest('GET',
			url,
			// success
			async (data) => {
				const workbook = convertDataToWorkbook(data);
				await populateGrid(workbook);
			},
			// error
			(error) => {
                console.log(error);
			}
		);
	};

    // 업로드한 파일 grid에 적용
    function upload() {
        if(!validateFile()) return;

        if(gx.getRows.length > 0) {
            if(!confirm("이전에 적용하신 파일 데이터는 초기화됩니다.\n새로운 파일 데이터를 적용하시겠습니까?")) return;
        }

        const file_data = $("#excel_file").prop("files")[0];

        const form_data = new FormData();
		form_data.append("file", file_data);
		form_data.append("_token", "{{ csrf_token() }}");

        alert("엑셀파일을 적용하고있습니다. 잠시만 기다려주세요.");
        
        axios({
            method: 'post',
            url: '/store/order/ord01/batch-import',
            data: form_data,
            headers: {
                "Content-Type": "multipart/form-data",
            }
        }).then(async (res) => {
            gx.gridOptions.api.setRowData([]);
            if (res.data.code == '1') {
                const file = res.data.file;
                await importExcel("/" + file);
            } else {
                console.log(res.data.message);
            }
        }).catch((error) => {
            console.log(error);
        });
    }

    // 일괄판매등록
    function save() {
        const store_cd = $("#store_no").val();

        let rows = gx.getRows();
        if(rows.length < 1) return alert("일괄판매할 주문건이 존재하지 않습니다.");

        let bank_code = $("[name=bank_code]").val();
        // if(bank_code === "") return alert("입금은행을 선택해주세요.");

        let apy_fee = $("[name=apy_fee]").is(":checked");
        let fee = $("[name=fee]").val();

        if(!confirm("일괄판매하시겠습니까?")) return;

        let orders = [];
        let failed_list = [];

        rows.forEach((row, i) => {
            if(row.prd_cd) {
                let cart = {
					ord_date: row.ord_date,
                    prd_cd: row.prd_cd,
                    goods_no: row.goods_no,
                    goods_nm: row.goods_nm,
                    goods_opt: row.goods_opt,
                    qty: row.qty,
                    price: row.price,
                    recv_amt: isNaN(row.qty * row.price) ? 0 : (row.qty * row.price),
                    dlv_amt: row.dlv_amt,
                    // dlv_comment: row.dlv_comment,
                    fee_rate: row.fee_rate,
                };

				// 하나의 row를 하나의 주문건(order_mst)로 설정 (2023-09-15)
				orders.push({...row, cart: [cart], dlv_comment: row.dlv_comment});
                // if(i > 0) {
                //     orders = orders.map(order => ({...order, cart: order.cart.concat(cart)}));
                // } else {
                //     orders.push({...row, cart: [cart]});
                // }
            } else {
                // 바코드 미기입 시 실패처리
                failed_list.push({...row, error_code: '-101'});
            }
        });

        axios({
            url: '/store/order/ord01/batch-add',
            method: 'put',
            data: {
                store_cd,
                bank_code,
                apy_fee,
                fee,
                orders,
            },
        }).then(async function (res) {

            let success_list = splitOrder(res.data.body.success_list);
            let failed_list = splitOrder(res.data.body.failed_list);
            
            gx.gridOptions.api.setRowData([]);
            await gx.gridOptions.api.applyTransaction({ add : failed_list });
            await gx.gridOptions.api.applyTransaction({ add : success_list });
            
            if(failed_list.length < 1) {
                alert("모든 주문건이 정상적으로 판매등록되었습니다.");
            } else {
                alert("등록에 실패한 주문건이 있습니다. 데이터 확인 후 다시 등록해주세요.");                
            }
            opener.Search();
        }).catch(function (err) {
            console.log(err);
            alert("등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
        });
    }

    // 주문매장 선택 시 '입금은행' / '판매수수료' 값 적용
    async function setStoreInfo(store_cd) {
        const { data: { body: store } } = await axios({ 
            url: '/store/api/stores/search-store-info/' + store_cd, 
            method: 'get' 
        });
        
        if(store !== null) {
            $("[name=bank_code]").val(store.bank_nm + '_' + store.bank_no).prop("selected", true);
            $("[name=fee]").val(store.sale_fee || 0);
        }
    }

    // 주문건 분할 (상품 기준)
    function splitOrder(orders) {
        let result = [];

        orders.forEach(order => {
            order.cart.forEach(c => {
                result.push({
                    ...order, 
                    ...c, 
                    cart: [],
                    result: order.code,
                });
            });
        });

        return result;
    }
</script>
