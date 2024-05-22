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

    /** 스타일넘버 자동완성 */
    $(".ac-style-no")
        .on('keydown',function(event){
            if ( event.keyCode === 13) {
                $(this).autocomplete('close');
            }
        })
        .autocomplete({
        //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
        source : function(request, response) {
            $.ajax({
                method: 'get',
                url: '/shop/auto-complete/style-no',
                data: { keyword : this.term },
                success: function (data) {
                    response(data);
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        },
        minLength: 1,
        autoFocus: false,
        delay: 100,
        create:function(event,ui){
            $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
                let txt;
                if(item.img !== undefined && item.img !== "") {
                    txt = '<div><img src=\"' + item.img + '\" style=\"width:30px\" onError="this.src=\'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==\'"/> ' + item.label + '</div>';
                } else {
                    txt = '<div>' + item.label + '</div>';
                }
                return $( "<li>" )
                    .append( txt )
                    .appendTo( ul );
            };
        }
    });

    /** 상품명 자동완성 */
    $(".ac-goods-nm")
        .on('keydown',function(event){
            if ( event.keyCode === 13) {
                $(this).autocomplete('close');
            }
        })
        .autocomplete({
        // keydown 됐을때 해당 값을 가지고 서버에서 검색함.
        source : function(request, response) {
            $.ajax({
                method: 'get',
                url: '/shop/auto-complete/goods-nm',
                data: { keyword : this.term },
                success: function (data) {
                    response(data);
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        },
        minLength: 1,
        autoFocus: false,
        delay: 100,
        create:function(event,ui) {
            $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
                let txt;
                if(item.img !== undefined && item.img !== "") {
                    txt = '<div><img src=\"' + item.img + '\" style=\"width:30px\" onError="this.src=\'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==\'"/> ' + item.label + '</div>';
                } else {
                    txt = '<div>' + item.label + '</div>';
                }
                return $( "<li>" )
                    .append( txt )
                    .appendTo( ul );
            };
        }
    });

    /** 상품명(영문) 자동완성 */
    $(".ac-goods-nm-eng")
        .on('keydown',function(event){
            if ( event.keyCode === 13) {
                $(this).autocomplete('close');
            }
        })
        .autocomplete({
        // keydown 됐을때 해당 값을 가지고 서버에서 검색함.
        source : function(request, response) {
            $.ajax({
                method: 'get',
                url: '/shop/auto-complete/goods-nm-eng',
                data: { keyword : this.term },
                success: function (data) {
                    response(data);
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        },
        minLength: 1,
        autoFocus: false,
        delay: 100,
        create:function(event,ui) {
            $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
                let txt;
                if(item.img !== undefined && item.img !== "") {
                    txt = '<div><img src=\"' + item.img + '\" style=\"width:30px\" onError="this.src=\'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==\'"/> ' + item.label + '</div>';
                } else {
                    txt = '<div>' + item.label + '</div>';
                }
                return $( "<li>" )
                    .append( txt )
                    .appendTo( ul );
            };
        }
    });

    $('.select2-brand').select2({
        ajax: {
            url: "/shop/auto-complete/brand",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    type:'select2',
                    keyword: params.term, // search term
                    page: params.page,
                    is_all: this[0].dataset.allBrand == "true",
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

    $( ".sch-brand" ).click(function() {
        searchBrand.Open();
    });

    $(".select2-user_group").select2();

    

    $('.select2-store').select2({
        ajax: {
            url: "/shop/auto-complete/store",
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

function SearchBrand(){
    this.grid = null;
}

SearchBrand.prototype.Open = function(callback = null){
    if(this.grid === null){
        this.SetGrid("#div-gd-brand");
        //gxBrand = new HDGrid(document.querySelector("#div-gd-brand"), columnsBrand);
        $("#SearchBrandModal").draggable();
        this.callback = callback;
    }
    $('#SearchBrandModal').modal({
        keyboard: false
    });
};

SearchBrand.prototype.SetGrid = function(divId){
    const columns = [
        {field:"brand" , headerName:"브랜드",width:100},
        {field:"brand_nm" , headerName:"브랜명",width:150},
        {field:"use_yn" , headerName:"사용여부",width:100,cellClass:'hd-grid-code'},
        {field:"choice" , headerName:"선택",width:100,cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                if (params.data.brand !== undefined) {
                    return '<a href="javascript:void(0);" onclick="return searchBrand.Choice(\'' + params.data.brand + '\',\'' + params.data.brand_nm + '\');">선택</a>';
                }
            }
        },
        {field:"nvl" , headerName:""},
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchBrand.prototype.Search = function(e) {
    const event_type = e?.type;
    if (event_type == 'keypress') {
        if (e.key && e.key == 'Enter') {
            let data = $('form[name="search_brand"]').serialize();
            this.grid.Request('/shop/api/brand/getlist', data);
        } else {
            return false;
        }
    } else {
        let data = $('form[name="search_brand"]').serialize();
        this.grid.Request('/shop/api/brand/getlist', data);
    }
};

SearchBrand.prototype.Choice = function(code,name){
    if(this.callback !== null){
        this.callback(code,name);
    } else {
        if($('#brand_cd.select2-brand').length > 0){
            $('#brand_cd').val(null);
            const option = new Option(name, code, true, true);
            $('#brand_cd').append(option).trigger('change');
        } else {
            if($('#brand_cd').length > 0){
                $('#brand_cd').val(code);
            }
            if($('#brand_nm').length > 0){
                $('#brand_nm').val(name);
            }
        }
    }
    $('#SearchBrandModal').modal('toggle');
};
let searchBrand = new SearchBrand();

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
        url: `/shop/api/stores/search-storetype`, 
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
            this.grid.Request('/shop/api/stores/search', data);
        } else {
            return false;
        }
    } else {
        let data = $('form[name="search_store"]').serialize();
        this.grid.Request('/shop/api/stores/search', data);
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

//창고검색
function SearchStorage(){
    this.grid = null;
}

SearchStorage.prototype.Open = async function(callback = null, multiple_type = false){
    if(this.grid === null){
        this.isMultiple = multiple_type === "multiple";
        this.SetGrid("#div-gd-storage");
        // this.SetStoreTypeSelect();
        $("#SearchStorageModal").draggable();
        if(this.isMultiple) $("#SearchStorageModal #search_storage_cbtn").css("display", "block");
        this.callback = callback;
    }
    $('#SearchStorageModal').modal({
        keyboard: false
    });
};


SearchStorage.prototype.SetGrid = function(divId){
    let columns = [];

    if(this.isMultiple) {
        columns.push({ field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null });
    }

    columns.push(
        { field:"storage_cd", headerName:"창고코드", width:100, cellStyle: { "text-align": "center" }, hide: true },
        { field:"storage_nm", headerName:"창고명", width: "auto" },
    );

    if(!this.isMultiple) {
        columns.push({ 
            field:"choice", headerName:"선택", width:100, cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                if (params.data.storage_cd !== undefined) {
                    return '<a href="javascript:void(0);" onclick="return searchStorage.Choice(\'' + params.data.storage_cd + '\',\'' + params.data.storage_nm + '\');">선택</a>';
                }
            }
        });
    }

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchStorage.prototype.Search = function(e) {
    const event_type = e?.type;
    if (event_type == 'keypress') {
        if (e.key && e.key == 'Enter') {
            let data = $('form[name="search_storage"]').serialize();
            this.grid.Request('/shop/api/storage/search', data);
        } else {
            return false;
        }
    } else {
        let data = $('form[name="search_storage"]').serialize();
        this.grid.Request('/shop/api/storage/search', data);
    }
};

SearchStorage.prototype.Choice = function(code,name){
    if(this.callback !== null){
        this.callback(code, name);
    } else {
        if($('#storage_no.select2-storage').length > 0){
            $('#storage_no').val(null);
            const option = new Option(name, code, true, true);
            $('#storage_no').append(option).trigger('change');
        } else {
            if($('#storage_no').length > 0){
                $('#storage_no').val(code);
            }
            if($('#storage_nm').length > 0){
                $('#storage_nm').val(name);
            }
        }
        if($('#storage_cd.select2-storage').length > 0){
            $('#storage_cd').val(null);
            const option = new Option(name, code, true, true);
            $('#storage_cd').append(option).trigger('change');
        } else {
            if($('#storage_cd').length > 0){
                $('#storage_cd').val(code);
            }
            if($('#storage_nm').length > 0){
                $('#storage_nm').val(name);
            }
        }
    }
    this.InitValue();
    $('#SearchStorageModal').modal('toggle');
};

// SearchStorage.prototype.ChoiceMultiple = function(){
//     let rows = this.grid.getSelectedRows();
//     if(this.callback !== null){
//         this.callback(rows);
//     } else {
//         let store_cds = rows.map(r => r.store_cd);
//         let store_nms = rows.map(r => r.store_nm);

//         if($('#store_no.select2-store').length > 0){
//             for(let r of rows) {
//                 if($("#store_no").val().includes(r.store_cd)) continue;
//                 const option = new Option(r.store_nm, r.store_cd, true, true);
//                 $('#store_no').append(option).trigger('change');
//             }
//         } else {
//             if($('#store_no').length > 0){
//                 $('#store_no').val(store_cds);
//             }
//             if($('#store_nm').length > 0){
//                 $('#store_nm').val(store_nms);
//             }
//         }
//         if($('#store_cd.select2-store').length > 0){
//             for(let r of rows) {
//                 if($("#store_cd").val().includes(r.store_cd)) continue;
//                 const option = new Option(r.store_nm, r.store_cd, true, true);
//                 $('#store_cd').append(option).trigger('change');
//             }
//         } else {
//             if($('#store_cd').length > 0){
//                 $('#store_cd').val(store_cds);
//             }
//             if($('#store_nm').length > 0){
//                 $('#store_nm').val(store_nms);
//             }
//         }
//     }
//     this.InitValue();
//     $('#SearchStoreModal').modal('toggle');
// }

SearchStorage.prototype.InitValue = () => {
    document.search_store.reset();
    searchStorage.grid.setRows([]);
    $('#gd-storage-total').html(0);
};


let searchStorage = new SearchStorage();

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
            this.grid.Request('/shop/api/mds/search', data);
        } else {
            return false;
        }
    } else {
        let data = $('form[name="search_md"]').serialize();
        this.grid.Request('/shop/api/mds/search', data);
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
    opt: '품목',
    item: '하위품목'
};
function SearchPrdcd(){
	this.grid = null;
}

SearchPrdcd.prototype.Open = async function(callback = null, match = false, prd_cd_p = false){
	if(this.grid === null){
		this.isMatch = match === "match";
		this.isPrdCdP = prd_cd_p;
		if(this.isPrdCdP) this.SetModal();
		this.SetGrid("#div-gd-prdcd");
		this.SetGridCond();
		// $("#SearchPrdcdModal").draggable();
		this.callback = callback;

		if (this.isMatch == false) {
			$("input:radio[name='match_yn']:radio[value='Y']").prop('checked', true);
		}
	}
	$('#SearchPrdcdModal').modal({
		keyboard: false
	});
};

SearchPrdcd.prototype.SetModal = function() {
	$("#SearchPrdcdModalLabel").text("품번 검색");
	$("#search_prdcd_match").addClass("d-none");
	$("#search_prdcd_code").addClass("col-lg-6");
	$("#search_prdcd_name").addClass("col-lg-6");
	$("#search_prdcd_name_eng").addClass("col-lg-6");
	$("#search_prdcd_styleno").addClass("col-lg-6");
	$("#search_prdcd_code label").text("품번");
	$("#select_prdcd_btn").addClass("d-none");
}

SearchPrdcd.prototype.SetGrid = function(divId){
	let columns = [];

	if (this.isMatch) {
		columns.push(
			{ field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null },
			{ field: "prd_cd", headerName: "바코드", width: 120, cellStyle: {"text-align": "center"} },
			{ field: "goods_no", headerName: "온라인코드", width: 60, cellStyle: {"text-align": "center"} },
			{ field: "prd_nm", headerName: "상품명", width: 300 },
			{ field: "prd_cd1", headerName: "품번", width: 120, cellStyle: {"text-align": "center"} },
			{ field: "color", headerName: "컬러", width: 60, cellStyle: {"text-align": "center"} },
			{ field: "size", headerName: "사이즈", width: 60, cellStyle: {"text-align": "center"} },
			{ field: "match_yn", headerName: '매칭여부', cellClass: 'hd-grid-code', width: 60},
			{ field: "rt", headerName: '등록일', cellClass: 'hd-grid-code', width: 150, hide:true},
			{ width: "auto" }
		);
	} else if (this.isPrdCdP) {
		columns.push(
			{ headerName: "선택", width: 60, cellStyle: {"text-align": "center"},
				cellRenderer: (params) => `<a href="javascript:void(0);" onclick="return searchPrdcd.ChoiceOne('${params.data.prd_cd_p}');">선택</a>`,
			},
			{ field: "prd_cd_p", headerName: "품번", width: 100, cellStyle: {"text-align": "center"} },
			{ field: "goods_no", headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"} },
			{ field: "style_no", headerName: "스타일넘버", width: 100, cellStyle: {"text-align": "center"} },
			{ field: "goods_nm", headerName: "상품명", width: 300 },
			{ field: "goods_nm_eng", headerName: "상품명(영문)", width: 280 },
			{ width: "auto" }
		);
	} else {
		columns.push(
			{ field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null },
			{ field: "prd_cd", headerName: "바코드", width: 120, cellStyle: {"text-align": "center"} },
			{ field: "goods_no", headerName: "온라인코드", width: 60, cellStyle: {"text-align": "center"} },
			{ field: "goods_nm", headerName: "상품명", width: 300 },
			{ field: "goods_opt", headerName: "옵션", width: 150 },
			{ field: "prd_cd1", headerName: "품번", width: 120, cellStyle: {"text-align": "center"} },
			{ field: "color", headerName: "컬러", width: 60, cellStyle: {"text-align": "center"} },
			{ field: "size_kind", headerName: "사이즈구분", width: 100, cellStyle: {"text-align": "center"} },
			{ field: "size", headerName: "사이즈", width: 60, cellStyle: {"text-align": "center"} },
			{ field: "match_yn", headerName: '매칭여부', cellClass: 'hd-grid-code', width: 60},
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
		url: '/shop/api/prdcd/conds',
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
		if(this.isPrdCdP) {
			this.grid.Request('/shop/api/prdcd/search_p', data, -1);
		} else {
			this.grid.Request('/shop/api/prdcd/search', data, -1);
		}
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
		if($('#prd_cd').length > 0){
			$('#prd_cd').val(rows.map(r => r.prd_cd).join(","));
		}

		const size_kind = rows.map(r => r.size_kind);
		let sizes = [];
		axios({
			url: '/shop/api/prdcd/size',
			params: { size_kind: size_kind },
			method: 'get'
		})
			.then(response => {
				sizes = response.data.body;
				$('#size').empty();

				let option = '';
				let sel =''
				for(let i = 0; i < sizes.length;i++) {
					sel = "<option value=''>선택</option>"
					option += '<option value='+ sizes[i].size_cd +'>' + sizes[i].size_cd + ' : ' + sizes[i].size_nm+ '</option>';
				}
				$('#size').append(sel);
				$('#size').append(option);

				if (sizes.length > 0) {
					let size = rows.map(r => r.size);
					size = size.map(function(size) {
						return size.replace(/\s/g, "");
					});
					$('#size').val(size);
				}
			})
			.catch(error => {
				console.error('Error fetching sizes:', error);
			});

		$('#goods_nm').val(rows.map(r => r.goods_nm));
		$('#color').val(rows.map(r => r.color));
		$('#size_kind').val(rows.map(r => r.size_kind));
		$('#size').val(rows.map(r => r.size));
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

SearchPrdcd.prototype.ChoiceOne = function(value) {
	let divId = this.isPrdCdP ? 'prd_cd_p' : 'prd_cd';
	if($('#' + divId).length > 0) {
		$('#' + divId).val(value).trigger("change");
	}

	document.search_prdcd.reset();
	// this.grid.setRows([]);
	// Object.keys(conds).forEach(c => {
	//     this[c + '_grid'].gridOptions.api.forEachNodeAfterFilter(node => {
	//         node.setSelected(false);
	//     });
	// });

	// $('#gd-prdcd-total').html(0);
	$('#SearchPrdcdModal').modal('toggle');
}

let searchPrdcd = new SearchPrdcd();

/**
 * 상품옵션범위검색
 */
function SearchPrdcdRange(){
    this.setGrid = false;
}

SearchPrdcdRange.prototype.Open = async function(callback = null, match = false){
    if(this.setGrid === false) {
        this.isMatch = match === "match";
        this.SetGridCond();
        this.callback = callback;
        this.setGrid = true;
    }
    $('#SearchPrdcdRangeModal').modal({
        keyboard: false
    });
};

SearchPrdcdRange.prototype.SetGridCond = async function() {
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

        this[cond_title + '_grid'] = await new HDGrid(document.querySelector( "#div-gd-prdcd-range-" + cond_title ), columns, {
            pinnedTopRowData: [{item: "포함", key: "contain"}],
            getRowStyle: (params) => {
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#f2f2f2', 'border': 'none'};
            },
        });
        document.querySelector( "#div-gd-prdcd-range-" + cond_title ).style.height = '204px';
    });
    const { data: { body: res } } = await axios({ 
        url: '/shop/api/prdcd/conds', 
        method: 'get' 
    });
    Object.keys(res).forEach(r => {
        this[r + '_grid'].gridOptions.api.setRowData(res[r]);
    });
}

SearchPrdcdRange.prototype.Reset = function() {
    Object.keys(conds).forEach(c => {
        this[c + '_grid'].gridOptions.api.forEachNodeAfterFilter(node => {
            node.setSelected(false);
        });

        this[c + '_grid'].gridOptions.api.setPinnedTopRowData([{item: "포함", key: "contain"}]);
    });
}

SearchPrdcdRange.prototype.Choice = function() {
    if(this.callback !== null){
        this.callback(code, name);
    } else {
        let data = "";
        let text = [];
        Object.keys(conds).forEach(c => {
            let rows = this[c + '_grid'].getSelectedRows();
            rows.forEach(r => {
                data += `&${c}[]=${r.code_id}`;
            });
            
            let is_contain = this[c + '_grid'].gridOptions.api.getPinnedTopRow(0).data.item === "포함";
            data += `&${c}_contain=${is_contain}`;
            
            if(rows.length > 0) text.push(conds[c] + "(" + rows.map(r => r.code_val).join(",") + ")" + (is_contain ? " 포함" : " 미포함"));
        });
        data += '&match='+this.isMatch;

        if($('#prd_cd_range').length > 0){
            $('#prd_cd_range').val(data);
            $('#prd_cd_range_nm').val(text.join(" / "));
        }
    }

    $('#SearchPrdcdRangeModal').modal('toggle');
};

let searchPrdcdRange = new SearchPrdcdRange();

/**
 * 원부자재코드검색
 */

const conds_sub = {
    brand: '구분',
    year: '년도',
    season: '시즌',
    gender: '성별',
    opt: '품목',
    item: '하위품목'
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
        url: '/shop/api/prdcd/conds_sub', 
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
        this.grid.Request('/shop/api/prdcd/search_sub', data, -1);
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

let searchPrdcd_sub = new SearchPrdcd_sub();


// 판매유형 검색
function SearchSellType(){
    this.grid = null;
}

SearchSellType.prototype.Open = async function(callback = null, multiple_type = false){
    if(this.grid === null){
        this.isMultiple = multiple_type === "multiple";
        this.SetGrid("#div-gd-selltype");
        $("#SearchSellTypeModal").draggable();
        if(this.isMultiple) $("#SearchSellTypeModal #search_selltype_cbtn").css("display", "block");
        this.callback = callback;
    }
    $('#SearchSellTypeModal').modal({
        keyboard: false
    });
};

SearchSellType.prototype.SetGrid = function(divId){
    let columns = [];

    if(this.isMultiple) {
        columns.push({ field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null });
    }

    columns.push(
        { field:"code_id", headerName:"판매유형코드", width:100, cellStyle: { "text-align": "center" }, hide: true },
        { field:"code_val", headerName:"판매유형명", width: "auto" },
    );

    if(!this.isMultiple) {
        columns.push({ 
            field:"choice", headerName:"선택", width:100, cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                if (params.data.code_id !== undefined) {
                    return '<a href="javascript:void(0);" onclick="return searchSellType.Choice(\'' + params.data.code_id + '\',\'' + params.data.code_val + '\');">선택</a>';
                }
            }
        });
    }

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchSellType.prototype.Search = function(e) {
    const event_type = e?.type;
    if (event_type == 'keypress') {
        if (e.key && e.key == 'Enter') {
            let data = $('form[name="search_sell_type"]').serialize();
            this.grid.Request('/shop/api/sale/search_sell_type', data);
        } else {
            return false;
        }
    } else {
        let data = $('form[name="search_sell_type"]').serialize();
        this.grid.Request('/shop/api/sale/search_sell_type', data);
    }
};

SearchSellType.prototype.Choice = function(code,name){
    if(this.callback !== null){
        this.callback(code, name);
    } else {
        if($('#sell_type.select2-sellType').length > 0){
            $('#sell_type').val(null);
            const option = new Option(name, code, true, true);
            $('#sell_type').append(option).trigger('change');
        } else {
            if($('#sell_type').length > 0){
                $('#sell_type').val(code);
            }
            if($('#sell_nm').length > 0){
                $('#sell_nm').val(name);
            }
        }
        if($('#sell_type.select2-sellType').length > 0){
            $('#sell_type').val(null);
            const option = new Option(name, code, true, true);
            $('#sell_type').append(option).trigger('change');
        } else {
            if($('#sell_type').length > 0){
                $('#sell_type').val(code);
            }
            if($('#sell_nm').length > 0){
                $('#sell_nm').val(name);
            }
        }
    }
    this.InitValue();
    $('#SearchSellTypeModal').modal('toggle');
};

SearchSellType.prototype.ChoiceMultiple = function(){
    let rows = this.grid.getSelectedRows();
    if(this.callback !== null){
        this.callback(rows);
    } else {
        let code_ids = rows.map(r => r.code_id);
        let code_vals = rows.map(r => r.code_val);

        if($('#sell_type.select2-sellType').length > 0){
            for(let r of rows) {
                if($("#sell_type").val().includes(r.code_id)) continue;
                const option = new Option(r.code_val, r.code_id, true, true);
                $('#sell_type').append(option).trigger('change');
            }
        } else {
            if($('#sell_type').length > 0){
                $('#sell_type').val(code_ids);
            }
            if($('#sell_nm').length > 0){
                $('#sell_nm').val(code_vals);
            }
        }
        if($('#sell_type.select2-sellType').length > 0){
            for(let r of rows) {
                if($("#sell_type").val().includes(r.code_id)) continue;
                const option = new Option(r.code_val, r.code_id, true, true);
                $('#sell_type').append(option).trigger('change');
            }
        } else {
            if($('#sell_type').length > 0){
                $('#sell_type').val(code_ids);
            }
            if($('#sell_nm').length > 0){
                $('#sell_nm').val(code_vals);
            }
        }
    }
    this.InitValue();
    $('#SearchSellTypeModal').modal('toggle');
}

SearchSellType.prototype.InitValue = () => {
    document.search_sell_type.reset();
    searchSellType.grid.setRows([]);
    $('#gd-selltype-total').html(0);
};


let searchSellType = new SearchSellType();



// 행사코드 검색
function SearchPrCode(){
    this.grid = null;
}

SearchPrCode.prototype.Open = async function(callback = null, multiple_type = false){
    if(this.grid === null){
        this.isMultiple = multiple_type === "multiple";
        this.SetGrid("#div-gd-prcode");
        $("#SearchPrCodeModal").draggable();
        if(this.isMultiple) $("#SearchPrCodeModal #search_prcode_cbtn").css("display", "block");
        this.callback = callback;
    }
    $('#SearchPrCodeModal').modal({
        keyboard: false
    });
};

SearchPrCode.prototype.SetGrid = function(divId){
    let columns = [];

    if(this.isMultiple) {
        columns.push({ field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null });
    }

    columns.push(
        { field:"code_id", headerName:"행사코드", width:100, cellStyle: { "text-align": "center" }, hide: true },
        { field:"code_val", headerName:"행사명", width: "auto" },
    );

    if(!this.isMultiple) {
        columns.push({ 
            field:"choice", headerName:"선택", width:100, cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                if (params.data.code_id !== undefined) {
                    return '<a href="javascript:void(0);" onclick="return searchPrCode.Choice(\'' + params.data.code_id + '\',\'' + params.data.code_val + '\');">선택</a>';
                }
            }
        });
    }

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchPrCode.prototype.Search = function(e) {
    const event_type = e?.type;
    if (event_type == 'keypress') {
        if (e.key && e.key == 'Enter') {
            let data = $('form[name="search_prcode"]').serialize();
            this.grid.Request('/shop/api/sale/search_prcode', data);
        } else {
            return false;
        }
    } else {
        let data = $('form[name="search_prcode"]').serialize();
        this.grid.Request('/shop/api/sale/search_prcode', data);
    }
};

SearchPrCode.prototype.Choice = function(code,name){
    if(this.callback !== null){
        this.callback(code, name);
    } else {
        if($('#pr_code.select2-prcode').length > 0){
            $('#pr_code').val(null);
            const option = new Option(name, code, true, true);
            $('#pr_code').append(option).trigger('change');
        } else {
            if($('#pr_code').length > 0){
                $('#pr_code').val(code);
            }
            if($('#pr_code_nm').length > 0){
                $('#pr_code_nm').val(name);
            }
        }
        if($('#pr_code.select2-prcode').length > 0){
            $('#pr_code').val(null);
            const option = new Option(name, code, true, true);
            $('#pr_code').append(option).trigger('change');
        } else {
            if($('#pr_code').length > 0){
                $('#pr_code').val(code);
            }
            if($('#pr_code_nm').length > 0){
                $('#pr_code_nm').val(name);
            }
        }
    }
    this.InitValue();
    $('#SearchPrCodeModal').modal('toggle');
};

SearchPrCode.prototype.ChoiceMultiple = function(){
    let rows = this.grid.getSelectedRows();
    if(this.callback !== null){
        this.callback(rows);
    } else {
        let code_ids = rows.map(r => r.code_id);
        let code_vals = rows.map(r => r.code_val);

        if($('#pr_code.select2-prcode').length > 0){
            for(let r of rows) {
                if($("#pr_code").val().includes(r.code_id)) continue;
                const option = new Option(r.code_val, r.code_id, true, true);
                $('#pr_code').append(option).trigger('change');
            }
        } else {
            if($('#pr_code').length > 0){
                $('#pr_code').val(code_ids);
            }
            if($('#pr_code_nm').length > 0){
                $('#pr_code_nm').val(code_vals);
            }
        }
        if($('#pr_code.select2-prcode').length > 0){
            for(let r of rows) {
                if($("#pr_code").val().includes(r.code_id)) continue;
                const option = new Option(r.code_val, r.code_id, true, true);
                $('#pr_code').append(option).trigger('change');
            }
        } else {
            if($('#pr_code').length > 0){
                $('#pr_code').val(code_ids);
            }
            if($('#pr_code_nm').length > 0){
                $('#pr_code_nm').val(code_vals);
            }
        }
    }
    this.InitValue();
    $('#SearchPrCodeModal').modal('toggle');
}

SearchPrCode.prototype.InitValue = () => {
    document.search_sell_type.reset();
    searchPrCode.grid.setRows([]);
    $('#gd-prcode-total').html(0);
};

let searchPrCode = new SearchPrCode();

/**
 * 아래는 head_search.js 와 동일
 * SearchGoods()
 * SearchGoodsNo()
 * SearchGoodsNos()
 */

function SearchGoods(){
    this.grid = null;
}

SearchGoods.prototype.Open = function(type = 'DISPLAY',callback = null){
    this.type = type;
    if(this.grid === null){
        this.SetGrid("#div-gd-goods");
        //gxBrand = new HDGrid(document.querySelector("#div-gd-brand"), columnsBrand);
        $("#SearchGoodsModal").draggable();
        this.callback = callback;
    }

    // $('#SearchBrandModal').modal({
    //     keyboard: false
    // });

    $('#SearchGoodsModal').modal({
        keyboard: false
    });
};

SearchGoods.prototype.SetGrid = function(divId){
    const columns = [
        {field:"d_cat_cd" , headerName:"코드",width:100},
        {field:"d_cat_nm" , headerName:"카테고리명",hide:true},
        {field:"full_nm" , headerName:"카테고리명",width:200},
        {field:"choice" , headerName:"선택",width:100,cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                if (params.data.d_cat_cd !== undefined) {
                    return '<a href="javascript:void(0);" onclick="return SearchGoods.Choice(\'' + params.data.d_cat_cd + '\',\'' + params.data.d_cat_nm + '\');">선택</a>';
                }
            }
        },
        {field:"nvl" , headerName:"",width:90},
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchGoods.prototype.Search = function(){
    let data = $('form[name="search_goods"]').serialize();
    const url = '/shop/api/goods/getlist/' + this.type;
    this.grid.Request(url, data);
};

SearchGoods.prototype.Choice = function(code,name){
    if(this.callback !== null){
        this.callback(code,name);
    } else {
        if($('#cat_cd.select2-goods').length > 0){
            $('#cat_cd').val(null);
            const option = new Option(name, code, true, true);
            $('#cat_cd').append(option).trigger('change');
        } else {
            if($('#cat_cd').length > 0){
                $('#cat_cd').val(code);
            }
            if($('#cat_nm').length > 0){
                $('#cat_nm').val(name);
            }
        }
    }
    $('#SearchGoodsModal').modal('toggle');
};

let searchGoods = new SearchGoods();


function SearchGoodsNos(){
    this.grid = null;
    this.id = '';
}

SearchGoodsNos.prototype.Init = function () { // 검색조건 초기화 기능 추가 - 사용 예) searchGoodsNos.Init();
    document.querySelector("form[name='search_goods_nos']").reset();
    document.querySelector("#gd-goods_nos-total").innerHTML = 0;
    if (this.grid) this.grid.deleteRows();
};

SearchGoodsNos.prototype.Open = function(id = 'goods_no',callback = null){
    if(this.grid === null){
        this.SetGrid("#div-gd-goods_nos");
        $("#SearchGoodsNosModal").draggable();
        this.id = id;
        this.callback = callback;
    }
    let goods_no = $('#' + this.id).val();
    if(goods_no !== ""){
        $('#sch_goods_nos').val(goods_no);
    }
    $('#SearchGoodsNosModal').modal({
        keyboard: false
    });
};

SearchGoodsNos.prototype.SetGrid = function(divId){
    const columns = [
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
        {field: "goods_no", headerName: "상품번호", width: 60, pinned: 'left'},
        {field: "style_no", headerName: "스타일넘버", width: 70, pinned: 'left'},
        {field: "img", headerName: "이미지", type:'GoodsImageType',width: 50},
        {field: "img", headerName: "이미지_url", hide: true},
        {field: "sale_stat_cl", headerName: "상품상태", type:'GoodsStateType',width: 80},
        {field: "goods_nm", headerName: "상품명",type:'HeadGoodsNameType'},
        {field:"nvl" , headerName:""},
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchGoodsNos.prototype.Search = function(){
    let data = "";
    data += "sch_goods_nos=" + $("#sch_goods_nos").val().split("\n").filter(v => v).join(",");
    data += "&sch_style_nos=" + $("#sch_style_nos").val().split("\n").filter(v => v).join(",");
    data += "&cmd=modal";
    this.grid.Request('/shop/api/goods-search', data, 1);
};

SearchGoodsNos.prototype.Choice = function(){

    let checkRows = this.grid.getSelectedRows();
    let style_nos = checkRows.map(function(row) {
        return row.style_no;
    });
    let goods_nos = checkRows.map(function(row) {
        return row.goods_no;
    });
    let goods_no = document.getElementById('sch_goods_nos').value;
    let style_no = document.getElementById('sch_style_nos').value;

    if(goods_no == "" || style_no != "") {
        $('#style_no').val(style_nos);
    } else if (goods_no != "" || style_no == "") {
        $('#goods_no').val(goods_nos);
    }
    
    $('#SearchGoodsNosModal').modal('toggle');
};
let searchGoodsNos = new SearchGoodsNos();

// 판매채널 세팅
SearchStore.prototype.SetStoreChannelSelect = async function(){
	const { data: { body: types } } = await axios({
		url: `/store/api/stores/search-storechannel`,
		method: 'get'
	});
	for(let type of types) {
		$("#search_store_channel").append(`<option value="${type.store_channel_cd}">${type.store_channel}</option>`);
	}
}

// 매장구분 세팅
SearchStore.prototype.SetStoreChannelKindSelect = async function(){
	const storeChannelSelect = document.getElementById("search_store_channel");

	storeChannelSelect.addEventListener("change", function() {

		const sel_channel = document.getElementById("search_store_channel").value;

		$.ajax({
			method: 'post',
			url: '/shop/stock/stk22/chg-store-channel',
			data: {
				'store_channel' : sel_channel
			},
			dataType: 'json',
			success: function (res) {
				if(res.code == 200){
					$('#search_store_channel_kind').empty();
					let select =  $("<option value=''>전체</option>");
					$('#search_store_channel_kind').append(select);

					for(let i = 0; i < res.store_kind.length; i++) {
						let option = $("<option value="+ res.store_kind[i].store_kind_cd +">" + res.store_kind[i].store_kind + "</option>");
						$('#search_store_channel_kind').append(option);
					}

				} else {
					alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
				}
			},
			error: function(e) {
				console.log(e.responseText)
			}
		});
	});
}


function SearchGoodsNo(){
    this.grid = null;
    this.id = '';
}

SearchGoodsNo.prototype.Init = function () { // 검색조건 초기화 기능 추가 - 사용 예) searchGoodsNo.Init();
    document.querySelector("form[name='search_goods_no']").reset();
    document.querySelector("#gd-goods_no-total").innerHTML = 0;
    if (this.grid) this.grid.deleteRows();
};

SearchGoodsNo.prototype.Open = function(id = 'goods_no',callback = null){
    if(this.grid === null){
        this.SetGrid("#div-gd-goods_no");
        $("#SearchGoodsNoModal").draggable();
        this.id = id;
        this.callback = callback;
    }
    let goods_no = $('#' + this.id).val();
    if(goods_no !== ""){
        $('#sch_goods_no').val(goods_no);
    }
    $('#SearchGoodsNoModal').modal({
        keyboard: false
    });
};

SearchGoodsNo.prototype.SetGrid = function(divId){
    const columns = [
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 35, pinned: 'left', sort: null},
        {field: "goods_no", headerName: "상품번호", width: 72, pinned: 'left'},
        {field: "style_no", headerName: "스타일넘버", width: 84, pinned: 'left'},
        {field: "img", headerName: "이미지", type:'GoodsImageType',width: 40},
        {field: "img", headerName: "이미지_url", hide: true},
        {field: "sale_stat_cl", headerName: "상품상태", type:'GoodsStateType',width: 72},
        {field: "goods_nm", headerName: "상품명",type:'HeadGoodsNameType'},
        {field:"nvl" , headerName:""},
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchGoodsNo.prototype.Search = function(){
    let data = $('form[name="search_goods_no"]').serialize();
    this.grid.Request('/shop/api/goods-search', data,1);
};

SearchGoodsNo.prototype.Choice = function(){

    let checkRows = this.grid.getSelectedRows();
    let goods_nos = checkRows.map(function(row) {
        return row.goods_no;
    });

    if(checkRows.length == 0){
        alert('상품을 선택해 주세요.');
        return false;
    }else if(checkRows.length > 1){
        alert('상품을 하나만 선택해 주세요.');
        return false;
    }

    if(this.callback !== null){
        this.callback();
    } else {
        if($('#' + this.id).length > 0){
            $('#' + this.id).val(goods_nos.join(","));
        }
    }
    $('#SearchGoodsNoModal').modal('toggle');
};

let searchGoodsNo = new SearchGoodsNo();

// 엑셀다운로드모달
function DepthExportChecker() {
    this.depths = null;
    this.download = null;
}

DepthExportChecker.prototype.Open = function({ depths = [], download = null }) {
    if (this.depths === null) {
        if (Array.isArray(depths)) this.SetDepths(depths);
        $("#CheckExportDepthModal").draggable();
        this.download = download;
    }
    $('#CheckExportDepthModal').modal({ keyboard: false });
};

DepthExportChecker.prototype.SetDepths = function(depths) {
    this.depths = depths;
    this.depths.push(['No Group']);

    $("#CheckExportDepthModal #depth_check_list").html(this.depths.map((depth, i) => `
        <div class="col-lg-4 inner-td">
            <div class="form-inline form-radio-box">
                <div class="custom-control custom-radio">
                    <input type="radio" name="export_depth" value="${i}" id="export_depth_${i}" class="custom-control-input" checked>
                    <label class="custom-control-label" for="export_depth_${i}">${depth}</label>
                </div>
            </div>
        </div>
    `));
}

DepthExportChecker.prototype.Download = function () {
    if (this.download !== null) {
        const level = $("#CheckExportDepthModal input[name=export_depth]:checked").val();
        this.download(level * 1);
        $('#CheckExportDepthModal').modal('toggle');
    }
}

let depthExportChecker = new DepthExportChecker();

$( document ).ready(function() {
    // 매장 검색 클릭 이벤트 바인딩 및 콜백 사용
    $( ".sch-store" ).on("click", function() {
        searchStore.Open();
    });

	// 창고 검색 클릭 이벤트 바인딩 및 콜백 사용
    $( ".sch-storage" ).on("click", function() {
        searchStorage.Open();
    });

    // 담당자 검색 클릭 이벤트 바인딩 및 콜백 사용
    $( ".sch-md" ).on("click", function() {
        searchMd.Open();
    });

    // 상품코드 검색 클릭 이벤트 바인딩 및 콜백 사용
    $(".sch-prdcd").on("click", function() {
        searchPrdcd.Open();
    });

    // 코드일련 검색 클릭 이벤트 바인딩 및 콜백 사용
    $(".sch-prdcd-p").on("click", function() {
        searchPrdcd.Open(null, false, true);
    });

    // 상품옵션 범위검색 클릭 이벤트 바인딩 및 콜백 사용
    $(".sch-prdcd-range").on("click", function() {
        searchPrdcdRange.Open();
    });

    // 원부자재코드 검색 클릭 이벤트 바인딩 및 콜백 사용
    $(".sch-prdcd_sub").on("click", function() {
        searchPrdcd_sub.Open();
    });

    // 입고 내 송장번호 선택
    $(".sch-invoice").on("click", function() {
        let url = "/shop/cs/cs01/choice";
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1400,height=800");
    });

     // 판매유형 검색
    $( ".sch-sellType" ).on("click", function() {
        searchSellType.Open();
    });
   
    // 행사코드 검색
    $( ".sch-prcode" ).on("click", function() {
        searchPrCode.Open();
    });

    /** 아래는 head_search.js 와 동일 */
    $( ".sch-goods" ).click(function() {
        searchGoods.Open();
    });
    $( ".sch-goods_nos" ).click(function() {
        if($(this).attr("data-name") !== null){
            searchGoodsNos.Open($(this).attr("data-name"));
        }
    });
    $( ".sch-goods_no" ).click(function() {
        if($(this).attr("data-name") !== null){
            searchGoodsNo.Open($(this).attr("data-name"));
        }
    });

	$(".sort_toggle_btn label").on("click", function(){
		$(".sort_toggle_btn label").attr("class","btn btn-secondary");
		$(this).attr("class","btn btn-primary");
	});
});

/**
 *
 * 판매채널 부분
 *
 */

// 판매채널 셀렉트박스가 선택되지 않으면 매장구분 셀렉트박스는 disabled처리
function load_store_channel() {
	const store_channel = document.getElementById("store_channel");
	const store_channel_kind = document.getElementById("store_channel_kind");

	store_channel.addEventListener("change", () => {
		if (store_channel.value) {
			store_channel_kind.disabled = false;
		} else {
			store_channel_kind.disabled = true;
		}
	});
}

// 판매채널 검색
function chg_store_channel() {

	const sel_channel = document.getElementById("store_channel").value;

	$.ajax({
		method: 'post',
		url: '/shop/stock/stk22/chg-store-channel',
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
    if ($('#storage_cd').length > 0) $('#storage_cd').val("").trigger('change'); // 매장명 select2 박스 초기화
    if ($('#goods_stat').length > 0) $('#goods_stat').val("").trigger('change'); // 상품상태 select2 박스 초기화
    if ($('#sell_type').length > 0) $('#sell_type').val("").trigger('change'); // 판매유형 select2 박스 초기화
    if ($('#pr_code').length > 0) $('#pr_code').val("").trigger('change'); // 행사코드 select2 박스 초기화
    /**
     * 동적 초기화
     */
    select2.map(key => {
        if ($(key).length > 0) $(key).val("").trigger('change'); // 전달받은 select2 박스 초기화
    });


};
