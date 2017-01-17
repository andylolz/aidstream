<div class="activity-element-wrapper">
    @if ($activity->budget)
        <a href="{{ route('lite.activity.budget.edit', $activity->id) }}"
           class="edit-element">
            <span>Edit Budget</span>
        </a>
        <div>
            {{--{!! Form::open(['method' => 'POST', 'route' => ['lite.activity.budget.destroy', $activity->id]]) !!}
            {!! Form::submit('Delete', ['class' => 'pull-left delete-transaction']) !!}
            {!! Form::close() !!}--}}
        </div>
        <div class="activity-element-list">
            <div class="activity-element-label">
                Budget
            </div>
            <div class="activity-element-info">
                @foreach ($activity->budget as $index => $budget)
                    <li>
                        {{ getVal($budget, ['value', 0, 'amount']) }} {{ getVal($budget, ['value', 0, 'currency']) }} [{{ getVal($budget, ['period_start', 0, 'date']) }}
                        - {{ getVal($budget, ['period_end', 0, 'date']) }}]

                        <form action="{{ route('lite.activity.budget.delete', $activity->id) }}" method="POST">
                            {{ csrf_field() }}
                            <input type="hidden" value="{{ $index }}" name="index">
                            <input type="submit" class="btn btn-primary btn-xs pull-right" value="{{ trans('global.delete') }}" />
                        </form>

                        {{--<a href="{{route('lite.activity.budget.delete', [$activity->id, 'budget'])}}" class="pull-right">@lang('global.delete')</a>--}}

                    </li>
                @endforeach
            </div>
            <a href="{{ route('lite.activity.budget.create', $activity->id) }}"
               class="add-more"><span>Add Budget</span></a>
        </div>
    @else
        <div class="activity-element-list">
            <div class="title">
                Budget
            </div>
            <a href="{{ route('lite.activity.budget.create', $activity->id) }}"
               class="add-more"><span>Add Budget</span></a>
        </div>
    @endif
</div>
