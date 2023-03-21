@extends('partner_with.layouts.layout')
@section('content')
<?php
  $com_type = ['', '공급', '입점', '제휴', '판매', '본사'];
  $baesong_kind = ['', '본사', '입점업체'];
  $baesong_info = ['', '국내', '해외', '해외구매대행'];
?>
<div id="write-area" class="show_layout">
  <form method="post" name="profile">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">업체 정보</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 업체 정보</span>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-primary shadow-sm" id="submit-btn"><i class="bx bx-save mr-1"></i>저장</button>
        </div>
    </div>
    <div class="card_wrap aco_card_wrap">
      <div class="card shadow">
          <div class="card-header mb-0">
              <a href="#" class="m-0 font-weight-bold">업체 기본 정보</a>
          </div>
          <div class="card-body">
            <div class="row_wrap">
                <div class="row">
                    <div class="col-12">
                        <div class="table-box-ty2 mobile">
                            <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <colgroup>
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                                </colgroup>
                                <tr>
                                    <th>업체아이디</th>
                                    <td>
                                        <div class="txt_box">{{ $user->com_id }}</div>
                                    </td>
                                    <th>단축아이디</th>
                                    <td>
                                        <div class="txt_box">{{$user->com_ab}}</div>
                                    </td>
                                    <th>비밀번호</th>
                                    <td colspan="3">
                                      <div class="txt_box flax_box">
                                        <input type="password" id="pwd" class="mwidth form-control form-control-sm" style='width:29%; display:inline' value="{{$user->pwd}}">
                                        <div class="custom-control custom-checkbox form-check-box ml-1">
                                            <input type="checkbox" id="pw_change" class="custom-control-input">
                                            <label class="custom-control-label" for="pw_change">비밀번호 변경</label>
                                        </div>
                                      </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>업체구분</th>
                                    <td>
                                        <div class="txt_box">{{$com_type[$user->com_type]}}</div>
                                    </td>
                                    <th>업체명</th>
                                    <td>
                                        <div class="txt_box">{{$user->com_nm}}</div>
                                    </td>
                                    <th>발송구분</th>
                                    <td>
                                        <div class="txt_box">{{$baesong_kind[$user->baesong_kind]}}</div>
                                    </td>
                                    <th>배송정보</th>
                                    <td>
                                      <div class="txt_box">{{$baesong_info[$user->baesong_info]}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>담당MD</th>
                                    <td>
                                        <div class="txt_box">{{$user->md_nm}}</div>
                                    </td>
                                    <th>정산담당자</th>
                                    <td>
                                        <div class="txt_box">{{$user->staff_nm2}}</div>
                                    </td>
                                    <th>판매수수료율</th>
                                    <td>
                                        <div class="txt_box">{{$user->pay_fee}}%</div>
                                    </td>
                                    <th>수수료지정</th>
                                    <td>
                                      <div class="txt_box">
                                        {{
                                          $user->margin_type === 'FEE'? '수수료지정' : '공급가지정'
                                        }}
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
      <div class="card mt-3">
        <div class="card-header mb-0">
            <a href="#" class="m-0 font-weight-bold">업체 배송 정보</a>
        </div>
        <div class="card-body">
            <div class="row_wrap">
                <div class="row">
                    <div class="col-12">
                      <div class="table-box-ty2 mobile">
                          <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                              <colgroup>
                                <col width="94px" />
                                <col width="23%" />
                                <col width="94px" />
                                <col width="23%" />
                                <col width="94px" />
                                <col width="23%" />
                              </colgroup>
                              <tr>
                                  <th>배송비 정책</th>
                                  <td>
                                    <div class="txt_box">
                                      {{
                                        $user->dlv_policy === 'S' ? '쇼핑몰' : '업체'
                                      }}
                                    </div>
                                  </td>
                                  <th class="ty2">배송비/무료배송금액</th>
                                  <td class="ty2">
                                      <div class="txt_box">{{$user->dlv_amt}}원 / {{$user->free_dlv_amt_limit}}원 이상 무료</div>
                                  </td>
                                  <th>배송기간(일)</th>
                                  <td>
                                    <div class="txt_box">{{$user->dlv_day}}일</div>
                                  </td>
                              </tr>
                          </table>
                      </div>
                  </div>
                </div>
            </div>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-header mb-0">
            <a href="#" class="m-0 font-weight-bold">API 연동 정보</a>
        </div>
        <div class="card-body">
            <div class="row_wrap">
                <div class="row">
                    <div class="col-12">
                      <div class="table-box-ty2 mobile">
                          <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                              <colgroup>
                                <col width="94px" />
                                <col width="38%" />
                                <col width="94px" />
                                <col width="38%" />
                              </colgroup>
                              <tr>
                                  <th>API 사용여부</th>
                                  <td>
                                    <div class="txt_box">{{ $user->api_yn === 'N' ? '미사용' : '사용' }}</div>
                                  </td>
                                  <th>API 인증키</th>
                                  <td>
                                      <div class="txt_box">{{ $user->api_key }}</div>
                                  </td>
                              </tr>
                          </table>
                      </div>
                  </div>
                </div>
            </div>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-header mb-0">
            <a href="#" class="m-0 font-weight-bold">세금계산서 및 계좌</a>
        </div>
        <div class="card-body">
            <div class="row_wrap">
                <div class="row">
                    <div class="col-12">
                      <div class="table-box-ty2 mobile">
                          <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                              <colgroup>
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                              </colgroup>
                              <tr>
                                  <th>상호</th>
                                  <td>
                                    <div class="input_box">
                                      <input type="text" name="name" class="form-control form-control-sm" value="{{$user->name}}">
                                    </div>
                                  </td>
                                  <th>사업자등록번호</th>
                                  <td>
                                    <div class="input_box">
                                      <input type="text" name="biz_num" class="form-control form-control-sm" value="{{$user->biz_num}}">
                                    </div>
                                  </td>
                                  <th>대표자</th>
                                  <td>
                                    <div class="input_box">
                                      <input type="text" name="ceo" class="form-control form-control-sm" value="{{$user->ceo}}">
                                    </div>
                                  </td>
                                  <th>주민(법인)번호</th>
                                  <td>
                                    <div class="input_box">
                                      <input type="text" name="jumin_num" class="form-control form-control-sm" value="{{$user->jumin_num}}">
                                    </div>
                                  </td>
                              </tr>
                              <tr>
                                  <th>업태</th>
                                  <td>
                                    <div class="input_box">
                                      <input type="text" name="uptae" class="form-control form-control-sm" value="{{$user->uptae}}">
                                    </div>
                                  </td>
                                  <th>업종</th>
                                  <td>
                                    <div class="input_box">
                                      <input type="text" name="upjong" class="form-control form-control-sm" value="{{$user->upjong}}">
                                    </div>
                                  </td>
                                  <th>거래은행</th>
                                  <td>
                                    <div class="input_box">
                                      <input
                                        type="text"
                                        name="bank"
                                        class="form-control form-control-sm"
                                        value="{{$user->bank}}"
                                      >
                                    </div>
                                  </td>
                                  <th>거래 계좌번호</th>
                                  <td>
                                    <div class="input_box">
                                      <input
                                        type="text"
                                        name="account"
                                        class="form-control form-control-sm"
                                        value="{{$user->account}}"
                                      >
                                    </div>
                                  </td>
                              </tr>
                              <tr>
                                  <th>예금주</th>
                                  <td>
                                    <div class="input_box">
                                      <input type="text" name="dipositor" class="form-control form-control-sm" value="{{$user->dipositor}}">
                                    </div>
                                  </td>
                                  <th>지불주기</th>
                                  <td>
                                    <div class="input_box">
                                      <input
                                        type="text"
                                        name="pay_day"
                                        class="form-control form-control-sm"
                                        value="{{$user->pay_day}}"
                                        style="width:90%; display:inline-block"
                                      > 일
                                    </div>
                                  </td>
                                  <th>홈페이지</th>
                                  <td colspan="3">
                                    <div class="input_box">
                                      <input type="text" name="homepage" class="form-control form-control-sm" value="{{$user->homepage}}">
                                    </div>
                                  </td>
                              </tr>
                              <tr>
                                  <th>사업장주소</th>
                                  <td colspan="7">
                                    <div class="input_box flax_box address_box">
                                      <input
                                        type="text"
                                        id="zip_code"
                                        name="zip_code"
                                        class="form-control form-control-sm"
                                        value="{{$user->zip_code}}"
                                        style="width:calc(25% - 10px);margin-right:10px;"
                                        readonly="readonly"
                                      >
                                      <input
                                        type="text"
                                        id="addr1"
                                        name="addr1"
                                        class="form-control form-control-sm"
                                        value="{{$user->addr1}}"
                                        style="width:calc(25% - 10px);margin-right:10px;"
                                        readonly="readonly"
                                      >
                                      <input
                                        type="text"
                                        id="addr2"
                                        name="addr2"
                                        class="form-control form-control-sm"
                                        value="{{$user->addr2}}"
                                        style="width:calc(25% - 10px);margin-right:10px;"
                                      >
                                      <a
                                        href="javascript:;"
                                        onclick="openFindAddress('zip_code', 'addr1')"
                                        class="btn btn-sm btn-primary shadow-sm"
                                        style="width:80px;"
                                      >
                                          <i class="fas fa-search fa-sm text-white-50"></i>
                                          검색
                                      </a>
                                    </div>
                                  </td>
                              </tr>
                              <tr>
                                  <th>반송지주소</th>
                                  <td colspan="7">
                                    <div class="input_box flax_box address_box">
                                      <input
                                        type="text"
                                        id="r_zip_code"
                                        name="r_zip_code"
                                        class="form-control form-control-sm"
                                        value="{{$user->r_zip_code}}"
                                        style="width:calc(25% - 10px);margin-right:10px;"
                                        readonly="readonly"
                                      >
                                      <input
                                        type="text"
                                        id="r_addr1"
                                        name="r_addr1"
                                        class="form-control form-control-sm"
                                        value="{{$user->r_addr1}}"
                                        style="width:calc(25% - 10px);margin-right:10px;"
                                        readonly="readonly"
                                      >
                                      <input
                                        type="text"
                                        id="r_addr2"
                                        name="r_addr2"
                                        class="form-control form-control-sm"
                                        value="{{$user->r_addr2}}"
                                        style="width:calc(25% - 10px);margin-right:10px;"
                                      >
                                      <a
                                        href="javascript:;"
                                        onclick="openFindAddress('r_zip_code', 'r_addr1')"
                                        class="btn btn-sm btn-primary shadow-sm"
                                        style="width:80px;margin-right:10px;"
                                      >
                                          <i class="fas fa-search fa-sm text-white-50"></i>
                                          검색
                                      </a>
                                      <!-- d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm -->
                                      <a href="javascript:;" id="copyAddress" class="btn btn-sm btn-outline-primary shadow-sm" style="width:120px;">사업장주소 복사</a>
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
      <div class="card mt-3">
        <div class="card-header mb-0">
            <a href="#" class="m-0 font-weight-bold">업체 CS 정보</a>
        </div>
        <div class="card-body">
            <div class="row_wrap">
                <div class="row">
                    <div class="col-12">
                        <div class="table-box-ty2 mobile">
                            <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <colgroup>
                                    <col width="94px" />
                                    <col width="23%" />
                                    <col width="94px" />
                                    <col width="23%" />
                                    <col width="94px" />
                                    <col width="23%" />
                                </colgroup>
                                <tr>
                                    <th>사업자구분</th>
                                    <td>
                                      <div class="form-inline form-radio-box flax_box">
                                          <div class="custom-control custom-radio">
                                              <input type="radio" name="biz_type" id="biz_type1" value='C' class="custom-control-input" {{$user->biz_type === 'C' ? 'checked' : ''}} >
                                              <label class="custom-control-label" for="biz_type1">법인</label>
                                          </div>
                                          <div class="custom-control custom-radio">
                                              <input type="radio" name="biz_type" id="biz_type2" value='P' class="custom-control-input" {{$user->biz_type === 'P' ? 'checked' : ''}} >
                                              <label class="custom-control-label" for="biz_type2">개인</label>
                                          </div>
                                      </div>
                                    </td>
                                    <th>통신판매신고</th>
                                    <td>
                                      <div class="input_box">
                                        <input type="text" name="mail_order_nm" class="form-control form-control-sm" value="{{$user->mail_order_nm}}">
                                      </div>
                                    </td>
                                    <th class="ty3">CS담당자</th>
                                    <td class="ty3">
                                      <div class="input_box">
                                        <input type="text" name="cs_nm" class="form-control form-control-sm" value="{{$user->cs_nm}}">
                                      </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ty3">CS담당자 이메일</th>
                                    <td class="ty3">
                                      <div class="input_box">
                                      <input type="text" name="cs_email" class="form-control form-control-sm" value="{{$user->cs_email}}">
                                      </div>
                                    </td>
                                    <th class="ty3">CS담당자 연락처</th>
                                    <td class="ty3">
                                      <div class="input_box flax_box">
                                        <input type="text" name="cs_hp" class="form-control form-control-sm mr-1" style="width:75%;" value="{{$user->cs_hp}}"> (-)포함
                                      </div>
                                    </td>
                                    <th class="ty3">CS담당자 휴대전화</th>
                                    <td class="ty3">
                                      <div class="input_box flax_box">
                                        <input type="text" name="cs_phone" class="form-control form-control-sm mr-1" style="width:75%;" value="{{$user->cs_phone}}"> (-)포함
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
      <div class="card mt-3">
        <div class="card-header mb-0">
            <a href="#" class="m-0 font-weight-bold">업체 담당자</a>
        </div>
        <div class="card-body">
            <div class="row_wrap">
                <div class="row">
                    <div class="col-12">
                        <div class="table-box-ty2 mobile">
                            <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <colgroup>
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                                </colgroup>
                                <tr>
                                    <th>담당자</th>
                                    <td>
                                      <div class="input_box">
                                        <input type="text" name="staff_nm1" class="form-control form-control-sm" value="{{$user->staff_nm1}}">
                                      </div>
                                    </td>
                                    <th>담당자 이메일</th>
                                    <td>
                                      <div class="input_box flax_box">
                                        <input type="text" name="staff_email1" class="form-control form-control-sm mr-1" style="width:100%;" value="{{$user->staff_email1}}">
                                      </div>
                                    </td>
                                    <th>담당자 연락처</th>
                                    <td>
                                      <div class="input_box flax_box">
                                        <input type="text" name="staff_phone1" class="form-control form-control-sm mr-1" style="width:80%;" value="{{$user->staff_phone1}}">(-)포함
                                      </div>
                                    </td>
                                    <th>담당자 휴대전화</th>
                                    <td>
                                      <div class="input_box flax_box">
                                        <input type="text" name="staff_hp1" class="form-control form-control-sm mr-1" style="width:80%;" value="{{$user->staff_hp1}}">(-)포함
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
      <div class="card mt-3">
        <div class="card-header mb-0">
            <a href="#" class="m-0 font-weight-bold">업체 정산 담당자</a>
        </div>
        <div class="card-body">
            <div class="row_wrap">
                <div class="row">
                    <div class="col-12">
                        <div class="table-box-ty2 mobile">
                            <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <colgroup>
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                                  <col width="94px" />
                                  <col width="15%" />
                                </colgroup>
                                <tr>
                                    <th>담당자</th>
                                    <td>
                                      <div class="input_box">
                                        <input type="text" name="staff_nm2" class="form-control form-control-sm" value="{{$user->staff_nm2}}">
                                      </div>
                                    </td>
                                    <th>담당자 이메일</th>
                                    <td>
                                      <div class="input_box flax_box">
                                        <input type="text" name="staff_email1" class="form-control form-control-sm mr-1" style="width:100%;" value="{{$user->staff_email2}}">
                                      </div>
                                    </td>
                                    <th>담당자 연락처</th>
                                    <td>
                                      <div class="input_box flax_box">
                                        <input type="text" name="staff_phone1" class="form-control form-control-sm mr-1" style="width:80%;" value="{{$user->staff_phone2}}">(-)포함
                                      </div>
                                    </td>
                                    <th>담당자 휴대전화</th>
                                    <td>
                                      <div class="input_box flax_box">
                                        <input type="text" name="staff_hp1" class="form-control form-control-sm mr-1" style="width:80%;" value="{{$user->staff_hp2}}">(-)포함
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
      <div class="row mt-3">
        <div class="col-sm-6">
          <div class="card">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">전시카테고리</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                  <div id="gd-display" style="max-height:200px; height:200px; width:100%;" class="ag-theme-balham"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 mt-3 mt-sm-0">
          <div class="card">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">용도카테고리</a>
            </div>
            <div class="card-body">
              <div id="gd-item" style="max-height:200px; height:200px; width:100%;" class="ag-theme-balham"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script language="javascript">
  function getOption() {
    return {
      defaultColDef: {
          editable: true,
          resizable: true,
          suppressSizeToFit: true
      },
      components: {
          loadingRenderer: function (params) {
              if (params.data.d_cat_cd !== undefined) {
                  return params.node.rowIndex+1;
              } else {
                  return '<img src="https://raw.githubusercontent.com/ag-grid/ag-grid/master/grid-packages/ag-grid-docs/src/images/loading.gif">';
              }
          },
      },
      getRowNodeId: function(data) {
          return data.ord_opt_no;
      },
      columnDefs: columnDefs,
      rowSelection:'multiple'
    };
  }

  var columnDefs = [
      {
          headerName: '#',
          width:50,
          maxWidth: 100,
          // it is important to have node.id here, so that when the id changes (which happens
          // when the row is loaded) then the cell is refreshed.
          valueGetter: 'node.id',
          cellRenderer: 'loadingRenderer',
      },
      {field:"d_cat_cd" , headerName:"출고차수"},
      {field:"full_nm" , headerName:"출고차수", width:300}
  ];

  var itemOptions = getOption();
  var displayOptions = getOption();
</script>
<script>
  var displayGrid = new App('',{
      gridId:"#gd-display",
  });

  var itemGrid = new App('',{
      gridId:"#gd-item",
  });

  $(document).ready(function() {
      var displayGridDiv  = document.querySelector(displayGrid.options.gridId);
      new agGrid.Grid(displayGridDiv , displayOptions);
      displayGrid.ResizeGrid(250);

      var itemGridDiv  = document.querySelector(itemGrid.options.gridId);
      new agGrid.Grid(itemGridDiv , itemOptions);
      itemGrid.ResizeGrid(250);

      displayOptions.api.sizeColumnsToFit();
      const remInPixel = parseFloat(getComputedStyle(document.documentElement).fontSize);
      displayOptions.columnApi.getAllColumns().forEach(function (column) {
          if(column.colDef.width == undefined){
              const hn = column.colDef.headerName;
              const hnWidth = hn.length*2*remInPixel;
              displayOptions.columnApi.setColumnWidth(column.colId,hnWidth);
          }
      });

      Search();
  });

  function Search() {
    $.ajax({
        async: true,
        type: 'get',
        url: '/partner/user/category/display',
        success: function (res) {
            displayOptions.api.setRowData(res.body);
            $('#gd-total').text(res.body.length);
        },
        error: function(request, status, error) {
            console.log("error")
        }
    });

    $.ajax({
        async: true,
        type: 'get',
        url: '/partner/user/category/item',
        success: function (res) {
            itemOptions.api.setRowData(res.body);
            // $('#gd-total').text(res.body.length);
        },
        error: function(request, status, error) {
            console.log("error")
        }
    });
  }

  function openFindAddress(zipName, addName) {
    new daum.Postcode({
        // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다..
        oncomplete: function(data) {
          $("#" + zipName).val(data.zonecode);
          $("#" + addName).val(data.address);
        }
    }).open();
  }

  $('#copyAddress').click(function(){
    if ($("#zip_code").val() == "") {
      alert("사업장 주소를 검색후 클릭해주세요.");
      return;
    }

    $("#r_zip_code").val($("#zip_code").val());
    $("#r_addr1").val($("#addr1").val());
    $("#r_addr2").val($("#addr2").val());
  });

  $("#pw_change").change(function(){
    $("#pwd").attr("name", this.checked ? "pwd" : "" );
  });

  $("#submit-btn").click(function(){
    $.ajax({
        async: true,
        type: 'put',
        url: '/partner/user',
        data : $("[name=profile]").serialize(),
        success: function (data) {
          alert("수정되었습니다.");
          location.reload();
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
        }
    });
  });
</script>
<style>
  .card-header {
    font-weight:bold;
  }
  [name=profile] > .card > table th{
    padding:10px 20px;
  }
</style>
@endsection
