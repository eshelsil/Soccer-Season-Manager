@php
    $initial_value = $initial_value ?? null;
    $with_all_option = $with_all_option ?? false;
    $key_as_value = $key_as_value ?? false;
@endphp
<label for="{{$id}}" class="col pl-0">{{$label}}</label>
<select  id="{{$id}}" class="custom-select" style="width:auto;" value="{{$initial_value}}">
    @if ($with_all_option)
    <option value='all' {{is_null($initial_value) ? 'selected' : ''}}>{{$all_label ?? '---'}}</option>
    @endif
    @foreach ($options as $value => $label)
        @php if (!$key_as_value){
            $value = $label;
        }
        @endphp
        <option value="{{$value}}" {{$initial_value == $value ? 'selected' : ''}}>{{$label}}</option>
    @endforeach
</select>
