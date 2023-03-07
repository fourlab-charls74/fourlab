

<!-- 상품관리(코드) > 상품매칭 > 바코드별 매칭 온라인코드 검색 기능-->

<!-- 온라인코드 검색 -->
<div id="SearchGoodsNoModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SearchGoodsNoModalLabel" aria-hidden="true">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myModalLabel">상품 검색</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body show_layout" style="background:#f5f5f5;">
                    <div class="card_wrap search_cum_form write">
                        <div class="card shadow">
                            <form name="search_goods_no2" method="get">
                                <div class="card-body">
                                    <div class="row_wrap">
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">온라인코드</label>
                                                    <div class="flax_box">
                                                        <input type="text" name="sch_goods_nos" id="sch_goods_nos" class="form-control form-control-sm w-80" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">상품명</label>
                                                    <div class="flax_box">
                                                        <input type="text" name="goods_nm" id="goods_nm" class="form-control form-control-sm w-80" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">&nbsp;</label>
                                                    <div class="flax_box">
                                                        <div class="resul_btn_wrap mt-2" style="display:block;">
                                                            <a href="#" id="search_sbtn_code" onclick="return searchGoodsNo2.Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                                            <a href="#" onclick="return searchGoodsNo2.Choice();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>선택</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card shadow mb-1">
                            <div class="card-body m-0">
                                <div class="card-title">
                                    <div class="filter_wrap">
                                        <div class="fl_box">
                                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-goods_no2-total" class="text-primary">0</span> 건</h6>
                                        </div>
                                        <div class="fr_box form-check-box">
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div id="div-gd-goods_no2" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<script language="javascript">

    function SearchGoodsNo2(){
        this.grid = null;
        this.id = '';
    }

    SearchGoodsNo2.prototype.Init = function () { // 검색조건 초기화 기능 추가 - 사용 예) searchGoodsNo.Init();
        document.querySelector("form[name='search_goods_no2']").reset();
        document.querySelector("#gd-goods_no2-total").innerHTML = 0;
        if (this.grid) this.grid.deleteRows();
    };

    SearchGoodsNo2.prototype.Open = function(id = 'goods_no2',callback = null){
        if(this.grid === null){
            this.SetGrid("#div-gd-goods_no2");
            $("#SearchGoodsNoModal2").draggable();
            this.id = id;
            this.callback = callback;
        }
        let goods_no2 = $('#' + this.id).val();
        if(goods_no2 !== ""){
            $('#sch_goods_no2').val(goods_no2);
        }
        $('#SearchGoodsNoModal2').modal({
            keyboard: false
        });
    };

    SearchGoodsNo2.prototype.SetGrid = function(divId){
        const columns = [
            {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 35, pinned: 'left', sort: null},
            {field: "goods_no", headerName: "온라인코드", width: 72, pinned: 'left'},
            {field: "style_no", headerName: "스타일넘버", width: 84, pinned: 'left'},
            {field: "img", headerName: "이미지", type:'GoodsImageType',width: 40},
            {field: "img", headerName: "이미지_url", hide: true},
            {field: "sale_stat_cl", headerName: "전시상태", type:'GoodsStateType',width: 72},
            {field: "goods_nm", headerName: "상품명",type:'HeadGoodsNameType'},
            {field:"nvl" , headerName:""},
        ];

        this.grid = new HDGrid(document.querySelector( divId ), columns);
    };

    SearchGoodsNo2.prototype.Search = function(){
        let data = $('form[name="search_goods_no2"]').serialize();
        //console.log(data);
        this.grid.Request('/head/api/goods', data, 1);
    };

    SearchGoodsNo2.prototype.Choice = function(){

        let checkRows = this.grid.getSelectedRows();
        let goods_nos = checkRows.map(function(row) {
            return row.goods_no;
        });

        if(checkRows.length == 0){
            alert('상품을 선택해 주세요.');
            return false;
        }else if(checkRows.length > 1){
            alert('상품을 하나만 선택해 주세요.');
            return false;
        }

        if(this.callback !== null){
            this.callback();
        } else {
            if($('#' + this.id).length > 0){
                $('#' + this.id).val(goods_nos.join(","));
            }
        }
        $('#SearchGoodsNoModal2').modal('toggle');
    };
    let searchGoodsNo2 = new SearchGoodsNo2();

    $(document).ready(function() {
        $( ".sch-goods_no2" ).click(function() {
            if($(this).attr("data-name") !== null){
                searchGoodsNo2.Open($(this).attr("data-name"));
            }
        });
    });

</script>