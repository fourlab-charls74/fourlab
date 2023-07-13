<nav>
	<div class="side_menu">
		<ul>
			@foreach ($menu_list as $lnb)
				<li>
					<a href="javascript:;" class="arrow waves-effect"><i class="bx {{ $lnb['icon'] }} fs-18"></i><span>{{ $lnb['kor_nm'] }}</span></a>
					@if(isset($lnb['sub']))
						<ul>
							@foreach ($lnb['sub'] as $depth1)
								@if(isset($depth1['sub']))
									<li class="depth2"><a href="javascript:;" class="arrow"></i><span>{{ $depth1['kor_nm'] }}</span></a>
										<ul>
											@foreach ($depth1['sub'] as $depth2)
												<li>
													<a href="{{ $depth2['action'] }}"@if($depth2['target']) target="{{ $depth2['target'] }}" @endif>
														@switch($depth2['state'])
															@case(0)
																@break
															@case(2)
																(개)
																@break
															@case(4)
																(테)
																@break
														@endswitch
														{{ $depth2['kor_nm'] }}</a>
												</li>
											@endforeach
										</ul>
									</li>
								@else
									<li><a href="{{ $depth1['action'] }}"@if($depth1['target']) target="{{ $depth1['target'] }}" @endif>
											@switch($depth1['state'])
												@case(0)
													@break
												@case(2)
													(개)
													@break
												@case(4)
													(테)
													@break
											@endswitch
											{{ $depth1['kor_nm'] }}
										</a></li>
								@endif
							@endforeach
						</ul>
					@endif
				</li>
			@endforeach
		</ul>
	</div>
</nav>
