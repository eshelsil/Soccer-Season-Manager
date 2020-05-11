@php
    $initial_value = $initial_value ?? 6;
    $with_all_option = $with_all_option ?? false;
    // if (!$key_as_value){
    //     $options_param = $options;
    //     $options = [];
    //     for ($options_param as $val){
    //         $options[$val] = $val;
    //     })
    // }
    // if ($with_all_option){
    //     $options['all'] = $all_label ?? '---';
    // }
    // $format_attrs = ['with_all_option' => $with_all_option, 'all_label' => $all_label ?? null]
    // $using_ng_option = $ng_options_func ?? false;
    
@endphp
<label for="{{$id}}" class="col pl-0">{{$label}}</label>
    <select  id="{{$id}}"  ng-model="{{$ng_model}}" class="custom-select" style="width:auto;">
    {{-- <option ng-repeat="opt in format_select_options({{$ng_options_func}}())" value="@{{opt.value}}" selected="">@{{opt.label}}</option> --}}
    <option ng-repeat="opt in {{$options_var}}" ng-value="'@{{opt.value}}'">@{{opt.label}}</option>
    {{-- @if ($with_all_option)
    <option value='all' {{is_null($initial_value) ? 'selected' : ''}}>{{$all_label ?? '---'}}</option>
    @endif
    @if ($using_ng_option)
    @else

    @foreach ($options as $value => $label)
        @php if (!$key_as_value){
            $value = $label;
        }
        @endphp
        <option value="{{$value}}" {{$initial_value == $value ? 'selected' : ''}}>{{$label}}</option>
    @endforeach

    @endif --}}
</select>
