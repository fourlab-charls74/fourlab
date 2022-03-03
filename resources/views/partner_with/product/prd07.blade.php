@extends('partner_with.layouts.layout')
@section('title','상품일괄수정')
@section('content')

<div class="show_layout py-3">
  <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
    <div>
      <h3 class="d-inline-flex">상품일괄수정</h3>
      <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 상품관리</span>
        <span>/ 상품일괄수정</span>
      </div>
    </div>
  </div>
  <form name="detail">
    <div class="card_wrap aco_card_wrap">
      <div class="card shadow">
        <div class="card-header">
          <a href="#">일괄수정품목</a>
        </div>
          <div class="fr_box flax_box" style="position: absolute; right: 2%;">
            <a href="#" id="add-goods" class="d-none d-sm-inline-block btn btn-sm btn-primary mr-1 shadow-sm">추가</a>
            <a href="#" class="btn-sm btn btn-primary mr-1 apply-btn">적용</a>
            <a href="#" onclick="document.detail.reset()"  class="btn btn-sm btn-primary shadow-sm">취소</a>
          </div>
          <style>
              .required:after {
                content:" *"; color: red;
              }
          </style>
          <div class="card-body">
            <div class="row_wrap">
              <div class="row">
                  <div class="col-12">
                      <div class="table-box-ty2 mobile">
                          <table class="table incont table-bordered" id="dataTable">
                              <colgroup>
                                <col width="15%">
                                <col width="35%">
                                <col width="15%">
                                <col width="35%">
                              </colgroup>
                                  <tr>
                                      <th class="required">품목</th>
                                      <td>
                                        <div class="select_box">
                                            <select name="op_cd" id="op_cd" class="form-control form-control-sm search-all">
                                                <option value="">선택하세요.</option>
                                                @foreach($opt_cd_list as $opt_cd)
                                                    <option value="{{$opt_cd->name}}">{{$opt_cd->value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                      </td>
                                      <th class="required">브랜드</th>
                                      <td>
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" class="form-control form-control-sm search-all" name="brand" id="brand_nm" value="" style="width: 100%">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                      </td>
                                  </tr>
                                  <tr>
                                      <th class="required">상품상태</th>
                                      <td>
                                        <div class="select_box">
                                          <select name='goods_stat' id="goods_stat" class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            @foreach ($goods_stats as $goods_stat)
                                                <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </td>
                                      <th class="required">대표카테고리</th>
                                      <td>
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" value="" name="rep_cat_nm" id="rep_cat_nm" class="form-control form-control-sm search-all" style="width: 100%"/>
                                            <input type="hidden" value="" name="rep_cat_cd" id="rep_cat_cd"/>
                                            <a href="#" class="btn btn-sm btn-outline-primary"
                                                onclick="searchCategory.Open('DISPLAY',function(code,name){
                                                    $('#rep_cat_cd').val(code);
                                                    $('#rep_cat_nm').val(name);
                                                });"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                      </td>
                                  </tr>
                                  <tr>
                                      <th>제조사</th>
                                      <td>
                                        <div class="input_box">
                                            <input type="text" name="make" id="make" class="form-control form-control-sm search-all" />
                                        </div>
                                      </td>
                                      <th class="required">원산지</th>
                                      <td>
                                        <div class="input_box">
                                            <input type="text" name="org_nm" id="org_nm" class="form-control form-control-sm search-all" />
                                        </div>
                                      </td>
                                  </tr>
                                  <tr>
                                    <th>배송비</th>
                                    <td>
                                      <div class="form-inline form-radio-box flax_box txt_box">
                                          <div class="custom-control custom-radio">
                                              <input type="radio" name="dlv_fee_cfg" value="S" onclick="change_dlv_cfg_form('s')" id="dlv_fee_cfg1" class="custom-control-input" checked="">
                                              <label class="custom-control-label" for="dlv_fee_cfg1">쇼핑몰 설정</label>
                                          </div>
                                          <div class="custom-control custom-radio mr-2">
                                              <input type="radio" name="dlv_fee_cfg" value="G" onclick="change_dlv_cfg_form('g')" id="dlv_fee_cfg2" class="custom-control-input">
                                              <label class="custom-control-label" for="dlv_fee_cfg2">상품 개별 설정</label>
                                          </div>
                                          <div class="dlv_config_detail_div txt_box" id="dlv_config_detail_s_div">
                                              유료, 배송비 2,500원(50,000원 이상 구매 시 무료)
                                          </div>
                                          <div class="dlv_config_detail_div" id="dlv_config_detail_g_div" style="display:none;">
                                              <div class="flax_box">
                                                  <div class="select_box mr-1">
                                                      <select name="bae_yn" class="form-control form-control-sm search-all">
                                                          <option value="Y" selected>유료</option>
                                                          <option value="N">무료</option>
                                                      </select>
                                                  </div>
                                                  <div class="input_box"><input type="text" name="baesong_price" id="baesong_price" class="form-control form-control-sm search-all" style="width:100px;text-align:right;"></div>
                                                  <div class="txt_box">원</div>
                                              </div>
                                          </div>
                                      </div>
                                    </td>
                                    <th>예약/배송</th>
                                    <td>
                                      <div class="input_box flax_box txt_box">
                                        <input type="text" name="baesong_desc" id="baesong_desc" class="form-control form-control-sm" style="width:75%" />
                                        <div class="custom-control custom-checkbox ml-2" data-toggle="tooltip" data-placement="top" data-original-title="체크시 해당 내용에 공백이 들어갑니다.">
                                            <input type="checkbox" class="custom-control-input" name="baesong_empty" id="baesong_empty" value="Y">
                                            <label class="custom-control-label" for="baesong_empty">공백</label>
                                        </div>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <th>재입고 알람</th>
                                    <td>
                                      <div class="flax_box select_box">
                                          <select name="restock_yn" class=" form-control form-control-sm search-all">
                                              <option value="Y" selected>재입고함</option>
                                              <option value="N">안함</option>
                                          </select>
                                      </div>
                                    </td>
                                    <th>상품상세</th>
                                    <td>
                                      <div class="input_box">
                                        <input type="text" name="goods_cont" id="goods_cont" class="form-control form-control-sm"/>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <th>제품사양</th>
                                    <td>
                                      <div class="input_box flax_box txt_box">
                                        <input type="text" name="spec_desc" id="spec_desc" class="form-control form-control-sm" style="width:75%" />
                                        <div class="custom-control custom-checkbox ml-2" data-toggle="tooltip" data-placement="top" data-original-title="체크시 해당 내용에 공백이 들어갑니다.">
                                            <input type="checkbox" class="custom-control-input" name="spec_empty" id="spec_empty" value="Y">
                                            <label class="custom-control-label" for="spec_empty">공백</label>
                                        </div>
                                      </div>
                                    </td>
                                    <th>MD 상품평</th>
                                    <td>
                                      <div class="input_box flax_box txt_box">
                                        <input type="text" name="opinion" id="opinion" class="form-control form-control-sm" style="width:75%" />
                                        <div class="custom-control custom-checkbox ml-2" data-toggle="tooltip" data-placement="top" data-original-title="체크시 해당 내용에 공백이 들어갑니다.">
                                            <input type="checkbox" class="custom-control-input" name="opinion_empty" id="opinion_empty" value="Y">
                                            <label class="custom-control-label" for="opinion_empty">공백</label>
                                        </div>
                                      </div>
                                    </td>
                                  </tr>
                          </table>
                      </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </form>
</div>

<div class="card shadow mb-3">
  <form method="post" name="save" id ="insert_form" action="/partner/stock/stk01">
      @csrf
      <textarea style="display:none" name="form_str" id="csvResult"></textarea>
      <div class="card-body shadow">
          <div class="card-title">
              <div class="filter_wrap">
                  <div class="fl_box">
                      <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                  </div>
                  <div class="fr_box">
          <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Cmder('save')">저장</a>
          <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Cmder('del');">삭제</a>
        </div>
              </div>
          </div>
          <div class="table-responsive">
              <div id="div-gd" style="width:100%;min-height:400px;" class="ag-theme-balham"></div>
          </div>
      </div>
  </form>
</div>

  <script language="javascript">
  const EDITABLE_COLOR = '#ffffcc';
  const CELL_STYLE_TEXT_RIGHT = { 'text-align' : 'right' };
  let columnDefs = [
      {
        field: "blank",
        headerName: '',
        headerCheckboxSelection: true,
        checkboxSelection: true,
        headerCheckboxSelectionFilteredOnly: true,
        width: 50,
        pinned: 'left'
      },
      {
        headerName:"상품번호",
        children : [
          {
            headerName : "번호",
            field : "goods_no",
            pinned: 'left'
          },
          {
            headerName : "하위",
            field : "goods_sub",
            pinned: 'left'
          }
        ],
        pinned: 'left'
      },
      { field:"style_no" , headerName:"스타일넘버", pinned: 'left', width: 100 },
      {field:"opt_kind_nm" , headerName:"품목", pinned: 'left', width: 120 },
      {field:"brand_nm" , headerName:"브랜드", pinned: 'left', width: 80 },
      {field:"full_nm" , headerName:"대표카테고리", pinned: 'left'  },
      {field:"sale_stat_nm" , headerName:"상품상태", pinned: 'left', width: 90 },
      {
        field:"img_s_62",
        headerName: "이미지",
        pinned: 'left',
        width: 80,
        cellRenderer: function(params) {
          if (params.value == null) return '';

          return '<img src="'+params.value+'" alt="상품이미지">';
        }
      },
      {
        field:"goods_nm",
        headerName:"상품명",
        width: 150,
        editable: true,
        cellStyle : {
          'background' : EDITABLE_COLOR
        }
      },
      {
        headerName:"현재가격",
        children : [
          {
            headerName : "판매가",
            field : "price",
            type:'currencyType',
            width: 100
          },
          {
            headerName : "수수료율(%)",
            field : "margin_rate",
            width: 110,
            cellStyle: CELL_STYLE_TEXT_RIGHT,
            cellRenderer: function(params) {
              let rate = params.value == '' ? 0 : params.value;
              return parseFloat(rate).toFixed(2);
            }
          },
          {
            headerName : "원가",
            field : "wonga",
            type:'currencyType',
            width: 100
          }
        ]
      },
      {
        headerName:"수정가격",
        children : [
          {
            headerName : "판매가",
            field : "mod_price",
            width: 100,
            editable: true,
            type:'currencyType',
            cellStyle : {
              'background' : EDITABLE_COLOR,
              'text-align' : 'right'
            },
            cellRenderer : function(params) {
              const nodeRow = gx.gridOptions.api.getRowNode(params.data.id);
              params.data.mod_wonga = getWonga(params.data);
              nodeRow.setData(params.data);

              return params.value;
            }
          },
          {
            headerName : "수수료율(%)",
            field : "mod_margin_rate",
            width: 110,
            editable: true,
            cellStyle : {
              'background' : EDITABLE_COLOR,
              'text-align' : 'right'
            },
            cellRenderer: function(params) {
              const nodeRow = gx.gridOptions.api.getRowNode(params.data.id);
              params.data.mod_wonga = getWonga(params.data);
              nodeRow.setData(params.data);

              let rate = params.value == '' ? 0 : params.value;
              return parseFloat(rate).toFixed(2);
            }
          },
          {
            headerName : "원가",
            field : "mod_wonga",
            type:'currencyType',
            width: 100
          }
        ]
      },
      {field:"bae_info" , headerName:"배송방식"},
      {field:"bae_kind" , headerName:"배송업체"  },
      {field:"dlv_pay_type_nm" , headerName:"배송비지불"  },
      {
        headerName:"배송비",
        children : [
          {
            headerName : "설정",
            field : "dlv_fee_cfg"
          },
          {
            headerName : "지급",
            field : "bae_yn"
          },
          {
            headerName : "금액",
            field : "baesong_price",
            type:'currencyType'
          }
        ]
      },
      {field:"org_nm" , headerName:"원산지"  },
      {field:"make" , headerName:"제조사"  },
      {field:"restock_yn" , headerName:"재고알림"  },
      {
        field:"goods_cont" ,
        headerName:"상품상세",
        editable: true,
        cellStyle : {
          'background' : EDITABLE_COLOR
        }
      },
      {
        field:"spec_desc",
        headerName:"제품사양",
        editable: true,
        cellStyle : {
          'background' : EDITABLE_COLOR
        }
      },
      {
        field:"baesong_desc",
        headerName:"예약/배송",
        editable: true,
        cellStyle : {
          'background' : EDITABLE_COLOR
        }
      },
      {
        field:"opinion",
        headerName:"MD상품평",
        editable: true,
        cellStyle : {
          'background' : EDITABLE_COLOR
        }
      }
  ];
</script>

<script>
  const pApp = new App('', { gridId: "#div-gd" });
  const gridDiv = document.querySelector(pApp.options.gridId);
  let gx;
  $(document).ready(function() {
      gx = new HDGrid(gridDiv, columnDefs);
      gx.gridOptions.getRowNodeId = function(data) {
        return data.id;
      }

      pApp.ResizeGrid(250);
      Search();
  });

  function Search(){
      let data = "goods_nos=" + $('[name=goods_nos]').val();
      gx.Request('/partner/product/prd07/search', data);
  }

  function Cmder(cmd){

  if(cmd =="del"){
      Delete();
  } else if(cmd =="save"){
      if(validate()){
          Save();
      }
  }
  } 

  function getWonga(data) {
    const price = data.mod_price;
    let rate  = data.mod_margin_rate;
        rate = rate == '' ? 0 : rate * 0.01;
    let gap = rate == 0 ? 0 : (price * rate);
    return price - gap;
  }

  $(".submit-btn").click(function(){
    let rows = gx.gridOptions.api.getSelectedRows();

    rows.forEach(function(data, idx){
      data._token = $('[name=_token]').val();
      data.mod_wonga = getWonga(data);

      $.ajax({
          async: true,
          type: 'put',
          url: '/partner/product/prd07/update',
          data: data,
          success: function () {
            if (idx === rows.length -1) {
              alert('상품이 수정되었습니다.');
              window.close();
            }
          },
          error: function(request, status, error) {
              console.log("error")
          }
      });
    });
  });

  $(".apply-btn").click(function(){
    let rows = gx.gridOptions.api.getSelectedRows();

    if(rows.length === 0) {
      alert("적용할 항목을 선택해주세요.");
      return;
    }

    rows.forEach(function(data){
      const nodeRow = gx.gridOptions.api.getRowNode(data.id);
      const item = $('#item').val();
      const brand = $('#brand_nm').val();
      const goods_stat = $('#goods_stat').val();
      const goods_stat_val = $('#goods_stat > option:selected').html();
      const dlv_cfg = $('[name=dlv_cfg]:checked').val();
      const make = $('#make').val();
      const org_nm = $('#org_nm').val();
      const restock_yn = $('#restock_yn:checked').length;
      const goods_cont = $('#goods_cont').val();
      const spec_desc = $('#spec_desc').val();
      const baesong_desc = $('#baesong_desc').val();
      const opinion = $('#opinion').val();

      if (item) {
        data.opt_kind_cd = item;
        data.opt_kind_nm = $('#item > option:selected').html();
      }

      if (brand) data.brand_nm = brand;

      if (goods_stat) {
        data.sale_stat_nm = goods_stat_val;
        data.sale_stat_cl = goods_stat;
      }

      if (dlv_cfg) {
        const dlv_fee_yn = $("#dlv_fee_yn").val();
        const dlv_price = $("#dlv_price").val();
        data.dlv_fee_cfg = dlv_cfg;

        if (dlv_cfg === 'S') {
          data.bae_yn = '';
          data.baesong_price = '';
        } else {
          data.bae_yn = dlv_fee_yn;
          data.baesong_price = dlv_price;
        }
      }

      if (make) data.make = make;
      if (org_nm) data.org_nm = org_nm;
      if (goods_cont) data.goods_cont = goods_cont;
      if (spec_desc) data.spec_desc = spec_desc;
      if (baesong_desc) data.baesong_desc = baesong_desc;
      if (opinion) data.opinion = opinion;

      if ($('#spec_empty:checked').length > 0) data.spec_desc = '';
      if ($('#baesong_empty:checked').length > 0) data.baesong_desc = '';
      if ($('#opinion_empty:checked').length > 0) data.opinion = '';

      data.restock_yn = restock_yn > 0 ? 'Y' : 'N';

      nodeRow.setData(data);
    });
  });

  $("#add-goods").click(function(e){
    e.preventDefault();

    window.open(
        '/partner/product/prd10?ismt=Y',
        "pop_goods",
        "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=900"
    );
  });

  $('.delete-btn').click(function(e){
      e.preventDefault();

      let rows = gx.gridOptions.api.getSelectedRows();

      rows.forEach(function(row){
        gx.gridOptions.api.applyTransaction({ remove: [ row ] });
      });
  });

  function addProductRow(row){
      const rows_cnt = gx.gridOptions.api.getDisplayedRowCount();
      const pushRow = [];

      for(let i=0; i<row.length; i++){
          const id = row[i].goods_no + '_' + row[i].goods_sub;
          const rowNode = gx.gridOptions.api.getRowNode(id);

          if (rowNode === undefined) {
            row[i].id = id;
            pushRow.push(row[i]);
          }
      }

      gx.gridOptions.api.applyTransaction({ add: pushRow });
  }
  function change_dlv_cfg_form(value){
      $(".dlv_config_detail_div").css("display","none");
      $("#dlv_config_detail_"+value+"_div").css("display","inline");
  }
</script>
@stop

