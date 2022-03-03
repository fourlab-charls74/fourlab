@extends('head_with.layouts.layout-nav')
@section('title','사방넷 주문연동 - SKU')
@section('content')

<div class="show_layout py-3">
    <!-- FAQ 세부 정보 -->
    <form method="post" name="search">

		<input type="HIDDEN" name="goods_no" value="{{ $goods_no }}">
		<input type="HIDDEN" name="id" value="{{ $id }}">

        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
					<a href="#">사방넷 주문연동 - SKU</a>
				</div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <!-- 업체아이디/비밀번호/업체 -->
                        <div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="20%">
										</colgroup>
										<tbody>
										<tr>
                                                <th>상품명</th>
                                                <td>
                                                    {{ $goods_nm }}
                                                </td>
                                            </tr>
											<tr>
                                                <th>SKU</th>
                                                <td>
                                                    <strong>{{ $sku }}</strong>
                                                </td>
                                            </tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- DataTales Example -->
		<div class="card shadow mb-4 last-card pt-2 pt-sm-0">
			<div class="card-body">
				<div class="card-title">
					<div class="filter_wrap">
						<div class="fl_box">
							<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<div id="div-gd" style="height:calc(100vh - 290px);width:100%;" class="ag-theme-balham"></div>
				</div>
			</div>
		</div>

	</form>

    <div class="resul_btn_wrap mt-3 d-block">
        <a href="#" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
    </div>
</div>

<script language="javascript">
    var columns = [
        {headerName: "#", field: "num",type:'NumType'},
		{field: "goods_opt",	headerName: "상품옵션", width:400},
		{field: "good_qty",		headerName: "재고", width:90, type:'numberType'},
		{field: "psend",		headerName: "선택", width:70, cellStyle:{"text-align":"center"},
            cellRenderer: function(params) {
				return '<a href="#" onClick="putOption(\''+ params.data.goods_opt +'\',\'' + params.data.good_qty + '\')">'+ params.value + '</a>'
            }
		},
    ];

	function putOption(opt,qty)
	{
		goods_no	= $('input[name="goods_no"]').val();
		id			= $('input[name="id"]').val();

		if( qty == 0 )
		{
			ret	= confirm("해당옵션 재고를 자동으로 1개 등록하고 진행합니다.\r\n등록된 재고의 삭제는 직접 진행해야합니다.\r\n진행하시겠습니까?");

			if(ret)
			{

				$.ajax({
					async: true,
					type: 'put',
					url: '/head/product/prd31/option',
					data: 
					{ 
						goods_no : goods_no,
						goods_opt : opt
					},
					success: function (data) {
						if( data.result_code == "200" )
						{
							alert("재고가 등록되었습니다.");

							opener.cbOptionPop(opt, id);
							self.close();
						}
						else
						{
							alert(data.result_msg);
						}
					},
					error: function(request, status, error) {
						console.log("error")
					}
				});

			}
		}
		else
		{
			opener.cbOptionPop(opt, id);
			self.close();
		}

		//const url='/head/stock/stk33/show/' + idx + '/?kind=' + kind;
        //window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
	const gridDiv = document.querySelector(pApp.options.gridId);
    let gx;

    $(document).ready(function() {
        gx = new HDGrid(gridDiv, columns);
        pApp.ResizeGrid();
        pApp.BindSearchEnter();
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/product/prd31/option_search', data);
    }

</script>
@stop
