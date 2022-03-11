@extends('head_with.layouts.layout-nav')
@section('title','쿠폰지급')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">쿠폰지급</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ 쿠폰지급</span>
            </div>
        </div>
        <div>
            <a href="#" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm save-btn">쿠폰지급</a>
        </div>
    </div>
    <div class="card_wrap aco_card_wrap">
        <div class="card">
            <div class="card-header mb-0 d-flex align-items-center justify-content-between">
                <a href="#" class="m-0 font-weight-bold">
                    쿠폰 선택 총 <span id="gx-total1" class="text-primary">0</span> 건 
                </a>
                <div class="fr_box">
                    <button class="btn-sm btn btn-primary mr-1 add-coupon-btn">쿠폰추가</button>
                    <button class="btn-sm btn btn-primary mr-1 del-coupon-btn">쿠폰삭제</button>
                </div>
            </div>
            <div class="card-body brtn mx-0">
                <div class="table-responsive mt-1">
                    <div id="div-gd1" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header mb-0 d-flex align-items-center justify-content-between">
                <a href="#" class="m-0 font-weight-bold">
                    회원 선택 총 <span id="gx-total2" class="text-primary">0</span> 건
                </a>
                <div class="flax_box">
                    <button class="btn-sm btn btn-primary mr-1 add-user-btn">회원추가</button>
                    <button class="btn-sm btn btn-primary mr-1 del-user-btn">회원삭제</button>
                </div>
            </div>
            <div class="card-body brtn mx-0">
                <div class="table-responsive mt-1">
                    <div id="div-gd2" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const user_ids = '{{$user_ids}}';
const coupon_nos = '{{$coupon_nos}}';
const coupon_types = {
    A : '전체',
    O : '온라인',
    E : '이벤트', 
    F : '오프라인',
    C : 'CRM'
};

const columns1 = [
    {
        headerName: '',
        headerCheckboxSelection: true,
        checkboxSelection: true,
        width:28
    },
    {field:"coupon_no", headerName:"상품코드"},
    {
        field:"coupon_type",
        headerName:"구분",
        cellRenderer : (p) => coupon_types[p.value]
    },
    {field:"coupon_nm", headerName:"쿠폰명", type:"HeadCouponType", width: 250},
    { width:"auto" }
];

const columns2 = [
    {
        headerName: '',
        headerCheckboxSelection: true,
        checkboxSelection: true,
        width:28
    },
    {field:"user_id", headerName:"아이디"},
    {field:"name", headerName:"이름"},
    { width:"auto" }
];

const pApp1 = new App('', {gridId: "#div-gd1"});
const gridDiv1 = document.querySelector(pApp1.options.gridId);
const gx1 = new HDGrid(gridDiv1, columns1);

const pApp2 = new App('', {gridId: "#div-gd2"});
const gridDiv2 = document.querySelector(pApp2.options.gridId);
const gx2 = new HDGrid(gridDiv2, columns2);

const addCoupon = (row) => {
    let isAdd = true;

    gx1.gridOptions.api.forEachNode(node => {
        if (!isAdd) return false;
        if (node.data.coupon_no == row.coupon_no) isAdd = false;
    });

    if (isAdd) {
        gx1.gridOptions.api.updateRowData({add: [row]});
    }
};

const addUser = (row) => {
    let isAdd = true;

    gx2.gridOptions.api.forEachNode(node => {
        if (!isAdd) return false;
        if (node.data.user_id == row.user_id) isAdd = false;
    });

    if (isAdd) {
        gx2.gridOptions.api.updateRowData({add: [row]});
    }
}

const validate = () => {
    if (gx1.getSelectedRows().length === 0) {
        alert('지급할 쿠폰을 선택해주세요.');
        return false;
    }

    if (gx2.getSelectedRows().length === 0) {
        alert('쿠폰을 지급할 회원을 선택해주세요.');
        return false;
    }

    return confirm('해당 회원에게 쿠폰을 지급하시겠습니까?');
}

function couponSelectedCallback(rows) {
    rows.forEach(addCoupon);
}

function usersCallback(rows) {
    console.log(rows);
    rows.forEach(addUser);
}

$('.add-coupon-btn').click((e) => {
    e.preventDefault();

    openCouponSelect();
});

$('.del-coupon-btn').click((e) => {
    e.preventDefault();

    const rows = gx1.getSelectedRows();

    if(rows.length === 0) {
        alert("삭제할 쿠폰을 선택해주세요.");
        return;
    }

    rows.forEach(function(row){
        gx1.gridOptions.api.updateRowData({remove: [row]});
    });

    $('#gx-total1').html(gx1.gridOptions.api.getDisplayedRowCount());
});

$('.add-user-btn').click((e) => {
    e.preventDefault();
    openUserSelect();
});

$('.save-btn').click((e) => {
    if (validate() === false) return;

    const rows1 = gx1.getSelectedRows();
    const rows2 = gx2.getSelectedRows();

    const coupon_nos = [];
    const user_ids = rows2.map((row) => row.user_id);

    let isCommit = true;

    rows1.forEach((row) => {
        if (isCommit === false) return;
        
        if (row.coupon_type == 'F') {
            alert('오프라인쿠폰은 쿠폰을 지급할 수 없습니다.');
            isCommit = false;
            return;
        }

        coupon_nos.push(row.coupon_no);
    });

    if (isCommit === false) return;

    $.ajax({    
        type: "put",
        url: `/head/promotion/prm10/gift/coupon`,
        data: { coupon_nos, user_ids },
        success: function(data) {
            alert("쿠폰을 지급하였습니다.");
            // window.close();
        },
        error : function(res, a, b) {
            alert(res.responseJSON.message);
        }
    });
});

const Search1 = () => {
    gx1.Request(`/head/promotion/prm10/search/gift/coupon`, `coupon_nos=${coupon_nos}`, -1, function(res){
        $('#gx-total1').html(res.head.total);
    });
}

const Search2 = () => {
    gx2.Request(`/head/promotion/prm10/search/gift/user`, `user_ids=${user_ids}`, -1, function(res){
        $('#gx-total2').html(res.head.total);
    });
}

coupon_nos && Search1();
user_ids && Search2();
</script>
@stop
