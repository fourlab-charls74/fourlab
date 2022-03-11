@extends('head_with.layouts.layout-nav')
@section('title','섹션 상세')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">섹션</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 코드 - {{ $code }}</span>
                </div>
            </div>
        </div>
        <!-- FAQ 세부 정보 -->
        <form name="detail">
            <div class="card_wrap aco_card_wrap">
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#">섹션 상세</a>
                    </div>
                    <div class="card-body mt-1">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="94px">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <th>구분</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <select name="sec_code" id="sec_code" class="form-control form-control-sm">
                                                            <option value="">전체</option>
                                                            @foreach ($section_types as $section_type)
                                                                <option value='{{ $section_type->code_id }}' @if($section_type->code_id == @$section->sec_code) selected @endif>{{ @$section_type->code_val }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>제목</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='subject' id="subject" value='{{@$section->subject}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>상품출력</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <div class="txt_box mr-2">최대 출력 상품수</div>
                                                        <div class="input_box wd200 mr-2">
                                                            <input type='text' class="form-control form-control-sm" name='max_limit' value='{{@$section->max_limit}}'>
                                                        </div>
                                                        <div class="custom-control custom-checkbox form-check-box">
                                                            <input type="checkbox" class="custom-control-input" value="Y" name="soldout_ex_yn" id="soldout_ex_yn" {{ (@$section->soldout_ex_yn=="Y") ? "checked" : "" }}>
                                                            <label class="custom-control-label" for="soldout_ex_yn">품절상품제외</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>상품정렬</th>
                                                <td>
                                                    <div class="form-inline form-box">
                                                        <select name="sort" class="form-control form-control-sm">
                                                            <option value="M" @if(@$section->sort == "M") selected @endif>수동</option>
                                                            <option value="M" @if(@$section->sort == "P") selected @endif>인기도</option>
                                                            <option value="M" @if(@$section->sort == "N") selected @endif>신상품</option>
                                                            <option value="M" @if(@$section->sort == "R") selected @endif>랜덤</option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if ($code !== '')
                                            <tr>
                                                <th>등록자</th>
                                                <td>
                                                    <div class="form-inline form-box">
                                                        {{@$section->admin_nm}}({{@$section->admin_id}})
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>등록일자</th>
                                                <td>
                                                    <div class="form-inline form-box">
                                                        {{@$section->regi_date}}
                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($code !== '')
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#">상품정보</a>
                    </div>
                    <div class="card-body pt-2">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fl_box px-0 mx-0">
                                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                                </div>
                                <div class="fr_box">
                                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return AddGoods();"><span class="fs-12">상품추가</span></a>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return DelGoods();"><span class="fs-12">상품삭제</span></a>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return ChangeSeq();"><span class="fs-12">순서변경</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="div-gd" class="ag-theme-balham"></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </form>
        <div class="resul_btn_wrap mt-3 d-block">
            <a href="javascript:Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
            @if ($code !== '')
                <a href="javascript:Delete();;" class="btn btn-sm btn-secondary delete-btn">삭제</a>
            @endif
            <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
        </div>
    </div>
    <style>
        img{height:30px;}
    </style>
    <script>

        let code = '{{ $code  }}';

        /**
         * @return {boolean}
         */
        function Save() {

            if(!confirm('저장하시겠습니까?')){
                return false;
            }

            if ($('#sec_code').val() === '') {
                $('#sec_code').focus();
                alert('구분을 선택해주세요.');
                return false;
            }

            if ($('#subject').val() === '') {
                $('#subject').focus();
                alert('제목을 입력해 주세요.');
                return false;
            }

            var frm = $('form');
            //console.log(frm.serialize());

            if(code == ""){
                $.ajax({
                    method: 'post',
                    url: '/head/product/prd11',
                    data: frm.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            alert("정상적으로 저장 되었습니다.");
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
            } else {
                $.ajax({
                    method: 'put',
                    url: '/head/product/prd11/' + code,
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

        function Delete() {
            if(confirm('삭제 하시겠습니까?')){
                $.ajax({
                    method: 'delete',
                    url: '/head/product/prd11/' + code,
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
        }
    </script>
    <script>
        const columns = [
            {headerName: '#', pinned: 'left', type:'NumType', cellStyle: {"line-height": "30px"}},
            {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, pinned: 'left', sort: null},
            {field: "goods_no", headerName: "상품번호", width: 75, pinned: 'left',rowDrag: true},
            {field: "head_desc", headerName: "상단홍보글", cellStyle: {"line-height": "30px"}},
            {field: "img", headerName: "이미지", width:46, type:'GoodsImageType'},
            {field: "img", headerName: "이미지_url", hide: true},
            {field: "goods_nm", headerName: "상품명",type:'HeadGoodsNameType', cellStyle: {"line-height": "30px"}},
            {field: "ad_desc", headerName: "하단홍보글", cellStyle: {"line-height": "30px"}},
            {field: "sale_stat_cl", headerName: "상품상태", width:70, type:'GoodsStateType', cellStyle: {"line-height": "30px"}},
            {field: "before_sale_price", headerName: "정상가", type:'currencyType', hide: true},
            {field: "price", headerName: "판매가", type:'currencyType', cellStyle: {"line-height": "30px"}},
            {field: "coupon_price", headerName: "쿠폰가", type:'currencyType', cellStyle: {"line-height": "30px"}},
            {field: "sale_rate", headerName: "세일율(,%)", type:'percentType', hide: true},
            {field: "sale_s_dt", headerName: "세일기간", hide: true},
            {field: "sale_e_dt", headerName: "세일기간", hide: true},
            {field: "qty", headerName: "재고수", type:'numberType', width:46, cellStyle: {"line-height": "30px"}},
            {field: "wqty", headerName: "보유재고수", width:70, type:'numberType', cellStyle: {"line-height": "30px"}},
            {field: "reg_dm", headerName: "등록일시", width:110, cellStyle: {"line-height": "30px"}},
            {field: "sale_price", headerName: "sale_price", hide: true},
            {field: "goods_type_cd", headerName: "goods_type", hide: true},
        ];
    </script>
    <script type="text/javascript" charset="utf-8">
        const pApp = new App('',{
            gridId:"#div-gd",
        });
        let gx;

        $(document).ready(function() {
            pApp.ResizeGrid(650);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns);
            gx.gridOptions.rowDragManaged = true;
            gx.gridOptions.animateRows = true;
            Search();
        });

        function Search() {
            let data = $('form[name="search"]').serialize();
            gx.Request('/head/product/prd11/' + code + '/search', data); 
        }


        /**
         * @return {boolean}
         */
        function ChoiceGoodsNo(goods_nos){

            var frm = $('form');
            //console.log(frm.serialize());

            $.ajax({
                method: 'post',
                url: '/head/product/prd11/' + code + '/save',
                data: {'goods_no':goods_nos},
                dataType: 'json',
                success: function (res) {
                    if(res.code == '200'){
                        Search();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        console.log(res.msg);
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

                $.ajax({
                    method: 'post',
                    url: '/head/product/prd11/' + code + '/del',
                    data: {'goods_nos':goods_nos},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            Search();
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
        function ChangeSeq(){
            let goods_nos = [];
            gx.gridOptions.api.forEachNode(function(node) {
                goods_nos.push(node.data.goods_no);
            });
            if(confirm('순서를 변경 하시겠습니까?')){
                $.ajax({
                    method: 'post',
                    url: '/head/product/prd11/' + code + '/seq',
                    data: {'goods_nos':goods_nos},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            Search();
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
    </script>
@stop
