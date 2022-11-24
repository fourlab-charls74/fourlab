@extends('store_with.layouts.layout-nav')
@section('title','동종업계매출관리')
@section('content')

<div class="py-3 px-sm-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">동종업계매출관리 추가</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 매장관리</span>
        </div>
    </div>
    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>기본 정보</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="Save_amt();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
                        <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
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
                                <label for="amt_date">매출일자</label>
                                <div >
                                    <select name="year" id="year" class="form-control form-control-sm" style="width:120px;display:inline-block">
                                    </select>&nbsp;년 &nbsp;&nbsp;

                                    <select name="month" id="month" class="form-control form-control-sm" style="width:60px;display:inline-block">
                                    </select>&nbsp;월&nbsp;&nbsp;

                                    <select name="day" id="day" class="form-control form-control-sm" style="width:60px;display:inline-block">
                                    </select>&nbsp;일
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                <a href="#" id="search_sbtn" onclick="Save_amt();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>    
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
            {headerName: "#", field: "num",type:'NumType', pinned:'left', cellClass: 'hd-grid-code'},
            {headerName: "동종업계코드", field: "competitor_cd", pinned:'left',  width: 85, cellClass: 'hd-grid-code'},
            {headerName: "동종업계명", field: "competitor_nm",  pinned:'left', width: 120, cellClass: 'hd-grid-code'},
            {headerName: "매출액", field: "sale_amt",  pinned:'left', width: 100, cellClass: 'hd-grid-code', editable: true, cellStyle: {"background-color": "#ffFF99"}, type:'currencyType'},
            {headerName: "매출액", field: "sale_date",  pinned:'left', width: 100, cellClass: 'hd-grid-code', hide:true},
            {width: 'auto'}
        ];

        const pApp = new App('',{
            gridId:"#div-gd",
        });
        let gx;

        $(document).ready(function() {
            pApp.ResizeGrid(205);
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns);
            pApp.BindSearchEnter();
            // Search();
        });

        function Search() {
            let data = $('form[name="search"]').serialize();

            let store_no = document.getElementById('store_no').value;
            let year = document.getElementById('year').value;
            let month = document.getElementById('month').value;
            let day = document.getElementById('day').value;

            if(store_no == '') {
                alert('매장을 선택해주세요.');
            } else if (year == '') {
                alert('매출년도를 선택해주세요.')
            } else if (month == '') {
                alert('매출월을 선택해주세요.')
            } else if (day == '') {
                alert('매출일을 선택해주세요.')
            } else {
                gx.Request('/store/stock/stk33/com_search', data);
            }
        }

        $(document).ready(function () {
            setDateBox();
        });

        // select box 연도 , 월 표시
        function setDateBox() {
            var dt = new Date();
            var year = "";
            var com_year = dt.getFullYear();

            $("#year").append("<option value=''>년도</option>");

            for (var y = com_year; y >= (com_year - 50); y--) {
            $("#year").append("<option value='" + y + "'>" + y + "</option>");
            }

            var month;
            $("#month").append("<option value=''>월</option>");
            for (var i = 1; i <= 12; i++) {
                if(i < 10) {
                    $("#month").append("<option value='"+ 0 + i + "'>0" + i + "</option>");
                } else {
                    $("#month").append("<option value='" + i + "'>" + i + "</option>");
                }
            }

            var day;
            $("#day").append("<option value=''>일</option>");
            for (var i = 1; i <= 31; i++) {
                if(i < 10) {
                    $("#day").append("<option value='"+ 0 + + i + "'>0" + i + "</option>");
                } else {
                    $("#day").append("<option value='" + i + "'>" + i + "</option>");
                }
            }

        }

        //매출액 저장
        function Save_amt() {
            let year = document.getElementById('year').value;
            let month = document.getElementById('month').value;
            let day = document.getElementById('day').value;
            let store_no = document.getElementById('store_no').value;


            let date = year+'-'+month+'-'+day;

            if (store_no === '') {
                alert('매장을 선택해주세요');
                return false;
            }

            if ($('#year').val() === '') {
                $('#year').focus();
                alert('매출년도를 선택해주세요');
                return false;
            }

            if ($('#month').val() === '') {
                $('#month').focus();
                alert(' 매출월을 선택해주세요');
                return false;
            }
            
            if ($('#day').val() === '') {
                $('#day').focus();
                alert('매출일을 선택해주세요');
                return false;
            }

            if(!confirm("매출액을 저장하시겠습니까?")) return;

                axios({
                    url: `/store/stock/stk33/save_amt`,
                    method: 'post',
                    data: {
                        data: gx.getRows(),
                        date: date
                    },
                }).then(function (res) {
                    if(res.data.code === 200) {
                        alert(res.data.msg);
                        // window.close();
                        // opener.parent.location.reload();
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