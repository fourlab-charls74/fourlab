@extends('store_with.layouts.layout')
@section('title','매장 공지사항')
@section('content')

<div class="page_tit">
    @if($store_notice_type === "notice")
        <h3 class="d-inline-flex">매장 공지사항</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 매장관리</span>
            <span>/ 매장 공지사항</span>
        </div>
    @else 
        <h3 class="d-inline-flex">VMD 게시판</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 매장관리</span>
            <span>/ VMD 게시판</span>
        </div>
    @endif
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <input type="hidden" id= "store_notice_type" name="store_notice_type" value="{{ $store_notice_type }}">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="#" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a> -->
                    <a href="/store/community/comm01/{{ $store_notice_type }}/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">등록일</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">공개여부</label>
                            <div class="flax_box">
                                <select name='use_yn' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    <option value='Y'>예</option>
                                    <option value='N'>아니요</option>
                                </select>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                          <label for="">제목</label>
                          <div class="flax_box">
                            <input type='text' class="form-control form-control-sm search-all search-enter" name='subject' value=''>
                          </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                        <label for="">내용</label>
                        <div class="flax_box">
                            <input type='text' class="form-control form-control-sm search-all search-enter" name='content' value=''>
                        </div>
                        </div>
                    </div>
                </div>
                    <div class="row">
                        @if($store_notice_type === "notice")
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="good_types">판매채널/매장구분</label>
                                    <div class="d-flex align-items-center">
                                        <div class="flex_box w-100">
                                            <select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
                                                <option value=''>전체</option>
                                            @foreach ($store_channel as $sc)
                                                <option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                        <span class="mr-2 ml-2">/</span>
                                        <div class="flex_box w-100">
                                            <select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled>
                                                <option value=''>전체</option>
                                            @foreach ($store_kind as $sk)
                                                <option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="store_no">매장</label>
                                    <div class="form-inline inline_btn_box search-enter" >
                                        <input type='hidden' id="store_nm" name="store_nm">
                                        <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">자료수/정렬</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                            <option value="2000">2000</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="rt">등록일</option>
                                            <option value="subject">제목</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                        <div class="btn-group" role="group">
                                            <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
                                            <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
                                        </div>
                                        <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                        <input type="radio" name="ord" id="sort_asc" value="asc">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="/store/community/comm01/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<!-- DataTales Example -->

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
<script language="javascript">
    let columns = [
        {headerName: "#", field: "num",type:'NumType', cellClass: 'hd-grid-code'},
        {headerName: "제목", field: "subject", width: 400,
            cellRenderer: function(params) {
                return '<a href="/store/community/comm01/show/' + $('#store_notice_type').val() + '/' + params.data.ns_cd +'" rel="noopener">'+ params.value+`${params.data.attach_file_yn === 'Y' ? `<i class="bi bi-paperclip"></i>` : '' }</a>`;
            },
        },
        {headerName: "ID", field: "admin_id",  width: 80, cellClass: 'hd-grid-code'},
        {headerName: "이름", field: "admin_nm",  width: 80, cellClass: 'hd-grid-code'},
        {headerName: "이메일", field: "admin_email", width: 130, cellClass: 'hd-grid-code'},
        {headerName: "조회수", field: "cnt", type:'numberType',width: 50, cellClass: 'hd-grid-code'},
        {headerName: "전체 공지 여부", field: "all_store_yn",width: 90, cellClass: 'hd-grid-code',
            cellStyle: params => {
                if(params.data.all_store_yn == 'Y'){
                    return {color:'red'}
                }else{
                    return {color:'blue'}
                }
            },
            cellRenderer: function(params){
                if(params.data.stores == null){
                    return params.data.all_store_yn = "Y";
                }else{
                    return params.data.all_store_yn = "N";
                }
            }
        },
        {headerName: "공지매장", field: "store_nm", width: 340, cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                return params.data.stores;
            }
        },
        {headerName: "등록일시", field: "rt", type:"DateTimeType"},
        {headerName: "수정일시", field: "ut", type:"DateTimeType"},
        {headerName: "글번호", field: "ns_cd", hide:true },
        {width: 'auto'}
    ];

</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        pApp.BindSearchEnter();
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        const notice_type = $('#store_notice_type').val();

        if(notice_type === 'notice'){
            gx.Request('/store/community/comm01/notice/search', data);
        } else {
            gx.Request('/store/community/comm01/vmd/search', data);
        }
    }

</script>

<script>
    const initSearchInputs = () => {
        document.search.reset(); // 모든 일반 input 초기화
        $('#store_no').val(null).trigger('change'); // 브랜드 select2 박스 초기화
        location.reload();
    };

    // 판매채널 셀렉트박스가 선택되지 않으면 매장구분 셀렉트박스는 disabled처리
    $(document).ready(function() {
            const store_channel = document.getElementById("store_channel");
            const store_channel_kind = document.getElementById("store_channel_kind");

            store_channel.addEventListener("change", () => {
                if (store_channel.value) {
                    store_channel_kind.disabled = false;
                } else {
                    store_channel_kind.disabled = true;
                }
            });
        });

        // 판매채널이 변경되면 해당 판매채널의 매장구분을 가져오는 부분
        function chg_store_channel() {

            const sel_channel = document.getElementById("store_channel").value;

            $.ajax({
                method: 'post',
                url: '/store/standard/std02/show/chg-store-channel',
                data: {
                    'store_channel' : sel_channel
                    },
                dataType: 'json',
                success: function (res) {
                    if(res.code == 200){
                        $('#store_channel_kind').empty();
                        let select =  $("<option value=''>전체</option>");
                        $('#store_channel_kind').append(select);

                        for(let i = 0; i < res.store_kind.length; i++) {
                            let option = $("<option value="+ res.store_kind[i].store_kind_cd +">" + res.store_kind[i].store_kind + "</option>");
                            $('#store_channel_kind').append(option);
                        }

                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    }
                },
                error: function(e) {
                    console.log(e.responseText)
                }
            });
        }	
</script>
@stop
