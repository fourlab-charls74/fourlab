@extends('head_skote.layouts.master-without-nav')
@section('title','파일 검색')
@section('content')
<div class="container-fluid show_layout py-3">
  <form action="">
    @csrf
      <div class="card shadow">
          <div class="card-header mb-0">
              <h5 class="m-0 font-weight-bold">파일로 상품검색</h5>
          </div>
          <div class="card-body">
              <div class="row">
                <div class="col-3">SMS 발송</div>
                <div class="col-9">
                  <input type="radio" name="file_type" id="csv" value="CSV" checked>
                  <label for="csv" >CSV</label>
                  <input type="radio" name="file_type" id="excel" value="Excel">
                  <label for="excel">Excel</label>
                </div>
              </div>
              <div class="row">
                <div class="col-3">파일 인코딩</div>
                <div class="col-9">
                  <input type="radio" name="encoding_type" id="utf-8" value="UTF-8" checked>
                  <label for="utf-8" >UTF-8</label>
                  <input type="radio" name="encoding_type" id="euc-kr" value="EUC-KR">
                  <label for="euc-kr">EUC-KR</label>
                   ※ 데이터가 정상적으로 출력되지 않을 경우 변경해주세요.
                </div>
              </div>
              <div class="row">
                <div class="col-3"><label for="file">파일선택</label></div>
                <div class="col-9">
                    <div class="inline-inner-box ty2 triple">
                        <div class="img_file_cum_wrap">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file" aria-describedby="inputGroupFileAddon03">
                            <label class="custom-file-label" for="file">파일 찾아보기</label>
                          </div>
                        </div>
                    </div>
                </div>
              </div>
          </div>
      </div>
  </form>

  <!-- DataTales Example -->
  <div class="filter_wrap pt-2 pb-3">
      <div class="fl_box flax_box">
      </div>
      <div class="fr_box flax_box">
          <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm delete-btn mr-1">삭제</a>
          <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm search-btn">검색</a>
      </div>
  </div>

  <div id="filter-area" class="card shadow-none mb-4 search_cum_form ty2 last-card">
      <div class="card-body shadow">
          <div class="card-title form-inline text-right">
              <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
          </div>
          <div class="table-responsive">
              <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
          </div>
      </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.5/xlsx.full.min.js"></script>
<script language="javascript">
const columnDefs = [
    {
        headerName: '',
        headerCheckboxSelection: true,
        checkboxSelection: true,
        width:50

    },
    {headerName: '상품',
        children: [
            {headerName: "번호", field: "goods_no",type: 'numberType'},
            {headerName: "하위", field: "goods_sub",type: 'currencyType'}
        ]
    },
    {field: "style_no", headerName: "스타일넘버"},
    {field: "goods_nm", headerName: "상품명", width:120}
];

const pApp = new App('', {gridId: "#div-gd"});
const gridDiv = document.querySelector(pApp.options.gridId);
const gx = new HDGrid(gridDiv, columnDefs);

const validateFile = () => {
    const target = $('#file')[0].files;

    if (target.length > 1) {
        alert("파일은 1개만 올려주세요.");
        return false;
    }

    if (target === null || target.length === 0) {
        alert("업로드할 파일을 선택해주세요.");
        return false;
    }

    const fileType = $('[name=file_type]:checked').val();

    if (fileType === 'CSV') {
        if (!/(.*?)\.(csv|CSV)$/i.test(target[0].name)) {
            alert("CSV파일만 업로드해주세요.");
            return false;
        }
    } else if (fileType === 'Excel') {
        if (!/(.*?)\.(xls|XLS|xlsx|XLSX)$/i.test(target[0].name)) {
            alert("Excel파일만 업로드해주세요.");
            return false;
        }
    }

    return true;
}

const fr = new FileReader();
const searchRows = [];
const loadData = () => {
    const target = $('#file')[0].files[0];
    
    const fileType = $('[name=file_type]:checked').val();

    if (fileType === 'CSV') {
        fr.onload = loadCSVData;
        fr.readAsText(target, $('[name=encoding_type]:checked').val());
    } else if(fileType === 'Excel') {
        fr.onload = loadExcelData;
        fr.readAsBinaryString(target);
    }
}

const loadCSVData = () => {
    const rows = fr.result;
    createRows(rows);
}

const loadExcelData = () => {
    const data = fr.result;
    const workBook = XLSX.read(data, { type: 'binary' });
    const rows = XLSX.utils.sheet_to_csv(workBook.Sheets[workBook.SheetNames[0]]);

    createRows(rows);
}

const createRows = (rowText) => {
    const temp_rows = rowText.split('\n');
    const rows = [];
    temp_rows.forEach((row) => {
        if (row != "") {
            row = row.split(',');

            addRow({
                'goods_no' : row[0],
                'goods_sub' : row[1],
                'style_no' : row[2],
                'goods_nm' : row[3]
            });
        }
    });
}

const addRow = (row) => {
    searchRows.push(row);
    gx.gridOptions.api.updateRowData({add: [row]});
}

$('#file').change(function(e){
  if (validateFile() === false) return;

  $('.custom-file-label').html(this.files[0].name);

  loadData();
});

$('.delete-btn').click(() => {
    gx.getSelectedRows().forEach((selectedRow, index) => {
        gx.gridOptions.api.updateRowData({remove: [selectedRow]});
    });
});

$('.search-btn').click(() => {
    opener.fileSearch(searchRows);
});

pApp.ResizeGrid();
</script>
@stop