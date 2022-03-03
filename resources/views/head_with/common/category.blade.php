@extends('partner_skote.layouts.master-without-nav')
@section('title','카테고리')
@section('content')
<script src="https://unpkg.com/@ag-grid-enterprise/all-modules@24.1.0/dist/ag-grid-enterprise.min.js"></script>
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<form name="f1">
    <input type="hidden" name="cat_type" value="{{$cat_type}}" />
<div class="container-fluid show_layout py-3">
    <!-- FAQ 세부 정보 -->
    <input type="hidden" name="type" value="{{$cat_type}}"/>

    <div class="header mb-3 justify-content-between d-sm-flex">
    <h1 class="h6 mb-0 font-weight-bold">{{ $category_nm }} 선택</h1>
    <div>
        <input type="checkbox" name="isclose" id="isclose" value="Y">
        <label for="">선택 후 닫기</label>
        <a href="#" onclick="Cmder('set');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">선택</a>
    </div>
</div>
<div id="filter-area" class="card shadow-none mb-4 search_cum_form ty2 last-card">
        <div class="card-body shadow">
            <div class="card-title form-inline text-right">
                <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                <SELECT class=select name=site>
                    <OPTION value="">모두</OPTION>
                    @foreach ($sites as $site_arr)
                        <option value='{{ $site_arr->com_id }}'>{{ $site_arr->com_nm }}</option>
                    @endforeach
                </SELECT>
                <a href="#" onclick="Search(1);" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">검색</a>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</form>
<script language="javascript">
var columns = [
        // this row shows the row index, doesn't use any data from the row
        
        {field:"p_d_cat_nm", headerName:"p_d_cat_nm", rowGroup: true, hide:true, max:100},
        {field:"p_d_cat_cd",headerName:"p_d_cat_cd", hide:true },
	    {field:"d_cat_cd",headerName:"d_cat_cd", width:150, hide:true },
	    {field:"d_cat_nm",headerName:"이름", width:200,},
	    {field:"full_nm", headerName: "full_nm", hide:true}
        
];

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        //gx.gridOptions.showRowGroup = true;
        gx.gridOptions.onRowGroupOpened = checkGroup;
        gx.gridOptions.autoGroupColumnDef = function(){return {'width': 80}}
        Search(1);
        gx.gridOptions.api.setInfiniteRowCount(130, false);
        
        
        
    });
    function checkGroup(event){
        console.log(event);
       
    }
   
</script>
<script type="text/javascript" charset="utf-8">
    var _isloading = false;
    function onscroll(params){
        if(_isloading === false && params.top > gridDiv.scrollHeight){
            
        }
    }
    var _page = 1;
    var _total = 0;
    var _grid_loading = false;
    var _code_items = "";
    var columns_arr = {};
    var option_key = {};
	var cat_type = "{{ $cat_type }}";

    function Search(page) {
        let data = $('form[name="f1"]').serialize();
        gx.Request('/head/api/category/getlist/'+cat_type, data, page);
    }

    function ChangeCategory(data){
        console.log("test");
        console.log("data : "+ data);
    }
    
    function Cmder(cmd){
        if(cmd == "set"){
            set();
        }
    }

    function set() {
        var selectedRowData = gx.gridOptions.api.getSelectedRows();
        var ff = document.f1;
        var d_cat_cd = "";
        var d_cat_nm = "";
		var full_nm = "";
        var data = [];
        //console.log("d_cd_cd : " + selectedRowData.d_cd_cd);
        selectedRowData.forEach( function(selectedRowData, index) {
            if(selectedRowData.d_cat_cd != ""){
                //data.push(selectedRowData.group_no +"_"+ selectedRowData.goods_sub);
                console.log("selectedRowData.d_cat_cd : " + selectedRowData.d_cat_cd);
                d_cat_cd = selectedRowData.d_cat_cd;
                d_cat_nm = selectedRowData.d_cat_nm;
				full_nm = selectedRowData.full_nm;
                console.log("d_cat_cd : "+ d_cat_cd);
                
            }
        });
        
        //opener.SetDCategory(d_cat_cd, d_cat_nm, d_cat_nm.length);
		opener.SetDCategory(d_cat_cd, full_nm, d_cat_nm.length);
        
        if($("[name=isclose]").is(":checked") == true){
            self.close();
        }
        
    }

</script>

    
@stop
