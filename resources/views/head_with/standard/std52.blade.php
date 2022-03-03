@extends('head_with.layouts.layout')
@section('title','테스트관리')
@section('content')

<!-- 상단 타이틀 -->
<div class="page_tit">
    <h3 class="d-inline-flex">테스트관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보</span>
        <span>/ 테스트관리</span>
    </div>
</div>


<!-- 등록 영역 -->
<form method="get" name="search" id="search">
    <button type="button" class="btn btn-sm btn-primary shadow-lg" onclick="saveItemList()" style="margin-bottom: 20px;">저장</button>
    <div id="search-area" class="search_cum_form"></div>
</form>

@include('head_with.standard.std52_addbox')

<!-- Javascript -->
<script type="text/javascript" charset="utf-8">

    var addList = [];
    var count = 1;
    var parents = document.querySelector("#search-area");

    // 아이템 추가
    function addItem() {
        if(addList.length > 9) return alert("최대 10개까지 등록 가능합니다.");
        addList = addList.concat({id: count});
        var item = document.createElement("div");
        item.className = "card mb-3";
        item.id = "item" + count;
        item.innerHTML = getAddBox({id: count});
        parents.appendChild(item);
        count++;
    }

    // 아이템 삭제
    function deleteItem(id) {
        console.log(id);
        if(addList.length < 2) return alert("최소 1개의 등록이 필요합니다.");
        addList = addList.filter((item) => item.id !== id);
        var item = document.querySelector(`#item${id}`);
        parents.removeChild(item);
    }

    // 저장버튼 클릭 시 실행
    function saveItemList() {
        alert("콘솔창을 확인해주세요.");
        var result = $("#search").serialize();
        console.log(test);
    }

    function showBox(id, bool) {
        var brand_row = document.querySelector(`#brand_show_${id}`);
        brand_row.style.display = bool ? "flex" : "none";
    }

    $(document).ready(function() {
        addItem();
    });

</script>

@stop