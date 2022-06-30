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

SearchStore.prototype.Open = function(callback = null){
    if(this.grid === null){
        this.SetGrid("#div-gd-store");
        $("#SearchStoreModal").draggable();
        this.callback = callback;
    }
    $('#SearchStoreModal').modal({
        keyboard: false
    });
};

SearchStore.prototype.SetGrid = function(divId){
    const columns = [
        { field:"store_cd", headerName:"매장코드", width:100, cellStyle: { "text-align": "center" }, hide: true },
        { field:"store_nm", headerName:"매장", width: "auto" },
        { field:"choice", headerName:"선택", width:100, cellClass:'hd-grid-code',
            cellRenderer: function (params) {
                if (params.data.store_cd !== undefined) {
                    return '<a href="javascript:void(0);" onclick="return searchStore.Choice(\'' + params.data.store_cd + '\',\'' + params.data.store_nm + '\');">선택</a>';
                }
            }
        }
    ];

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

SearchStore.prototype.InitValue = () => {
    document.search_store.reset();
    searchStore.grid.setRows([]);
    $('#gd-store-total').html(0);
};


let searchStore = new SearchStore();