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
                url: '/head/auto-complete/template',
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

    $(".ac-template-q").autocomplete({
        //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
        source : function(request, response) {
            $.ajax({
                method: 'get',
                url: '/head/auto-complete/template-q',
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
                url: '/head/auto-complete/style-no',
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
                url: '/head/auto-complete/brand',
                data: { keyword : this.term },
                success: function (data) {
                    response(data);
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        },
        // 아이템 선택시 com_id 값 반영
        select : (event, ui) => {
            const input_wrap = event.target.parentNode;
            let input;
            if (input = input_wrap.querySelector('#brand_cd')) {
                input.value = ui.item.id;
            } else if (input = input_wrap.querySelector('#brand_id')) {
                input.value = ui.item.id;
            }
        },
        minLength: 1,
        autoFocus: true,
        delay: 100
    });

    $(".ac-goods-nm")
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
                url: '/head/auto-complete/goods-nm',
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

    $(".ac-company").autocomplete({
        // 해당 값을 가지고 서버에서 검색함.
        source : function(request, response) {
            $.ajax({
                method: 'get',
                url: '/head/auto-complete/company',
                data: { keyword : this.term },
                success: function (data) {
                    response(data);
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        },
        // 아이템 선택시 com_id 값 반영
        select : (event, ui) => {
            const input_wrap = event.target.parentNode;
            let input;
            if (input = input_wrap.querySelector('#com_cd')) {
                input.value = ui.item.id;
            } else if (input = input_wrap.querySelector('#com_id')) {
                input.value = ui.item.id;
            }
        },
        minLength: 1,
        autoFocus: true,
        delay: 100
    });

    $('.select2-category').select2({
        ajax: {
            url: "/head/auto-complete/category",
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
            url: "/head/auto-complete/style-no",
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
            url: "/head/auto-complete/brand",
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

            var $state = $(
                '<span>' + '[' + state.id +  '] ' + state.text + '</span>'
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

    $(".select2-user_group").select2();

    $( ".sch-brand" ).click(function() {
        searchBrand.Open();
    });

    $( ".sch-category" ).click(function() {
        let cat_type = $('#cat_type').val();
        if(cat_type !== "ITEM"){
            cat_type = "DISPLAY";
        }
        searchCategory.Open(cat_type);
    });

    $( ".sch-goods" ).click(function() {
        searchGoods.Open();
    });

    $( ".sch-company" ).click(function() {
        searchCompany.Open();
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


    $(".sch-ad_type").change(function(){
        var ad_type = $(this).val();
        //console.log(ad_type);
        //console.log($(this).val());
        if(ad_type !== "1" || ad_type !== ""){
            $.ajax({
                async: true,
                type: 'get',
                url: '/head/auto-complete/ad_type',
                data:{
                    "ad_type": ad_type
                },
                success: function (data) {
                    var res = data.results;
                    $(".sch_ad option").remove();
                    $(".sch_ad").append("<option value=''>- 광고 -</option>");
                    for(i=0; i<res.length; i++){
                        $(".sch_ad").append("<option value='"+ res[i]['id'] +"'>"+ res[i]['val'] +"</option>");
                    }
                },
                error: function(request, status, error) {
                    //alert(request.responseJSON.msg);
                    console.log("error");
                    //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                }
            });
        } else {
            $(".sch_ad option").remove();
            $(".sch_ad").append("<option value=''>선택</option>");
        }
    });

    /**
     * 스타일넘버 다중선택 추가
     */
    const sch_style_no_dom = document.querySelector(".sch-style_nos");
    if (sch_style_no_dom) {
        sch_style_no_dom.addEventListener('click', () => {
            const btn = document.querySelector(".sch-style_nos");
            const icon = document.querySelector(".sch-style_nos .bx");
            const input = btn.parentNode.querySelector("input[name='style_no']");
            input.disabled = !input.disabled;
            if (icon.classList.contains('bx-plus')) {
                const textarea = 
                    `<textarea name='style_no' style='height:150px'
                        class='form-control form-control-sm search-all search-enter w-100' rows='5'></textarea>`;
                textarea.innerText = "";
                $(textarea).prependTo(btn.parentNode);
            } else if (icon.classList.contains('bx-minus')) {
                const textarea = btn.parentNode.querySelector("textarea[name='style_no']");
                textarea.remove();
            }
            input.classList.toggle('d-none');
            icon.classList.toggle('bx-plus');
            icon.classList.toggle('bx-minus');
        })
    }

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

SearchBrand.prototype.Search = function(e) {
    const event_type = e?.type;
    if (event_type == 'keypress') {
        if (e.key && e.key == 'Enter') {
            let data = $('form[name="search_brand"]').serialize();
            this.grid.Request('/head/api/brand/getlist', data);
        } else {
            return false;
        }
    } else {
        let data = $('form[name="search_brand"]').serialize();
        this.grid.Request('/head/api/brand/getlist', data);
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
        {field:"d_cat_cd" , headerName:"코드",width:100},
        {field:"d_cat_nm" , headerName:"카테고리명",hide:true},
        {field:"full_nm" , headerName:"카테고리명",width: "auto"},
        {field:"choice" , headerName:"선택",width:100,cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                if (params.data.d_cat_cd !== undefined) {
                    return '<a href="javascript:void(0)" onclick="return searchCategory.Choice(\'' + params.data.d_cat_cd + '\',\'' + params.data.d_cat_nm + '\',\'' + params.data.full_nm + '\',\'' + params.data.mx_len + '\');">선택</a>';
                }
            }
        },
        {field:"mx_len" , headerName:"코드길이",hide:true}
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchCategory.prototype.Search = function(){
    let data = $('form[name="search_category"]').serialize();
    const cat_type = $("#search_category select[name=cat_type]").val();
    //const url = '/head/api/category/getlist/' + this.type;
    const url = '/head/api/category/getlist/' + cat_type;
    this.grid.Request(url, data);
    this.type = cat_type;
};

SearchCategory.prototype.Choice = function(code, name, full_nm, mx_len){
    if(this.callback !== null){
        this.callback(code, name, full_nm, mx_len);
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
                    return '<a href="#" onclick="return SearchGoods.Choice(\'' + params.data.d_cat_cd + '\',\'' + params.data.d_cat_nm + '\');">선택</a>';
                }
            }
        },
        {field:"nvl" , headerName:"",width:90},
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchGoods.prototype.Search = function(){
    let data = $('form[name="search_goods"]').serialize();
    console.log(data);
    const url = '/head/api/goods/getlist/' + this.type;
    this.grid.Request(url, data);
};

SearchGoods.prototype.Choice = function(code,name){
    console.log(code);
    console.log(name);

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

    $('#SearchCompanyModal').keydown((evt) => {
        // 엔터키 입력시 모달 창이 아닌 부모 창에서 검색되는 문제 수정
        if ((evt.keyCode || evt.which) === 13) {
            evt.preventDefault();
            let data = $('form[name="search_company"]').serialize();
            const url = '/head/api/company/getlist/';
            this.grid.Request(url, data);
        }
    });
};

SearchCompany.prototype.SetGrid = function(divId){
    const columns = [
        {field:"com_type_nm" , headerName:"업체구분",width:80},
        {field:"com_id" , headerName:"업체코드",width:80},
        {field:"com_nm" , headerName:"업체명",width:100},
        {field:"biz_num" , headerName:"사업자번호",width:90},
        {field:"md_nm" , headerName:"담당MD",width:70, hide:true},
        {field:"com_type", headerName:"업체타입", hide:true},
        {field:"baesong_kind", headerName:"배송업체", hide:true},
        {field:"baesong_info", headerName:"배송지역", hide:true},
        {field:"margin_type", headerName:"수수료타입", hide:true},
        {field:"dlv_amt", headerName:"배송료", hide:true},
        {field:"choice" , headerName:"선택",width:50,cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                return '<a href="#" onclick="return searchCompany.Choice(\'' + params.data.com_id + '\',\'' + params.data.com_nm + '\',\'' + params.data.com_type + '\',\'' + params.data.baesong_kind + '\',\'' + params.data.baesong_info + '\',\'' + params.data.margin_type + '\',\'' + params.data.dlv_amt + '\',\'' + params.data.com_type_nm + '\');">선택</a>';
            }
        },
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchCompany.prototype.Search = function(){
    let data = $('form[name="search_company"]').serialize();
    const url = '/head/api/company/getlist/';
    this.grid.Request(url, data);
};

SearchCompany.prototype.Choice = function(code,name, com_type, baesong_kind, baesong_info, margin_type, dlv_amt, com_type_nm)
{
	if( this.callback !== null )
	{
		this.callback(code, name, com_type, baesong_kind, baesong_info, margin_type, dlv_amt, com_type_nm);
	}
	else
	{
		if( $('#com_cd.select2-company').length > 0 )
		{
			$('#com_cd').val(null);
			const option = new Option(name, code, true, true);
			$('#com_cd').append(option).trigger('change');
		}
		else
		{
			if( $('#com_cd').length > 0 )
			{
				$('#com_cd').val(code);
			}
            if( $('#com_id').length > 0 )
            {
                $('#com_id').val(code);
            }
			if( $('#com_nm').length > 0 )
			{
				$('#com_nm').val(name);
			}
		}
	}
	$('#SearchCompanyModal').modal('toggle');
};

let searchCompany = new SearchCompany();
if ($('.ac-company').length > 0) {
    //#com_nm가 수정될 경우 #com_cd값 초기화
    $('#com_nm').change(function(){
        $('#com_cd').val('');
    });
}


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
        {field: "style_no", headerName: "스타일넘버", width: 100, pinned: 'left'},
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
    this.grid.Request('/head/api/goods', data,1);
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

ControlOption.prototype.Open = function(goods_no = 0, callback = null) {
    this.goods_no = goods_no;
    this.callback = callback;

    if(this.grid === null){
        this.SetGrid("#div-gd-option");
        $("#ControlOptionModal").draggable();
    }

    $('#ControlOptionModal').modal({
        keyboard: false
    });
};

ControlOption.prototype.SetGrid = function(divId) {
    const columns = [
        {headerName: '', width:40, valueGetter: 'node.id', cellRenderer: 'loadingRenderer'},
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, sort: null},
        {field: "opt_name" , headerName: "옵션구분", width: 150},
        {field: "goods_opt" , headerName: "옵션", width: 200, editable: true, cellStyle: {'background' : '#ffff99'}},
        {field: "goods_no", hide: true},
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
    this.grid.Request(`/head/product/prd01/${this.goods_no}/get-option`, null, 1, function(e) {
        const opt_kinds = e.head.opt_kinds;
        this.kinds = opt_kinds;
        opt_kinds.forEach(opt => {
            $("#opt_kind").append(`<option value='${JSON.stringify(opt)}'>${opt.name}</option>`);
        });
        $("#gd-option-total").text(e.head.total);
    });
};

ControlOption.prototype.Add = function(e) {
    if(e.key === "Enter" || e.type === "click") {
        const opt_kind_no = JSON.parse($("#opt_kind").val());
        const opt_nm = $("#opt_nm").val();

        if(opt_kind_no == 0) return alert("옵션구분을 선택해주세요.");
        if(opt_nm == '') return;

        if(this.grid.getRows().filter(n => n.opt_name === opt_kind_no.name && n.goods_opt === opt_nm).length > 0) return alert("이미 등록된 옵션입니다.");

        this.grid.addRows([{
            opt_name: opt_kind_no.name,
            goods_opt: opt_nm,
        }]);
        
        $("#opt_nm").val('');
    }
};

ControlOption.prototype.Save = function() {
    if(!confirm("옵션 정보를 저장하시겠습니까?")) return;

    $.ajax({
        async: true,
        type: 'post',
        url: `/head/product/prd01/${this.goods_no}/option-save`,
        data: {
            'opt_list': this.grid.getRows(),
        },
        success: function (res) {
            if(res.code === 200) {
                // 작업필요
                console.log(res);
            }
            else alert(res.msg);
        },
        error: function(request, status, error) {
            console.log(request, status, error)
        }
    });
};

ControlOption.prototype.Delete = function() {
    const selected = this.grid.getSelectedRows();
    if(selected.length < 1) return alert("삭제할 옵션을 선택해주세요.");
    if(!confirm("선택하신 옵션을 삭제하시겠습니까?")) return;
    console.log(this.grid.getSelectedRows());
};

let controlOption = new ControlOption();