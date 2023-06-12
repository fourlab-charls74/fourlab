@extends('store_with.layouts.layout-nav')
@section('title','동종업계매출등록')
@section('content')

<div class="py-3 px-sm-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">동종업계매출등록</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 매장관리</span>
            <span>/ 동종업계매출등록</span>
        </div>
    </div>
    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>기본 정보</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="Save_amt();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
                        <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="store_no">매장명</label>
                                <div class="form-inline inline_btn_box search-enter" >
                                    <input type='hidden' id="store_nm" name="store_nm">
                                    <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="formrow-firstname-input">매출기간</label>
                                <div class="form-inline">
                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date month" id="date" name="date" value="{{ $date }}" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                <a href="#" id="search_sbtn" onclick="Save_amt();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>    
            </div>
        </div>
        <div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
            <div class="card-body shadow">
                <div class="card-title">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                        <div class="fr_box">

                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" charset="utf-8">
    let columns = [
            {headerName: "#", field: "num",type:'NumType', pinned:'left', width: 30, cellClass: 'hd-grid-code'},
            {headerName: "코드", field: "competitor_cd", pinned:'left',  width: 40, cellClass: 'hd-grid-code'},
            {headerName: "동종업계명", field: "competitor_nm",  pinned:'left', width: 97, cellClass: 'hd-grid-code'},
            {headerName: "메모", field: "sale_memo",  pinned:'left', width: 100, cellClass: 'hd-grid-code', editable:true, cellStyle:{'background' : '#ffFF99'}},
        ];

        const pApp = new App('',{
            gridId:"#div-gd",
        });
        let gx;
        var mutable_cols = [];
        let date = "";
        let sale_date;
        let in_val = [];
        $(document).ready(function() {
            pApp.ResizeGrid(205);
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns, {
                onCellValueChanged: (e) => {
                    // e.node.setSelected(true);
                    for (let i = 1; i <= sale_date;i++) {
                        if (i<10) {
                            if (e.column.colId == "sale_amt_0" + i) {
                                if (isNaN(e.newValue) == true) {
                                    alert("숫자만 입력가능합니다.");
                                    gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                                }
                            }
                        } else {
                            if (e.column.colId == "sale_amt_" + i) {
                                if (isNaN(e.newValue) == true) {
                                    alert("숫자만 입력가능합니다.");
                                    gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                                }
                            }
                        }
                    }
                }
            });
            pApp.BindSearchEnter();
        });

       

        function Search() {
            let data = $('form[name="search"]').serialize();
            let store_no = document.getElementById('store_no').value;
            date = document.getElementById('date').value;
            let date_format = date.split('-');

            //매출기간의 월에 따라 일수 구하기
            sale_date = new Date(date_format[0],date_format[1],0).getDate();

            data += "&day=" + sale_date;

            if(store_no == '') {
                alert('매장을 선택해주세요.');
            } else {
                gx.Request('/store/stock/stk34/com_search', data, -1, function(e) {
                    formatDate(e);
                });
            }
        }

        const formatDate = (e) => {
            days = e.head.day;
            setMutableColumns(days);
        }

        const setMutableColumns = (days) => {
            gx.gridOptions.api.setColumnDefs([]);
            mutable_cols = [];
            columns.map(col => {
                mutable_cols.push(col);
            });

            mutable_cols.push(dayColumns(days));
            mutable_cols.push({ headerName: "", field: "", width: "auto" });
            gx.gridOptions.api.setColumnDefs(mutable_cols);
        };

        const dayColumns = (days) => {
            let col = { fields: "year_month", headerName: date, children: [] };
            for (let i = 1; i <= days; i++) {
                let day_field = "";
                let day_headerName = "";
                if (i < 10) {
                    day_headerName = '0'+ i +'일';
                    day_field = 'sale_amt_0' + i;
                } else{
                    day_headerName = i +'일';
                    day_field = 'sale_amt_' + i;
                }
                let add_col = {field: day_field, headerName: day_headerName, minWidth: 60, type: "currencyType" , editable:true, cellStyle: {"background-color": "#ffFF99","text-align": "right"}};
                col.children.push(add_col)
            }
            return col;
        };

        //매출액 저장
        function Save_amt() {
            let store_no = document.getElementById('store_no').value;
            let date = document.getElementById('date').value;
            let data = gx.getRows();

            if (store_no === '') {
                alert('매장을 선택해주세요');
                return false;
            }

            if(!confirm("매출액을 저장하시겠습니까?")) return;

                axios({
                    url: `/store/stock/stk34/save_amt`,
                    method: 'post',
                    data: {
                        data: data,
                        date: date,
                        day: sale_date,
                    },
                }).then(function (res) {
                    if(res.data.code === 200) {
                        alert(res.data.msg);
                        opener.Search();
                        Search();
                    } else {
                        console.log(res.data.msg);
                        alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
                    }
                }).catch(function (err) {
                    console.log(err);
                });
        }

</script>

@stop