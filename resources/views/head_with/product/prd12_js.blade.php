    <script type="text/javascript" charset="utf-8">

        $(document).ready(function() {

            $("[name=plan_img_file]").change(function(){
                console.log('upload');
                uploadImage(this.files,'plan_img');
            });

            $("[name=plan_preview_img_file]").change(function(){
                uploadImage(this.files,'plan_preview_img');
            });

            $("[name=plan_top_img_file]").change(function(){
                uploadImage(this.files,'plan_top_img');
            });

            $(document).on("dragover", function(e) {
                e.stopPropagation();
                e.preventDefault();
            });

            $(document).on('drop', function(e){
                e.preventDefault();
                var files = e.originalEvent.dataTransfer.files;
                if (files.length === 0) return;
                if (files.length > 1) {
                    alert("파일은 1개만 올려주세요.");
                    return;
                }
                uploadImage(files,e.target.id);
            });
        });

        /**
         * @return {boolean}
         */
        function Save() {

            if ($('#subject').val() === '') {
                $('#subject').focus();
                alert('제목을 입력해 주세요.');
                return false;
            }

            if(!confirm('저장하시겠습니까?')){
                return false;
            }

            var frm = $('form[name="detail"]');

            if(code == ""){
                $.ajax({
                    method: 'post',
                    url: '/head/product/prd12',
                    data: frm.serialize(),
                    dataType: 'json',
                    async: false,
                    success: function (res) {
                        if(res.code == '200'){
                            alert("정상적으로 저장 되었습니다.");
                            self.close();
                            opener.location.reload();
                            //opener.Search(1);
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                            console.log(res.msg);
                        }
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
            } else {
                $.ajax({
                    method: 'put',
                    url: '/head/product/prd12/' + code,
                    data: frm.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        // console.log(res);
                        if(res.code == '200'){
                            alert("정상적으로 변경 되었습니다.");
                            self.close();
                            opener.Search(1);
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }


            return true;
        }

        /**
         * @return {boolean}
         */
        function Delete() {
            if(confirm('삭제 하시겠습니까?')){
                $.ajax({
                    method: 'delete',
                    url: '/head/product/prd12/' + code,
                    dataType: 'json',
                    success: function (res) {
                        // console.log(response);
                        if(res.code == '200'){
                            alert("삭제되었습니다.");
                            self.close();
                            opener.Search(1);
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }
            return false;
        }

        function View() {
            var url = "https://" + '{{ @$domain }}' + "/app/planning/views/" + '{{ @$plan->p_no }}' + '/' + code + "?is_preview=y";
            window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=400,left=550,width=1024,height=900");
        }

        function SearchCategoryGoods(d_cat_cd = '', click_tab_id = '#tab-goods') {
            if(d_cat_cd !== ''){
                $('#d_cat_cd').val(d_cat_cd);
            }
            //let data = 'd_cat_cd=' + '{{ @$plan->p_no }}';
            let data = 'd_cat_cd=' + $("#d_cat_cd").val();
            gx.Request('/head/product/prd12/' + code + '/search', data);

			$(click_tab_id).click();
            CategorySearch();
        }

        /**
         * @return {boolean}
         */
        function ChoiceGoodsNo(goods_nos){

            var frm = $('form');
            //console.log(frm.serialize());
			let d_cat_cd = $('#d_cat_cd').val();

            $.ajax({
                method: 'post',
                url: '/head/product/prd12/' + code + '/save',
                data: {'d_cat_cd':d_cat_cd,'goods_no':goods_nos},
                dataType: 'json',
                success: function (res) {
                    if(res.code == '200'){
						FolderSearch();
                        SearchCategoryGoods();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    }
                },
                error: function(e) {
                    console.log(e.responseText)
                }
            });
            return true;
        }

        /**
         * @return {boolean}
         */
        function AddGoods(goods_nos){
            var url = '/head/product/prd01/choice';
            var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
        }

        /**
         * @return {boolean}
         */
        function DelGoods(){
            let goods_nos = [];
            gx.getSelectedRows().forEach((selectedRow, index) => {
                goods_nos.push(selectedRow.goods_no);
            });

            if(goods_nos.length === 0) {
                alert('삭제할 상품을 선택 해 주십시오.');
            } else if(goods_nos.length > 0 && confirm('삭제 하시겠습니까?')){

				let d_cat_cd = $('#d_cat_cd').val();

                $.ajax({
                    method: 'post',
                    url: '/head/product/prd12/' + code + '/del',
                    data: {'d_cat_cd':d_cat_cd,'goods_nos':goods_nos},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            SearchCategoryGoods();
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }
            return true;

        }

        /**
         * @return {boolean}
         */
        function ChangeGoodsSeq(){
            let goods_nos = [];
            gx.gridOptions.api.forEachNode(function(node) {
                goods_nos.push(node.data.goods_no);
            });
            if(confirm('순서를 변경 하시겠습니까?')){

                {{--let d_cat_cd = '{{ @$plan->p_no }}';--}}
				let d_cat_cd = $('#d_cat_cd').val();
				
                $.ajax({
                    method: 'post',
                    url: '/head/product/prd12/' + code + '/seq',
                    data: {'d_cat_cd':d_cat_cd,'goods_nos':goods_nos},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            SearchCategoryGoods();
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }
            return true;
        }

        function FolderSearch() {
            gxFolder.Request('/head/product/prd12/' + code + '/search_folder', '');
        }

        /**
         * @return {boolean}
         */
        function FolderAdd(){

            var newData = {
                chk: '',
                d_cat_cd: '',
                d_cat_nm: '',
                use_yn: 'Y',
                goods_cnt: 0,
                tpl_kind: 'A',
                sale_yn: 'N',
                editable: 'Y'
            };

            gxFolder.gridOptions.api.applyTransaction({
                add: [newData],
                addIndex: 0,
            });

			$("#gd_folder-total").text(gxFolder.getRows().length);

			return false;
            //gx.gridOptions.defaultColDef.editable = true;
        }

        /**
         * @return {boolean}
         */
        function FolderSave(){

            let folders = [];
            let checkRows = gxFolder.getSelectedRows();
            checkRows.map(function(row) {
				folders.push(row);
            });

            if(folders.length > 0){
                $.ajax({
                    method: 'post',
                    url: '/head/product/prd12/' + code + '/save_folder',
                    data: {data:JSON.stringify(folders)},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            FolderSearch();
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                            console.log(res.msg);
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            } else {
                alert('저장 할 하위카테고리를 선택해 주십시오.');
            }
        }


        /**
         * @return {boolean}
         */
        function FolderDel(){

            let folders = [];
            let checkRows = gxFolder.getSelectedRows();
            checkRows.map(function(row) {
                folders.push(row.d_cat_cd);
            });

            if(folders.length === 0) {
                alert('삭제할 폴더를 선택 해 주십시오.');
            } else if(folders.length > 0 && confirm('삭제 하시겠습니까?')){

                $.ajax({
                    method: 'post',
                    url: '/head/product/prd12/' + code + '/del_folder',
                    data: {'folders':folders},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            FolderSearch();
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                            console.log(res.msg);
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }
            return true;
        }

        /**
         * @return {boolean}
         */
        function FolderChangeSeq(){

            let folders = [];
            gxFolder.gridOptions.api.forEachNode(function(node) {
                folders.push(node.data.d_cat_cd);
            });

            if(confirm('순서를 변경 하시겠습니까?')){
                $.ajax({
                    method: 'post',
                    url: '/head/product/prd12/' + code + '/seq_folder',
                    data: {'folders':folders},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            FolderSearch();
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                            console.log(res.msg);
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }
            return true;
        }

        /**
         * @return {boolean}
         */
        function CategorySearch() {

            var d_cat_cd = '{{ @$plan->p_no }}';

            console.log(d_cat_cd);

            $.ajax({
                method: 'get',
                url: '/head/product/prd12/' + code + '/search_category',
                data: {'d_cat_cd': d_cat_cd},
                dataType: 'json',
                success: function (res) {
                    console.log(res);
                    if(res.code == 200){
                        // $('#header_html').val(res.body.header_html);
                        // $('#sale_amt').val(res.body.sale_amt);
                        // $('#sale_kind').val(res.body.sale_kind);
                        // $('#tpl_kind').val(res.body.tpl_kind);
						$('[name=sale_yn][value=' + (res.body.sale_yn || 'N') + ']').prop('checked', true).trigger('change');
						$('#sale_kind').val(res.body.sale_kind || 'P');
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    }
                },
                error: function(e) {
                    console.log(e.responseText)
                }
            });
            return false;
        }


        /**
         * @return {boolean}
         */
        function CategorySave() {
			if ($("[name=sale_yn]").val() === 'Y' && (isNaN($("[name=sale_amt]").val()) || $("[name=sale_amt]").val() <= 0)) {
				return alert("세일값을 올바르게 입력해주세요.");
			}
			
            var frm = $('form[name="category"]');

            $.ajax({
                method: 'post',
                url: '/head/product/prd12/' + code + '/save_category',
                data: frm.serialize(),
                dataType: 'json',
                success: function (res) {
                    // console.log(res);
                    if(res.code == '200'){
                        alert("정상적으로 변경 되었습니다.");
						FolderSearch();
						CategorySearch();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    }
                },
                error: function(e) {
                    console.log(e.responseText)
                }
            });
            return false;
        }

        /**
         * @return {boolean}
         */
        function CategoryReset() {
            var frm = $('form[name="category"]');
            frm[0].reset();
            return false;
        }

		function setCategoryDetail(d_cat_cd, key) {
			let node;
			gxFolder.gridOptions.api.forEachNode(n => {
				if (n.data.d_cat_cd === d_cat_cd) node = n;
			});

			if (key === 'use_yn') {
				node.setDataValue('use_yn', node.data.use_yn === 'Y' ? 'N' : 'Y');
			} else if (['tpl_kind', 'sale_yn', 'header'].includes(key)) {
				SearchCategoryGoods(node.data.d_cat_cd, '#tab-category');
			}
		}

        function validatePhoto(files) {
            if (files === null || files.length === 0) {
                alert("업로드할 이미지를 선택해주세요.");
                return false;
            }
            if (!/(.*?)\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$/i.test(files[0].name)) {
                alert("이미지 형식이 아닙니다.");
                return false;
            }
            return true;
        }

        function uploadImage(files,id){
            console.log(files);
            if (validatePhoto(files) === false) return;
            var fr = new FileReader();
            //appendCanvas(size, id);
            fr.onload = function (e) {
                console.log(e.target.result);
                $('#' + id ).attr('src',e.target.result);
                $('#' + id + '_url' ).val(e.target.result);
                //drawImage(id,e);
            };
            fr.readAsDataURL(files[0]);
        }

        /*
        노출기간설정 관련
        */

        function setDisplayDate(start, end) {
            $("input[name='start_date']").val(getDate(start));
            $("input[name='end_date']").val(getDate(end));

            // 시간옵션세팅
            const options = [...Array(24).keys()].map((i) => (i < 10 ? "0" : "") + i);
            $(".select-time").each(function(i, node) {
                let opt_html = '';
                options.forEach(opt => opt_html += `<option value=${opt}>${opt}</option>`);
                node.innerHTML = opt_html;
            })
            $("select[name='start_time']").val(getTime(start));
            $("select[name='end_time']").val(getTime(end));
            
            // date -> 날짜 추출
            function getDate(str) {
                return `${str.substr(0,4)}-${str.substr(4,2)}-${str.substr(6,2)}`;
            }
            
            // date -> 시간 추출
            function getTime(str) {
                let time = str.substr(8,2);
                return time === "24" ? "00" : time;
            }
        }

        $("[name='plan_date_yn']").on("click", function(e) {
            setUseDisplayDate(e.target.checked);
        })
        
        // 노출기간 사용 미선택 시 해당입력 row -> disabled 처리
        function setUseDisplayDate(is_use) {
            $("input[name='start_date']").attr("disabled", !is_use);
            $(".start_date_btn").attr("disabled", !is_use);
            $("input[name='end_date']").attr("disabled", !is_use);
            $(".end_date_btn").attr("disabled", !is_use);
            $("select[name='start_time']").attr("disabled", !is_use);
            $("select[name='end_time']").attr("disabled", !is_use);
        }

    </script>
