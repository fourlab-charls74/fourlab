<!-- 
    보내는 매장 selector 설정
-->

<!-- sample modal content -->
<div id="SearchSendStoreModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SearchSendStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">매장 검색</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body show_layout" style="background:#f5f5f5;">
                <div class="card_wrap search_cum_form write">
                    <div class="card shadow">
                        <form name="search_send_store" method="get" onsubmit="return false">
                            <div class="card-body">
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-lg-12 inner-td">
                                            <div class="form-group">
                                                <label style="min-width:60px;">매장명</label>
                                                <div class="flex_box">
                                                    <input type='text' class="form-control form-control-sm search-all" onkeypress="searchSendStore.Search(event);" name='send_store_nm' value=''>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="resul_btn_wrap" style="padding-top:7px;text-align:right;display:block;">
                                    <a href="javascript:void(0);" id="search_send_store_sbtn" onclick="return searchSendStore.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card shadow mb-1 pt-0">
                        <div class="card-body m-0">
                            <div class="card-title">
                                <div class="filter_wrap">
                                    <div class="fl_box">
                                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-send-store-total" class="text-primary">0</span> 건</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div id="div-gd-send-store" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script language="javascript">
	function SearchSendStore(){
        this.grid = null;
    }

    SearchSendStore.prototype.Open = function(callback = null){
        if(this.grid === null){
            this.SetGrid("#div-gd-send-store");
            $("#SearchSendStoreModal").draggable();
            if(this.isMultiple) $("#SearchSendStoreModal #search_send_store_cbtn").css("display", "block");
            this.callback = callback;
        }
        $('#SearchSendStoreModal').modal({
            keyboard: false
        });
    };

    SearchSendStore.prototype.SetGrid = function(divId){
        let columns = [
            { field:"store_cd", headerName:"매장코드", width:100, cellStyle: { "text-align": "center" }, hide: true },
            { field:"store_nm", headerName:"매장", width: "auto" },
            { 
                field:"choice", headerName:"선택", width:100, cellClass:'hd-grid-code',
                cellRenderer: function (params) {
                    if (params.data.store_cd !== undefined) {
                        return '<a href="javascript:void(0);" onclick="return searchSendStore.Choice(\'' + params.data.store_cd + '\',\'' + params.data.store_nm + '\');">선택</a>';
                    }
                }
            }
        ];

        this.grid = new HDGrid(document.querySelector( divId ), columns);
    };

    SearchSendStore.prototype.Search = function(e) {
        const event_type = e?.type;
        if (event_type == 'keypress') {
            if (e.key && e.key == 'Enter') {
                let data = $('form[name="search_send_store"]').serialize();
                this.grid.Request('/store/api/stores/search', data);
            } else {
                return false;
            }
        } else {
            let data = $('form[name="search_send_store"]').serialize();
            this.grid.Request('/store/api/stores/search', data);
        }
    };

    SearchSendStore.prototype.Choice = function(code,name){
        if(this.callback !== null){
            this.callback(code, name);
        } else {
            if($('#send_store_no.select2-store').length > 0){
                $('#send_store_no').val(null);
                const option = new Option(name, code, true, true);
                $('#send_store_no').append(option).trigger('change');
            } else {
                if($('#send_store_no').length > 0){
                    $('#send_store_no').val(code);
                }
                if($('#send_store_nm').length > 0){
                    $('#send_store_nm').val(name);
                }
            }
            if($('#send_store_cd.select2-store').length > 0){
                $('#send_store_cd').val(null);
                const option = new Option(name, code, true, true);
                $('#send_store_cd').append(option).trigger('change');
            } else {
                if($('#send_store_cd').length > 0){
                    $('#send_store_cd').val(code);
                }
                if($('#send_store_nm').length > 0){
                    $('#send_store_nm').val(name);
                }
            }
        }
        this.InitValue();
        $('#SearchSendStoreModal').modal('toggle');
    };

    SearchSendStore.prototype.InitValue = () => {
        document.search_store.reset();
        searchSendStore.grid.setRows([]);
        $('#gd-send-store-total').html(0);
    };


    let searchSendStore = new SearchSendStore();

    $(document).ready(function() {
        $( ".sch-send-store" ).on("click", function() {
            searchSendStore.Open();
        });
    });
</script>
