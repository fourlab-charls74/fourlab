/*
  _token 값을 ajax할때 기본적으로 보내도록 설정
*/
$.ajaxSetup({
  headers: {
     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
/*
    autocomplete에서 선택되었을때의 이벤트 추가하는 예제
    $('.ac-template').autocomplete({ select : 이벤트명 });

    source : autocomplete 검색했을때의 보여질 목록
    minLength : 최소 단어 입력
    autoFocus : 첫번째 검색어 자동선택
    delay : 검색 후 화면에 나오는 딜레이
*/

$( document ).ready(function() {
    $(".ac-template").autocomplete({
        //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
        source : function(request, response) {
            $.ajax({
                method: 'get',
                url: '/partner/auto-complete/template',
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
        autoFocus: true,
        delay: 100
    });

    $(".ac-style-no")
        .on('keydown',function(evvent){
            if ( event.keyCode === 13) {
                $(this).autocomplete('close');
            }
        })
        .autocomplete({
        //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
        source : function(request, response) {
            $.ajax({
                method: 'get',
                url: '/partner/auto-complete/style-no',
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

    $(".ac-brand").autocomplete({
        //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
        source : function(request, response) {
            $.ajax({
                method: 'get',
                url: '/partner/auto-complete/brand',
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
        autoFocus: true,
        delay: 100
    });

    $(".ac-goods-nm")
        .on('keydown',function(evvent){
            if ( event.keyCode === 13) {
                $(this).autocomplete('close');
            }
        })
        .autocomplete({
        //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
        source : function(request, response) {
            $.ajax({
                method: 'get',
                url: '/partner/auto-complete/goods-nm',
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

    $('.select2-category').select2({
        ajax: {
            url: "/partner/auto-complete/category",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                let cat_type = '';
                if($('#cat_type').length > 0){
                    cat_type = $('#cat_type option:selected').val();
                }
                return {
                    type:'select2',
                    cat_type:cat_type,
                    keyword: params.term, // search term
                    page: params.page
                };
            },
            cache: true
        },
        width:'100%',
        placeholder: '',
        minimumInputLength: 1,
        allowClear: true,
        templateResult: function (state) {
            if (!state.id) {
                return state.text;
            }
            var $state = $(
                '<span>' + state.full_nm + '</span>'
            );
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

    $('.select2-style_no').select2({
        ajax: {
            url: "/partner/auto-complete/style-no",
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
        placeholder: '',
        allowClear: true,
        minimumInputLength: 1,
        templateResult: function (state) {
            if (!state.id) {
                return state.text;
            }
            if(state.img !== ""){
                var $state = $(
                    '<span><img src="' + state.img + '" style="width:30px" onError="this.src=\'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==\'"/> ' + state.text + '</span>'
                );
            } else {
                var $state = $(
                    '<span><span style="padding:0 15px;"></span> ' + state.text + '</span>'
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


    $('.select2-brand').select2({
        ajax: {
            url: "/partner/auto-complete/brand",
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
                    '<span><img src="'+ state.img + '" style="width:50px" onError="this.src=\'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==\'"/> ' + '[' + state.id +  '] '  + state.text + '</span>'
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
            }, noResults: function () {
                return "해당 상품이 없습니다."
            }, searching: function () {
                return "검색중..."
            }
        }
    });

    $( ".sch-brand" ).click(function() {
        searchBrand.Open();
    });


    $('.select2-company').select2({
        ajax: {
            url: "/head/auto-complete/company",
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
                    '<span><img src="'+ state.img + '" style="width:50px" /> ' + '[' + state.id +  '] '  + state.text + '</span>'
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
            }, noResults: function () {
                return "해당 상품이 없습니다."
            }, searching: function () {
                return "검색중..."
            }
        }
    });

    $( ".sch-company" ).click(function() {
        searchBrand.Open();
    });

    $( ".sch-category" ).click(function() {
        let cat_type = $('#cat_type').val();
        if(cat_type !== "ITEM"){
            cat_type = "DISPLAY";
        }
        searchCategory.Open(cat_type);
    });

    $( ".sch-goods_nos" ).click(function() {
        if($(this).attr("data-name") !== null){
            searchGoodsNos.Open($(this).attr("data-name"));
        }
    });

    $(".sort_toggle_btn label").on("click", function(){
        $(".sort_toggle_btn label").attr("class","btn btn-secondary");
        $(this).attr("class","btn btn-primary");
    });
});


const number_format_obj = new Intl.NumberFormat();

function numberFormat(number) {
  return number_format_obj.format(number);
}



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

SearchBrand.prototype.Search = function(){
    let data = $('form[name="search_brand"]').serialize();
    this.grid.Request('/partner/api/brand/getlist', data); // 파트너에서 에러 발생 - 주석처리 (미구현 추정)
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
				$('#brand_cd').trigger("change");
            }
            if($('#brand_nm').length > 0){
                $('#brand_nm').val(name);
				$('#brand_nm').trigger("change");
            }
        }
    }
    $('#SearchBrandModal').modal('toggle');
};
let searchBrand = new SearchBrand();


function SearchCategory(){
    this.grid = null;
}

SearchCategory.prototype.Open = function(type = 'DISPLAY',callback = null){
    if(this.grid === null){
        this.SetGrid("#div-gd-category");
        $("#SearchCategoryModal").draggable();
    }
    this.callback = callback;
    if(this.type !== type){
        this.type = type;
        $("#search_category select[name=cat_type]").val(this.type).attr("selected","selected");
        this.grid.deleteRows();
    }

    $('#SearchCategoryModal').modal({
        keyboard: false
    });
};

SearchCategory.prototype.SetGrid = function(divId){
    const columns = [
		{field: "d_cat_cd", headerName: "코드", width: 80},
		{field: "d_cat_nm", headerName: "카테고리명", hide: true},
		{field: "full_nm", headerName: "카테고리명", width: "auto"},
		{field: "choice", headerName: "선택", width: 70, cellClass: 'hd-grid-code',
            cellRenderer: function (params) {
                if (params.data.d_cat_cd !== undefined) {
                    return '<a href="javascript:void(0);" onclick="return searchCategory.Choice(\'' + params.data.d_cat_cd + '\',\'' + params.data.d_cat_nm + '\',\'' + params.data.full_nm + '\',\'' + params.data.mx_len + '\');">선택</a>';
                }
            }
        },
        {field:"mx_len" , headerName:"코드길이",hide:true},
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchCategory.prototype.Search = function(){
    let data = $('form[name="search_category"]').serialize();
    const cat_type = $("#search_category select[name=cat_type]").val();
    //const url = '/partner/api/category/getlist/' + this.type;
    const url = '/partner/api/category/getlist/' + cat_type;
    this.grid.Request(url, data);
};

SearchCategory.prototype.Choice = function(code,name,full_nm = '', mx_len = 0){

    if(this.callback !== null){
        this.callback(code,name,full_nm, mx_len);
    } else {
        if($('#cat_cd.select2-category').length > 0){
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
    $('#SearchCategoryModal').modal('toggle');
    this.InitValue();
};

SearchCategory.prototype.InitValue = () => {
    $('#SearchCategoryModal input[name="cat_nm"]').val('');
    searchCategory.grid.setRows([]);
};

let searchCategory = new SearchCategory();


function SearchCompany(){
    this.grid = null;
}

SearchCompany.prototype.Open = function(callback = null){
    if(this.grid === null){
        this.SetGrid("#div-gd-company");
        //gxBrand = new HDGrid(document.querySelector("#div-gd-brand"), columnsBrand);
        $("#SearchCompanyModal").draggable();
        this.callback = callback;
    }
    $('#SearchCompanyModal').modal({
        keyboard: false
    });
};

SearchCompany.prototype.SetGrid = function(divId){
    const columns = [
        {field:"com_type_nm" , headerName:"업체구분",width:80},
        {field:"com_id" , headerName:"업체코드",width:80},
        {field:"com_nm" , headerName:"업체명",width:100},
        {field:"biz_num" , headerName:"사업자번호",width:90},
        {field:"md_nm" , headerName:"담당MD",width:70},
        {field:"choice" , headerName:"선택",width:50,cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                return '<a href="javascript:void(0);" onclick="return searchCompany.Choice(\'' + params.data.com_id + '\',\'' + params.data.com_nm + '\');">선택</a>';
            }
        }
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchCompany.prototype.Search = function(){
    let data = $('form[name="search_company"]').serialize();
    console.log(data);
    this.grid.Request('/partner/api/company/getlist', data);
};

SearchCompany.prototype.Choice = function(code,name){
    console.log(code);
    console.log(name);

    if(this.callback !== null){
        this.callback(code,name);
    } else {
        if($('#com_cd.select2-company').length > 0){
            $('#com_cd').val(null);
            const option = new Option(name, code, true, true);
            $('#com_cd').append(option).trigger('change');
        } else {
            if($('#com_cd').length > 0){
                $('#com_cd').val(code);
				$('#com_cd').trigger("change");
            }
            if($('#com_nm').length > 0){
                $('#com_nm').val(name);
				$('#com_nm').trigger("change");
            }
        }
    }
    $('#SearchCompanyModal').modal('toggle');
};
let searchCompany = new SearchCompany();

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
        {field: "goods_no", headerName: "상품번호", width: 100, pinned: 'left'},
        {field: "style_no", headerName: "스타일넘버",width: 100 },
        {field: "img", headerName: "이미지", type:'GoodsImageType',width: 60},
        {field: "img", headerName: "이미지_url", hide: true},
        {field: "sale_stat_cl", headerName: "상품상태", type:'GoodsStateType',width: 100},
        {field: "goods_nm", headerName: "상품명",type:'GoodsNameType'},
        {field:"nvl" , headerName:""},
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchGoodsNos.prototype.Search = function(){
    let data = $('form[name="search_goods_nos"]').serialize();
    //console.log(data);
    this.grid.Request('/partner/api/goods', data,1);
};

SearchGoodsNos.prototype.Choice = function(){

    let checkRows = this.grid.getSelectedRows();
    let goods_nos = checkRows.map(function(row) {
        return row.goods_no;
    });

    if(this.callback !== null){
        this.callback();
    } else {
        if($('#' + this.id).length > 0){
            $('#' + this.id).val(goods_nos.join(","));
        }
    }
    $('#SearchGoodsNosModal').modal('toggle');
};
let searchGoodsNos = new SearchGoodsNos();


/**
   * 옵션관리모달
   */
function ControlOption() {
    this.grid = null;
    this.goods_no = null;
    this.kinds = [];
    this.deleted_opts = [];
}

ControlOption.prototype.SetGoodsNo = (goods_no) => {
    this.goods_no = goods_no;
}

ControlOption.prototype.Open = function(goods_no = 0, afterSaveOrDel = null) {
    this.goods_no = goods_no;
    this.afterSaveOrDel = afterSaveOrDel;

    if(this.grid === null){
        this.SetGrid("#div-gd-option");
        $("#ControlOptionModal").draggable();
        $('#ControlOptionModal').draggable( 'disable' ) // ag-grid의 rowDrag 기능 사용을 위해 disable 처리
    } else {
        this.grid.setRows([]);
        this.grid.Request(`/partner/product/prd01/${this.goods_no}/get-basic-options`, null, -1);
    }

    $('#ControlOptionModal').modal({
        keyboard: false
    });
};

ControlOption.prototype.SetGrid = function(divId) {

    const columns = [
        {headerName: '', width:40, valueGetter: 'node.id', cellRenderer: 'loadingRenderer', rowDrag: true},
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, sort: null},
        {field: "opt_name" , headerName: "옵션구분", width: 150},
        {field: "goods_opt" , headerName: "옵션", width: 200, editable: true, cellStyle: {'background' : '#ffff99'}},
        {field: "goods_no", hide: true},
    ];

    const option = { rowDragManaged: true, animateRows: true };
    this.grid = new HDGrid(document.querySelector( divId ), columns, option);

    this.grid.Request(`/partner/product/prd01/${this.goods_no}/get-basic-options`, null, -1, function(e) {

        const opt_kinds = e.head.opt_kinds;
        this.kinds = opt_kinds;

        $("#opt_kind").html("");
        $("#opt_kind").append(`<option value=0>= 옵션구분 =</option>`);
		
		opt_kinds.map(item => {
			$("#opt_kind").append(`<option value='${JSON.stringify(item.name)}'>${item.name}</option>`);
		});
		$("#gd-option-total").text(e.head.total);
    });
};

ControlOption.prototype.Add = function(e) {
    if(e.key === "Enter" || e.type === "click") {

        const opt_kind = JSON.parse($("#opt_kind").val());
        const opt_nm = $("#opt_nm").val();

        if (opt_kind == 0) {
            $("#opt_kind").trigger("focus");
            return alert("옵션구분을 선택해주세요.");
        }
        if (opt_nm == '') return;

        if (this.grid.getRows().filter(n => n.opt_name === opt_kind && n.goods_opt === opt_nm).length > 0) return alert("이미 등록된 옵션입니다.");

        this.grid.addRows([{
            opt_name: opt_kind,
            goods_opt: opt_nm,
        }]);

        $("#opt_nm").val('');
    }
};

ControlOption.prototype.Save = function() {
    if (this.grid.getRows()?.length == 0) {
        return alert("옵션을 추가해주세요.");
    }
    if (!confirm("옵션 정보를 저장하시겠습니까?")) return;


    const afterSuccess = (data) => {
        const { code, msg } = data;
        if (code == 200) {
            alert(msg);
            this.afterSaveOrDel(data);
        }
    };

    $.ajax({
        async: true,
        type: 'post',
        url: `/partner/product/prd01/${this.goods_no}/save-basic-options`,
        data: {
            'opt_list': this.grid.getRows(),
        },
        success: function (res) {
            afterSuccess(res);
        },
        error: function(response, status, error) {
            const { code, msg } = response?.responseJSON;
            alert(msg);
        }
    });
};

ControlOption.prototype.Delete = async function() {

    const afterSuccess = (data) => {
        const { code, msg } = data;
        if (code == 200) {
            alert(msg);
            this.afterSaveOrDel(data);
        } else alert(msg);
    };

    const rows = this.grid.getSelectedRows();
    if (Array.isArray(rows) && !(rows.length > 0)) {
        alert('삭제할 옵션을 선택해주세요.');
        return false;
    } else {
        if (!confirm("선택하신 옵션을 삭제하시겠습니까? \n(하나의 옵션구분만 남게되면 등록된 모든 옵션이 삭제됩니다.)")) return false;
        try {
            const response = await axios({ url: `/partner/product/prd01/${this.goods_no}/delete-basic-options`,
                method: 'post', data: { del_opt_list: rows }
            });
            afterSuccess(response?.data);
        } catch (error) {
            console.log(error);
        }
    };

};

let controlOption = new ControlOption();

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
	if ($("#goods_stat[name='goods_stat[]']").length > 0) $('#goods_stat').val([]).trigger('change'); // 전시상태 select2 박스 초기화
	searchGoodsNos.Init();

	/**
	 * 동적 초기화
	 */
	select2.map(key => {
		if ($(key).length > 0) $(key).val("").trigger('change'); // 전달받은 select2 박스 초기화
	});
};
