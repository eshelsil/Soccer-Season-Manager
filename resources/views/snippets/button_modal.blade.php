<div class="{{$wrapper_class ?? "pr-1 pl-1"}}">
  <button type="button" id="{{$button_id}}" class="btn {{$button_class ?? "btn-danger"}}" data-toggle="modal" data-target="#{{$button_id}}_modal">
    {{$button_label}}
  </button>

  <div class="modal fade" id="{{$button_id}}_modal" tabindex="-1" role="dialog" aria-labelledby="{{$button_id}}_modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="{{$button_id}}_modal_label">{{$title}}</h5>
          <button type="button" id="{{$button_id}}_dismiss_modal" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <h6>{{$msg}}</h6>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{$cancel_label}}</button>
        <button type="button" id="{{$button_id}}_confirm" ng-click="{{$button_action}}()" class="btn {{$button_class ?? "btn-danger"}}">{{$action_label}}</button>
        </div>
      </div>
    </div>
  </div>
</div>