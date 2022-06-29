$( document ).ready(function() {

    $('.select2-store').select2({
        ajax: {
            url: "/head/auto-complete/store",
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

    $( ".sch-store" ).on("click", function() {
        searchStore.Open();
    });

    function SearchStore(){
        this.grid = null;
    }
    
    SearchStore.prototype.Open = function(callback = null){
        if(this.grid === null){
            this.SetGrid("#div-gd-Store");
            $("#SearchStoreModal").draggable();
            this.callback = callback;
        }
        $('#SearchStoreModal').modal({
            keyboard: false
        });
    };
    
    SearchStore.prototype.SetGrid = function(divId){
        const columns = [
            {field:"Store" , headerName:"브랜드",width:100},
            {field:"Store_nm" , headerName:"브랜명",width:150},
            {field:"use_yn" , headerName:"사용여부",width:100,cellClass:'hd-grid-code'},
            {field:"choice" , headerName:"선택",width:100,cellClass:'hd-grid-code',
                cellRenderer: function (params) {
                    if (params.data.Store !== undefined) {
                        return '<a href="javascript:void(0);" onclick="return searchStore.Choice(\'' + params.data.Store + '\',\'' + params.data.Store_nm + '\');">선택</a>';
                    }
                }
            },
            {field:"nvl" , headerName:""},
        ];
    
        this.grid = new HDGrid(document.querySelector( divId ), columns);
    };
    
    SearchStore.prototype.Search = function(e) {
        const event_type = e?.type;
        if (event_type == 'keypress') {
            if (e.key && e.key == 'Enter') {
                let data = $('form[name="search_Store"]').serialize();
                this.grid.Request('/head/api/Store/getlist', data);
            } else {
                return false;
            }
        } else {
            let data = $('form[name="search_Store"]').serialize();
            this.grid.Request('/head/api/Store/getlist', data);
        }
    };
    
    SearchStore.prototype.Choice = function(code,name){
        if(this.callback !== null){
            this.callback(code,name);
        } else {
            if($('#Store_cd.select2-Store').length > 0){
                $('#Store_cd').val(null);
                const option = new Option(name, code, true, true);
                $('#Store_cd').append(option).trigger('change');
            } else {
                if($('#Store_cd').length > 0){
                    $('#Store_cd').val(code);
                }
                if($('#Store_nm').length > 0){
                    $('#Store_nm').val(name);
                }
            }
        }
        $('#SearchStoreModal').modal('toggle');
    };
    let searchStore = new SearchStore();

});