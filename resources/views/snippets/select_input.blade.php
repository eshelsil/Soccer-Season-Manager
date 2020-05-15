<label for="{{$id}}" class="col pl-0">{{$label}}</label>
    <select  id="{{$id}}"  ng-model="{{$ng_model}}" class="custom-select" style="width:auto;">
        <option ng-repeat="opt in {{$options_var}}" ng-value="'@{{opt.value}}'">@{{opt.label}}</option>
</select>
