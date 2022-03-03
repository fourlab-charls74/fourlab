@extends('partner_with.layouts.layout')
@section('title','공지사항')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">FAQ</h1>
  <div>
    <a href="#" id="search_sbtn" onclick="Search();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
    <a href="/partner/promotion/prm01/create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-sm text-white-50"></i> 추가</a>
    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
  </div>
</div>
<div class="page_tit">
    <h3 class="d-inline-flex">FAQ</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ FAQ</span>
    </div>
</div>

<form method="get" name="search">
<div class="row mb-2">
<div class="col">
  <div class="card border-left-primary shadow h-100 py-2">
    <div class="card-body">
      <div class="row no-gutters align-items-center">
        <div class="col mr-2">
          <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" >등록일</div>
          <div class="h5 mb-0 font-weight-bold text-gray-800">
            <input type="date" class="form-control form-control-sm" name="sdate" value="{{ $sdate }}" onchange="" style='width:150px;display:inline'> ~
            <input type="date" class="form-control form-control-sm" name="edate" value="{{ $edate }}" onchange="" style='width:150px;display:inline'>
          </div>
        </div>
        <div class="col mr-2">
          <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">공개여부</div>
          <div class="h5 mb-0 font-weight-bold text-gray-800">
            <select name='use_yn' class="form-control form-control-sm"  style='width:160px;'>
                <option value=''>전체</option>
                <option value='Y'>예</option>
                <option value='N'>아니요</option>
            </select>
          </div>
        </div>
        <div class="col mr-2">
          <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">제목</div>
          <div class="h5 mb-0 font-weight-bold text-gray-800">
            <input type='text' class="form-control form-control-sm" name='subject' value='' style='width:70%;'>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-1">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">총 : 300 건</h6>
  </div>
  <div class="card-body">
    <div class="table-responsive">
        <div id="divfg" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-alpine"></div>
    </div>
  </div>
</div>
<script language="javascript">
    var columnDefs = [
        {headerName: "#", field: "num",sortable:"ture",filter:true,width:50,valueGetter: function(params) {return params.node.rowIndex+1;}},
        {headerName: "제목", field: "question",sortable:"ture",filter:true,width:500,
            cellRenderer: function(params) {
                return '<a href="/partner/promotion/prm02/' + params.data.no +'" rel="noopener">'+ params.value+'</a>'
            }},
        {headerName: "분류", field: "type",sortable:"ture",filter:true},
        {headerName: "공개여부", field: "show_yn",sortable:"ture",filter:true },
        {headerName: "베스트여부", field: "best_yn",sortable:"ture",filter:true },
        {headerName: "작성자", field: "admin_nm",sortable:"ture",filter:true},
        {headerName: "등록일시", field: "regi_date",sortable:"ture",filter:true },
        {headerName: "글번호", field: "no",sortable:"ture",filter:true,hide:true },
        {headerName: "", field: "nvl"}
    ];

    // let the grid know which columns to use
    var gridOptions = {
        columnDefs: columnDefs,
        defaultColDef: {
            // set every column width
            flex: 1,
            width: 100,
            // make every column editable
            editable: true,
            resizable: true,
            autoHeight: true,
            // make every column use 'text' filter by default
            filter: 'agTextColumnFilter'
        },
        rowSelection:'multiple',
        rowHeight: 275,
    };

    // lookup the container we want the Grid to use
    var eGridDiv = document.querySelector('#divfg');

    new agGrid.Grid(eGridDiv, gridOptions);
    gridOptions.api.sizeColumnsToFit();
    // pull out the values we're after, converting it into an array of rowData

    function formatNumber(params) {
        // this puts commas into the number eg 1000 goes to 1,000,
        // i pulled this from stack overflow, i have no idea how it works
        return Math.floor(params.value)
            .toString()
            .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }

</script>
<script type="text/javascript" charset="utf-8">

    $(document).ready(function() {
        Search();
    });

    function getSelectedRows() {
        var selectedNodes = gridOptions.api.getSelectedNodes()
        var selectedData = selectedNodes.map( function(node) { return node.data })
        var selectedDataStringPresentation = selectedData.map( function(node) { return node.make + ' ' + node.model }).join(', ')
        alert('Selected nodes: ' + selectedDataStringPresentation);
    }

    function Search() {

        var frm = $('form');
        //console.log(frm.serialize());

        $.ajax({
            async: true,
            type: 'get',
            url: '/partner/promotion/prm02/search',
            data: frm.serialize(),
            success: function (data) {
                //console.log(data);
                var row = jQuery.parseJSON(data);
                gridOptions.api.setRowData(row);
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    }

</script>


@stop
