<script type="text/javascript" charset="utf-8">

    function getAddBox({id}) {
        var addboxHtml = `
                <div class="d-flex card-header justify-content-between">
                    <h4>등록</h4>
                    <div>
                        <a href="#" class="btn btn-sm btn-primary shadow-sm pr-2" onclick="addItem()"><i class="fas fa-plus fa-sm text-white-50"></i></a>
                        <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="deleteItem(${id})"><i class="fas fa-minus fa-sm text-white-50"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="code_kind_cd_${id}">코드 종류</label>
                                <div class="flax_box">
                                    <div class="custom-control custom-radio pr-2">
                                        <input type="radio" class="custom-control-input" name="code_type_${id}" id="code_type_upche_${id}" value="upche" checked onclick="showBox(${id}, false)">
                                        <label class="custom-control-label" for="code_type_upche_${id}">업체</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="code_type_${id}" id="code_type_brand_${id}" value="brand" onclick="showBox(${id}, true)">
                                        <label class="custom-control-label" for="code_type_brand_${id}">브랜드</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="code_id_${id}">코드ID</label>
                                <div class="flax_box">
                                    <input type="text" name="code_id_${id}" id="code_id_${id}" class="form-control form-control-sm search-enter">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="code_val_${id}">코드값</label>
                                <div class="flax_box">
                                    <input type="text" name="code_val_${id}" id="code_val_${id}" class="form-control form-control-sm search-enter">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="brand_show_${id}" style="display:none;">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="code_category_${id}">코드 분류</label>
                                <div class="flax_box">
                                    <input type="text" name="code_category_${id}" id="code_category_${id}" class="form-control form-control-sm search-enter">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="code_stat_${id}">코드 상태</label>
                                <div class="flax_box">
                                    <input type="text" name="code_stat_${id}" id="code_stat_${id}" class="form-control form-control-sm search-enter">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="code_content_${id}">코드 내용</label>
                                <div class="flax_box">
                                    <input type="text" name="code_content_${id}" id="code_content_${id}" class="form-control form-control-sm search-enter">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        `;
        return addboxHtml;
    }
</script>