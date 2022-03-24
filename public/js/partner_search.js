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
                    return '<a href="#" onclick="return searchBrand.Choice(\'' + params.data.brand + '\',\'' + params.data.brand_nm + '\');">선택</a>';
                }
            }
        },
        {field:"nvl" , headerName:""},
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchBrand.prototype.Search = function(){
    let data = $('form[name="search_brand"]').serialize();
    console.log(data);
    this.grid.Request('/partner/api/brand/getlist', data);
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
    if($("#search_brand_close").is(":checked")) {
        $('#SearchBrandModal').modal('toggle');
    }
};
let searchBrand = new SearchBrand();


function SearchCategory(){
    this.grid = null;
}

SearchCategory.prototype.Open = function(type = 'DISPLAY',callback = null){
    if(this.grid === null){
        this.SetGrid("#div-gd-category");
        $("#SearchCategoryModal").draggable();
        this.callback = callback;
    }
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
        {field:"d_cat_cd" , headerName:"코드",width:100},
        {field:"d_cat_nm" , headerName:"카테고리명",hide:true},
        {field:"full_nm" , headerName:"카테고리명",width:200},
        {field:"choice" , headerName:"선택",width:100,cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                if (params.data.d_cat_cd !== undefined) {
                    return '<a href="#" onclick="return searchCategory.Choice(\'' + params.data.d_cat_cd + '\',\'' + params.data.d_cat_nm + '\',\'' + params.data.full_nm + '\',\'' + params.data.mx_len + '\');">선택</a>';
                }
            }
        },
        {field:"mx_len" , headerName:"코드길이",hide:true},
        {field:"", headerName:"", width: "auto"},
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
                return '<a href="#" onclick="return searchCompany.Choice(\'' + params.data.com_id + '\',\'' + params.data.com_nm + '\');">선택</a>';
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
            }
            if($('#com_nm').length > 0){
                $('#com_nm').val(name);
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
        {field: "goods_nm", headerName: "상품명",type:'HeadGoodsNameType'},
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
