@extends('partner_with.layouts.layout-nav')
@section('title','상품 입고')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">입고</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품 - {{ $goods_no }}</span>
                <span>/ 입고처리</span>
            </div>
        </div>
        <div>
            <a href="#" class="btn btn-sm btn-primary shadow-sm submit-btn"><i class="bx bx-save mr-1"></i>입고</a>
        </div>
    </div>
    <!-- 상품 세부 정보 -->
    <div class="card_wrap aco_card_wrap">
        <div class="card">
            <div class="card-header mb-0">
                <a href="javascript:;">입고 세부 정보</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-box">
                            <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <th>입고일자</th>
                                        <td>
                                            <div class="docs-datepicker form-inline-inner">
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm docs-date" name="stock_date" id="stock_date" value="{{ date('Ymd') }}" autocomplete="off" disable>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="docs-datepicker-container"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>송장번호</th>
                                        <td>
                                            <div class="input_box">
                                                <input type="text" name="invoice_no" id="invoice_no" class="form-control form-control-sm search-all" value="{{date('Ymd')}}">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>원가</th>
                                        <td>
                                            <div class="txt_box" style="line-height:24px !important;">63,700원<br/>※원가는 "상품 관리 - 상세" 화면에서 변경할 수 있습니다.</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 입고 정보 -->
        <div class="card">
            <div class="card-header mb-0">
                <a href="javascript:;">입고 수량 정보</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div id="grid" style="height:calc(100vh - 450px);min-height:200px;idth:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const columns = [
        {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, sort: null},
        {field:"goods_opt",headerName:"옵션"},
        {field:"qty",headerName:"입고수량", editable: true,cellStyle :{ 'background' : '#ffff99', 'text-align' : 'right' } }
    ];

    const pApp = new App('', { gridId: "#grid" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns);

    const goods_no = {{$goods_no}};
    const goods_sub = {{$goods_sub}};

    function Search() {
        const class_value = $('.goods_class').val();
        const data = `goods_sub=${goods_sub}`;

        gx.Request(`/partner/product/prd01/${goods_no}/options`, data, -1);
    }

    Search();

    $('.docs-date').change(function(){
      this.value = (this.value).replace(/-/g, "");
    });

    $('.submit-btn').click(function(e){

        e.preventDefault();

        const rows = gx.getSelectedRows();
        if(rows.length === 0){
            alert('입고할 옵션을 선택해 주십시오.');
            return false;
        }

        if(confirm('선택한 상품을 입고하시겠습니까?')){

            var options = [];
            rows.forEach(function(data, idx) {
                options.push(data);
            });

            var data = {
                'stock_date' : $('#stock_date').val(),
                'invoice_no' : $('#invoice_no').val(),
                'options' : options
            };

            $.ajax({
                async: true,
                type: 'put',
                url: `/partner/product/prd01/${goods_no}/in-qty`,
                data: data,
                success: function (res) {
                    alert("입고하였습니다.");
                    self.close();
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    });
</script>
@stop
