<?php
    use App\Components\Lib;
    /**
     * View - Blade에서 컴포넌트 사용하는 방법
     * 
     * arrow: 툴팁 방향 (top, left, right, bottom)
     * align: 툴팁 내용 정렬 방향 (left, center, right) - 현재 center 안먹고 있음
     * html: 툴팁 내용
     * 
     * 예)
     * <x-tool-tip>
     *      <x-slot name="arrow">top</x-slot>
     *      <x-slot name="align">left</x-slot>
     *      <x-slot name="html">
     *          툴팁내용
     *      </x-slot>
     * </x-tool-tip>
     * 
     * bootstrap 에서는 html 태그 안에 스타일 속성을 넣으면 자동으로 필터링되고 있음 - 글자색 사용 불가능
     * style의 point-events: none;은 클릭했을 경우 툴팁을 유지함
     */

    $html = Lib::quote($html); // 큰 따옴표, 작은 따옴표 충돌 제거

    $box_color = "#FFF59D";
    $tool_tip_color = "#556ee6";
?>

<style>
    .bs-tooltip-top .arrow::before, .bs-tooltip-auto[x-placement^=top] .arrow::before {
        border-top-color: {{$box_color}};
    }
    .bs-tooltip-right .arrow::before, .bs-tooltip-auto[x-placement^=right] .arrow::before {
        border-right-color: {{$box_color}};
    }
    .bs-tooltip-bottom .arrow::before, .bs-tooltip-auto[x-placement^=bottom] .arrow::before {
        border-bottom-color: {{$box_color}};
    }
    .bs-tooltip-left .arrow::before, .bs-tooltip-auto[x-placement^=left] .arrow::before {
        border-left-color: {{$box_color}};   
    }     
    .custom.tooltip .tooltip-inner {
        background-color: {{$box_color}};
        padding-bottom: 10px;
        color: inherit;
        text-align: {{$align}};
    }
</style>

<button type="button" class="tool-tip" data-toggle="tooltip" data-placement="{{$arrow}}" data-html="true"
    data-template="
        <div class='custom tooltip' role='tooltip'>
            <div class='arrow'></div>
            <div class='tooltip-inner'></div>
        </div>'"
    style="border: none; background: none;" title="{{$html}}">
    <i class="fas fa-question-circle" style="color: {{$tool_tip_color}}; pointer-events: none;"></i>
</button>

<script>

</script>