<nav>
    <div class="side_menu">
        <ul>
        @foreach ($menu_list as $lnb)
        <li>
            <a href="javascript:;" class="arrow waves-effect"><i class="bx {{ $lnb['icon'] }} fs-18"></i><span>{{ $lnb['kor_nm'] }}</span></a>
            @if(isset($lnb['sub']))
            <ul>
                @foreach ($lnb['sub'] as $lnb_sub)
                <li><a href="{{ $lnb_sub['action'] }}"@if($lnb_sub['target']) target="{{ $lnb_sub['target'] }}" @endif>
                    @switch($lnb_sub['state'])
                        @case(0)
                            @break
                        @case(2)
                            (개)
                            @break
                        @case(4)
                            (테)
                            @break
                    @endswitch
                {{ $lnb_sub['kor_nm'] }}
                </a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
        </ul>
    </div>
</nav>

