/*
  _token 값을 ajax할때 기본적으로 보내도록 설정
*/
$.ajaxSetup({
    headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  
/**
 * 매장검색
 */

$( document ).ready(function() {

    $('.select2-store').select2({
        ajax: {
            url: "/store/auto-complete/store",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    type:'select2',
                    keyword: params.term, // search term
                    page: params.page
                };
            },
            cache: true
        },
        width:'100%',
        placeholder: '',
        allowClear: true,
        minimumInputLength: 1,
        templateResult: function (state) {
            if (!state.id) {
                return state.text;
            }
            if(state.img !== undefined && state.img !== ""){
                var $state = $(
                    '<span><img src="' + state.img + '" style="width:50px" onError="this.src=\'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==\'"/> ' + '[' + state.id +  '] '  + state.text + '</span>'
                );
            } else {
                var $state = $(
                    '<span>' + '[' + state.id +  '] ' + state.text + '</span>'
                );
            }
            return $state;
        },
        //templateSelection: formatRepoSelection,
        language: {
            // You can find all of the options in the language files provided in the
            // build. They all must be functions that return the string that should be
            // displayed.
            inputTooShort: function () {
                return "한글자 이상 입력해 주세요.";
            }
        }
    });
    
});

function SearchStore(){
    this.grid = null;
}

SearchStore.prototype.Open = async function(callback = null, multiple_type = false){
    if(this.grid === null){
        this.isMultiple = multiple_type === "multiple";
        this.SetGrid("#div-gd-store");
        this.SetStoreTypeSelect();
        $("#SearchStoreModal").draggable();
        if(this.isMultiple) $("#SearchStoreModal #search_store_cbtn").css("display", "block");
        this.callback = callback;
    }
    $('#SearchStoreModal').modal({
        keyboard: false
    });
};

// 매장구분 세팅
SearchStore.prototype.SetStoreTypeSelect = async function(){
    const { data: { body: types } } = await axios({ 
        url: `/store/api/stores/search-storetype`, 
        method: 'get' 
    });
    for(let type of types) {
        $("#search_store_type").append(`<option value="${type.code_id}">${type.code_val}</option>`);
    }
}

SearchStore.prototype.SetGrid = function(divId){
    let columns = [];

    if(this.isMultiple) {
        columns.push({ field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null });
    }

    columns.push(
        { field:"store_cd", headerName:"매장코드", width:100, cellStyle: { "text-align": "center" }, hide: true },
        { field:"store_nm", headerName:"매장", width: "auto" },
    );

    if(!this.isMultiple) {
        columns.push({ 
            field:"choice", headerName:"선택", width:100, cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                if (params.data.store_cd !== undefined) {
                    return '<a href="javascript:void(0);" onclick="return searchStore.Choice(\'' + params.data.store_cd + '\',\'' + params.data.store_nm + '\');">선택</a>';
                }
            }
        });
    }

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchStore.prototype.Search = function(e) {
    const event_type = e?.type;
    if (event_type == 'keypress') {
        if (e.key && e.key == 'Enter') {
            let data = $('form[name="search_store"]').serialize();
            this.grid.Request('/store/api/stores/search', data);
        } else {
            return false;
        }
    } else {
        let data = $('form[name="search_store"]').serialize();
        this.grid.Request('/store/api/stores/search', data);
    }
};

SearchStore.prototype.Choice = function(code,name){
    if(this.callback !== null){
        this.callback(code, name);
    } else {
        if($('#store_no.select2-store').length > 0){
            $('#store_no').val(null);
            const option = new Option(name, code, true, true);
            $('#store_no').append(option).trigger('change');
        } else {
            if($('#store_no').length > 0){
                $('#store_no').val(code);
            }
            if($('#store_nm').length > 0){
                $('#store_nm').val(name);
            }
        }
        if($('#store_cd.select2-store').length > 0){
            $('#store_cd').val(null);
            const option = new Option(name, code, true, true);
            $('#store_cd').append(option).trigger('change');
        } else {
            if($('#store_cd').length > 0){
                $('#store_cd').val(code);
            }
            if($('#store_nm').length > 0){
                $('#store_nm').val(name);
            }
        }
    }
    this.InitValue();
    $('#SearchStoreModal').modal('toggle');
};

SearchStore.prototype.ChoiceMultiple = function(){
    let rows = this.grid.getSelectedRows();
    if(this.callback !== null){
        this.callback(rows);
    } else {
        let store_cds = rows.map(r => r.store_cd);
        let store_nms = rows.map(r => r.store_nm);

        if($('#store_no.select2-store').length > 0){
            for(let r of rows) {
                if($("#store_no").val().includes(r.store_cd)) continue;
                const option = new Option(r.store_nm, r.store_cd, true, true);
                $('#store_no').append(option).trigger('change');
            }
        } else {
            if($('#store_no').length > 0){
                $('#store_no').val(store_cds);
            }
            if($('#store_nm').length > 0){
                $('#store_nm').val(store_nms);
            }
        }
        if($('#store_cd.select2-store').length > 0){
            for(let r of rows) {
                if($("#store_cd").val().includes(r.store_cd)) continue;
                const option = new Option(r.store_nm, r.store_cd, true, true);
                $('#store_cd').append(option).trigger('change');
            }
        } else {
            if($('#store_cd').length > 0){
                $('#store_cd').val(store_cds);
            }
            if($('#store_nm').length > 0){
                $('#store_nm').val(store_nms);
            }
        }
    }
    this.InitValue();
    $('#SearchStoreModal').modal('toggle');
}

SearchStore.prototype.InitValue = () => {
    document.search_store.reset();
    searchStore.grid.setRows([]);
    $('#gd-store-total').html(0);
};


let searchStore = new SearchStore();

/**
 * 담당자 (MD) 검색
 */
function SearchMD(){
    this.grid = null;
}

SearchMD.prototype.Open = async function(callback = null){
    if(this.grid === null){
        this.SetGrid("#div-gd-md");
        $("#SearchMdModal").draggable();
        this.callback = callback;
    }
    this.Search();
    $('#SearchMdModal').modal({
        keyboard: false
    });
};

SearchMD.prototype.SetGrid = function(divId){
    let columns = [];

    columns.push(
        { field:"md_id", hide: true},
        { field:"md_nm", headerName:"이름", width: 150, cellStyle: {"text-align": "center"} },
        { 
            field:"choice", headerName:"선택", width: 60, cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                if (params.data.md_id !== undefined) {
                    return '<a href="javascript:void(0);" onclick="return searchMd.Choice(\'' + params.data.md_id + '\',\'' + params.data.md_nm + '\');">선택</a>';
                }
            }
        },
        { width: "auto" }
    );

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchMD.prototype.Search = function(e) {
    const event_type = e?.type;
    if (event_type == 'keypress') {
        if (e.key && e.key == 'Enter') {
            let data = $('form[name="search_md"]').serialize();
            this.grid.Request('/store/api/mds/search', data);
        } else {
            return false;
        }
    } else {
        let data = $('form[name="search_md"]').serialize();
        this.grid.Request('/store/api/mds/search', data);
    }
};

SearchMD.prototype.Choice = function(code,name) {
    if(this.callback !== null){
        this.callback(code, name);
    } else {
        if($('#md_id').length > 0){
            $('#md_id').val(code);
        }
        if($('#md_nm').length > 0){
            $('#md_nm').val(name);
        }
    }

    document.search_md.reset();
    searchMd.grid.setRows([]);
    $('#gd-md-total').html(0);

    $('#SearchMdModal').modal('toggle');
};

let searchMd = new SearchMD();

/**
 * 상품코드검색
 */

const conds = {
    brand: '브랜드',
    year: '년도',
    season: '시즌',
    gender: '성별',
    item: '아이템',
    opt: '품목'
};
function SearchPrdcd(){
    this.grid = null;
}

SearchPrdcd.prototype.Open = async function(callback = null, match = false){
    if(this.grid === null){
        this.isMatch = match === "match";
        this.SetGrid("#div-gd-prdcd");
        this.SetGridCond();
        // $("#SearchPrdcdModal").draggable();
        this.callback = callback;
    }
    $('#SearchPrdcdModal').modal({
        keyboard: false
    });
};

SearchPrdcd.prototype.SetGrid = function(divId){
    let columns = [];

    if (this.isMatch) {
        columns.push(
            { field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null },
            { field: "prd_cd", headerName: "상품코드", width: 120, cellStyle: {"text-align": "center"} },
            { field: "goods_no", headerName: "상품번호", width: 60, cellStyle: {"text-align": "center"} },
            { field: "prd_nm", headerName: "상품명", width: 400 },
            { field: "color", headerName: "컬러", width: 60, cellStyle: {"text-align": "center"} },
            { field: "size", headerName: "사이즈", width: 60, cellStyle: {"text-align": "center"} },
            { field: "match_yn", headerName: '매칭여부', cellClass: 'hd-grid-code', width: 60},
            { field: "rt", headerName: '등록일', cellClass: 'hd-grid-code', width: 150, hide:true},
            { width: "auto" }
            );
    } else {
        columns.push(
            { field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null },
            { field: "prd_cd", headerName: "상품코드", width: 120, cellStyle: {"text-align": "center"} },
            { field: "goods_no", headerName: "상품번호", width: 60, cellStyle: {"text-align": "center"} },
            { field: "goods_nm", headerName: "상품명", width: 400 },
            { field: "goods_opt", headerName: "옵션", width: 300 },
            { field: "color", headerName: "컬러", width: 60, cellStyle: {"text-align": "center"} },
            { field: "size", headerName: "사이즈", width: 60, cellStyle: {"text-align": "center"} },
            { width: "auto" }
            );
    }

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchPrdcd.prototype.SetGridCond = async function() {
    Object.keys(conds).forEach( async (cond_title) => {
        let columns = [];
        
        columns.push(
            { field: "chk", headerName: '', cellClass: 'hd-grid-code', checkboxSelection: true, width: 28, sort: null },
            { field: "item", headerName: conds[cond_title], width: "auto",
                cellStyle: (params) => (params.data.key || '') === 'contain' ? {"color": params.data.item === '포함' ? "green" : "red"} : '',
                editable: (params) => (params.data.key || '') === 'contain',
                cellRenderer: (params) => {
                    if((params.data.key || '') === 'contain') return params.value;
                    return `${(params.data.code_id || '') != '' ? `[${params.data.code_id}] ` : ''}${params.data.code_val || ''}`;
                },
                cellEditorSelector: function(params) {
                    if((params.data.key || '') === 'contain') {
                        return {
                            component: 'agRichSelectCellEditor',
                            params: { 
                                values: ['포함', '미포함']
                            },
                        };
                    }
                    return false;
                },
            },
        );

        this[cond_title + '_grid'] = await new HDGrid(document.querySelector( "#div-gd-prdcd-" + cond_title ), columns, {
            pinnedTopRowData: [{item: "포함", key: "contain"}],
            getRowStyle: (params) => {
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#f2f2f2', 'border': 'none'};
            },
        });
        document.querySelector( "#div-gd-prdcd-" + cond_title ).style.height = '204px';
    });
    const { data: { body: res } } = await axios({ 
        url: '/store/api/prdcd/conds', 
        method: 'get' 
    });
    Object.keys(res).forEach(r => {
        this[r + '_grid'].gridOptions.api.setRowData(res[r]);
    });
}

SearchPrdcd.prototype.Search = function(e) {
    const event_type = e?.type;

    const request = () => {
        let data = $('form[name="search_prdcd"]').serialize();

        Object.keys(conds).forEach(c => {
            let rows = this[c + '_grid'].getSelectedRows();
            rows.forEach(r => {
                data += `&${c}[]=${r.code_id}`;
            });

            let is_contain = this[c + '_grid'].gridOptions.api.getPinnedTopRow(0).data.item === "포함";
            data += `&${c}_contain=${is_contain}`;
            data += '&match='+this.isMatch;
        });
        this.grid.Request('/store/api/prdcd/search', data, -1);
    }

    if (event_type == 'keypress') {
        if (e.key && e.key == 'Enter') {
            request();
        } else {
            return false;
        }
    } else {
        request();
    }
};

SearchPrdcd.prototype.Choice = function() {
    if(this.callback !== null){
        this.callback(code, name);
    } else {
        let rows = this.grid.getSelectedRows();
        if(rows.length < 1) return alert("항목을 선택해주세요.");
        
        if($('#prd_cd').length > 0){
            $('#prd_cd').val(rows.map(r => r.prd_cd).join(","));
        }
    }

    document.search_prdcd.reset();
    this.grid.setRows([]);
    Object.keys(conds).forEach(c => {
        this[c + '_grid'].gridOptions.api.forEachNodeAfterFilter(node => {
            node.setSelected(false);
        });
    });

    $('#gd-prdcd-total').html(0);
    $('#SearchPrdcdModal').modal('toggle');
};

/**
 * 원부자재코드검색
 */

const conds_sub = {
    brand: '구분',
    year: '년도',
    season: '시즌',
    gender: '성별',
    item: '아이템',
    opt: '품목'
};
function SearchPrdcd_sub(){
    this.grid = null;
}

SearchPrdcd_sub.prototype.Open = async function(callback = null){
    if(this.grid === null){
        this.SetGrid("#div-gd-prdcd-sub");
        this.SetGridCond();
        this.callback = callback;
    }
    $('#SearchPrdcd_sub_Modal').modal({
        keyboard: false
    });
};

SearchPrdcd_sub.prototype.SetGrid = function(divId){
    let columns = [];
        columns.push(
            { field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null },
            { field: "prd_cd", headerName: "원부자재코드", width: 120, cellStyle: {"text-align": "center"} },
            { field: "goods_no", headerName: "상품번호", width: 60, cellStyle: {"text-align": "center"} },
            { field: "prd_nm", headerName: "상품명", width: 400 },
            { field: "goods_opt", headerName: "옵션", width: 300 },
            { field: "color", headerName: "컬러", width: 60, cellStyle: {"text-align": "center"} },
            { field: "size", headerName: "사이즈", width: 60, cellStyle: {"text-align": "center"} },
            { width: "auto" }
            );
    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchPrdcd_sub.prototype.SetGridCond = async function() {
    Object.keys(conds_sub).forEach( async (cond_title) => {
        let columns = [];
        
        columns.push(
            { field: "chk", headerName: '', cellClass: 'hd-grid-code', checkboxSelection: true, width: 28, sort: null },
            { field: "item", headerName: conds_sub[cond_title], width: "auto",
                cellStyle: (params) => (params.data.key || '') === 'contain' ? {"color": params.data.item === '포함' ? "green" : "red"} : '',
                editable: (params) => (params.data.key || '') === 'contain',
                cellRenderer: (params) => {
                    if((params.data.key || '') === 'contain') return params.value;
                    return `${(params.data.code_id || '') != '' ? `[${params.data.code_id}] ` : ''}${params.data.code_val || ''}`;
                },
                cellEditorSelector: function(params) {
                    if((params.data.key || '') === 'contain') {
                        return {
                            component: 'agRichSelectCellEditor',
                            params: { 
                                values: ['포함', '미포함']
                            },
                        };
                    }
                    return false;
                },
            },
        );

        this[cond_title + '_grid'] = await new HDGrid(document.querySelector( "#div-gd-prdcd-sub-" + cond_title ), columns, {
            pinnedTopRowData: [{item: "포함", key: "contain"}],
            getRowStyle: (params) => {
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#f2f2f2', 'border': 'none'};
            },
        });
        document.querySelector( "#div-gd-prdcd-sub-" + cond_title ).style.height = '204px';
    });
    const { data: { body: res } } = await axios({ 
        url: '/store/api/prdcd/conds_sub', 
        method: 'get' 
    });
    Object.keys(res).forEach(r => {
        this[r + '_grid'].gridOptions.api.setRowData(res[r]);
    });
}

SearchPrdcd_sub.prototype.Search = function(e) {
    const event_type = e?.type;

    const request = () => {
        let data = $('form[name="search_prdcd_sub"]').serialize();

        Object.keys(conds_sub).forEach(c => {
            let rows = this[c + '_grid'].getSelectedRows();
            rows.forEach(r => {
                data += `&${c}[]=${r.code_id}`;
            });

            let is_contain = this[c + '_grid'].gridOptions.api.getPinnedTopRow(0).data.item === "포함";
            data += `&${c}_contain=${is_contain}`;
            data += '&match='+this.isMatch;
        });
        this.grid.Request('/store/api/prdcd/search_sub', data, -1);
    }

    if (event_type == 'keypress') {
        if (e.key && e.key == 'Enter') {
            request();
        } else {
            return false;
        }
    } else {
        request();
    }
};

SearchPrdcd_sub.prototype.Choice = function() {
    if(this.callback !== null){
        this.callback(code, name);
    } else {
        let rows = this.grid.getSelectedRows();
        if(rows.length < 1) return alert("항목을 선택해주세요.");
        
        if($('#prd_cd_sub').length > 0){
            $('#prd_cd_sub').val(rows.map(r => r.prd_cd).join(","));
        }
    }

    document.search_prdcd_sub.reset();
    this.grid.setRows([]);
    Object.keys(conds_sub).forEach(c => {
        this[c + '_grid'].gridOptions.api.forEachNodeAfterFilter(node => {
            node.setSelected(false);
        });
    });

    $('#gd-prdcd-sub-total').html(0);
    $('#SearchPrdcd_sub_Modal').modal('toggle');
};

let searchPrdcd = new SearchPrdcd();
let searchPrdcd_sub = new SearchPrdcd_sub();

$( document ).ready(function() {
    // 매장 검색 클릭 이벤트 바인딩 및 콜백 사용
    $( ".sch-store" ).on("click", function() {
        searchStore.Open();
    });

    // 담당자 검색 클릭 이벤트 바인딩 및 콜백 사용
    $( ".sch-md" ).on("click", function() {
        searchMd.Open();
    });

    // 상품코드 검색 클릭 이벤트 바인딩 및 콜백 사용
    $(".sch-prdcd").on("click", function() {
        searchPrdcd.Open();
    });

    // 원부자재코드 검색 클릭 이벤트 바인딩 및 콜백 사용
    $(".sch-prdcd_sub").on("click", function() {
        searchPrdcd_sub.Open();
    });

    // 입고 내 송장번호 선택
    $(".sch-invoice").on("click", function() {
        let url = "/store/cs/cs01/choice";
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1400,height=800");
    });
});

/**
 * @param {Array} select2 초기화할 select2 css 선택자 이름 추가 - ex) ['.test_cd', '#test_cd']
 * @param {String} form_name 초기화할 검색 폼 이름 - ex ) "search", "search2", "f1"
 */
var initSearch = (select2 = [], form_name = "search") => { // 검색 초기화 함수 추가    
    document[form_name].reset();
    /**
     * 기본 초기화
     */
    if ($('#brand_cd').length > 0) $('#brand_cd').val("").trigger('change'); // 브랜드 select2 박스 초기화
    if ($('#cat_cd').length > 0) $('#cat_cd').val("").trigger('change'); // 카테고리 select2 박스 초기화
    if ($('#com_cd').length > 0) $('#com_cd').val("").trigger('change'); // 업체 select2 박스 초기화
    if ($('#store_cd').length > 0) $('#store_cd').val("").trigger('change'); // 매장명 select2 박스 초기화
    if ($('#goods_stat').length > 0) $('#goods_stat').val("").trigger('change'); // 상품상태 select2 박스 초기화
    /**
     * 동적 초기화
     */
    select2.map(key => {
        if ($(key).length > 0) $(key).val("").trigger('change'); // 전달받은 select2 박스 초기화
    });
};