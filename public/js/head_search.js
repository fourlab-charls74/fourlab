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

    /**
     * 
     * store/product/prd02/create_barcode
     * 바코드등록(new)에서 브랜드+년도+시즌+성별+품목 값을 조합해서 style_no를 검색한 후 처음 자동완성은 해당 style_no를 출력
     * 값 입력 시 입력한 값의 자동완성값 불러오기
     * 입력한 스타일넘버가 없으면 입력한 스타일넘버 뒤에 (신규)라고 붙음
     * 
     */
    $(".dup-style-no")
    .on('focus',function(event){
        $(this).autocomplete('search', "");
    })
    .autocomplete({
        source : function(request, response) {
            let brand = $('#brand').val();
            let year = $('#year').val();
            let season = $('#season').val();
            let gender = $('#gender').val();
            let item = $('#item').val();

            let prd_cd_p = brand + year + season + gender + item;

            console.log(prd_cd_p);

            $.ajax({
                method: 'get',
                url: '/head/auto-complete/dup-style-no',
                data: { 
                    keyword : this.term,
                    prd_cd_p : prd_cd_p
                    
                },
                success: function (data) {
                    console.log(data.cnt);
                    response(data);
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        },
        minLength: 0,
        autoFocus: false,
        delay: 10,
        create:function(event,ui){
            $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
                console.log(item);
                let txt;
                if(item.img !== undefined && item.img !== "") {
                    txt = '<div><img src=\"' + item.img + '\" style=\"width:30px\" onError="this.src=\'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==\'"/> ' + item.label + '</div>';
                } else {
                    txt = '<div>' + item.label + '</div>';
                }

                if(item.label == null) {
                    item.value = item.text;
                    txt = '<div>' + item.keyword + '(신규)</div>';
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
        // keydown 됐을때 해당 값을 가지고 서버에서 검색함.
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
                url: '/head/auto-complete/goods-nm-eng',
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
        // 업체 검색시 키보드 사용 가능하도록 수정
        focus : (event, ui) => {
            event.preventDefault();
            jQuery(this).val(ui.item.suggestion);
        },
        // 업체명 입력시 관련 업체가 하단에 노출 될 때 스크롤 스타일 클래스 설정
        classes: {
            "ui-autocomplete": "ac-company-scroll"
        },
        minLength: 1,
        autoFocus: true,
        delay: 100
    });
    // 업체명 검색 스크롤 스타일 클래스 적용
    (() => {
        const acCompanyStyleObj = {
            'box-sizing': 'content-box',
            'overflow-y': 'scroll',
            'padding-right': '3px',
            'height' : '282px'
        };
        $('.ac-company-scroll').css(acCompanyStyleObj);

        // search-enter로 업체명 검색시 com_nm이 빈값인 경우 검색 안되는 문제 수정
        const ac_companies = document.querySelectorAll('.ac-company');
        for (let i = 0; i < ac_companies?.length; i++ ) {
            const com_nm = ac_companies[i];
            const input_wrap = com_nm.parentNode;
            com_nm.addEventListener('keyup', () => {
                if (com_nm.value == "") {
                    let input;
                    if (input = input_wrap.querySelector('#com_cd')) input.value = "";
                    else if (input = input_wrap.querySelector('#com_id')) input.value = "";
                }
            });
        }
    })();

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


    $('.select2-dup-style_no').select2({
        ajax: {
            url: "/head/auto-complete/dup-style-no",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                let brand = $('#brand').val();
                let year = $('#year').val();
                let season = $('#season').val();
                let gender = $('#gender').val();
                let item = $('#item').val();

                let prd_cd_p = brand + year + season + gender + item;

                return {
                    type:'select2',
                    keyword: params.term, // search term
                    page: params.page,
                    prd_cd_p : prd_cd_p
                };
            },
            cache: true
        },
        placeholder: '',
        allowClear: true,
        minimumInputLength: 0,
        // tags: true,
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

            // if($('.select2-search__field').val() != '') {
            //     var $state = $(
            //         '<span><span style="padding:0 15px;"></span>테스트</span>'
            //     );
            // }

            return $state;
        },
        // templateSelection: formatRepoSelection,
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

    /**
     * 
     * 공급업체 자동완성 부분
     * 
     */
    $('.select2-sup_company').select2({
        ajax: {
            url: "/head/auto-complete/sup_company",
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

	$('#SearchBrandModal').on('shown.bs.modal', function () {
		$('#SearchBrandModal [name=brand]').focus();
	});

	$('#SearchBrandModal').on('hide.bs.modal', function () {
		searchBrand.InitValue();
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

    $( ".sch-sup-company" ).on("click", () => {
        searchCompany.Open(null, '1');
    });

	$('#SearchCompanyModal').on('shown.bs.modal', function () {
		$('#SearchCompanyModal [name=com_nm]').focus();
	});

	$('#SearchCompanyModal').on('hide.bs.modal', function () {
		searchCompany.InitValue();
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


    $(".sch-ad_type").on("change", function(){
        var ad_type = $(this).val();
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

/** 그리드 헤더 개인화 설정 레이어 */
function SetMyGridHeader() {
	this.grid = null;
	this.setting_btn = null;
	this.save_callback = null;
	this.reset_callback = null;
}

SetMyGridHeader.prototype.Init = function(grid_object = null, save_callback = null, reset_callback = null) {
	this.grid = grid_object;
	this.save_callback = save_callback;
	this.reset_callback = reset_callback;

	if ($(".setting-grid-col").length > 0) {
		this.DrawComponent($(".setting-grid-col")[0]);
	}
}

SetMyGridHeader.prototype.DrawComponent = function(ele) {
	this.setting_btn = ele;
	if (ele) {
		$(ele).after(`
			<div id="setting-grid-layer" class="hide">
				<div class="p-2">
					<p class="fs-14 font-weight-bold mb-3"><i class="fas fa-cog mr-1"></i> 테이블 헤더 사용자화</p>
					<div class="flex" style="height:35px;">
						<button type="button" class="btn btn-outline-secondary h-100 mr-2" onclick="return setMyGridHeader.Reset();" style="width: 100px;">초기화</button>
						<button type="button" class="btn btn-primary h-100" onclick="return setMyGridHeader.Save(this);" style="width: 100px;">저장</button>
					</div>
				</div>
			</div>
		`);
		$(ele).data('toggle', 'popover');
		$(ele).popover({
			container: 'body',
			placement: 'left',
			html: true,
			sanitize: false,
			content: $('#setting-grid-layer').html(),
		});
	}
};

SetMyGridHeader.prototype.Save = function(obj) {
	// 저장 시 로직 추가 start
	console.log('save');
	// 저장 시 로직 추가 end
	
	// 아래코드는 저장완료 표시 후 팝오버를 닫는 코드입니다. 저장완료 후 실행해주세요.
	$(obj).html('<i class="fas fa-check"></i> 저장');
	setTimeout(() => {
		$(this.setting_btn).popover('hide');
	}, 500);
};

SetMyGridHeader.prototype.Reset = function() {
	if (!confirm("초기화하시겠습니까?")) return;
	// 삭제 시 로직 추가 start
	console.log('reset');
	// 삭제 시 로직 추가 end

	$(this.setting_btn).popover('hide');
};

let setMyGridHeader = new SetMyGridHeader();

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

SearchBrand.prototype.InitValue = () => {
	document.querySelector("#SearchBrandModal form[name=search_brand]").reset();
	searchBrand.grid.setRows([]);
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

SearchCompany.prototype.Open = function(callback = null, type = "", wonboo = false){
    if(this.grid === null){
        console.log(type);
        this.isWonboo = wonboo === "wonboo";
        this.type = type;
        this.SetGrid("#div-gd-company");
        //gxBrand = new HDGrid(document.querySelector("#div-gd-brand"), columnsBrand);
        $("#SearchCompanyModal").draggable();
        if (this.type === '1') $("#SearchCompanyModalLabel").text('공급업체 검색');
        if (this.type === '6') $("#SearchCompanyModalLabel").text('원부자재업체 검색');
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
            data += "&com_type=" + this.type;
            const url = '/head/api/company/getlist/';
            this.grid.Request(url, data);
        }
    });
};

SearchCompany.prototype.SetGrid = function(divId) {
    const columns = [
        {field:"com_type_nm" , headerName:"업체구분", width:80},
        {field:"com_id" , headerName:"업체코드", width:80},
        {field:"com_nm" , headerName: this.isWonboo === true ? "원부자재업체명" : "업체명", width:100},
        {field:"biz_num" , headerName:"사업자번호", width:115},
        {field:"md_nm" , headerName:"담당MD", width:70, hide:true},
        {field:"com_type", headerName:"업체타입", hide:true},
        {field:"baesong_kind", headerName:"배송업체", hide:true},
        {field:"baesong_info", headerName:"배송지역", hide:true},
        {field:"margin_type", headerName:"수수료타입", hide:true},
        {field:"dlv_amt", headerName:"배송료", hide:true},
        {field:"choice" , headerName:"선택", width:50,cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                return '<a href="javascript:void(0);" onclick="return searchCompany.Choice(\'' + params.data.com_id + '\',\'' + params.data.com_nm + '\',\'' + params.data.com_type + '\',\'' + params.data.baesong_kind + '\',\'' + params.data.baesong_info + '\',\'' + params.data.margin_type + '\',\'' + params.data.dlv_amt + '\',\'' + params.data.com_type_nm + '\');">선택</a>';
            }
        },
    ];

    this.grid = new HDGrid(document.querySelector( divId ), columns);
};

SearchCompany.prototype.Search = function() {
    let data = $('form[name="search_company"]').serialize();
    data += "&com_type=" + this.type;
    data += "&wonboo=" + this.isWonboo;
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
		} else if ($('#com_cd.select2-sup_company').length > 0) {
            $('#com_cd').val(null);
			const option = new Option(name, code, true, true);
			$('#com_cd').append(option).trigger('change');
        }
		else
		{
            if( $('#com_cd').length > 0 )
			{
				$('#com_cd').val(code);
                $('#com_cd').trigger("change");
			}
            if( $('#com_id').length > 0 )
            {
                $('#com_id').val(code);
                $('#com_id').trigger("change");
            }
			if( $('#com_nm').length > 0 )
			{
                $('#com_nm').val(name);
                $('#com_nm').trigger("change");
			}
		}
	}
	$('#SearchCompanyModal').modal('toggle');
};

SearchCompany.prototype.InitValue = () => {
	$('#SearchCompanyModal input[name="com_nm"]').val('');
	searchCompany.grid.setRows([]);
};

let searchCompany = new SearchCompany();
if ($('.ac-company').length > 0) {
    //#com_nm가 수정되어 빈값이 될 경우 #com_cd값 초기화
    $("#com_nm").on("change", (e) => {
        if(e.target.value === '') $("#com_cd").val('');
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
        {field: "goods_no", headerName: "온라인코드", width: 70, pinned: 'left'},
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
    // let data = $('form[name="search_goods_nos"]').serialize();
    let data = "";
    data += "sch_goods_nos=" + $("#sch_goods_nos").val().split("\n").filter(v => v).join(",");
    data += "&sch_style_nos=" + $("#sch_style_nos").val().split("\n").filter(v => v).join(",");
    data += "&cmd=modal";
    this.grid.Request('/head/api/goods', data,-1);
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
    //console.log(data);
    this.grid.Request('/head/api/goods', data,1);
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
        this.grid.Request(`/head/product/prd01/${this.goods_no}/get-basic-options`, null, 1);
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
    
    this.grid.Request(`/head/product/prd01/${this.goods_no}/get-basic-options`, null, 1, function(e) {

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
        url: `/head/product/prd01/${this.goods_no}/save-basic-options`,
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
            const response = await axios({ url: `/head/product/prd01/${this.goods_no}/delete-basic-options`, 
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
    if ($('#com_cd').length > 0) $('#com_cd').val("").trigger('change'); // 업체 select2 박스 초기화
    if ($("#goods_stat[name='goods_stat[]']").length > 0) $('#goods_stat').val([]).trigger('change'); // 전시상태 select2 박스 초기화
	searchGoodsNos.Init();
	
    /**
     * 동적 초기화
     */
    select2.map(key => {
        if ($(key).length > 0) $(key).val("").trigger('change'); // 전달받은 select2 박스 초기화
    });
};
