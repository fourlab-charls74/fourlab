@extends('store_with.layouts.layout-nav')
@section('title','입고')
@section('content')

<!-- import excel lib -->
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<style>
    #help ul li {list-style: disc;list-style-position: inside;text-indent: -20px;padding-left: 20px;font-size: 13px;font-weight: 400;}
    #help p {font-size: 14px;font-weight: 700;padding-bottom: 5px;}
    .form-control[readonly] {
        background: #eeeeee;
    }
</style>
<div class="py-3 px-sm-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">입고 {{ $invoice_no ? (" - " . $invoice_no) : "" }}</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 생산입고관리</span>
        </div>
    </div>
    <form method="get" name="search">
        <input type="hidden" name="cmd" value="<?=$cmd ? $cmd : "" ?>">
        <input type="hidden" name='stock_no' value='<?=$stock_no ? $stock_no : ""?>'>
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>기본 정보</h4>
                    <div>
                        @if ($state > 0 && $state < 40)
                        <a href="javascript:void(0);" onclick="cmder('{{$cmd}}')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>저장</a>
                            @if ($stock_no != "" && $state < 30)
                            <a href="javascript:void(0);" onclick="cmder('delcmd')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>입고삭제</a>
                            @endif
                            @if ($state == 30)
                            <a href="javascript:void(0);" onclick="cmder('addstockcmd')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>추가입고</a>
                            <a href="javascript:void(0);" onclick="cmder('cancelcmd')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>입고취소</a>
                            @endif
                        @endif
                        <a href="javascript:void(0);" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                        <a href="javascript:void(0);" onclick="displayHelp()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx mr-1"></i>도움말</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="com_nm" class="required">공급처</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-75 pr-1">
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company sch-sup-company" value="<?=$com_nm ? $com_nm : ""?>">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                    <div class="form-inline-inner input-box w-25 pl-1">
                                        <input type="text" id="com_id" name="com_id" class="form-control form-control-sm" value="<?=$com_id ? $com_id : ""?>" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="invoice_no" class="required">송장번호</label>
                                <div class="flex_box">
                                    <input type="text" onfocus="return getInvoiceNo();" class="form-control form-control-sm" name="invoice_no" value="<?=$invoice_no ? $invoice_no : '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="" class="required">입고상태</label>
                                <div class="flex_box">
                                    <select name="state" class="form-control form-control-sm w-100">
                                        @foreach (@$states as $stt)
                                            <option value="{{ $stt['code_id'] }}" {{ $state == $stt['code_id'] ? 'selected' : '' }}>{{ $stt['code_val'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="formrow-firstname-input" class="required">입고일자</label>
                                <div class="flex_box">
                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                        <div class="input-group">
                                        <?php $stock_date = substr($stock_date, 0, 4).'-'.substr($stock_date, 4, 2) . '-' . substr($stock_date, 6, 2); ?>
                                            <input type="text" class="form-control form-control-sm docs-date" id="stock_date" 
                                                name="stock_date" value="<?=$stock_date ? $stock_date : ""?>" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="f_sqty" class="required">환율</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 pr-2">
                                        <select id="currency_unit" name="currency_unit" class="form-control form-control-sm w-100" onchange="changeUnit(this)">
                                            <?php
                                                $currencies = ['KRW', 'USD', 'EUR', 'JPY', 'CNY', 'HKD'];
                                                collect($currencies)->map(function ($currency) use ($currency_unit) {
                                                    $selected = ($currency == $currency_unit) ? "selected" : "";
                                                    echo "<option value='${currency}' $selected>${currency}</option>";
                                                });
                                            ?>
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center form-inline-inner input-box w-75">
                                        <input readonly disabled type='text' class="form-control form-control-sm" name='exchange_rate' id='exchange_rate' 
                                            value='<?= $exchange_rate ? $exchange_rate : 0 ?>' style="width:100%;" onkeypress="checkFloat(this);" onkeyup="com3(this);calCustomTaxRate();" onfocus="this.select()">
                                        <span class="ml-2">원</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="custom_amt" class="required">(신고)금액</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm text-right" id="custom_amt" name="custom_amt" value="<?= $custom_amt ? $custom_amt : 0 ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="custom_total_amt" class="required">관세총액/관세율</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm w-75" id="custom_total_amt" name="custom_total_amt" value="<?= @$custom_total_amt ? $custom_total_amt : 0 ?>"
                                        onfocus="this.select()" onkeypress="checkFloat(this);" onkeyup="com3(this);calCustomTaxRate();" <?=$currency_unit == "KRW" ? "readonly disabled" : ""?>
                                    >
                                    <div class="d-flex align-items-center w-25 pl-2">
                                        <input type="text" class="form-control form-control-sm text-right mr-1" id="custom_rate" name="custom_rate" value="<?= @$custom_rate ? $custom_rate : 0 ?>" readonly>
                                        <span>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="freight_amt" class="required">운임비/운임율</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm w-75" id="freight_amt" name="freight_amt" value="<?= @$freight_amt ? $freight_amt : 0 ?>"
                                        onfocus="this.select()" onkeypress="checkFloat(this);" onkeyup="com3(this);calCustomTaxRate();" <?=$currency_unit == "KRW" ? "readonly disabled" : ""?>
                                    >
                                    <div class="d-flex align-items-center w-25 pl-2">
                                        <input type="text" class="form-control form-control-sm text-right mr-1" id="freight_rate" name="freight_rate" value="<?= @$freight_rate ? $freight_rate : 0 ?>" readonly>
                                        <span>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="custom_tax" class="required">통관비/통관세율</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm" id="custom_tax" name="custom_tax" value="<?= $custom_tax ? $custom_tax : 0 ?>" readonly style="width:30%;">
                                    <div class="form-inline inline_select_box pl-2" style="width:70%;">
                                        <div class="d-flex align-items-center form-inline-inner input-box w-50">
                                            <input type='text' class="form-control form-control-sm text-right mr-1" name='custom_tax_rate' id='custom_tax_rate' value='{{ $custom_tax_rate ? $custom_tax_rate : "" }}' style="width:100%;"
                                                <?=$currency_unit == "KRW" ? "disabled" : ""?> onfocus="this.select()" onkeypress="checkFloat(this);" readonly
                                            >
                                            <span>%</span>
                                        </div>
                                        <div class="w-50 pl-2">
                                            <a href="#" class="btn btn-sm btn-outline-primary shadow-sm w-100" onclick="calExchange();" onfocus="this.blur()">환율/세율 적용</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="bl_no">B/L No.</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm" name="bl_no">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="area_type">위치</label>
                                <div class="flex_box">
                                    <select name="loc" class="form-control form-control-sm w-100">
                                        @foreach ($locs as $item)
                                            <option value='{{ $item->code_id }}' selected='{{ $item == $loc ? selected : "" }}'>{{ $item->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="f_sqty">파일</label>
                                <div class="d-flex">
                                    <div class="custom-file w-100">
                                        <input name="excel_file" type="file" class="custom-file-input" id="excel_file">
                                        <label class="custom-file-label" for="file"></label>
                                    </div>
                                    <div style="min-width: 120px;">
                                        <div class="btn-group ml-2">
                                            <button class="btn btn-outline-primary apply-btn" type="button" onclick="upload();">적용</button>
                                        </div>
                                        <a href="/sample/sample_store_stock_order.xlsx" class="ml-2" style="text-decoration: underline !important;">샘플파일</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="help" class="row" style="display: none;">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label style="color: #0000ff;">도움말<i class="fa fa-question-circle ml-1" aria-hidden="true"></i></label>
                                <div class="d-flex">
                                    <div class="d-flex flex-column" style="width: 20%; min-width: 250px;">
                                        <p>기본정보</p>
                                        <ul>
                                            <li>관세율 = 관세총액 &divide; (신고)금액</li>
                                            <li>운임율 = 운임비 &divide; (신고)금액</li>
                                            <li>통관비 = 관세총액 &plus; 운임비</li>
                                            <li>통관세율 = 통관비 &divide; (신고)금액</li>
                                        </ul>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <p>상품정보</p>
                                        <ul>        
                                            <li>금액 = 수량 x 단가</li>
                                            <li>수입금액 = 환율 x 단가</li>
                                            <li>총수입금액 = 환율 x 수량 x 단가</li>
                                            <li>총원가 = 총수입금액 &plus; (총수입금액 x 통관세율)</li>
                                            <li>개당원가 = 총원가 &divide; 수량</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                @if ($state > 0 && $state < 40)
                <a href="javascript:void(0);" onclick="cmder('{{$cmd}}')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>저장</a>
                    @if ($stock_no != "" && $state < 30)
                    <a href="javascript:void(0);" onclick="cmder('delcmd')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>입고삭제</a>
                    @endif
                    @if ($state == 30)
                    <a href="javascript:void(0);" onclick="cmder('addstockcmd')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>추가입고</a>
                    <a href="javascript:void(0);" onclick="cmder('cancelcmd')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>입고취소</a>
                    @endif
                @endif
                <a href="javascript:void(0);" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                <a href="javascript:void(0);" onclick="displayHelp()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx mr-1"></i>도움말</a>
            </div>
        </div>
        <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
            <div class="card-body">
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fr_box">
                            <a href="#" onclick="deleteRows();" class="btn-sm btn btn-primary" onfocus="this.blur()">상품삭제</a>
                            <a href="#" onclick="getSearchGoods();" class="btn-sm btn btn-primary" onfocus="this.blur()">상품 가져오기</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" charset="utf-8">
    

    const calExchange = async () => {

        var ff = document.search;
        var unit = ff.currency_unit.value;
        var exchange_rate = unComma(ff.exchange_rate.value);
        var custom_tax = ff.custom_tax.value;
        var custom_amt = ff.custom_amt.value;

        const state = STATE; // 입고취소: -10, 입고대기: 10, 입고처리중: 20, 입고완료: 30
        // 입고 대기이거나 입고 처리중인 경우에만 환율 및 세율 적용 가능하게함
        
        if (state == -10 || state >= 30) {
        } else {
            const rows = gx.getRows();
            if (rows && Array.isArray(rows) && rows.length > 0) {
                await rows.map((row, idx) => {
                    calProduct(row, unit, exchange_rate, custom_tax, custom_amt);
                })
                updatePinnedRow();
            }
        }

    };

</script>

@stop